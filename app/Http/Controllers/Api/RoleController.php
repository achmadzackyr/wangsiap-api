<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $role = Role::latest()->paginate(10);
        return new CommonResource(true, 'List Data Role', $role);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $role = Role::create([
            'nama' => $request->nama,
        ]);

        return new CommonResource(true, 'Role Successfully Added!', $role);
    }

    public function show(Role $role)
    {
        return new CommonResource(true, 'Role Found!', $role);
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $role->update([
            'nama' => $request->nama,
        ]);
        return new CommonResource(true, 'Role Successfully Updated!', $role);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return new CommonResource(true, 'Role Successfully Deleted!', null);
    }
}
