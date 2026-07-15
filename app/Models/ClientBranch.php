<?php

namespace App\Models;

use App\Traits\HasCodeGeneration;
use App\Traits\HasEntityCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientBranch extends Model
{
    use HasFactory, SoftDeletes, HasEntityCode, HasCodeGeneration;


    protected $fillable = [
        'client_id',
        'branch_code',
        'branch_name',
        'contact_person',
        'mobile',
        'email',
        'address',
        'city',
        'state',
        'pincode',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function getCodeColumn(): string
    {
        return 'branch_code';
    }

    public function getCodePrefix(): string
    {
        return 'CB';
    }
}
