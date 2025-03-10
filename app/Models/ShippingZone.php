<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model {
    use HasFactory;
    protected $fillable = ['zone_name', 'additional_fee'];

    public function shippingZone() {
    return $this->belongsTo(ShippingZone::class, 'zone_id');
}
}

