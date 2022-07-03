<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return new OrderResource(true, 'List Data Orders', $orders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'payment_id' => 'required',
            'user_id' => 'required',
            'total_harga' => 'required',
            'total_berat' => 'required',
            'total_pcs' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'payment_id' => $request->payment_id,
            'user_id' => $request->user_id,
            'order_status_id' => 1,
            //'tanggal_pesan_string' => date("d-m-Y H:i:s", strtotime('+7 hours')),
            'tanggal_pesan_string' => date("d-m-Y H:i:s"),
            'total_harga' => $request->total_harga,
            'total_berat' => $request->total_berat,
            'total_pcs' => $request->total_pcs,
        ]);

        return new OrderResource(true, 'Order Successfully Added!', $order);
    }

    public function show(Order $order)
    {
        return new OrderResource(true, 'Order Found!', $order);
    }

    public function update(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'payment_id' => 'required',
            'user_id' => 'required',
            'order_status_id' => 'required',
            'total_harga' => 'required',
            'total_berat' => 'required',
            'total_pcs' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            if ($request->awb != null) {
                $order->update([
                    'customer_id' => $request->customer_id,
                    'payment_id' => $request->payment_id,
                    'user_id' => $request->user_id,
                    'order_status_id' => $request->order_status_id,
                    'total_harga' => $request->total_harga,
                    'total_berat' => $request->total_berat,
                    'total_pcs' => $request->total_pcs,
                    'awb' => $request->awb,
                ]);
            } else {
                $order->update([
                    'customer_id' => $request->customer_id,
                    'payment_id' => $request->payment_id,
                    'user_id' => $request->user_id,
                    'order_status_id' => $request->order_status_id,
                    'total_harga' => $request->total_harga,
                    'total_berat' => $request->total_berat,
                    'total_pcs' => $request->total_pcs,
                ]);
            }
            return new OrderResource(true, 'Order Successfully Updated!', $order);
        } catch (\Illuminate\Database\QueryException$e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return response()->json(new OrderResource(false, 'AWB is already exist', null), 422);
            }
        }
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return new OrderResource(true, 'Order Successfully Deleted!', null);
    }
}
