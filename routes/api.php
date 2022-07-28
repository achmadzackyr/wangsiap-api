<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerStatusController;
use App\Http\Controllers\Api\GatewayController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderedProductController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WaSessionController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/auth/register', 'register');
    Route::post('/auth/login', 'login');
    Route::post('/auth/forgot-password', 'forgot');
    Route::post('/auth/reset-password', 'reset');

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('/auth/profile')->group(function () {
            Route::get('/', 'profile');
            Route::post('/', 'update');
        });
        Route::post('/auth/logout', 'logout');
        Route::post('/products/get-my-product', [ProductController::class, 'getMyProduct']);
        Route::post('/products/store-my-product', [ProductController::class, 'storeMyProduct']);
        Route::post('/products/check-my-sku', [ProductController::class, 'checkMySku']);
        Route::post('/products/get-my-product-detail', [ProductController::class, 'getByProductDetail']);
        Route::post('/customers/get-my-customer', [CustomerController::class, 'getMyCustomer']);
    });
});

Route::apiResource('/customers', CustomerController::class);
Route::apiResource('/customer-status', CustomerStatusController::class);
Route::apiResource('/ordered-products', OrderedProductController::class);
Route::apiResource('/payments', PaymentController::class);
Route::apiResource('/orders', OrderController::class);
Route::apiResource('/order-status', OrderStatusController::class);
Route::apiResource('/products', ProductController::class);
Route::apiResource('/users', UserController::class);
Route::apiResource('/wa-sessions', WaSessionController::class);
Route::apiResource('/logs', LogController::class);

Route::post('/users/get-user-by-phone', [UserController::class, 'getUserByPhone']);
Route::post('/wa-sessions/get-by-user-id', [WaSessionController::class, 'getByUserId']);
Route::post('/wa-sessions/set-session/{wa_session}', [WaSessionController::class, 'setSession']);
Route::post('/products/getBySku', [ProductController::class, 'getBySku']);
Route::post('/gateway/order', [GatewayController::class, 'order']);
Route::post('/gateway/order-list', [GatewayController::class, 'orderList']);
Route::post('/gateway/get-destinations-by-zip', [GatewayController::class, 'getDestinationsByZip']);
Route::post('/gateway/get-zip-by-destination', [GatewayController::class, 'getZipByDestination']);
Route::post('/gateway/get-origin', [GatewayController::class, 'getOrigin']);
Route::post('/gateway/downloadLoader', [GatewayController::class, 'downloadLoader']);
Route::get('/customers/export/all', [CustomerController::class, 'export']);
Route::post('/orders/get-latest-by-sender', [OrderController::class, 'getLatestOrderBySender']);
Route::put('/orders/update-status/{order}', [OrderController::class, 'updateStatus']);
Route::post('/orders/get-order-detail', [OrderController::class, 'getOrderDetail']);
