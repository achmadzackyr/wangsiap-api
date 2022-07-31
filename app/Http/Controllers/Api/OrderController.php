<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDetailResource;
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
            'no_pengirim' => 'required',
            'no_penerima' => 'required',
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
            'no_pengirim' => $request->no_pengirim,
            'no_penerima' => $request->no_penerima,
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
            'from' => 'required',
            'thru' => 'required',
            'ongkir' => 'required',
            'no_pengirim' => 'required',
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
                    'from' => $request->from,
                    'thru' => $request->thru,
                    'ongkir' => $request->ongkir,
                    'no_pengirim' => $request->no_pengirim,
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
                    'from' => $request->from,
                    'thru' => $request->thru,
                    'ongkir' => $request->ongkir,
                    'no_pengirim' => $request->no_pengirim,
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

    public function getLatestOrderBySender(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_pengirim' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hp = explode("@", $request->no_pengirim, 2)[0];

        $order = Order::where('no_pengirim', $hp)->latest()->first();
        if ($order == null || $order->order_status_id != 1) {
            return new OrderResource(false, 'Order not found', null);
        }
        //check if expire max 60 min
        $now = \Carbon\Carbon::now();
        $created_date = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $order->created_at);

        $diff_in_minutes = $now->diffInMinutes($created_date);
        if ($diff_in_minutes <= 60) {
            return new OrderResource(true, 'Order found!', $order);
        } else {
            //check if still 24 hours
            if ($diff_in_minutes <= 1440) {
                return new OrderResource(false, 'Order expired', null);
            } else {
                return new OrderResource(false, 'Order not found', null);
            }
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'order_status_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order->update([
            'order_status_id' => $request->order_status_id,
        ]);

        return new OrderResource(true, 'Order Successfully Updated!', $order);
    }

    public function getOrderDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order = Order::find($request->order_id);
        if ($order == null) {
            return new OrderResource(false, 'Order not found', null);
        }

        $customer = $order->customer()->first();
        $ordered_product = $order->ordered_products()->get();

        return new OrderDetailResource($order, $customer, $ordered_product);
    }
}
