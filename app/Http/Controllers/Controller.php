<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Shipping API",
 *     version="1.0",
 *     description="API quản lý vận chuyển, tính phí, và quản lý phương thức vận chuyển."
 * )
 * @OA\Server(
 *     url="/api",
 *     description="API LocalHost Server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
