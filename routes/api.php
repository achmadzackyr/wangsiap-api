<?php

use App\Http\Controllers\Api\GatewayController;
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

Route::apiResource('/customers', App\Http\Controllers\Api\CustomerController::class);
Route::apiResource('/customer-status', App\Http\Controllers\Api\CustomerStatusController::class);
Route::apiResource('/ordered-products', App\Http\Controllers\Api\OrderedProductController::class);
Route::apiResource('/payments', App\Http\Controllers\Api\PaymentController::class);
Route::apiResource('/orders', App\Http\Controllers\Api\OrderController::class);
Route::apiResource('/order-status', App\Http\Controllers\Api\OrderStatusController::class);
Route::apiResource('/products', App\Http\Controllers\Api\ProductController::class);
Route::post('/gateway/order', [GatewayController::class, 'order']);
