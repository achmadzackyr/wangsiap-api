<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderedProductResource;
use App\Models\OrderedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderedProductController extends Controller
{
    public function index()
    {
        $orderedProducts = OrderedProduct::latest()->paginate(10);
        return new OrderedProductResource(true, 'List Data Ordered Products', $orderedProducts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'product_id' => 'required',
            'pcs' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderedProduct = OrderedProduct::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'pcs' => $request->pcs,
        ]);

        return new OrderedProductResource(true, 'Ordered Product Successfully Added!', $orderedProduct);
    }

    public function show(OrderedProduct $orderedProduct)
    {
        return new OrderedProductResource(true, 'Ordered Product Found!', $orderedProduct);
    }

    public function update(Request $request, OrderedProduct $orderedProduct)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'product_id' => 'required',
            'pcs' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $orderedProduct->update([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'pcs' => $request->pcs,
        ]);
        return new OrderedProductResource(true, 'Ordered Product Successfully Updated!', $orderedProduct);
    }

    public function destroy(OrderedProduct $orderedProduct)
    {
        $orderedProduct->delete();
        return new OrderedProductResource(true, 'Ordered Product Successfully Deleted!', null);
    }

    public function getByOrderId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new OrderedProductResource(false, $validator->errors(), null), 422);
        }

        $orderedProduct = OrderedProduct::where('order_id', $request->order_id)->get();
        if ($orderedProduct == null) {
            return response()->json(new OrderedProductResource(false, 'Product Not Found', null), 422);
        }
        return new OrderedProductResource(true, 'Product Found', $orderedProduct);
    }
}
