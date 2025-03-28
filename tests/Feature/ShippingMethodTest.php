<?php

namespace Tests\Feature;

use App\Models\ShippingMethod;
use App\Models\Order; // Make sure to import the Order model
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingMethodTest extends TestCase {
    use DatabaseTransactions;

    public function test_can_create_shipping_method()
    {
        $response = $this->postJson('/api/shipping/method', [
            'name' => 'Express Delivery',
            'base_cost' => 10.0,
            'cost_per_kg' => 2.0,
            'description' => 'Dịch vụ giao hàng nhanh trong 24h',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Tạo phương thức vận chuyển thành công',
                 ]);

        $this->assertDatabaseHas('shipping_methods', [
            'name' => 'Express Delivery',
            'base_cost' => 10.0,
            'cost_per_kg' => 2.0,
        ]);
    }

    public function test_can_update_shipping_method()
    {
        $method = ShippingMethod::factory()->create([
            'base_cost' => 30,
            'cost_per_kg' => 5,
            'estimated_days' => 2,
        ]);

        $response = $this->putJson("/api/shipping/method/{$method->id}", [
            'base_cost' => 50,
            'cost_per_kg' => 10,
            'estimated_days' => 5,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Cập nhật phương thức vận chuyển thành công',
                     'data' => [
                         'id' => $method->id,
                         'base_cost' => 50,
                         'cost_per_kg' => 10,
                         'estimated_days' => 5,
                     ]
                 ]);
    }

    public function test_can_delete_shipping_method()
    {
        $method = ShippingMethod::factory()->create();

        $response = $this->deleteJson("/api/shipping/method/{$method->id}");

        $response->assertStatus(201)
                 ->assertJson(['status' => true, 'message' => 'Xóa phương thức vận chuyển thành công']);

        $this->assertDatabaseMissing('shipping_methods', ['id' => $method->id]);
    }

    public function test_cannot_delete_shipping_method_in_use()
    {
        $method = ShippingMethod::factory()->create();
        // Assuming you have an Order model and it has a relationship with ShippingMethod
        Order::factory()->create(['shipping_method_id' => $method->id, 'total_price' => 1,]);

        $response = $this->deleteJson("/api/shipping/method/{$method->id}");

        $response->assertStatus(409)
                 ->assertJson(['status' => false, 'message' => 'Không thể xóa: Phương thức vận chuyển đang được sử dụng']);

        $this->assertDatabaseHas('shipping_methods', ['id' => $method->id]);
    }

    public function test_can_get_shipping_method_details()
    {
        $method = ShippingMethod::factory()->create([
            'name' => 'Express',
            'description' => 'Fastest delivery option',
            'base_cost' => 50,
            'cost_per_kg' => 5,
            'estimated_days' => 1,
        ]);

        $response = $this->getJson("/api/shipping/method/{$method->id}");

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Lấy thông tin thành công',
                     'data' => [
                         'id' => $method->id,
                         'name' => 'Express',
                         'description' => 'Fastest delivery option',
                         'base_cost' => 50,
                         'cost_per_kg' => 5,
                         'estimated_days' => 1,
                     ]
                 ]);
    }
}