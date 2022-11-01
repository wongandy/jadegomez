<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class LocalSupplierSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'id' => 1,
                'name' => 'Supplier 1'
            ],
            [
                'id' => 2,
                'name' => 'Supplier 2'
            ],
        ];

        foreach ($items as $item) {
            Supplier::updateOrCreate(['id' =>$item['id']], $item);
        }
    }
}
