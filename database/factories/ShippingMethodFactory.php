<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ShippingMethod;

class ShippingMethodFactory extends Factory {
    protected $model = ShippingMethod::class;

    public function definition() {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'base_cost' => $this->faker->randomFloat(2, 10, 100),
            'cost_per_kg' => $this->faker->randomFloat(2, 1, 10),
            'estimated_days' => $this->faker->numberBetween(1, 10),
        ];
    }
}
