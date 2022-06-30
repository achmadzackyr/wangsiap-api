<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerStatusResource;
use App\Models\CustomerStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerStatusController extends Controller
{
    public function index()
    {
        $customerStatus = CustomerStatus::latest()->paginate(10);
        return new CustomerStatusResource(true, 'List Data Customer Status', $customerStatus);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $customerStatus = CustomerStatus::create([
            'nama_status' => $request->nama_status,
        ]);

        return new CustomerStatusResource(true, 'Customer Status Successfully Added!', $customerStatus);
    }

    public function show(CustomerStatus $customerStatus)
    {
        return new CustomerStatusResource(true, 'Customer Status Found!', $customerStatus);
    }

    public function update(Request $request, CustomerStatus $customerStatus)
    {
        $validator = Validator::make($request->all(), [
            'nama_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $customerStatus->update([
            'nama_status' => $request->nama_status,
        ]);
        return new CustomerStatusResource(true, 'Customer Status Successfully Updated!', $customerStatus);
    }

    public function destroy(CustomerStatus $customerStatus)
    {
        $customerStatus->delete();
        return new CustomerStatusResource(true, 'Customer Status Successfully Deleted!', null);
    }
}
