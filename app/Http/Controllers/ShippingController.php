<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Shipping",
 *     description="API liên quan đến vận chuyển"
 * )
 *
 * @OA\Schema(
 *     schema="ShippingZone",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="zone_name", type="string", example="New York"),
 *     @OA\Property(property="additional_fee", type="number", format="float", example=5.0)
 * )
 *
 * @OA\Schema(
 *     schema="ShippingMethod",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Express Delivery"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Dịch vụ giao hàng nhanh trong 24h"),
 *     @OA\Property(property="base_cost", type="number", format="float", example=10.0),
 *     @OA\Property(property="cost_per_kg", type="number", format="float", example=2.0),
 *     @OA\Property(property="estimated_days", type="integer", example=3)
 * )
 */

class ShippingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/shipping/zone",
     *     summary="Lấy danh sách khu vực vận chuyển (có phân trang)",
     *     tags={"1. Get Shipping"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Trang hiện tại",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Số lượng mục trên mỗi trang",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Danh sách khu vực vận chuyển có phân trang",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lấy danh sách khu vực vận chuyển thành công"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ShippingZone")),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     ),
     *    @OA\Response(
     *      response=422,
     *      description="Dữ liệu không hợp lệ",
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="status", type="boolean", example=false),
     *          @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *          @OA\Property(property="errors", type="object",
     *          @OA\Property(property="per_page", type="array",
     *              @OA\Items(type="string", example="Số mục trên mỗi trang phải là một số nguyên.")
     *          ),
     *          @OA\Property(property="page", type="array",
     *              @OA\Items(type="string", example="Trang phải là một số nguyên.")
     *          )
     *          )
     *      )
     *  )
     * )
     */
    public function getShippingZones(Request $request): JsonResponse
    {
        try {
            $messages = [
                'per_page.integer' => 'Số mục trên mỗi trang phải là một số nguyên.',
                'per_page.min' => 'Số mục trên mỗi trang phải lớn hơn hoặc bằng 1.',
                'per_page.max' => 'Số mục trên mỗi trang không được vượt quá 100.',
                'page.integer' => 'Trang phải là một số nguyên.',
                'page.min' => 'Trang phải lớn hơn hoặc bằng 1.',
            ];
            $validator = Validator::make($request->all(), [
                'per_page' => 'sometimes|integer|min:1|max:100',
                'page' => 'sometimes|integer|min:1',
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $perPage = $request->query('per_page', 10); // Mặc định 10 mục mỗi trang
            $zones = ShippingZone::paginate($perPage);
            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách khu vực vận chuyển thành công',
                'data' => $zones->items(),
                'pagination' => [
                    'current_page' => $zones->currentPage(),
                    'last_page' => $zones->lastPage(),
                    'per_page' => $zones->perPage(),
                    'total' => $zones->total(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 400);
        }
    }
/**
 * @OA\Get(
 *     path="/shipping/method",
 *     summary="Lấy danh sách phương thức vận chuyển (có phân trang)",
 *     tags={"1. Get Shipping"},
 *     @OA\Parameter(
 *         name="name",
 *         in="query",
 *         description="Lọc theo tên phương thức vận chuyển",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="estimated_days",
 *         in="query",
 *         description="Lọc theo số ngày giao hàng dự kiến",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="sort_by",
 *         in="query",
 *         description="Trường cần sắp xếp (base_cost hoặc estimated_days)",
 *         required=false,
 *         @OA\Schema(type="string", enum={"base_cost", "estimated_days"})
 *     ),
 *     @OA\Parameter(
 *         name="sort_order",
 *         in="query",
 *         description="Thứ tự sắp xếp (asc hoặc desc)",
 *         required=false,
 *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Trang hiện tại",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Số lượng mục trên mỗi trang",
 *         required=false,
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Danh sách phương thức vận chuyển có phân trang",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Lấy danh sách thành công"),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ShippingMethod")),
 *             @OA\Property(property="pagination", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=5),
 *                 @OA\Property(property="per_page", type="integer", example=10),
 *                 @OA\Property(property="total", type="integer", example=50)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Dữ liệu không hợp lệ",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="per_page", type="array",
 *                     @OA\Items(type="string", example="Số mục trên mỗi trang phải là một số nguyên từ 1 đến 100.")
 *                 ),
 *                 @OA\Property(property="page", type="array",
 *                     @OA\Items(type="string", example="Trang phải là một số nguyên lớn hơn hoặc bằng 1.")
 *                 ),
 *                 @OA\Property(property="sort_by", type="array",
 *                     @OA\Items(type="string", example="Trường sắp xếp chỉ được nhận giá trị base_cost hoặc estimated_days.")
 *                 ),
 *                 @OA\Property(property="sort_order", type="array",
 *                     @OA\Items(type="string", example="Thứ tự sắp xếp chỉ được nhận giá trị asc hoặc desc.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */

    public function getShippingMethods(Request $request): JsonResponse
    {
        try {
            // Validate dữ liệu đầu vào
            $messages = [
                'name.string' => 'Tên phải là chuỗi ký tự.',
                'name.max' => 'Tên không được vượt quá 255 ký tự.',
                'estimated_days.integer' => 'Số ngày dự kiến phải là số nguyên.',
                'estimated_days.min' => 'Số ngày dự kiến phải lớn hơn hoặc bằng 1.',
                'sort_by.in' => 'Trường sắp xếp chỉ được nhận giá trị base_cost hoặc estimated_days.',
                'sort_order.in' => 'Thứ tự sắp xếp chỉ được nhận giá trị asc hoặc desc.',
                'per_page.integer' => 'Số mục trên mỗi trang phải là một số nguyên.',
                'per_page.min' => 'Số mục trên mỗi trang phải lớn hơn hoặc bằng 1.',
                'per_page.max' => 'Số mục trên mỗi trang không được vượt quá 100.',
                'page.integer' => 'Trang phải là một số nguyên.',
                'page.min' => 'Trang phải lớn hơn hoặc bằng 1.',
            ];

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'estimated_days' => 'nullable|integer|min:1',
                'sort_by' => 'nullable|in:base_cost,estimated_days',
                'sort_order' => 'nullable|in:asc,desc',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
            ], $messages);
            $query = ShippingMethod::query();

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $validatedData = $validator->validated();

            if (!empty($validatedData['name'])) {
                $query->where('name', 'like', '%' . $validatedData['name'] . '%');
            }

            if (!empty($validatedData['estimated_days'])) {
                $query->where('estimated_days', $validatedData['estimated_days']);
            }

            if (!empty($validatedData['sort_by'])) {
                $sortField = $validatedData['sort_by'];
                $sortOrder = $validatedData['sort_order'] ?? 'asc';
                $query->orderBy($sortField, $sortOrder);
            }

            $perPage = $validatedData['per_page'] ?? 10;
            $methods = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách phương thức vận chuyển thành công',
                'data' => $methods->items(),
                'pagination' => [
                    'current_page' => $methods->currentPage(),
                    'last_page' => $methods->lastPage(),
                    'per_page' => $methods->perPage(),
                    'total' => $methods->total(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi dữ liệu: ' . $e->getMessage(),
            ], 400);
        }
    }
    /**
     * @OA\Post(
     *     path="/shipping/calculate",
     *     summary="Tính phí vận chuyển",
     *     tags={"2. Post Shipping"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shipping_method_id", "weight", "destination"},
     *             @OA\Property(property="shipping_method_id", type="integer", example=1, description="ID của phương thức vận chuyển"),
     *             @OA\Property(property="weight", type="number", format="float", example=2.5, description="Khối lượng hàng hóa (kg)"),
     *             @OA\Property(property="destination", type="string", example="Hà Nội", description="Điểm đến giao hàng")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Đơn hàng được tạo thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Đơn hàng đã được tạo thành công."),
     *             @OA\Property(property="total_fee", type="number", format="float", example=15.50, description="Tổng phí vận chuyển"),
     *             @OA\Property(property="order_id", type="integer", example=123, description="ID của đơn hàng vừa tạo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu đầu vào không hợp lệ",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="shipping_method_id", type="array",
     *                     @OA\Items(type="string", example="Phương thức vận chuyển không hợp lệ.")
     *                 ),
     *                 @OA\Property(property="weight", type="array",
     *                     @OA\Items(type="string", example="Khối lượng phải là một số lớn hơn hoặc bằng 0.")
     *                 ),
     *                 @OA\Property(property="destination", type="array",
     *                     @OA\Items(type="string", example="Điểm đến không được để trống.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi máy chủ."),
     *             @OA\Property(property="error", type="string", example="Lỗi không xác định.")
     *         )
     *     )
     * )
     */

    public function calculateShippingFee(Request $request): JsonResponse
    {
        try {
            // Danh sách thông báo lỗi bằng tiếng Việt
            $messages = [
                'shipping_method_id.required' => 'Phương thức vận chuyển không được để trống.',
                'shipping_method_id.exists' => 'Phương thức vận chuyển không hợp lệ.',
                'weight.required' => 'Khối lượng là bắt buộc.',
                'weight.numeric' => 'Khối lượng phải là một số.',
                'weight.min' => 'Khối lượng phải lớn hơn hoặc bằng 0.',
                'destination.required' => 'Điểm đến không được để trống.',
                'destination.string' => 'Điểm đến phải là một chuỗi ký tự.',
            ];

            // Validate dữ liệu đầu vào
            $validator = Validator::make($request->all(), [
                'shipping_method_id' => 'required|exists:shipping_methods,id',
                'weight' => 'required|numeric|min:0',
                'destination' => 'required|string',
            ], $messages);

            // Nếu validation thất bại, trả về lỗi 422
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu không hợp lệ.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Lấy dữ liệu sau khi validate
            $validatedData = $validator->validated();

            // Lấy phương thức vận chuyển
            $method = ShippingMethod::findOrFail($validatedData['shipping_method_id']);

            // Lấy khu vực vận chuyển
            $zone = ShippingZone::where('zone_name', $validatedData['destination'])->first();
            $additional_fee = $zone ? $zone->additional_fee : 0;

            // Tính tổng phí
            $total_fee = $method->base_cost + ($method->cost_per_kg * $validatedData['weight']) + $additional_fee;

            // Lưu vào bảng orders
            $order = Order::create([
                'shipping_method_id' => $method->id,
                'total_price' => $total_fee,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Đơn hàng đã được tạo thành công.',
                'total_fee' => $total_fee,
                'order_id' => $order->id,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi máy chủ.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

/**
 * @OA\Post(
 *     path="/shipping/zone",
 *     summary="Tạo khu vực vận chuyển mới",
 *     tags={"2. Post Shipping"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"zone_name", "additional_fee"},
 *             @OA\Property(property="zone_name", type="string", example="Zone 1"),
 *             @OA\Property(property="additional_fee", type="number", example=5.0)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Tạo thành công",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Tạo khu vực vận chuyển thành công"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="zone_name", type="string", example="Zone 1"),
 *                 @OA\Property(property="additional_fee", type="number", example=5.0),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ),
 *             @OA\Property(property="pagination", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="per_page", type="integer", example=10),
 *                 @OA\Property(property="total", type="integer", example=100)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Dữ liệu không hợp lệ",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi dữ liệu đầu vào"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="zone_name", type="array",
 *                     @OA\Items(type="string", example="zone_name đã tồn tại.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi máy chủ",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Đã xảy ra lỗi trong quá trình tạo khu vực vận chuyển"),
 *             @OA\Property(property="error", type="string", example="Lỗi database...")
 *         )
 *     )
 * )
 */
    public function createShippingZone(Request $request): JsonResponse
    {
        try {
            // Định nghĩa thông báo lỗi bằng tiếng Việt
            $messages = [
                'zone_name.required' => 'Tên khu vực là bắt buộc.',
                'zone_name.string' => 'Tên khu vực phải là chuỗi ký tự.',
                'zone_name.unique' => 'Tên khu vực đã tồn tại, vui lòng chọn tên khác.',
                'additional_fee.required' => 'Phí bổ sung là bắt buộc.',
                'additional_fee.numeric' => 'Phí bổ sung phải là một số.',
                'additional_fee.min' => 'Phí bổ sung không thể nhỏ hơn 0.',
            ];

            // Thực hiện validate với Validator::make()
            $validator = Validator::make($request->all(), [
                'zone_name' => 'required|string|unique:shipping_zones,zone_name',
                'additional_fee' => 'required|numeric|min:0',
            ], $messages);

            // Nếu validation thất bại, trả về lỗi 422
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lỗi dữ liệu đầu vào',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Tạo khu vực vận chuyển
            $zone = ShippingZone::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Tạo khu vực vận chuyển thành công',
                'data' => $zone,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình tạo khu vực vận chuyển',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
