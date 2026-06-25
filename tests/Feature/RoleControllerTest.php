<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_role_via_api_returns_success()
    {
        $role = Role::create(['name' => 'test-role', 'guard_name' => 'api']);

        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $permission = Permission::create(['name' => 'delete roles', 'guard_name' => 'api']);
        $adminRole->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($adminRole);

        Sanctum::actingAs($user, ['*'], 'sanctum');

        $response = $this->deleteJson('/api/roles/' . $role->id);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Role deleted successfully']);
        $this->assertSoftDeleted('roles', ['id' => $role->id]);
    }
}
