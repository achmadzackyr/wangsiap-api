<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscription = Subscription::latest()->paginate(10);
        return new CommonResource(true, 'List Data Subscription', $subscription);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'jumlah_hari' => 'required',
            'harga' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $subscription = Subscription::create([
            'nama' => $request->nama,
            'jumlah_hari' => $request->jumlah_hari,
            'harga' => $request->harga,
        ]);

        return new CommonResource(true, 'Subscription Successfully Added!', $subscription);
    }

    public function show(Subscription $subscription)
    {
        return new CommonResource(true, 'Subscription Found!', $subscription);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'jumlah_hari' => 'required',
            'harga' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $subscription->update([
            'nama' => $request->nama,
            'jumlah_hari' => $request->jumlah_hari,
            'harga' => $request->harga,
        ]);
        return new CommonResource(true, 'Subscription Successfully Updated!', $subscription);
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return new CommonResource(true, 'Subscription Successfully Deleted!', null);
    }
}
