<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingMethodSeeder extends Seeder {
    public function run(): void {
        DB::table('shipping_methods')->insert([
            ['name' => 'Standard Shipping', 'description' => 'Giao hàng tiêu chuẩn', 'base_cost' => 30.00, 'cost_per_kg' => 5.00, 'estimated_days' => 5],
            ['name' => 'Express Shipping', 'description' => 'Giao hàng nhanh', 'base_cost' => 50.00, 'cost_per_kg' => 10.00, 'estimated_days' => 2],
        ]);
    }
}
