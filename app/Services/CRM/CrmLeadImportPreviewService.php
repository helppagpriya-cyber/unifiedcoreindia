<?php

namespace App\Services\CRM;

use App\Imports\CrmLeadPreviewRowsImport;
use App\Models\CrmLead;
use App\Models\CrmLeadSource;
use App\Models\CrmLeadStatus;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use Throwable;

class CrmLeadImportPreviewService
{
    public function preview(string $path, string $disk = 'local'): array
    {
        $import = new CrmLeadPreviewRowsImport();

        Excel::import($import, $path, $disk);

        $sources = CrmLeadSource::where('is_active', true)->get();
        $statuses = CrmLeadStatus::where('is_active', true)->get();

        $sourceMap = $this->makeLookup($sources);
        $statusMap = $this->makeLookup($statuses);

        $userMap = User::query()
            ->whereNotNull('email')
            ->get(['id', 'email'])
            ->keyBy(fn($user) => strtolower(trim($user->email)));

        $rows = [];
        $gstNumbers = [];
        $mobiles = [];
        $emails = [];
        $companyNames = [];

        foreach ($import->rows as $index => $row) {
            $data = $this->cleanRow($row->toArray());

            if (empty(array_filter($data, fn($value) => $value !== null && $value !== ''))) {
                continue;
            }

            $rowNumber = $index + 2;

            $data['company_name'] = $this->cleanText($data['company_name'] ?? null);
            $data['gst_number'] = $this->normalizeGst($data['gst_number'] ?? null);
            $data['mobile'] = $this->normalizePhone($data['mobile'] ?? null);
            $data['alternate_mobile'] = $this->normalizePhone($data['alternate_mobile'] ?? null);
            $data['whatsapp_number'] = $this->normalizePhone($data['whatsapp_number'] ?? null);
            $data['email'] = $this->normalizeEmail($data['email'] ?? null);

            $data['priority'] = strtolower(trim((string) ($data['priority'] ?? 'normal')));
            $data['priority'] = $data['priority'] ?: 'normal';

            $data['source'] = $this->cleanText($data['source'] ?? null) ?: 'Other';
            $data['status'] = $this->cleanText($data['status'] ?? null) ?: 'New';

            $data['lead_date'] = $this->normalizeDate($data['lead_date'] ?? null, false);
            $data['next_follow_up_at'] = $this->normalizeDate(
                $data['next_follow_up_at'] ?? null,
                true
            );

            $errors = [];

            $validator = Validator::make($data, [
                'company_name' => ['required', 'string', 'max:255'],
                'contact_person' => ['nullable', 'string', 'max:255'],
                'mobile' => ['nullable', 'string', 'max:255'],
                'alternate_mobile' => ['nullable', 'string', 'max:255'],
                'whatsapp_number' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'business_type' => ['nullable', 'string', 'max:255'],
                'address' => ['nullable', 'string'],
                'city' => ['nullable', 'string', 'max:255'],
                'state' => ['nullable', 'string', 'max:255'],
                'pincode' => ['nullable', 'numeric', 'max:999999'],
                'country' => ['nullable', 'string', 'max:255'],
                'gst_number' => ['nullable', 'string', 'max:30'],
                'pan_number' => ['nullable', 'string', 'max:20'],
                'product_interested' => ['nullable', 'string', 'max:255'],
                'existing_software' => ['nullable', 'string', 'max:255'],
                'requirements' => ['nullable', 'string'],
                'estimated_budget' => ['nullable', 'numeric', 'min:0'],
                'priority' => ['required', 'in:low,normal,high,urgent'],
                'lead_date' => ['nullable', 'date'],
                'next_follow_up_at' => ['nullable', 'date'],
            ]);

            if ($validator->fails()) {
                $errors = array_merge($errors, $validator->errors()->all());
            }

            $source = $sourceMap[$this->lookupKey($data['source'])] ?? null;

            if (!$source) {
                $errors[] = "Unknown or inactive lead source: {$data['source']}";
            }

            $status = $statusMap[$this->lookupKey($data['status'])] ?? null;

            if (!$status) {
                $errors[] = "Unknown or inactive lead status: {$data['status']}";
            }

            $assignedEmail = $this->normalizeEmail($data['assigned_to_email'] ?? null);
            $assignedUser = $assignedEmail ? ($userMap[$assignedEmail] ?? null) : null;

            if ($assignedEmail && !$assignedUser) {
                $errors[] = "Employee email not found: {$assignedEmail}";
            }

            $data['source_id'] = $source?->id;
            $data['status_id'] = $status?->id;
            $data['assigned_to'] = $assignedUser?->id;

            $data['country'] = $data['country'] ?: 'India';

            $rows[] = [
                'row_number' => $rowNumber,
                'data' => $data,
                'errors' => $errors,
                'status' => $errors ? 'invalid' : 'ready',
                'duplicate_type' => null,
                'duplicate_message' => null,
            ];

            if ($data['gst_number']) {
                $gstNumbers[] = $data['gst_number'];
            }

            if ($data['mobile']) {
                $mobiles[] = $data['mobile'];
            }

            if ($data['email']) {
                $emails[] = $data['email'];
            }

            if ($data['company_name']) {
                $companyNames[] = $this->lookupKey($data['company_name']);
            }
        }

        $existingLeads = CrmLead::withTrashed()
            ->where(function ($query) use ($gstNumbers, $mobiles, $emails, $companyNames) {
                if ($gstNumbers) {
                    $query->orWhereIn('gst_number', array_unique($gstNumbers));
                }

                if ($mobiles) {
                    $query->orWhereIn('mobile', array_unique($mobiles));
                }

                if ($emails) {
                    $query->orWhereIn('email', array_unique($emails));
                }

                if ($companyNames) {
                    $query->orWhereIn('company_name', array_unique(
                        array_map(fn($name) => $name, $companyNames)
                    ));
                }
            })
            ->get([
                'id',
                'lead_number',
                'company_name',
                'gst_number',
                'mobile',
                'email',
            ]);

        $existingGst = [];
        $existingMobile = [];
        $existingEmail = [];
        $existingCompany = [];

        foreach ($existingLeads as $lead) {
            if ($key = $this->normalizeGst($lead->gst_number)) {
                $existingGst[$key] = $lead;
            }

            if ($key = $this->normalizePhone($lead->mobile)) {
                $existingMobile[$key] = $lead;
            }

            if ($key = $this->normalizeEmail($lead->email)) {
                $existingEmail[$key] = $lead;
            }

            if ($key = $this->lookupKey($lead->company_name)) {
                $existingCompany[$key] = $lead;
            }
        }

        $seenGst = [];
        $seenMobile = [];
        $seenEmail = [];
        $seenCompany = [];

        foreach ($rows as &$item) {
            $data = $item['data'];

            $duplicate = null;

            if ($data['gst_number'] && isset($existingGst[$data['gst_number']])) {
                $duplicate = ['GST number', $existingGst[$data['gst_number']]];
            } elseif ($data['mobile'] && isset($existingMobile[$data['mobile']])) {
                $duplicate = ['Mobile number', $existingMobile[$data['mobile']]];
            } elseif ($data['email'] && isset($existingEmail[$data['email']])) {
                $duplicate = ['Email address', $existingEmail[$data['email']]];
            } elseif ($data['gst_number'] && isset($seenGst[$data['gst_number']])) {
                $duplicate = ['GST number in uploaded file', $seenGst[$data['gst_number']]];
            } elseif ($data['mobile'] && isset($seenMobile[$data['mobile']])) {
                $duplicate = ['Mobile number in uploaded file', $seenMobile[$data['mobile']]];
            } elseif ($data['email'] && isset($seenEmail[$data['email']])) {
                $duplicate = ['Email address in uploaded file', $seenEmail[$data['email']]];
            }

            if ($duplicate) {
                $item['status'] = 'duplicate';
                $item['duplicate_type'] = 'strong';
                $item['duplicate_message'] = $duplicate[0];

                if ($duplicate[1] instanceof CrmLead) {
                    $item['duplicate_message'] .= ' already exists in lead ' .
                        ($duplicate[1]->lead_number ?: "#{$duplicate[1]->id}");
                } else {
                    $item['duplicate_message'] .= " (first found in row {$duplicate[1]})";
                }
            } elseif (
                $data['company_name'] &&
                isset($existingCompany[$this->lookupKey($data['company_name'])])
            ) {
                $lead = $existingCompany[$this->lookupKey($data['company_name'])];

                $item['status'] = $item['errors'] ? 'invalid' : 'possible_duplicate';
                $item['duplicate_type'] = 'possible';
                $item['duplicate_message'] = 'Company name matches existing lead ' .
                    ($lead->lead_number ?: "#{$lead->id}") .
                    '. Please review manually.';
            } elseif (
                $data['company_name'] &&
                isset($seenCompany[$this->lookupKey($data['company_name'])])
            ) {
                $item['status'] = $item['errors'] ? 'invalid' : 'possible_duplicate';
                $item['duplicate_type'] = 'possible';
                $item['duplicate_message'] = "Company name also appears in uploaded row {$seenCompany[$this->lookupKey($data['company_name'])]}.";
            }

            if ($data['gst_number']) {
                $seenGst[$data['gst_number']] = $item['row_number'];
            }

            if ($data['mobile']) {
                $seenMobile[$data['mobile']] = $item['row_number'];
            }

            if ($data['email']) {
                $seenEmail[$data['email']] = $item['row_number'];
            }

            if ($data['company_name']) {
                $seenCompany[$this->lookupKey($data['company_name'])] = $item['row_number'];
            }
        }

        unset($item);

        return [
            'rows' => $rows,
            'summary' => [
                'total' => count($rows),
                'ready' => count(array_filter($rows, fn($row) => $row['status'] === 'ready')),
                'invalid' => count(array_filter($rows, fn($row) => $row['status'] === 'invalid')),
                'duplicates' => count(array_filter($rows, fn($row) => $row['status'] === 'duplicate')),
                'possible_duplicates' => count(array_filter($rows, fn($row) => $row['status'] === 'possible_duplicate')),
            ],
        ];
    }

    private function makeLookup($records): array
    {
        $lookup = [];

        foreach ($records as $record) {
            $lookup[$this->lookupKey($record->name)] = $record;
            $lookup[$this->lookupKey($record->slug)] = $record;
        }

        return $lookup;
    }

    private function cleanRow(array $row): array
    {
        return collect($row)
            ->map(fn($value) => is_string($value) ? trim($value) : $value)
            ->toArray();
    }

    private function cleanText($value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function normalizeEmail($value): ?string
    {
        $value = strtolower(trim((string) ($value ?? '')));

        return $value === '' ? null : $value;
    }

    private function normalizeGst($value): ?string
    {
        $value = strtoupper(preg_replace('/\s+/', '', (string) ($value ?? '')));

        return $value === '' ? null : $value;
    }

    private function normalizePhone($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = preg_replace('/\.0$/', '', (string) $value);
        $value = preg_replace('/\D+/', '', $value);

        return $value === '' ? null : $value;
    }

    private function lookupKey($value): string
    {
        return Str::lower(trim((string) ($value ?? '')));
    }

    private function normalizeDate($value, bool $withTime): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value) && (float) $value > 0) {
                $date = ExcelDate::excelToDateTimeObject((float) $value);
                $date = Carbon::instance($date);
            } else {
                $date = Carbon::parse($value);
            }

            return $withTime
                ? $date->format('Y-m-d H:i:s')
                : $date->format('Y-m-d');
        } catch (Throwable) {
            return $value;
        }
    }
}
