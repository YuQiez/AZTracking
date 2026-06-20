<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_crud_flow()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $customer = Customer::create([
            'name' => 'ACME',
            'email' => 'acme@example.com',
            'phone' => '1234567890',
        ]);

        $status = Status::create([
            'name' => 'new',
            'display_name' => 'New',
            'order' => 1,
        ]);

        // Create
        $resp = $this->postJson('/api/orders/store', [
            'name' => 'Test Order',
            'address' => '123 Main St',
            'customer_id' => $customer->id,
            'status_id' => $status->id,
        ]);

        $resp->assertStatus(200)->assertJsonStructure(['message', 'order']);
        $orderId = $resp->json('order.id');

        // Show
        $this->getJson("/api/orders/{$orderId}")->assertStatus(200)->assertJsonPath('order.id', $orderId);

        // Update
        $this->postJson("/api/orders/{$orderId}/update", [
            'name' => 'Updated Order'
        ])->assertStatus(200)->assertJsonPath('order.name', 'Updated Order');

        // Delete
        $this->deleteJson("/api/orders/{$orderId}/delete")->assertStatus(200);

        $this->getJson("/api/orders/{$orderId}")->assertStatus(404);
    }
}
