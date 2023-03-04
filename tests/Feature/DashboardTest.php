<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_unauthenticated_user_cannot_access_dashboard_page()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard_page()
    {
        $response = $this->actingAs($this->createUser(false))->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('total_items', Item::count());
        $response->assertViewHas('total_branches', Branch::count());
        $response->assertViewHas('total_users', User::count());
    }

    public function createUser($withPermission = false)
    {
        $role = Role::factory()->create();

        $branch = Branch::factory()->create();

        $user = User::factory()->create([
            'branch_id' => $branch->id
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }
}
