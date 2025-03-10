<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateShippingRequest;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Http\JsonResponse;

class ShippingController extends Controller {
    public function calculate(CalculateShippingRequest $request): JsonResponse {
        $data = $request->validated();

        // Lấy phương thức vận chuyển
        $shippingMethod = ShippingMethod::findOrFail($data['shipping_method_id']);

        // Lấy phí khu vực
        $shippingZone = ShippingZone::where('zone_name', $data['destination'])->firstOrFail();

        // Công thức tính phí
        $totalFee = $shippingMethod->base_cost + ($shippingMethod->cost_per_kg * $data['weight']) + $shippingZone->additional_fee;

        return response()->json([
            'shipping_method' => $shippingMethod->name,
            'destination' => $shippingZone->zone_name,
            'weight' => $data['weight'],
            'total_fee' => $totalFee,
        ]);
    }

    public function calculateShippingFee(Request $request) {
        $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'weight' => 'required|numeric|min:0',
            'destination' => 'required|string',
        ]);
    
        $method = ShippingMethod::findOrFail($request->shipping_method_id);
        $zone = ShippingZone::where('zone_name', $request->destination)->first();
    
        $additional_fee = $zone ? $zone->additional_fee : 0;
        $total_fee = $method->base_cost + ($method->cost_per_kg * $request->weight) + $additional_fee;
    
        return response()->json([
            'total_fee' => $total_fee
        ]);
    }
    
}
