<?php

namespace App\Services\CRM;

use App\Imports\CrmLeadPreviewRowsImport;
use App\Models\CrmLead;
use App\Models\CrmLeadSource;
use App\Models\CrmLeadStatus;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use Throwable;

class CrmLeadImportPreviewService
{
    public function preview(string $path, string $disk = 'local'): array
    {
        $import = new CrmLeadPreviewRowsImport($this->getDataRowLimit($path, $disk));

        Excel::import($import, $path, $disk);

        $sources = CrmLeadSource::where('is_active', true)->get();
        $statuses = CrmLeadStatus::where('is_active', true)->get();

        $sourceMap = $this->makeLookup($sources);
        $statusMap = $this->makeLookup($statuses);

        $userMap = User::query()
            ->where('is_active', true)
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

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $rowNumber = $import->rowNumbers[$index] ?? ($index + 2);

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

            $errors = array_merge($errors, $this->validateLeadData($data));

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
                $companyNames[] = $data['company_name'];
            }
        }

        $existingLeads = $this->findExistingLeads(
            $gstNumbers,
            $mobiles,
            $emails,
            $companyNames,
        );

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
                $duplicateValue = match (true) {
                    str_contains($duplicate[0], 'GST number') => $data['gst_number'],
                    str_contains($duplicate[0], 'Mobile number') => $data['mobile'],
                    str_contains($duplicate[0], 'Email address') => $data['email'],
                    default => null,
                };
                $item['duplicate_message'] = $duplicate[0] . ($duplicateValue ? " ({$duplicateValue})" : '');

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
                    ($lead->lead_number ?: "#{$lead->id}") . ' (' . $data['company_name'] . ')' .
                    '. Please review manually.';
            } elseif (
                $data['company_name'] &&
                isset($seenCompany[$this->lookupKey($data['company_name'])])
            ) {
                $item['status'] = $item['errors'] ? 'invalid' : 'possible_duplicate';
                $item['duplicate_type'] = 'possible';
                $item['duplicate_message'] = "Company name ({$data['company_name']}) also appears in uploaded row {$seenCompany[$this->lookupKey($data['company_name'])]}.";
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

    public function revalidateRow(array $row, array $allRows): array
    {
        $previousStatus = $row['status'] ?? null;
        $wasIncluded = (bool) ($row['include_in_import'] ?? false);
        $data = $this->cleanRow($row['data'] ?? []);
        $data['company_name'] = $this->cleanText($data['company_name'] ?? null);
        $data['gst_number'] = $this->normalizeGst($data['gst_number'] ?? null);
        $data['mobile'] = $this->normalizePhone($data['mobile'] ?? null);
        $data['alternate_mobile'] = $this->normalizePhone($data['alternate_mobile'] ?? null);
        $data['whatsapp_number'] = $this->normalizePhone($data['whatsapp_number'] ?? null);
        $data['email'] = $this->normalizeEmail($data['email'] ?? null);
        $data['priority'] = strtolower(trim((string) ($data['priority'] ?? 'normal'))) ?: 'normal';
        $data['source'] = $this->cleanText($data['source'] ?? null) ?: 'Other';
        $data['status'] = $this->cleanText($data['status'] ?? null) ?: 'New';
        $data['lead_date'] = $this->normalizeDate($data['lead_date'] ?? null, false);
        $data['next_follow_up_at'] = $this->normalizeDate($data['next_follow_up_at'] ?? null, true);
        $data['country'] = $this->cleanText($data['country'] ?? null) ?: 'India';

        $errors = $this->validateLeadData($data);
        $source = CrmLeadSource::query()->where('is_active', true)
            ->where(fn ($query) => $query
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($data['source'])])
                ->orWhereRaw('LOWER(slug) = ?', [mb_strtolower($data['source'])]))
            ->first();
        $status = CrmLeadStatus::query()->where('is_active', true)
            ->where(fn ($query) => $query
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($data['status'])])
                ->orWhereRaw('LOWER(slug) = ?', [mb_strtolower($data['status'])]))
            ->first();

        if (! $source) {
            $errors[] = "Unknown or inactive lead source: {$data['source']}";
        }

        if (! $status) {
            $errors[] = "Unknown or inactive lead status: {$data['status']}";
        }

        $assignedEmail = $this->normalizeEmail($data['assigned_to_email'] ?? null);
        $assignedUser = $assignedEmail
            ? User::query()->where('is_active', true)->whereRaw('LOWER(email) = ?', [$assignedEmail])->first(['id', 'email'])
            : null;

        if ($assignedEmail && ! $assignedUser) {
            $errors[] = "Employee email not found: {$assignedEmail}";
        }

        $data['source_id'] = $source?->id;
        $data['status_id'] = $status?->id;
        $data['assigned_to'] = $assignedUser?->id;

        $row['data'] = $data;
        $row['errors'] = $errors;
        $row['status'] = $errors ? 'invalid' : 'ready';
        $row['duplicate_type'] = null;
        $row['duplicate_message'] = null;

        $existing = $this->findExistingLeads(
            array_filter([$data['gst_number']]),
            array_filter([$data['mobile']]),
            array_filter([$data['email']]),
            array_filter([$data['company_name']]),
        );

        $strongMatch = null;
        $strongField = null;
        $strongValue = null;
        $possibleMatch = null;

        foreach ($existing as $lead) {
            if ($data['gst_number'] && $this->normalizeGst($lead->gst_number) === $data['gst_number']) {
                $strongMatch = $lead;
                $strongField = 'GST number';
                $strongValue = $data['gst_number'];
                break;
            }

            if ($data['mobile'] && $this->normalizePhone($lead->mobile) === $data['mobile']) {
                $strongMatch = $lead;
                $strongField = 'Mobile number';
                $strongValue = $data['mobile'];
                break;
            }

            if ($data['email'] && $this->normalizeEmail($lead->email) === $data['email']) {
                $strongMatch = $lead;
                $strongField = 'Email address';
                $strongValue = $data['email'];
                break;
            }

            if ($data['company_name'] && $this->lookupKey($lead->company_name) === $this->lookupKey($data['company_name'])) {
                $possibleMatch = 'existing lead ' . ($lead->lead_number ?: "#{$lead->id}");
            }
        }

        foreach ($allRows as $otherRow) {
            if ((int) ($otherRow['row_number'] ?? 0) === (int) ($row['row_number'] ?? 0)) {
                continue;
            }

            $other = $otherRow['data'] ?? [];
            if ($data['gst_number'] && $this->normalizeGst($other['gst_number'] ?? null) === $data['gst_number']) {
                $strongMatch = "uploaded row {$otherRow['row_number']}";
                $strongField = 'GST number';
                $strongValue = $data['gst_number'];
                break;
            }

            if ($data['mobile'] && $this->normalizePhone($other['mobile'] ?? null) === $data['mobile']) {
                $strongMatch = "uploaded row {$otherRow['row_number']}";
                $strongField = 'Mobile number';
                $strongValue = $data['mobile'];
                break;
            }

            if ($data['email'] && $this->normalizeEmail($other['email'] ?? null) === $data['email']) {
                $strongMatch = "uploaded row {$otherRow['row_number']}";
                $strongField = 'Email address';
                $strongValue = $data['email'];
                break;
            }

            if ($data['company_name'] && $this->lookupKey($other['company_name'] ?? null) === $this->lookupKey($data['company_name'])) {
                $possibleMatch = "uploaded row {$otherRow['row_number']}";
            }
        }

        if ($strongMatch) {
            $row['status'] = 'duplicate';
            $row['duplicate_type'] = 'strong';
            $row['duplicate_message'] = $strongMatch instanceof CrmLead
                ? "{$strongField} ({$strongValue}) already exists in lead " . ($strongMatch->lead_number ?: "#{$strongMatch->id}") . '.'
                : "{$strongField} ({$strongValue}) also appears in {$strongMatch}.";
        } elseif ($possibleMatch) {
            $row['status'] = $errors ? 'invalid' : 'possible_duplicate';
            $row['duplicate_type'] = 'possible';
            $row['duplicate_message'] = 'Company name (' . $data['company_name'] . ') matches ' . $possibleMatch . '. Review before importing.';
        }

        $row['include_in_import'] = match ($row['status']) {
            'ready' => ($previousStatus === 'ready') ? $wasIncluded : true,
            'possible_duplicate' => $wasIncluded,
            default => false,
        };

        return $row;
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

    private function getDataRowLimit(string $path, string $disk): ?int
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $absolutePath = Storage::disk($disk)->path($path);

        if ($extension === 'csv') {
            $file = new \SplFileObject($absolutePath, 'r');
            $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::DROP_NEW_LINE);
            $records = 0;

            while (! $file->eof()) {
                $record = $file->fgetcsv();

                if ($record !== false && $record !== [null]) {
                    $records++;
                }
            }

            return max(0, $records - 1);
        }

        if ($extension !== 'xlsx' || ! class_exists(\ZipArchive::class) || ! class_exists(\XMLReader::class)) {
            return null;
        }

        $zip = new \ZipArchive();
        if ($zip->open($absolutePath) !== true) {
            return null;
        }

        $lastDataRow = 1;

        try {
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $name = $zip->getNameIndex($index);

                if (! is_string($name) || ! preg_match('#^xl/worksheets/sheet\d+\.xml$#', $name)) {
                    continue;
                }

                $xml = $zip->getFromIndex($index);
                if (! is_string($xml)) {
                    continue;
                }

                $reader = new \XMLReader();
                if (! $reader->XML($xml, null, LIBXML_NONET | LIBXML_COMPACT)) {
                    continue;
                }

                while ($reader->read()) {
                    if ($reader->nodeType !== \XMLReader::ELEMENT || $reader->localName !== 'c') {
                        continue;
                    }

                    $reference = $reader->getAttribute('r');
                    if (! is_string($reference) || ! preg_match('/\d+$/', $reference, $rowMatch)) {
                        continue;
                    }

                    $cellXml = $reader->readOuterXml();
                    if (preg_match('/<(?:\w+:)?f(?:\s|>)/', $cellXml)) {
                        $lastDataRow = max($lastDataRow, (int) $rowMatch[0]);

                        continue;
                    }

                    if (preg_match('/<(?:\w+:)?v(?:\s[^>]*)?>(.*?)<\/(?:\w+:)?v>/s', $cellXml, $valueMatch)
                        && trim($valueMatch[1]) !== '') {
                        $lastDataRow = max($lastDataRow, (int) $rowMatch[0]);

                        continue;
                    }

                    if (preg_match('/<(?:\w+:)?is\b.*?<(?:\w+:)?t(?:\s[^>]*)?>(.*?)<\/(?:\w+:)?t>/s', $cellXml, $inlineMatch)
                        && trim($inlineMatch[1]) !== '') {
                        $lastDataRow = max($lastDataRow, (int) $rowMatch[0]);
                    }
                }

                $reader->close();
            }
        } finally {
            $zip->close();
        }

        return max(0, $lastDataRow - 1);
    }

    private function isEmptyRow(array $data): bool
    {
        foreach ($data as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }

    private function validateLeadData(array $data): array
    {
        $errors = [];
        $requiredCompany = $data['company_name'] ?? null;

        if (! is_string($requiredCompany) || $requiredCompany === '') {
            $errors[] = 'The company name field is required.';
        } elseif (mb_strlen($requiredCompany) > 255) {
            $errors[] = 'The company name may not be greater than 255 characters.';
        }

        foreach ([
            'contact_person' => 255,
            'mobile' => 20,
            'alternate_mobile' => 20,
            'whatsapp_number' => 20,
            'business_type' => 255,
            'city' => 255,
            'state' => 255,
            'country' => 255,
            'gst_number' => 20,
            'pan_number' => 20,
            'product_interested' => 255,
            'existing_software' => 255,
        ] as $field => $maxLength) {
            $value = $data[$field] ?? null;

            if ($value !== null && ! is_string($value)) {
                $errors[] = "The {$field} must be a string.";
            } elseif (is_string($value) && mb_strlen($value) > $maxLength) {
                $errors[] = "The {$field} may not be greater than {$maxLength} characters.";
            }
        }

        $email = $data['email'] ?? null;
        if ($email !== null && (! filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255)) {
            $errors[] = 'The email must be a valid email address of at most 255 characters.';
        }

        $pincode = $data['pincode'] ?? null;
        if ($pincode !== null && (! is_numeric($pincode) || (float) $pincode > 999999)) {
            $errors[] = 'The pincode must be numeric and no greater than 999999.';
        }

        $budget = $data['estimated_budget'] ?? null;
        if ($budget !== null && (! is_numeric($budget) || (float) $budget < 0)) {
            $errors[] = 'The estimated budget must be a number greater than or equal to zero.';
        }

        if (! in_array($data['priority'] ?? null, ['low', 'normal', 'high', 'urgent'], true)) {
            $errors[] = 'The priority must be low, normal, high, or urgent.';
        }

        foreach (['lead_date', 'next_follow_up_at'] as $field) {
            if (($data[$field] ?? null) !== null && strtotime((string) $data[$field]) === false) {
                $errors[] = "The {$field} is not a valid date.";
            }
        }

        foreach (['address', 'requirements'] as $field) {
            if (($data[$field] ?? null) !== null && ! is_string($data[$field])) {
                $errors[] = "The {$field} must be a string.";
            }
        }

        return $errors;
    }

    private function findExistingLeads(array $gstNumbers, array $mobiles, array $emails, array $companyNames)
    {
        $matches = collect();

        foreach ([
            'gst_number' => $gstNumbers,
            'mobile' => $mobiles,
            'email' => $emails,
            'company_name' => $companyNames,
        ] as $column => $values) {
            foreach (array_chunk(array_values(array_unique($values)), 500) as $chunk) {
                if ($chunk === []) {
                    continue;
                }

                CrmLead::withTrashed()
                    ->whereIn($column, $chunk)
                    ->get(['id', 'lead_number', 'company_name', 'gst_number', 'mobile', 'email'])
                    ->each(fn (CrmLead $lead) => $matches->put($lead->id, $lead));
            }
        }

        return $matches->values();
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
