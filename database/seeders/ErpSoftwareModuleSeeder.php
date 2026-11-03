<?php

namespace Database\Seeders;

use App\Models\ErpSoftwareModule;
use App\Models\ErpSoftwareProduct;
use Illuminate\Database\Seeder;

class ErpSoftwareModuleSeeder extends Seeder
{
    public function run(): void
    {
        $product = ErpSoftwareProduct::where('name', 'EGJEWEL')
            ->firstOrFail();

        $modules = [
            [
                'name' => 'GIRVI',
                'code' => 'GIRVI',
                'legacy_flag_key' => 'GTMP',
                'legacy_group' => 1,
                'legacy_position' => 1,
            ],
            [
                'name' => 'KITTY',
                'code' => 'KITTY',
                'legacy_flag_key' => 'KTMP',
                'legacy_group' => 1,
                'legacy_position' => 2,
            ],
            [
                'name' => 'SMS',
                'code' => 'SMS',
                'legacy_flag_key' => 'SMSTMP',
                'legacy_group' => 1,
                'legacy_position' => 3,
            ],
            [
                'name' => 'DFEED',
                'code' => 'DFEED',
                'legacy_flag_key' => 'DLFEED',
                'legacy_group' => 1,
                'legacy_position' => 4,
            ],
            [
                'name' => 'BULL',
                'code' => 'BULL',
                'legacy_flag_key' => 'BULLTMP',
                'legacy_group' => 2,
                'legacy_position' => 1,
            ],
            [
                'name' => 'EXPORT',
                'code' => 'EXPORT',
                'legacy_flag_key' => 'EXPTMP',
                'legacy_group' => 2,
                'legacy_position' => 2,
            ],
            [
                'name' => 'SALARY',
                'code' => 'SALARY',
                'legacy_flag_key' => 'SALTMP',
                'legacy_group' => 2,
                'legacy_position' => 3,
            ],
            [
                'name' => 'BRANCH',
                'code' => 'BRANCH',
                'legacy_flag_key' => 'BRANCHTMP',
                'legacy_group' => 2,
                'legacy_position' => 4,
            ],
            [
                'name' => 'CURRENCY',
                'code' => 'CURRENCY',
                'legacy_flag_key' => 'MCUR',
                'legacy_group' => 3,
                'legacy_position' => 1,
            ],
            [
                'name' => 'RAPA',
                'code' => 'RAPA',
                'legacy_flag_key' => 'RAPA',
                'legacy_group' => 3,
                'legacy_position' => 2,
            ],
            [
                'name' => 'JOBWISE',
                'code' => 'JOBWISE',
                'legacy_flag_key' => 'JOBIR',
                'legacy_group' => 3,
                'legacy_position' => 3,
            ],
            [
                'name' => 'DASSORT',
                'code' => 'DASSORT',
                'legacy_flag_key' => 'DASSORT',
                'legacy_group' => 3,
                'legacy_position' => 4,
            ],
        ];

        foreach ($modules as $module) {
            ErpSoftwareModule::updateOrCreate(
                [
                    'software_product_id' => $product->id,
                    'code' => $module['code'],
                ],
                [
                    ...$module,
                    'description' => $module['name'] . ' module',
                    'is_active' => true,
                ]
            );
        }
    }
}
