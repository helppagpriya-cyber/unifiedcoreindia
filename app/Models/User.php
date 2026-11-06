<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasEntityCode;

#[Fillable([
    'name',
    'email',
    'password',
    'employee_code',
    'mobile',
    'designation',
    'joining_date',
    'is_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasEntityCode;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'joining_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->hasAnyRole(['Super Admin', 'Admin']),
            'egjewel' => $this->hasAnyRole([
                'Super Admin', 'Admin', 'ERP Manager', 'EGJewel Executive',
                'Project Manager', 'Supervisor', 'Scanner Operator', 'QC Operator', 'Data Entry Operator',
            ]),
            default => false,
        };
    }
    public function getCodeColumn(): string
    {
        return 'employee_code';
    }

    public function getCodePrefix(): string
    {
        return 'EMP';
    }
}
