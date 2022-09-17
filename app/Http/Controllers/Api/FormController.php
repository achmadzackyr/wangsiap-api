<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Models\Form;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    public function index()
    {
        $form = Form::latest()->paginate(10);
        return new CommonResource(true, 'List Data Form', $form);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new CommonResource(false, $validator->errors(), null), 422);
        }

        $user = Auth::user();

        //Check url unique per user
        $form = Form::where('url', $request->url)->first();
        if ($form != null) {
            return response()->json(new CommonResource(false, 'Form ' . $request->url . ' already exist', null), 500);
        }

        $form = Form::create([
            'user_id' => $user->id,
            'url' => $request->url,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'aktif' => true,
        ]);

        return new CommonResource(true, 'Form Successfully Added!', $form);
    }

    public function show(Form $form)
    {
        return new CommonResource(true, 'Form Found!', $form);
    }

    public function update(Request $request, Form $form)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new CommonResource(false, $validator->errors(), null), 422);
        }

        $user = Auth::user();

        $form->update([
            'url' => $request->url,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'aktif' => $request->aktif,
        ]);
        return new CommonResource(true, 'Form Successfully Updated!', $form);
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return new CommonResource(true, 'Form Successfully Deleted!', null);
    }
}
