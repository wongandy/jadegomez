<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class ProductionRoleSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'id' => 1,
                'name' => 'superadmin'
            ],
            [
                'id' => 2,
                'name' => 'admin'
            ],
            [
                'id' => 3,
                'name' => 'cashier'
            ],
            [
                'id' => 4,
                'name' => 'manager'
            ],
        ];

        foreach ($items as $item) {
            Role::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
