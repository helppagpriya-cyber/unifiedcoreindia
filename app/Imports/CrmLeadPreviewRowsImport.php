<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Row;

class CrmLeadPreviewRowsImport implements OnEachRow, WithHeadingRow, WithChunkReading, WithLimit, SkipsEmptyRows
{
    use RemembersChunkOffset;

    public Collection $rows;

    /** @var array<int, int> */
    public array $rowNumbers = [];

    public function __construct(private readonly ?int $rowLimit = null)
    {
        $this->rows = collect();
    }

    public function onRow(Row $row): void
    {
        $this->rows->push(collect($row->toArray()));
        $this->rowNumbers[] = $row->getIndex();
    }

    public function chunkSize(): int
    {
        // Keep a normal-sized workbook read to one chunk, and cap large files
        // to limit peak memory without reopening the workbook hundreds of times.
        return $this->rowLimit === null
            ? 25000
            : max(1000, min(25000, $this->rowLimit));
    }

    public function limit(): int
    {
        return $this->rowLimit ?? 2147483647;
    }
}
