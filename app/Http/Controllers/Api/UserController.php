<?php

namespace App\Http\Controllers\Api;

//use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

//use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return new UserResource(true, 'List Data Users', $users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'hp' => 'required',
            'from' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new UserResource(false, $validator->errors(), null), 422);

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
            'from' => $request->from,
        ]);

        return new UserResource(true, 'User Successfully Added!', $user);
    }

    public function show(User $user)
    {
        return new UserResource(true, 'User Found!', $user);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email',
            'hp' => 'required',
            'from' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new UserResource(false, $validator->errors(), null), 422);
        }

        try {
            $user->update([
                'nama' => $request->nama,
                'email' => $request->email,
                'hp' => $request->hp,
                'gender' => $request->gender,
                'jne_id' => $request->jne_id,
                'jne_id_cod' => $request->jne_id_cod,
                'alamat' => $request->alamat,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'kodepos' => $request->kodepos,
                'from' => $request->from,
            ]);
            return new UserResource(true, 'User Successfully Updated!', $user);
        } catch (\Illuminate\Database\QueryException$e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return response()->json(new UserResource(false, 'Email already exist', null), 422);
            }
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return new UserResource(true, 'User Successfully Deleted!', null);
    }

    // public function export()
    // {
    //     return Excel::download(new UsersExport, 'Users.xlsx');
    // }
}
