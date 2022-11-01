<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'ragomez',
            'branch_id' => 1,
            'email' => 'ragomez3@gmail.com',
            'password' => bcrypt('ragomez')
        ])->roles()->sync(1);
    }
}
