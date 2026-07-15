<?php

namespace App\Models;

use App\Enums\BillingType;
use App\Enums\ProjectStatus;
use App\Traits\HasCodeGeneration;
use App\Traits\HasEntityCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes, HasEntityCode, HasCodeGeneration;

    protected $fillable = [
        'client_branch_id',
        'project_code',
        'project_name',
        'description',
        'tender_no',
        'loa_no',
        'work_order_no',
        'work_order_date',
        'agreement_no',
        'start_date',
        'expected_completion_date',
        'actual_completion_date',
        'contract_value',
        'billing_type',
        'estimated_boxes',
        'estimated_files',
        'estimated_documents',
        'estimated_pages',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'work_order_date' => 'date',
            'start_date' => 'date',
            'expected_completion_date' => 'date',
            'actual_completion_date' => 'date',
            'contract_value' => 'decimal:2',

            'billing_type' => BillingType::class,
            'status' => ProjectStatus::class,
        ];
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function branch()
    {
        return $this->belongsTo(ClientBranch::class, 'client_branch_id');
    }

    public function boxes()
    {
        return $this->hasMany(Box::class);
    }


    public function projectDocuments()
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function assignments()
    {
        return $this->hasMany(WorkAssignment::class);
    }
    public function getCodeColumn(): string
    {
        return 'project_code';
    }

    public function getCodePrefix(): string
    {
        return 'PRJ';
    }
}
