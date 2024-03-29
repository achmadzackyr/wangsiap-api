<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerStatusController;
use App\Http\Controllers\Api\GatewayController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderedProductController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Route::controller(AuthController::class)->group(function () {
//     Route::post('/auth/register', 'register');
//     Route::post('/login', 'login');
//     Route::post('/auth/forgot-password', 'forgot');
//     Route::post('/auth/reset-password', 'reset');

//     Route::middleware('auth:sanctum')->group(function () {
//         Route::prefix('/auth/profile')->group(function () {
//             Route::get('/', 'profile');
//             Route::post('/', 'update');
//         });

//         Route::post('/auth/logout', 'logout');
//     });

// });

Route::post('auth/register', [PassportAuthController::class, 'register']);
Route::post('auth/login', [PassportAuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('auth/profile', [PassportAuthController::class, 'profile']);
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
