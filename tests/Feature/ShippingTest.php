<?php
namespace Tests\Feature;

use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingTest extends TestCase {
    use RefreshDatabase;

    public function test_can_calculate_shipping_fee() {
        $zone = ShippingZone::factory()->create([
            'zone_name' => 'City A',
            'additional_fee' => 10,
        ]);

        $method = ShippingMethod::factory()->create([
            'name' => 'Express',
            'base_cost' => 50,
            'cost_per_kg' => 5,
        ]);

        $response = $this->postJson('/api/shipping/calculate', [
            'shipping_method_id' => $method->id,
            'weight' => 2,
            'destination' => 'City A',
        ]);

        $response->assertStatus(200)
        ->assertJson([
            'total_fee' => 50 + (5 * 2) + 10, // base_cost + (cost_per_kg * weight) + additional_fee
        ]);
    
    }
}
