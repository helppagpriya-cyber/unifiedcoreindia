<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkAssignment extends Model
{
    protected $fillable = [
        'project_id',
        'assignable_type',
        'assignable_id',
        'employee_id',
        'assigned_by',
        'assigned_at',
        'started_at',
        'completed_at',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function assignable()
    {
        return $this->morphTo();
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
