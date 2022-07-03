<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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

    public function forgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(new UserResource(false, $validator->errors(), null), 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status === Password::RESET_LINK_SENT) {
            return new UserResource(true, 'Password reset link has been sent', $status);
        } else {
            return response()->json(new UserResource(false, $status, null), 422);
        }

        switch ($status) {
            case Password::RESET_LINK_SENT:
                return new UserResource(true, 'Password reset link has been sent', $status);
                break;
            case Password::RESET_THROTTLED:
                return response()->json(new UserResource(false, "Please wait 60 seconds to request link again", null), 400);
                break;
            default:
                return response()->json(new UserResource(false, $status, null), 422);
        }
    }

    public function reset(Request $request)
    {
        $input = $request->only('email', 'token', 'password', 'password_confirmation');
        $validator = Validator::make($input, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(new UserResource(false, $validator->errors(), null), 422);
        }

        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        switch ($response) {
            case Password::PASSWORD_RESET:
                return new UserResource(true, 'Password reset successfully', $response);
                break;
            case Password::INVALID_TOKEN:
                return response()->json(new UserResource(false, "Invalid token provided", null), 400);
                break;
            case Password::INVALID_USER:
                return response()->json(new UserResource(false, "Email not found", null), 500);
                break;
            default:
                return response()->json(new UserResource(false, $response, null), 500);
        }
    }
}
