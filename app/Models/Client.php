<?php

namespace App\Models;

use App\Enums\ClientType;
use App\Traits\HasCodeGeneration;
use App\Traits\HasEntityCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    use HasCodeGeneration, HasEntityCode;

    protected $fillable = [
        'client_code',
        'company_name',
        'client_type',
        'gst_no',
        'website',
        'is_active',
    ];
    protected function casts(): array
    {
        return [
            'client_type' => ClientType::class,
            'is_active' => 'boolean',
        ];
    }
    public function branches()
    {
        return $this->hasMany(ClientBranch::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function getCodeColumn(): string
    {
        return 'client_code';
    }

    public function getCodePrefix(): string
    {
        return 'CLI';
    }
}
