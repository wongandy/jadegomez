<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BranchTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_branches_page()
    {
        $response = $this->get('/branch');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_create_branch_page()
    {
        $response = $this->get('/branch/create');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_edit_branch_page()
    {
        $branch = Branch::create([
            'name' => 'test',
            'address' => 'test address',
        ]);

        $response = $this->get('/branch/' . $branch->id . '/edit');

        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_cannot_access_branches_page()
    {
        $response = $this->actingAs($this->createUser(false))->get('/branch');

        $response->assertStatus(403);
    }
    
    public function test_unauthorized_user_cannot_access_create_branch_page()
    {
        $response = $this->actingAs($this->createUser(false))->get('/branch/create');

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_access_edit_branch_page()
    {
        $branch = Branch::create([
            'name' => 'test',
            'address' => 'test address',
        ]);

        $response = $this->actingAs($this->createUser(false))->get('/branch/' . $branch->id . '/edit');
        
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_branches_page()
    {
        $response = $this->actingAs($this->createUser(true))->get('/branch');

        $response->assertStatus(200);
    }

    public function test_authorized_user_can_see_create_branch_button()
    {
        $response = $this->actingAs($this->createUser(true))->get('/branch');

        $response->assertStatus(200);
        $response->assertSee('Create Branch');
    }

    public function test_authorized_user_can_see_branches_table()
    {
        $response = $this->actingAs($this->createUser(true))->get('/branch');

        $branch = Branch::first();
        $response->assertStatus(200);
        $response->assertViewHas('branches', function ($collection) use ($branch) {
            return $collection->contains($branch);
        });
    }

    public function test_authorized_user_can_see_edit_button()
    {
        $response = $this->actingAs($this->createUser(true))->get('/branch');
        
        $response->assertStatus(200);
        $response->assertSee('Edit');
    }

    public function test_authorized_user_can_access_create_branch_page()
    {
        $response = $this->actingAs($this->createUser(true))->get('/branch/create');
        
        $response->assertStatus(200);
    }

    public function test_authorized_user_can_create_a_branch()
    {
        $response = $this->actingAs($this->createUser(true))->post('/branch', [
            'name' => 'test',
            'address' => 'test address',
        ]);
        
        $response->assertRedirect('/branch');
    }

    public function test_authorized_user_can_access_edit_branch_page()
    {
        $branch = Branch::create([
            'name' => 'test',
            'address' => 'test address',
        ]);

        $response = $this->actingAs($this->createUser(true))->get('/branch/' . $branch->id . '/edit');
        
        $response->assertStatus(200);
    }

    public function test_authorized_user_can_edit_a_branch()
    {
        $branch = Branch::create([
            'name' => 'test',
            'address' => 'test address',
        ]);

        $response = $this->actingAs($this->createUser(true))->put('/branch/' . $branch->id, [
            'name' => 'test2',
            'address' => 'test address',
        ]);

        $response->assertRedirect('/branch');
    }

    public function createUser($withPermission = false)
    {
        $role = Role::factory()->create();

        $branch = Branch::factory()->create();

        $user = User::factory()->create([
            'branch_id' => $branch->id
        ]);

        $user->roles()->attach($role->id);

        $role->permissions()->createMany([
            ['name' => 'view branches'],
            ['name' => 'create branches'],
            ['name' => 'edit branches'],
        ]);

        $user->roles->first()->permissions->each(function ($permission) use ($withPermission) {
            Gate::define($permission->name, function () use ($withPermission) {
                return $withPermission;
            });
        });

        return $user;
    }
}
