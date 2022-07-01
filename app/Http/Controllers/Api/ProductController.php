<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return new ProductResource(true, 'List Data Products', $products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required',
            'nama' => 'required',
            'harga' => 'required',
            'berat' => 'required',
            'lebar' => 'required',
            'tinggi' => 'required',
            'panjang' => 'required',
            'pecah_belah' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new ProductResource(false, $validator->errors(), null), 422);
        }

        try {
            $product = Product::create([
                'user_id' => $request->user_id,
                'sku' => $request->sku,
                'nama' => $request->nama,
                'harga' => $request->harga,
                'berat' => $request->berat,
                'lebar' => $request->lebar,
                'tinggi' => $request->tinggi,
                'panjang' => $request->panjang,
                'dibuat_pada_string' => date("d-m-Y H:i:s", strtotime('+7 hours')),
                'pecah_belah' => $request->pecah_belah,
                'aktif' => true,
            ]);
            return new ProductResource(true, 'Product Successfully Added!', $product);

        } catch (\Illuminate\Database\QueryException$e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return response()->json(new ProductResource(false, 'SKU is already exist', null), 422);
            }
        }
    }

    public function show(Product $product)
    {
        return new ProductResource(true, 'Product Found!', $product);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required',
            'nama' => 'required',
            'harga' => 'required',
            'berat' => 'required',
            'lebar' => 'required',
            'tinggi' => 'required',
            'panjang' => 'required',
            'pecah_belah' => 'required',
            'aktif' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new ProductResource(false, $validator->errors(), null), 422);
        }

        try {
            $product->update([
                'sku' => $request->sku,
                'nama' => $request->nama,
                'harga' => $request->harga,
                'berat' => $request->berat,
                'lebar' => $request->lebar,
                'tinggi' => $request->tinggi,
                'panjang' => $request->panjang,
                'pecah_belah' => $request->pecah_belah,
                'aktif' => $request->aktif,
            ]);
            return new ProductResource(true, 'Product Successfully Updated!', $product);
        } catch (\Illuminate\Database\QueryException$e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return response()->json(new ProductResource(false, 'SKU is already exist', null), 422);
            }
        }
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return new ProductResource(true, 'Product Successfully Deleted!', null);
    }

    public function getBySku(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new ProductResource(false, $validator->errors(), null), 422);
        }

        $product = Product::where('sku', $request->sku)->first();
        if ($product == null) {
            return response()->json(new ProductResource(false, 'Product Not Found', null), 422);
        }
        return new ProductResource(true, 'Product Found', $product);
    }
}
