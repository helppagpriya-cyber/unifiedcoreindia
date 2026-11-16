<?php

namespace Database\Seeders;

use App\Models\ErpSoftwareProduct;
use App\Models\ErpSoftwareVersion;
use Illuminate\Database\Seeder;

class ErpSoftwareVersionSeeder extends Seeder
{
    public function run(): void
    {
        $product = ErpSoftwareProduct::where('code', 'EGJEWEL-ERP')->firstOrFail();

        $editions = [
            1  => '1UCILite W',
            2  => '2UCIAce Lite W',
            3  => '3UCIAce W',
            4  => '4UCILite R',
            5  => '5UCIAce Lite R',
            6  => '6UCIAce R',
            7  => '7UCILite X',
            8  => '8UCIAce Lite X',
            9  => '9UCIAce X',
            10 => '10UCIPro SW',
            11 => '11UCIPro GW',
            12 => '12UCIAce Pro W',
            13 => '13UCIAce Pro X',
            14 => '14UCIGirvi Ace X',
            15 => '15UCIGirvi X',
            16 => '16UCIBul',
            17 => '17UCITag',
            18 => '18UCIBul X',
            19 => '19UCIPro W',
            20 => '20UCICatalog',
            21 => '21UCIGirvi Lite',
            22 => '22UCILite RX',
            23 => '23UCIXP Lite',
            24 => '24UCIXP ACE X',
            25 => '25UCIXP Lite X',
            26 => '26UCIAce Pro SW',
            27 => '27UCIAce Pro GW',
            28 => '28UCIKit Lite',
            29 => '29UCIDia Lite',
            30 => '30UCIDia Ace',
            31 => '31UCIPro SW X',
            32 => '32UCIPro GW X',
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
