<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeederLocal extends Seeder
{
    public function run()
    {
        $items = [
            ['id' => 1, 'name' =>'create branches'],
            ['id' => 2, 'name' =>'view branches'],
            ['id' => 3, 'name' =>'edit branches'],
            ['id' => 4, 'name' =>'delete branches'],
            ['id' => 5, 'name' =>'create suppliers'],
            ['id' => 6, 'name' =>'view suppliers'],
            ['id' => 7, 'name' =>'edit suppliers'],
            ['id' => 8, 'name' =>'delete suppliers'],
            ['id' => 9, 'name' =>'create items'],
            ['id' => 10, 'name' =>'view items'],
            ['id' => 11, 'name' =>'edit items'],
            ['id' => 12, 'name' =>'delete items'],
            ['id' => 13, 'name' =>'create roles'],
            ['id' => 14, 'name' =>'view roles'],
            ['id' => 15, 'name' =>'edit roles'],
            ['id' => 16, 'name' =>'delete roles'],
            ['id' => 17, 'name' =>'create users'],
            ['id' => 18, 'name' =>'view users'],
            ['id' => 19, 'name' =>'edit users'],
            ['id' => 20, 'name' =>'delete users'],
            ['id' => 21, 'name' =>'create purchases'],
            ['id' => 22, 'name' =>'view purchases'],
            ['id' => 23, 'name' =>'edit purchases'],
            ['id' => 24, 'name' =>'delete purchases'],
            ['id' => 25, 'name' =>'create sales'],
            ['id' => 26, 'name' =>'view sales'],
            ['id' => 27, 'name' =>'edit sales'],
            ['id' => 28, 'name' =>'delete sales'],
            ['id' => 29, 'name' =>'create transfers'],
            ['id' => 30, 'name' =>'view transfers'],
            ['id' => 31, 'name' =>'edit transfers'],
            ['id' => 32, 'name' =>'delete transfers'],
            ['id' => 33, 'name' =>'approve sales'],
            ['id' => 34, 'name' =>'approve transfers'],
            ['id' => 35, 'name' =>'print unlimited sale DR'],
            ['id' => 36, 'name' =>'generate reports'],
            ['id' => 37, 'name' =>'view cost price'],
            ['id' => 38, 'name' =>'create return item'],
            ['id' => 39, 'name' =>'void return item'],
            ['id' => 40, 'name' =>'create defective item'],
            ['id' => 41, 'name' =>'void defective item'],
            ['id' => 42, 'name' =>'create change item'],
            ['id' => 43, 'name' =>'void change item'],
            ['id' => 44, 'name' =>'create liquidation forms'],
        ];

        foreach ($items as $item) {
            Permission::updateOrCreate(['id' => $item['id']], $item);
        }
    }
}
