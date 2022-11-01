<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class LocalBranchSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'id' => 1,
                'name' => 'Jade Gomez Computer Trading',
                'address' => 'Basak Pardo',
                'contact_number' => 1234
            ],
            [
                'id' => 2,
                'name' => 'Jade Gomez Computer Trading',
                'address' => 'Mandaue City',
                'contact_number' => 2345
            ],
            [
                'id' => 3,
                'name' => 'Ragomez Computer Trading',
                'address' => 'Carcar City',
                'contact_number' => 6789
            ]
        ];

        foreach ($items as $item) {
            Branch::updateOrCreate(['id' =>$item['id']], $item);
        }
    }
}
