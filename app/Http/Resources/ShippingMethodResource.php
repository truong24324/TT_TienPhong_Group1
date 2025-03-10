<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'base_cost' => $this->base_cost,
            'cost_per_kg' => $this->cost_per_kg,
            'estimated_days' => $this->estimated_days,
        ];
    }
}
