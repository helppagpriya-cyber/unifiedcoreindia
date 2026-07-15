<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileRecord extends Model
{
    protected $fillable = [
        'box_id',
        'file_no',
        'file_name',
        'remarks',
    ];

    public function box()
    {
        return $this->belongsTo(Box::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
