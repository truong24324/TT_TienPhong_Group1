<?php

namespace Tests\Feature;

use App\Models\ShippingMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingMethodTest extends TestCase {
    use RefreshDatabase;

    public function test_can_create_shipping_method() {
        $response = $this->postJson('/api/shipping-methods', [
            'name' => 'Test Shipping',
            'description' => 'Fast delivery',
            'base_cost' => 20,
            'cost_per_kg' => 2,
            'estimated_days' => 3
        ]);
        $response->assertStatus(201);
    }

    public function test_can_list_shipping_methods() {
        ShippingMethod::factory()->count(3)->create();
        $response = $this->getJson('/api/shipping-methods');
        $response->assertStatus(200);
    }

    public function test_can_filter_and_sort_shipping_methods() {
        ShippingMethod::factory()->create(['name' => 'Express', 'estimated_days' => 1, 'base_cost' => 50]);
        ShippingMethod::factory()->create(['name' => 'Standard', 'estimated_days' => 3, 'base_cost' => 20]);
    
        // Kiểm tra filter theo estimated_days
        $response = $this->getJson('/api/shipping-methods?estimated_days=2');
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    
        // Kiểm tra sắp xếp theo base_cost
        $response = $this->getJson('/api/shipping-methods?sort_by=base_cost&order=asc');
        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'Standard'); // Standard có base_cost thấp hơn
    }
    
    public function test_can_get_shipping_method_details() {
        $method = ShippingMethod::factory()->create([
            'name' => 'Express',
            'description' => 'Fastest delivery option',
            'base_cost' => 50,
            'cost_per_kg' => 5,
            'estimated_days' => 1,
        ]);
    
        $response = $this->getJson("/api/shipping-methods/{$method->id}");
    
        $response->assertStatus(200)
            ->assertJson([
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
    
    public function test_can_update_shipping_method() {
        $method = ShippingMethod::factory()->create([
            'base_cost' => 30,
            'cost_per_kg' => 5,
            'estimated_days' => 2,
        ]);
    
        $response = $this->putJson("/api/shipping-methods/{$method->id}", [
            'base_cost' => 50,
            'cost_per_kg' => 10,
            'estimated_days' => 5
        ]);
    
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $method->id,
                    'base_cost' => 50,
                    'cost_per_kg' => 10,
                    'estimated_days' => 5,
                ]
            ]);
    }
    public function test_can_delete_shipping_method() {
        $method = ShippingMethod::factory()->create();
    
        $response = $this->deleteJson("/api/shipping-methods/{$method->id}");
    
        $response->assertStatus(200)
            ->assertJson(['message' => 'Deleted successfully']);
        
        $this->assertDatabaseMissing('shipping_methods', ['id' => $method->id]);
    }
    
    public function test_cannot_delete_shipping_method_in_use() {
        $method = ShippingMethod::factory()->create();
        Order::factory()->create(['shipping_method_id' => $method->id]);
    
        $response = $this->deleteJson("/api/shipping-methods/{$method->id}");
    
        $response->assertStatus(400)
            ->assertJson(['message' => 'Cannot delete: This shipping method is in use']);
        
        $this->assertDatabaseHas('shipping_methods', ['id' => $method->id]);
    }
       
}
