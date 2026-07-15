<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'client.view',
            'client.create',
            'client.update',
            'client.delete',

            'client_branch.view',
            'client_branch.create',
            'client_branch.update',
            'client_branch.delete',

            'project.view',
            'project.create',
            'project.update',
            'project.delete',

            'box.view',
            'box.create',
            'box.update',
            'box.delete',

            'file.view',
            'file.create',
            'file.update',
            'file.delete',


            'document.view',
            'document.create',
            'document.update',
            'document.delete',
            'document.scan',
            'document.index',
            'document.qc',
            'document.approve',
            'document.export',

            'assignment.view',
            'assignment.create',
            'assignment.update',
            'assignment.delete',
            'assignment.allocate',
            'assignment.reassign',

            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            'project.close',
            'project.reopen',

            'report.view',
            'report.export',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
