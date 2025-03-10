<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingMethodRequest extends FormRequest {
    public function rules(): array {
        return [
            'name' => 'required|string|unique:shipping_methods,name',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'cost_per_kg' => 'required|numeric|min:0',
            'estimated_days' => 'required|integer|min:1',
        ];
    }
}
