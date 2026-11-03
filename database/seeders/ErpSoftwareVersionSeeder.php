<?php

namespace Database\Seeders;

use App\Models\ErpSoftwareProduct;
use App\Models\ErpSoftwareVersion;
use Illuminate\Database\Seeder;

class ErpSoftwareVersionSeeder extends Seeder
{
    public function run(): void
    {
        $product = ErpSoftwareProduct::where('name', 'EGJEWEL')
            ->firstOrFail();

        $editions = [
            1  => 'UCILite W',
            2  => 'UCIAce Lite W',
            3  => 'UCIAce W',
            4  => 'UCILite R',
            5  => 'UCIAce Lite R',
            6  => 'UCIAce R',
            7  => 'UCILite X',
            8  => 'UCIAce Lite X',
            9  => 'UCIAce X',
            10 => 'UCIPro SW',
            11 => 'UCIPro GW',
            12 => 'UCIAce Pro W',
            13 => 'UCIAce Pro X',
            14 => 'UCIGirvi Ace X',
            15 => 'UCIGirvi X',
            16 => 'UCIBul',
            17 => 'UCITag',
            18 => 'UCIBul X',
            19 => 'UCIPro W',
            20 => 'UCICatalog',
            21 => 'UCIGirvi Lite',
            22 => 'UCILite RX',
            23 => 'UCIXP Lite',
            24 => 'UCIXP ACE X',
            25 => 'UCIXP Lite X',
            26 => 'UCIAce Pro SW',
            27 => 'UCIAce Pro GW',
            28 => 'UCIKit Lite',
            29 => 'UCIDia Lite',
            30 => 'UCIDia Ace',
            31 => 'UCIPro SW X',
            32 => 'UCIPro GW X',
        ];

        foreach ($editions as $number => $name) {
            ErpSoftwareVersion::updateOrCreate(
                [
                    'software_product_id' => $product->id,
                    'legacy_edition_number' => $number,
                ],
                [
                    'version_name' => $name,
                    'legacy_version_name' => null,
                    'legacy_version_code' => null,
                    'description' => "EGJEWEL Edition - VTMP {$number}",
                    'is_active' => true,
                ]
            );
        }
    }
}
