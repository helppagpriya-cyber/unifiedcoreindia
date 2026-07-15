<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'file_record_id',
        'document_no',
        'document_title',
        'page_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => DocumentStatus::class,
        ];
    }

    public function fileRecord()
    {
        return $this->belongsTo(FileRecord::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function qcChecks()
    {
        return $this->hasMany(QcCheck::class);
    }
}
