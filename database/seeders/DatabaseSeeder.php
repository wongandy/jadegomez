<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // for development
        // $this->call(RoleSeeder::class);
        // $this->call(PermissionSeeder::class);
        // $this->call(BranchSeeder::class);
        // $this->call(UserSeeder::class);
        // $this->call(ItemSeeder::class);
        // $this->call(SupplierSeeder::class);
        // $this->call(CustomerSeeder::class);

        // for production
        $this->call(RoleSeederProduction::class);
        $this->call(PermissionSeederProduction::class);
        $this->call(BranchSeederProduction::class);
        $this->call(UserSeederProduction::class);
    }
}
