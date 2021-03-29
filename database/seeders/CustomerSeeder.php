<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = [
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

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
