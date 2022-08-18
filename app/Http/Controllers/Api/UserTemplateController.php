<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Http\Traits\WhatsappTrait;
use App\Models\UserTemplate;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserTemplateController extends Controller
{
    use WhatsappTrait;

    public function index()
    {
        $userTemplate = UserTemplate::latest()->paginate(10);
        return new CommonResource(true, 'List Data User Template', $userTemplate);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hp' => 'required',
            'reply_id' => 'required',
            'template_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userTemplate = UserTemplate::create([
            'hp' => $request->hp,
            'reply_id' => $request->reply_id,
            'template_id' => $request->template_id,
            'user_id' => Auth::id(),
        ]);

        return new CommonResource(true, 'User Template Successfully Added!', $userTemplate);
    }

    public function show(UserTemplate $userTemplate)
    {
        return new CommonResource(true, 'User Template Found!', $userTemplate);
    }

    public function update(Request $request, UserTemplate $userTemplate)
    {
        $validator = Validator::make($request->all(), [
            'reply_id' => 'required',
            'template_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userTemplate->update([
            'reply_id' => $request->reply_id,
            'template_id' => $request->template_id,
        ]);
        return new CommonResource(true, 'User Template Successfully Updated!', $userTemplate);
    }

    public function destroy(UserTemplate $userTemplate)
    {
        $userTemplate->delete();
        return new CommonResource(true, 'User Template Successfully Deleted!', null);
    }

    public function getReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hp' => 'required',
            'template_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $response = $this->getWaReply($request->hp, $request->template_id);

        return new CommonResource(true, 'Reply Found!', $response);
    }
}
