<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment(['local', 'staging'])) {
            $this->call(LocalRoleSeeder::class);
            $this->call(LocalPermissionSeeder::class);
            $this->call(LocalBranchSeeder::class);
            $this->call(LocalUserSeeder::class);
            $this->call(LocalItemSeeder::class);
            $this->call(LocalSupplierSeeder::class);
            $this->call(LocalCustomerSeeder::class);
            $this->call(LocalCashDenominationSeeder::class);
        }
        else if (app()->environment('production')) {
            $this->call(ProductionRoleSeeder::class);
            $this->call(ProductionPermissionSeeder::class);
            $this->call(ProductionBranchSeeder::class);
            $this->call(ProductionUserSeeder::class);
            $this->call(ProductionCashDenominationSeeder::class);
        }
    }
}
