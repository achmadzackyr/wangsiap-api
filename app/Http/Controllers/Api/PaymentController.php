<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        $payment = Payment::latest()->paginate(10);
        return new PaymentResource(true, 'List Data Payment', $payment);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $payment = Payment::create([
            'nama' => $request->nama,
        ]);

        return new PaymentResource(true, 'Payment Successfully Added!', $payment);
    }

    public function show(Payment $payment)
    {
        return new PaymentResource(true, 'Payment Found!', $payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $payment->update([
            'nama' => $request->nama,
        ]);
        return new PaymentResource(true, 'Payment Successfully Updated!', $payment);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return new PaymentResource(true, 'Payment Successfully Deleted!', null);
    }
}
