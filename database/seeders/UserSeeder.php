<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'andy',
            'branch_id' => 1,
            'email' => 'andy@andy.com',
            'password' => bcrypt('andy')
        ])->roles()->sync(1);

        User::create([
            'name' => 'mark',
            'branch_id' => 1,
            'email' => 'mark@mark.com',
            'password' => bcrypt('mark')
        ])->roles()->sync(3);

        User::create([
            'name' => 'aileen',
            'branch_id' => 1,
            'email' => 'aileen@aileen.com',
            'password' => bcrypt('aileen')
        ])->roles()->sync(3);

        User::create([
            'name' => 'mary',
            'branch_id' => 2,
            'email' => 'mary@mary.com',
            'password' => bcrypt('mary')
        ])->roles()->sync(3);

        User::create([
            'name' => 'jade',
            'branch_id' => 2,
            'email' => 'jade@jade.com',
            'password' => bcrypt('jade')
        ])->roles()->sync(1);

        User::create([
            'name' => 'ann',
            'branch_id' => 2,
            'email' => 'ann@ann.com',
            'password' => bcrypt('ann')
        ])->roles()->sync(3);
    }
}
