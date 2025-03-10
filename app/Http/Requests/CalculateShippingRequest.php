<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateShippingRequest extends FormRequest {
    public function rules(): array {
        return [
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'weight' => 'required|numeric|min:0.1',
            'destination' => 'required|string|exists:shipping_zones,zone_name',
        ];
    }
}
