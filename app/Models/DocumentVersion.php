<?php

namespace App\Models;

use App\Enums\VersionType;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $fillable = [
        'document_id',
        'version_type',
        'disk',
        'file_name',
        'file_path',
        'file_size',
        'uploaded_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'version_type' => VersionType::class,
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
