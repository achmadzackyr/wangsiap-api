<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function sendCsConfirmation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $employee = User::find($request->employee_id);
        if ($employee == null) {
            return response()->json(new CommonResource(false, 'Employee Not Found', null), 422);
        }

        //Check employee is a user

        $manager = $request->user();
        $encrypted = Crypt::encryptString($manager->id . '|' . $request->employee_id);

        $msg = "Kamu ditambahkan sebagai CS oleh " . $manager->nama . ". Klik link berikut untuk menyetujuinya https://api.wangsiap.com/api/employees/approve-cs/" . $encrypted;

        $encoded = json_encode('{"receiver": "' . $employee->hp . '", "message": {"text": "' . $msg . '"}}');
        $decoded = json_decode($encoded, true);
        $response = Http::withBody($decoded, 'application/json')
            ->post('https://wagw.wangsiap.com/chats/send?id=' . $manager->hp);

        $res = json_decode($response, true);

        return new CommonResource(true, 'Cs Confirmation Successfully Added!', $res);
    }

    public function approveCs($token)
    {
        try {
            $decrypted = Crypt::decryptString($token);
            $splitted = explode('|', $decrypted);
            $manager_id = $splitted[0];
            $employee_id = $splitted[1];

            //Check if already employeed
            $isAlreadyEmployeed = Employee::where('employee_id', $employee_id)->first();
            if ($isAlreadyEmployeed != null) {
                return response()->json(new CommonResource(false, 'You are already an employee', null), 500);
            }

            $employee = Employee::create([
                'manager_id' => $manager_id,
                'employee_id' => $employee_id,
            ]);
        } catch (DecryptException $e) {
            return response()->json(new CommonResource(false, 'Failed to Decrypt Token', null), 500);
        }

        return new CommonResource(true, 'Employee Successfully Approved!', $employee);
    }
}
