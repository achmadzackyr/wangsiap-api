<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerStatusController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\FormProductController;
use App\Http\Controllers\Api\GatewayController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderedProductController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReplyController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TemplateController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserTemplateController;
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
        Route::post('/products/get-my-product-form', [ProductController::class, 'getMyProductForm']);
        Route::post('/products/store-my-product', [ProductController::class, 'storeMyProduct']);
        Route::post('/products/check-my-sku', [ProductController::class, 'checkMySku']);
        Route::post('/products/get-my-product-detail', [ProductController::class, 'getByProductDetail']);
        Route::post('/customers/get-my-customer', [CustomerController::class, 'getMyCustomer']);
        Route::post('/users/assign-role', [UserController::class, 'assignRole']);
        Route::post('/employees/cs-confrim', [EmployeeController::class, 'sendCsConfirmation']);

        Route::post('/replies/get', [ReplyController::class, 'get']);
        Route::post('/replies/get-reply', [ReplyController::class, 'getReply']);
        Route::post('/replies/add', [ReplyController::class, 'store']);
        Route::post('/replies/update', [ReplyController::class, 'update']);
        Route::post('/replies/delete', [ReplyController::class, 'delete']);
        Route::post('/replies/list', [ReplyController::class, 'list']);
        Route::post('/user-templates/get-reply', [UserTemplateController::class, 'getReply']);
        Route::post('/forms/get-my-form', [FormController::class, 'getMyForm']);

        Route::apiResource('/subscriptions', SubscriptionController::class);
        Route::apiResource('/roles', RoleController::class);
        Route::apiResource('/templates', TemplateController::class);
        Route::apiResource('/user-templates', UserTemplateController::class);
        Route::apiResource('/forms', FormController::class);
        Route::apiResource('/form-products', FormProductController::class);
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

Route::get('/employees/approve-cs/{token}', [EmployeeController::class, 'approveCs']);
Route::post('/users/get-user-by-phone', [UserController::class, 'getUserByPhone']);
Route::post('/wa-sessions/get-by-user-id', [WaSessionController::class, 'getByUserId']);
Route::post('/wa-sessions/set-session/{wa_session}', [WaSessionController::class, 'setSession']);
Route::post('/products/getBySku', [ProductController::class, 'getBySku']);
Route::post('/gateway/order', [GatewayController::class, 'order']);
Route::post('/gateway/order-list', [GatewayController::class, 'orderList']);
Route::post('/gateway/get-destinations-by-zip', [GatewayController::class, 'getDestinationsByZip']);
Route::post('/gateway/get-zip-by-destination', [GatewayController::class, 'getZipByDestination']);
Route::post('/gateway/get-origin', [GatewayController::class, 'getOrigin']);
Route::post('/gateway/get-tarif', [GatewayController::class, 'getTarif']);
Route::post('/gateway/downloadLoader', [GatewayController::class, 'downloadLoader']);
Route::post('/gateway/orderx', [GatewayController::class, 'orderx']);
Route::post('/gateway/orderfull', [GatewayController::class, 'orderFull']);
Route::post('/gateway/confirm-order', [GatewayController::class, 'confirmOrder']);
Route::get('/customers/export/all', [CustomerController::class, 'export']);
Route::post('/orders/get-latest-by-sender', [OrderController::class, 'getLatestOrderBySender']);
Route::put('/orders/update-status/{order}', [OrderController::class, 'updateStatus']);
Route::post('/orders/get-order-detail', [OrderController::class, 'getOrderDetail']);
Route::post('/ordered-products/get-by-order', [OrderedProductController::class, 'getByOrderId']);
Route::post('/ordered-products/update-by-order', [OrderedProductController::class, 'updateByOrderId']);
Route::post('/form-products/get-by-url', [FormProductController::class, 'getByUrl']);
