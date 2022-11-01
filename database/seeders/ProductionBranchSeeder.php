<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class ProductionBranchSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'id' => 1,
                'name' => 'Jade Gomez Computer Trading',
                'address' => 'Basak Pardo',
                'contact_number' => '(032) 263-2489'
            ]
        ];

        foreach ($items as $item) {
            Branch::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
