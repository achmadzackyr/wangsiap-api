<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    public function index()
    {
        $template = Template::latest()->paginate(10);
        return new CommonResource(true, 'List Data Template', $template);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $template = Template::create([
            'nama' => $request->nama,
        ]);

        return new CommonResource(true, 'Template Successfully Added!', $template);
    }

    public function show(Template $template)
    {
        return new CommonResource(true, 'Template Found!', $template);
    }

    public function update(Request $request, Template $template)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $template->update([
            'nama' => $request->nama,
        ]);
        return new CommonResource(true, 'Template Successfully Updated!', $template);
    }

    public function destroy(Template $template)
    {
        $template->delete();
        return new CommonResource(true, 'Template Successfully Deleted!', null);
    }
}
