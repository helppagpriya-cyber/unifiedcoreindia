<?php

namespace App\Support;

use Spatie\Permission\Models\Permission;

class PermissionGroup
{
    public static function grouped(): array
    {
        $permissions = Permission::orderBy('name')->get();

        $groups = [];

        foreach ($permissions as $permission) {

            $parts = explode('.', $permission->name);

            $module = ucfirst(str_replace('_', ' ', $parts[0]));

            $groups[$module][$permission->id] = ucfirst(
                str_replace('_', ' ', $parts[1] ?? $permission->name)
            );
        }

        return $groups;
    }
}
