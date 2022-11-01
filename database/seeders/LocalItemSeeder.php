<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class LocalItemSeeder extends Seeder
{
    public function run()
    {
        Item::create([
            'name' => 'Mouse',
            'upc' => 'AB1CEEB',
            'selling_price' => 499.50,
            'with_serial_number' => 0
        ]);

        Item::create([
            'name' => 'Keyboard',
            'selling_price' => 795.75,
            'with_serial_number' => 0,
        ]);

        Item::create([
            'name' => 'Radeon RX501',
            'upc' => 'CR1F3BC',
            'selling_price' => 4500,
            'with_serial_number' => 1
        ]);

        Item::create([
            'name' => 'Radeon RX509',
            'upc' => 'CR1F3BC',
            'selling_price' => 5300,
            'with_serial_number' => 1
        ]);
    }
}
