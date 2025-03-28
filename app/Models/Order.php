<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['shipping_method_id', 'total_price'];

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }
}
