<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class LocalCustomerSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'name' => 'John Doe',
                'contact_number' => 1342255
            ],
            [
                'name' => 'Dave Lee',
                'contact_number' => 2553422
            ],
            [
                'name' => 'Walk In'
            ]
        ];

        foreach ($items as $item) {
            Customer::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
