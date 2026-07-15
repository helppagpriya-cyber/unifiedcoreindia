<?php

namespace App\Models;

use App\Enums\QcStatus;
use Illuminate\Database\Eloquent\Model;

class QcCheck extends Model
{
    protected $fillable = [
        'document_id',
        'employee_id',
        'status',
        'remarks',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => QcStatus::class,
            'checked_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class);
    }
}
