<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\CashDenomination;

class LocalCashDenominationSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'id' => 1,
                'name' => 'one thousand',
                'number' => '1000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'five hundred',
                'number' => '500',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'two hundred',
                'number' => '200',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'one hundred',
                'number' => '100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'fifty',
                'number' => '50',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'twenty',
                'number' => '20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'ten',
                'number' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'five',
                'number' => '5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'name' => 'one',
                'number' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'name' => 'twenty-five cents',
                'number' => '0.25',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'name' => 'ten cents',
                'number' => '0.10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($items as $item) {
            CashDenomination::updateOrCreate(['id' => $item['id']], $items);
        }
    }
}
