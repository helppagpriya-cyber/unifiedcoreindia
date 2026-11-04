<?php

namespace App\Services\CRM;

use App\Models\CrmLead;
use App\Models\CrmLeadAssignment;
use App\Models\CrmLeadImportBatch;
use Illuminate\Support\Facades\DB;
use Throwable;

class CrmLeadImportService
{
    public function import(array $preview, string $filePath): CrmLeadImportBatch
    {
        $rows = $preview['rows'] ?? [];

        $removedRows = (int) ($preview['removed_rows'] ?? 0);

        $originalTotal = (int) (
            $preview['original_total'] ?? (count($rows) + $removedRows)
        );

        $batch = CrmLeadImportBatch::create([
            'file_name' => basename($filePath),
            'imported_by' => auth()->id(),
            'total_rows' => $originalTotal,
            'successful_rows' => 0,
            'skipped_rows' => 0,
            'failed_rows' => 0,
            'status' => 'processing',
            'started_at' => now(),
        ]);

        $successful = 0;
        $skipped = $removedRows;
        $failed = 0;

        $rowResults = [];

        foreach ($rows as $row) {
            $rowNumber = $row['row_number'] ?? null;
            $company = $row['data']['company_name'] ?? null;
            $status = $row['status'] ?? 'unknown';

            $selected = (bool) ($row['include_in_import'] ?? false);

            /*
             * Invalid rows can never be imported.
             *
             * Duplicate and possible_duplicate rows can be
             * imported only when manually selected.
             */
            $allowedStatuses = [
                'ready',
                'duplicate',
                'possible_duplicate',
            ];

            if (! $selected || ! in_array($status, $allowedStatuses, true)) {
                $skipped++;

                $reason = match ($status) {
                    'invalid' => 'Skipped: validation errors.',
                    'duplicate' => 'Skipped: duplicate row not approved.',
                    'possible_duplicate' => 'Skipped: possible duplicate not approved.',
                    'ready' => 'Skipped: not selected for import.',
                    default => 'Skipped: row is not eligible for import.',
                };

                $rowResults[] = [
                    'row_number' => $rowNumber,
                    'company_name' => $company,
                    'result' => 'skipped',
                    'reason' => $reason,
                ];

                continue;
            }

            try {
                $lead = DB::transaction(function () use (
                    $row,
                    $batch
                ) {
                    $data = $row['data'] ?? [];

                    /*
                     * Only permit fields belonging to CRM leads.
                     * Never accept lead_number or created_by
                     * from an uploaded spreadsheet.
                     */
                    $allowedFields = [
                        'lead_date',
                        'company_name',
                        'business_type',
                        'address',
                        'city',
                        'state',
                        'pincode',
                        'country',
                        'gst_number',
                        'pan_number',
                        'contact_person',
                        'mobile',
                        'alternate_mobile',
                        'whatsapp_number',
                        'email',
                        'product_interested',
                        'existing_software',
                        'requirements',
                        'estimated_budget',
                        'source_id',
                        'status_id',
                        'priority',
                        'assigned_to',
                        'next_follow_up_at',
                        'notes',
                    ];

                    $leadData = array_intersect_key(
                        $data,
                        array_flip($allowedFields)
                    );

                    $leadData['created_by'] = auth()->id();

                    $lead = CrmLead::create($leadData);

                    /*
                     * Create assignment history when assigned.
                     */
                    if ($lead->assigned_to) {
                        $assignment = new CrmLeadAssignment();

                        $assignment->forceFill([
                            'assigned_to' => $lead->assigned_to,
                            'assigned_by' => auth()->id(),
                            'assigned_at' => now(),
                            'remarks' => 'Assigned during CRM lead import. Batch #' . $batch->id,
                        ]);

                        $lead->assignments()->save($assignment);
                    }

                    return $lead;
                });

                $successful++;

                $rowResults[] = [
                    'row_number' => $rowNumber,
                    'company_name' => $company,
                    'result' => 'imported',
                    'lead_id' => $lead->id,
                    'lead_number' => $lead->lead_number,
                    'reason' => null,
                ];
            } catch (Throwable $exception) {
                report($exception);

                $failed++;

                $rowResults[] = [
                    'row_number' => $rowNumber,
                    'company_name' => $company,
                    'result' => 'failed',
                    'reason' => 'Database error while creating this lead. Check the application log.',
                ];
            }
        }

        $batch->update([
            'successful_rows' => $successful,
            'skipped_rows' => $skipped,
            'failed_rows' => $failed,

            'status' => ($failed > 0 && $successful === 0)
                ? 'failed'
                : 'completed',

            'summary' => [
                'removed_rows' => $removedRows,
                'row_results' => $rowResults,
            ],

            'completed_at' => now(),
        ]);

        return $batch->fresh();
    }
}
