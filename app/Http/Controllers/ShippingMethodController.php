<?php
// lệnh test: php artisan test


namespace App\Http\Controllers;

use App\Http\Requests\ShippingMethodRequest;
use App\Http\Resources\ShippingMethodResource;
use App\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller {
    public function index(Request $request) {
        return ShippingMethodResource::collection(ShippingMethod::paginate(10));
    }

    public function store(ShippingMethodRequest $request) {
        $method = ShippingMethod::create($request->validated());
        return new ShippingMethodResource($method);
    }

    public function show($id) {
        return new ShippingMethodResource(ShippingMethod::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $method = ShippingMethod::findOrFail($id);
        
        // Chỉ cập nhật các trường base_cost, cost_per_kg, estimated_days
        $validated = $request->validate([
            'base_cost' => 'required|numeric|min:0',
            'cost_per_kg' => 'required|numeric|min:0',
            'estimated_days' => 'required|integer|min:1',
        ]);
    
        $method->update($validated);
    
        return new ShippingMethodResource($method);
    }
    
    public function destroy($id): JsonResponse {
        $method = ShippingMethod::findOrFail($id);
    
        // Kiểm tra nếu có đơn hàng đang sử dụng phương thức này
        if ($method->orders()->exists()) {
            return response()->json(['message' => 'Cannot delete: This shipping method is in use'], 400);
        }
    
        $method->delete();
    
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    

    public function index(Request $request) {
        $query = ShippingMethod::query();
    
        // Bộ lọc theo carrier (giả sử có trường carrier)
        if ($request->has('carrier')) {
            $query->where('carrier', $request->carrier);
        }
    
        // Bộ lọc theo estimated_delivery
        if ($request->has('estimated_days')) {
            $query->where('estimated_days', '<=', $request->estimated_days);
        }
    
        // Sắp xếp
        if ($request->has('sort_by') && in_array($request->sort_by, ['base_cost', 'estimated_days'])) {
            $order = $request->get('order', 'asc') === 'desc' ? 'desc' : 'asc';
            $query->orderBy($request->sort_by, $order);
        }
    
        return ShippingMethodResource::collection($query->paginate(10));
    }
    
}
