<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WaSessionResource;
use App\Models\WaSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WaSessionController extends Controller
{
    public function index()
    {
        $waSession = WaSession::latest()->paginate(10);
        return new WaSessionResource(true, 'List Data Wa Session', $waSession);
    }

    public function getByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new WaSessionResource(false, $validator->errors(), null), 422);
        }

        $waSession = WaSession::where('user_id', $request->user_id)->first();
        if ($waSession == null) {
            return [];
        }
        return $waSession->session;
    }

    public function setSession(Request $request, WaSession $waSession)
    {
        $waSession->update([
            'session' => $request->session,
        ]);
        return new WaSessionResource(true, 'Wa Session Successfully Updated!', $waSession);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $waSession = WaSession::create([
            'user_id' => $request->user_id,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'session' => $request->session,
        ]);

        return new WaSessionResource(true, 'Wa Session Successfully Added!', $waSession);
    }

    public function show(WaSession $waSession)
    {
        return new WaSessionResource(true, 'Wa Session Found!', $waSession);
    }

    public function update(Request $request, WaSession $waSession)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $waSession->update([
            'user_id' => $request->user_id,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'session' => $request->session,
        ]);
        return new WaSessionResource(true, 'Wa Session Successfully Updated!', $waSession);
    }

    public function destroy(WaSession $waSession)
    {
        $waSession->delete();
        return new WaSessionResource(true, 'Wa Session Successfully Deleted!', null);
    }
}
