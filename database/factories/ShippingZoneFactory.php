<?php

namespace Database\Factories;

use App\Models\ShippingZone;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShippingZoneFactory extends Factory {
    protected $model = ShippingZone::class;

    public function definition() {
        return [
            'zone_name' => $this->faker->city,
            'additional_fee' => $this->faker->randomFloat(2, 5, 20), // Phí ngẫu nhiên từ 5 đến 20
        ];
    }
}
