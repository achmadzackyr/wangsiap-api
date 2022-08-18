<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Http\Traits\WhatsappTrait;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    use WhatsappTrait;

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
        return $this->sendTextMessage($manager->hp, $employee->hp, $msg);
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
