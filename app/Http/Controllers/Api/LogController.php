<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogController extends Controller
{
    public function index()
    {
        return Log::latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $from = $request->from;
        if ($from[0] == "0") {
            $from = substr($from, 1);
        }

        if ($from[0] == "8") {
            $from = "62" . $from;
        }

        $to = $request->to;
        if ($to[0] == "0") {
            $to = substr($to, 1);
        }

        if ($to[0] == "8") {
            $to = "62" . $to;
        }

        return Log::create([
            'from' => $from,
            'to' => $to,
            'message' => $request->message,
        ]);
    }

    public function show(Log $log)
    {
        return $log;
    }

    public function update(Request $request, Log $log)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $from = $request->from;
        if ($from[0] == "0") {
            $from = substr($from, 1);
        }

        if ($from[0] == "8") {
            $from = "62" . $from;
        }

        $to = $request->to;
        if ($to[0] == "0") {
            $to = substr($to, 1);
        }

        if ($to[0] == "8") {
            $to = "62" . $to;
        }

        $log->update([
            'from' => $from,
            'to' => $to,
            'message' => $request->message,
        ]);
        return $log;
    }

    public function destroy(Log $log)
    {
        $log->delete();
        return 'Log Successfully Deleted!';
    }
}
