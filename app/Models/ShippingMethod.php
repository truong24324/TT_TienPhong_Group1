<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model {
    use HasFactory; // Đảm bảo dòng này có trong model

    protected $fillable = ['name', 'description', 'base_cost', 'cost_per_kg', 'estimated_days'];
    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_method_id');
    }
}
