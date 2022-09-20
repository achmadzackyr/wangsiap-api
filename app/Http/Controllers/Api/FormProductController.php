<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Models\FormProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormProductController extends Controller
{
    public function index()
    {
        $form = FormProduct::latest()->paginate(10);
        return new CommonResource(true, 'List Data Form Product', $form);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_id' => 'required',
            'product_id_list' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new CommonResource(false, $validator->errors(), null), 422);
        }

        $pList = explode(',', $request->product_id_list);

        //iteration product list
        foreach ($pList as $key => $p) {
            FormProduct::create([
                'form_id' => $request->form_id,
                'product_id' => $p,
            ]);
        }

        return new CommonResource(true, 'Form Product Successfully Added!', null);
    }

    public function show(FormProduct $form)
    {
        return new CommonResource(true, 'Form Product Found!', $form);
    }

    public function updateProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_id' => 'required',
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new CommonResource(false, $validator->errors(), null), 422);
        }

        //delete all by form_id

        //create new

        $form->update([
            'url' => $request->url,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'aktif' => $request->aktif,
        ]);
        return new CommonResource(true, 'Form Successfully Updated!', $form);
    }

    public function destroy(FormProduct $form)
    {
        $form->delete();
        return new CommonResource(true, 'Form Successfully Deleted!', null);
    }

    public function getByUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hp' => 'required',
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new CommonResource(false, $validator->errors(), null), 422);
        }

        $result = User::where('hp', $request->hp)->first()->forms()->where('url', $request->url)->first()->form_products()->get();
        return new CommonResource(true, 'Form Product Found!', $result);
    }
}
