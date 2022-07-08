<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerStatusController;
use App\Http\Controllers\Api\GatewayController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderedProductController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\PassportAuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/register', [PassportAuthController::class, 'register']);
Route::post('/auth/login', [PassportAuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/profile', [PassportAuthController::class, 'profile']);
});

Route::apiResource('/customers', CustomerController::class);
Route::apiResource('/customer-status', CustomerStatusController::class);
Route::apiResource('/ordered-products', OrderedProductController::class);
Route::apiResource('/payments', PaymentController::class);
Route::apiResource('/orders', OrderController::class);
Route::apiResource('/order-status', OrderStatusController::class);
Route::apiResource('/products', ProductController::class);
Route::apiResource('/users', UserController::class);

Route::post('/products/getBySku', [ProductController::class, 'getBySku']);
Route::post('/gateway/order', [GatewayController::class, 'order']);
Route::post('/gateway/order-list', [GatewayController::class, 'orderList']);
Route::get('/gateway/downloadLoader', [GatewayController::class, 'downloadLoader']);
Route::get('/customers/export/all', [CustomerController::class, 'export']);
