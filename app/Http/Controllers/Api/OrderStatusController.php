<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderStatusController extends Controller
{
    public function index()
    {
        $orderStatus = OrderStatus::latest()->paginate(10);
        return new OrderStatusResource(true, 'List Data Order Status', $orderStatus);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderStatus = OrderStatus::create([
            'order_status' => $request->order_status,
        ]);

        return new OrderStatusResource(true, 'Order Status Successfully Added!', $orderStatus);
    }

    public function show(OrderStatus $orderStatus)
    {
        return new OrderStatusResource(true, 'Order Status Found!', $orderStatus);
    }

    public function update(Request $request, OrderStatus $orderStatus)
    {
        $validator = Validator::make($request->all(), [
            'order_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderStatus->update([
            'order_status' => $request->order_status,
        ]);
        return new OrderStatusResource(true, 'Order Status Successfully Updated!', $orderStatus);
    }

    public function destroy(OrderStatus $orderStatus)
    {
        $orderStatus->delete();
        return new OrderStatusResource(true, 'Order Status Successfully Deleted!', null);
    }
}
