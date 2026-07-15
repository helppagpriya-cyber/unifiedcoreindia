<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProjectDocument extends Model
{
    use InteractsWithMedia;

    protected $fillable = [
        'project_id',
        'document_type',
        'document_title',
        'document_no',
        'document_date',
        'remarks',
        'file_path',
        'uploaded_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('project-documents');
    }
}
