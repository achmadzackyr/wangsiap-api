<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GatewayResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function order(Request $request)
    {
        $message = $request->message;
        $sender = $request->sender;

        $mEx = explode('#', $message);

        $customer = Customer::create([
            'nama' => $mEx[1],
            'alamat' => $mEx[2],
            //'kecamatan' => $request->kecamatan,
            //'kota' => $request->kota,
            'kodepos' => $mEx[3],
            'hp' => $mEx[4],
            'order_date_string' => date("d-m-Y H:i:s", strtotime('+7 hours')),
        ]);

        return new GatewayResource(true, 'Order Successfully Added!', $customer);
    }
}
