<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShippingMethodResource;
use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Shipping Methods",
 *     description="API quản lý phương thức vận chuyển"
 * )
 */
class ShippingMethodController extends Controller
{
    /**
     * @OA\Get(
     *     path="/shipping/method/{id}",
     *     summary="Lấy chi tiết phương thức vận chuyển theo ID",
     *     tags={"1. Get Shipping"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID của phương thức vận chuyển",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lấy chi tiết phương thức vận chuyển thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lấy thông tin thành công"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $shippingMethod = ShippingMethod::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Lấy thông tin thành công',
                'data' => new ShippingMethodResource($shippingMethod),
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy phương thức vận chuyển',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi hệ thống',
            ], 500);
        }
    }

/**
 * @OA\Put(
 *     path="/shipping/method/{id}",
 *     summary="Cập nhật phương thức vận chuyển",
 *     tags={"3. Shipping Methods"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID của phương thức vận chuyển",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"base_cost", "cost_per_kg", "estimated_days"},
 *             @OA\Property(property="base_cost", type="number", example=12.0),
 *             @OA\Property(property="cost_per_kg", type="number", example=2.5),
 *             @OA\Property(property="estimated_days", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Cập nhật thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Cập nhật phương thức vận chuyển thành công"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="base_cost", type="number", example=12.0),
 *                 @OA\Property(property="cost_per_kg", type="number", example=2.5),
 *                 @OA\Property(property="estimated_days", type="integer", example=3)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Lỗi dữ liệu đầu vào",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi dữ liệu đầu vào"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="base_cost", type="array",
 *                     @OA\Items(type="string", example="Chi phí cơ bản phải là số và không được âm.")
 *                 ),
 *                 @OA\Property(property="cost_per_kg", type="array",
 *                     @OA\Items(type="string", example="Chi phí mỗi kg phải là số và không được âm.")
 *                 ),
 *                 @OA\Property(property="estimated_days", type="array",
 *                     @OA\Items(type="string", example="Số ngày dự kiến phải là số nguyên dương.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi máy chủ",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Đã xảy ra lỗi trong quá trình cập nhật"),
 *             @OA\Property(property="error", type="string", example="Chi tiết lỗi")
 *         )
 *     )
 * )
 */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Thông báo lỗi tiếng Việt
            $messages = [
                'base_cost.required' => 'Chi phí cơ bản là bắt buộc.',
                'base_cost.numeric' => 'Chi phí cơ bản phải là số.',
                'base_cost.min' => 'Chi phí cơ bản không được âm.',
                'cost_per_kg.required' => 'Chi phí mỗi kg là bắt buộc.',
                'cost_per_kg.numeric' => 'Chi phí mỗi kg phải là số.',
                'cost_per_kg.min' => 'Chi phí mỗi kg không được âm.',
                'estimated_days.required' => 'Số ngày dự kiến là bắt buộc.',
                'estimated_days.integer' => 'Số ngày dự kiến phải là số nguyên.',
                'estimated_days.min' => 'Số ngày dự kiến phải lớn hơn hoặc bằng 1.',
            ];

            // Validate dữ liệu đầu vào
            $validator = Validator::make($request->all(), [
                'base_cost' => 'required|numeric|min:0',
                'cost_per_kg' => 'required|numeric|min:0',
                'estimated_days' => 'required|integer|min:1',
            ], $messages);

            // Nếu validation thất bại, trả về lỗi
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lỗi dữ liệu đầu vào',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Kiểm tra phương thức vận chuyển có tồn tại không
            $method = ShippingMethod::find($id);
            if (!$method) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy phương thức vận chuyển',
                ], 201);
            }

            // Cập nhật phương thức vận chuyển
            $method->update($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật phương thức vận chuyển thành công',
                'data' => $method,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình cập nhật',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

/**
 * @OA\Delete(
 *     path="/shipping/method/{id}",
 *     summary="Xóa phương thức vận chuyển",
 *     tags={"3. Shipping Methods"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID của phương thức vận chuyển",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Xóa thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Xóa phương thức vận chuyển thành công")
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Không thể xóa do đang được sử dụng",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Không thể xóa: Phương thức vận chuyển đang được sử dụng")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi máy chủ",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Đã xảy ra lỗi trong quá trình xóa"),
 *             @OA\Property(property="error", type="string", example="Chi tiết lỗi")
 *         )
 *     )
 * )
 */
    public function destroy($id): JsonResponse
    {
        try {
            // Kiểm tra phương thức vận chuyển có tồn tại không
            $method = ShippingMethod::find($id);
            if (!$method) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy phương thức vận chuyển',
                ], 201);
            }

            // Kiểm tra xem có đơn hàng nào đang sử dụng phương thức này không
            if ($method->orders()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không thể xóa: Phương thức vận chuyển đang được sử dụng',
                ], 409);
            }

            // Xóa phương thức vận chuyển
            $method->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa phương thức vận chuyển thành công',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình xóa',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

/**
 * @OA\Post(
 *     path="/shipping/method",
 *     summary="Tạo phương thức vận chuyển mới",
 *     tags={"3. Shipping Methods"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "base_cost", "cost_per_kg"},
 *             @OA\Property(property="name", type="string", example="Express Delivery"),
 *             @OA\Property(property="base_cost", type="number", example=10.0),
 *             @OA\Property(property="cost_per_kg", type="number", example=2.0),
 *             @OA\Property(property="description", type="string", example="Dịch vụ giao hàng nhanh trong 24h")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Tạo thành công",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Tạo phương thức vận chuyển thành công"),
 *             @OA\Property(property="data", type="object", example={"id": 1, "name": "Express Delivery", "base_cost": 10.0, "cost_per_kg": 2.0, "description": "Dịch vụ giao hàng nhanh trong 24h"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Lỗi dữ liệu đầu vào",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Lỗi dữ liệu đầu vào"),
 *             @OA\Property(property="errors", type="object", example={"name": {"Tên đã tồn tại"}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Lỗi hệ thống",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Lỗi máy chủ"),
 *             @OA\Property(property="error", type="string", example="Chi tiết lỗi...")
 *         )
 *     )
 * )
 */

    public function createShippingMethod(Request $request): JsonResponse
    {
        try {
            // Danh sách thông báo lỗi tiếng Việt
            $messages = [
                'name.required' => 'Tên phương thức vận chuyển không được để trống.',
                'name.string' => 'Tên phương thức vận chuyển phải là chuỗi ký tự.',
                'name.unique' => 'Tên phương thức vận chuyển đã tồn tại.',
                'base_cost.required' => 'Chi phí cơ bản không được để trống.',
                'base_cost.numeric' => 'Chi phí cơ bản phải là số.',
                'base_cost.min' => 'Chi phí cơ bản không được nhỏ hơn 0.',
                'cost_per_kg.required' => 'Chi phí mỗi kg không được để trống.',
                'cost_per_kg.numeric' => 'Chi phí mỗi kg phải là số.',
                'cost_per_kg.min' => 'Chi phí mỗi kg không được nhỏ hơn 0.',
                'description.string' => 'Mô tả phải là chuỗi ký tự.',
                'description.max' => 'Mô tả không được vượt quá 255 ký tự.',
            ];

            // Kiểm tra validate
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:shipping_methods,name',
                'base_cost' => 'required|numeric|min:0',
                'cost_per_kg' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:255',
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Lỗi dữ liệu đầu vào',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Tạo phương thức vận chuyển
            $method = ShippingMethod::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Tạo phương thức vận chuyển thành công',
                'data' => $method,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi máy chủ',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

}
