<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ErpSoftwareProduct;

class ErpSoftwareProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define your core products here
        $products = [
            [
                'name' => 'EGJEWEL ERP',
                'code' => 'EGJEWEL-ERP',
                'legacy_product_code' => 'EGJEWEL',
                'software_id_format' => ['format' => 'EG{3456}'],
                'license_profile' => 'Enterprise',
                'description' => 'Flagship enterprise resource planning software.',
                'is_active' => true,
            ],
            // You can add more products here if needed
        ];

        foreach ($products as $productData) {
            ErpSoftwareProduct::updateOrCreate(
                ['code' => $productData['code']], // Unique key to check against
                $productData
            );
        }
    }
}
