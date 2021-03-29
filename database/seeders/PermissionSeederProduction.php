<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeederProduction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::get();
        $actions = ['create', 'view', 'edit', 'delete'];
        $models = ['branches', 'suppliers', 'items', 'roles', 'users', 'purchases', 'sales', 'transfers'];

        foreach ($roles as $role) {
            if ($role->name == 'superadmin') {
                foreach ($models as  $model) {
                    foreach ($actions as $action) {
                        Permission::create([
                            'name' => "$action $model"
                        ])->roles()->attach($role->id);
                    }
                }

                Permission::create([
                    'name' => "approve sales"
                ])->roles()->attach($role->id);

                Permission::create([
                    'name' => "approve transfers"
                ])->roles()->attach($role->id);

                Permission::create([
                    'name' => "print unlimited sale DR"
                ])->roles()->attach($role->id);

                Permission::create([
                    'name' => "generate reports"
                ])->roles()->attach($role->id);
            }

            // if ($role->name == 'cashier') {
            //     foreach ($models as  $model) {
            //         if ($model == 'sales') {
            //             foreach ($actions as $action) {
            //                 if ($action == 'create') {
            //                     Permission::where('name', 'create')->role->attach($role->id);
            //                 }
            //             }
            //         }
            //     }

            //     Permission::create([
            //         'name' => "approve sales"
            //     ])->roles()->attach($role->id);

            //     Permission::create([
            //         'name' => "approve transfers"
            //     ])->roles()->attach($role->id);
            // }
        }
    }
}
