<?php

namespace App\Http\Controllers\Api;

use App\Exports\CustomersExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return new CustomerResource(true, 'List Data Customers', $customers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'alamat' => 'required',
            'kodepos' => 'required',
            'hp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $customer = Customer::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'kecamatan' => $request->kecamatan,
            'kota' => $request->kota,
            'kodepos' => $request->kodepos,
            'hp' => $request->hp,
            'order_date_string' => date("d-m-Y H:i:s", strtotime('+7 hours')),
            'customer_status_id' => 1,
        ]);

        return new CustomerResource(true, 'Customer Successfully Added!', $customer);
    }

    public function show(Customer $customer)
    {
        return new CustomerResource(true, 'Customer Found!', $customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'alamat' => 'required',
            'kodepos' => 'required',
            'hp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $customer->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'kecamatan' => $request->kecamatan,
            'kota' => $request->kota,
            'kodepos' => $request->kodepos,
            'hp' => $request->hp,
        ]);
        return new CustomerResource(true, 'Customer Successfully Updated!', $customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return new CustomerResource(true, 'Customer Successfully Deleted!', null);
    }

    public function export()
    {
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }
}
