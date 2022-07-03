<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'hp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new UserResource(false, $validator->errors(), null), 422);
        }

        $hp = $request['hp'];
        if ($request['hp'][0] == "0") {
            $hp = substr($hp, 1);
        }

        if ($hp[0] == "8") {
            $hp = "62" . $hp;
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'hp' => $request->hp,
            'gender' => $request->gender,
            'jne_id' => $request->jne_id,
            'jne_id_cod' => $request->jne_id_cod,
            'alamat' => $request->alamat,
            'kecamatan' => $request->kecamatan,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'kodepos' => $request->kodepos,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->token = $token;

        return new UserResource(true, 'User Successfully Added!', $user);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(new UserResource(false, "Unauthorized", null), 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->token = $token;

        return new UserResource(true, 'You Successfully Logged In!', $user);
    }

    public function profile()
    {
        return response()->json(['message' => 'Your Profile', 'data' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'hp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new UserResource(false, $validator->errors(), null), 422);
        }

        $hp = $request['hp'];
        if ($request['hp'][0] == "0") {
            $hp = substr($hp, 1);
        }

        if ($hp[0] == "8") {
            $hp = "62" . $hp;
        }

        $user->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'hp' => $request->hp,
            'gender' => $request->gender,
            'jne_id' => $request->jne_id,
            'jne_id_cod' => $request->jne_id_cod,
            'alamat' => $request->alamat,
            'kecamatan' => $request->kecamatan,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'kodepos' => $request->kodepos,
        ]);
        return new UserResource(true, 'Your Profile Successfully Updated!', $user);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return new UserResource(true, 'You have been logged out', null);
    }
}
