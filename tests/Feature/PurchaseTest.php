<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_select_purchase_supplier_page()
    {
        $response = $this->get('/purchase/supplier');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_create_purchase_page()
    {
        $response = $this->get('/purchase/1/create');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_cannot_access_create_purchase_page()
    {
        $response = $this->actingAs($this->createUser(false))->get('/purchase/1/create');

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_create_purchase_page()
    {
        $supplier = Supplier::factory()->create();
        
        $item = Item::factory()->create();

        $response = $this->actingAs($this->createUser(true))->get('/purchase/' . $supplier->id . '/create');
        
        $response->assertStatus(200);
        $response->assertViewHas('items', function ($collection) use ($item) {
            return $collection->contains($item);
        });
    }

    public function test_authorized_user_can_create_a_purchase()
    {
        $items['purchase_number'] = 'PO-00001532';
        $items['supplier'] = 'HYW IT DISTRIBUTOR';
        $items['supplier_id'] = '1';
        $items['items'] = [
            [
                'item_id' => 1,
                'with_serial_number' => 1,
                'name' => 'AMD A6-7480',
                'serial_number' => [
                    'asdfd',
                    'cxcse'
                ],
                'quantity' => 2,
                'cost_price' => 1800
            ],
            [
                'item_id' => 2,
                'with_serial_number' => 1,
                'name' => 'AMD A8-9600',
                'serial_number' => [
                    'rtrvcv',
                    'cvbdfg'
                ],
                'quantity' => 2,
                'cost_price' => 1800
            ]
        ];

        $response = $this->actingAs($this->createUser(true))->post('/purchase/store', $items);
        
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_cannot_access_select_purchase_supplier_page()
    {
        $response = $this->actingAs($this->createUser(false))->get('/purchase/supplier');

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_select_purchase_supplier_page()
    {
        $response = $this->actingAs($this->createUser(true))->get('/purchase/supplier');

        $response->assertViewHas('suppliers', function ($collection) {
            $supplier = Supplier::first();
            return $collection->contains($supplier);
        });
        $response->assertStatus(200);
    }

    public function test_authorized_user_can_select_supplier_from_purchase_supplier_page()
    {
        Supplier::factory()->create();
        $supplierId = Supplier::select('id AS supplier_id')->first()->toArray();
        $response = $this->actingAs($this->createUser(true))->post('/purchase/supplier', $supplierId);

        $response->assertStatus(302);
    }

    public function createUser($withPermission = false)
    {
        $role = Role::factory()->create();

        $branch = Branch::factory()->create();

        $user = User::factory()->create([
            'branch_id' => $branch->id
        ]);

        $item = Item::factory(2)->create();

        $supplier = Supplier::factory()->create();

        $purchase = Purchase::factory()->create();

        $user->roles()->attach($role->id);

        $role->permissions()->createMany([
            ['name' => 'view purchases'],
            ['name' => 'create purchases'],
            ['name' => 'delete purchases'],
        ]);

        $user->roles->first()->permissions->each(function ($permission) use ($withPermission) {
            Gate::define($permission->name, function () use ($withPermission) {
                return $withPermission;
            });
        });

        return $user;
    }
}
