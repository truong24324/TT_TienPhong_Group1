<?php

namespace Tests\Feature;

use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_get_shipping_zones()
    {
        ShippingZone::factory()->count(5)->create();

        $response = $this->getJson('/api/shipping/zone?page=1&per_page=5');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'zone_name',
                        'additional_fee',
                    ],
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    public function test_can_calculate_shipping_fee()
    {
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

        $expectedTotalFee = $method->base_cost + ($method->cost_per_kg * 2) + $zone->additional_fee;

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Đơn hàng đã được tạo thành công.',
                'total_fee' => $expectedTotalFee,
            ]);
    }

    public function test_can_create_shipping_zone()
    {
        $response = $this->postJson('/api/shipping/zone', [
            'zone_name' => 'Zone 1',
            'additional_fee' => 5.0,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Tạo khu vực vận chuyển thành công',
            ]);

        $this->assertDatabaseHas('shipping_zones', [
            'zone_name' => 'Zone 1',
            'additional_fee' => 5.0,
        ]);
    }

    public function test_cannot_create_duplicate_shipping_zone()
    {
        ShippingZone::factory()->create(['zone_name' => 'Zone 1']);

        $response = $this->postJson('/api/shipping/zone', [
            'zone_name' => 'Zone 1',
            'additional_fee' => 5.0,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => [
                    'zone_name',
                ],
            ]);
    }
}
