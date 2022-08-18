<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Models\Reply;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ReplyController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hp' => 'required',
            'type' => 'required',
            'keyword' => 'required',
            'reply' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new CommonResource(false, $validator->errors(), null), 422);
        }

        $type = $request->type;

        $cek = Reply::where('hp', $request->hp)->where('keyword', $request->keyword)->first();
        if ($cek) {
            return response()->json(new CommonResource(false, "Keyword sudah ada", null), 500);
        }

        switch ($type) {
            case 'text':
                $reply = ["text" => $request->reply];
                break;
            case 'image';
                $request->validate([
                    'image' => ['required'],
                    'caption' => 'required',
                ]);
                $arr = explode('.', $request->image);
                $ext = end($arr);
                $allowext = ['jpg', 'png', 'jpeg'];
                if (!in_array($ext, $allowext)) {
                    return redirect(route('autoreply'))->with('alert', [
                        'type' => 'danger',
                        'msg' => 'Only extension jpg,png and jpeg allowed!',
                    ]);
                }
                $reply = [
                    "image" => ["url" => $request->image],
                    "caption" => $request->caption,
                ];
                break;
            case 'button':
                $request->validate([

                    'button1' => 'required',
                ]);
                if ($request->image) {
                    $arr = explode('.', $request->image);
                    $ext = end($arr);
                    $allowext = ['jpg', 'png', 'jpeg'];
                    if (!in_array($ext, $allowext)) {
                        return redirect(route('autoreply'))->with('alert', [
                            'type' => 'danger',
                            'msg' => 'Only extension jpg,png and jpeg allowed!',
                        ]);
                    }
                }
                $buttons = [
                    ["buttonId" => "id1", "buttonText" => ["displayText" => $request->button1], "type" => 1],
                ];
                // add if exist button2
                if ($request->button2) {
                    $buttons[] = ["buttonId" => "id2", "buttonText" => ["displayText" => $request->button2], "type" => 1];
                }
                // add if exist button3
                if ($request->button3) {
                    $buttons[] = ["buttonId" => "id3", "buttonText" => ["displayText" => $request->button3], "type" => 1];
                }
                $buttonMessage = [
                    "text" => $request->reply,
                    "footer" => $request->footer ?? '',
                    "buttons" => $buttons,
                    "headerType" => 1,
                ];
                //add image to buttonMessage if exists
                if ($request->image) {
                    unset($buttonMessage['text']);
                    $buttonMessage['caption'] = $request->reply;
                    $buttonMessage['image'] = ["url" => $request->image];
                    $buttonMessage['headerType'] = 4;

                }
                $reply = $buttonMessage;
                break;
            default:
                # code...
                break;
        }

        $jsonReply = json_encode($reply);
        $rep = Reply::create([
            'user_id' => Auth::id(),
            'hp' => $request->hp,
            'keyword' => $request->keyword,
            'type' => $request->type,
            'reply' => $jsonReply,
        ]);

        return new CommonResource(true, 'Reply Successfully Added!', $rep);
    }

    public function getReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hp' => 'required',
            'keyword' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new CommonResource(false, $validator->errors(), null), 422);
        }

        $reply = Reply::where('hp', $request->hp)->where('keyword', $request->keyword)->first();
        if (!$reply) {
            return response()->json(new CommonResource(false, "Template tidak ditemukan", null), 404);
        }

        switch ($reply->type) {
            case 'text':
                $response = [
                    'text' => json_decode($reply->reply)->text,
                    'keyword' => $reply->keyword,
                ];
                return new CommonResource(true, 'Reply Found!', $response);
                break;
            case 'image':
                $response = [
                    'keyword' => $reply->keyword,
                    'caption' => json_decode($reply->reply)->caption,
                    'image' => json_decode($reply->reply)->image->url,
                ];
                return new CommonResource(true, 'Reply Found!', $response);
                break;
            case 'button':
                $response = [
                    'keyword' => $reply->keyword,
                    'message' => json_decode($reply->reply)->text ?? json_decode($reply->reply)->caption,
                    'footer' => json_decode($reply->reply)->footer,
                    'buttons' => json_decode($reply->reply)->buttons,
                    'image' => json_decode($reply->reply)->image->url ?? null,
                ];
                return new CommonResource(true, 'Reply Found!', $response);
                break;
            default:
                # code...
                break;
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hp' => 'required',
            'type' => 'required',
            'keyword' => 'required',
            'reply' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new CommonResource(false, $validator->errors(), null), 422);
        }

        $type = $request->type;

        $myReply = Reply::where('hp', $request->hp)->where('keyword', $request->keyword)->first();
        if (!$myReply) {
            return response()->json(new CommonResource(false, "Template tidak ditemukan", null), 404);
        }

        switch ($type) {
            case 'text':
                $reply = ["text" => $request->reply];
                break;
            case 'image';
                $request->validate([
                    'image' => ['required'],
                    'caption' => 'required',
                ]);
                $arr = explode('.', $request->image);
                $ext = end($arr);
                $allowext = ['jpg', 'png', 'jpeg'];
                if (!in_array($ext, $allowext)) {
                    return redirect(route('autoreply'))->with('alert', [
                        'type' => 'danger',
                        'msg' => 'Only extension jpg,png and jpeg allowed!',
                    ]);
                }
                $reply = [
                    "image" => ["url" => $request->image],
                    "caption" => $request->caption,
                ];
                break;
            case 'button':
                $request->validate([

                    'button1' => 'required',
                ]);
                if ($request->image) {
                    $arr = explode('.', $request->image);
                    $ext = end($arr);
                    $allowext = ['jpg', 'png', 'jpeg'];
                    if (!in_array($ext, $allowext)) {
                        return redirect(route('autoreply'))->with('alert', [
                            'type' => 'danger',
                            'msg' => 'Only extension jpg,png and jpeg allowed!',
                        ]);
                    }
                }
                $buttons = [
                    ["buttonId" => "id1", "buttonText" => ["displayText" => $request->button1], "type" => 1],
                ];
                // add if exist button2
                if ($request->button2) {
                    $buttons[] = ["buttonId" => "id2", "buttonText" => ["displayText" => $request->button2], "type" => 1];
                }
                // add if exist button3
                if ($request->button3) {
                    $buttons[] = ["buttonId" => "id3", "buttonText" => ["displayText" => $request->button3], "type" => 1];
                }
                $buttonMessage = [
                    "text" => $request->reply,
                    "footer" => $request->footer ?? '',
                    "buttons" => $buttons,
                    "headerType" => 1,
                ];
                //add image to buttonMessage if exists
                if ($request->image) {
                    unset($buttonMessage['text']);
                    $buttonMessage['caption'] = $request->reply;
                    $buttonMessage['image'] = ["url" => $request->image];
                    $buttonMessage['headerType'] = 4;

                }
                $reply = $buttonMessage;
                break;
            default:
                # code...
                break;
        }

        $jsonReply = json_encode($reply);
        $myReply->update([
            'user_id' => Auth::id(),
            'hp' => $request->hp,
            'keyword' => $request->keyword,
            'type' => $request->type,
            'reply' => $jsonReply,
        ]);

        return new CommonResource(true, 'Reply Successfully Updated!', $myReply);
    }

    function list(Request $request) {
        $validator = Validator::make($request->all(), [
            'hp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new CommonResource(false, $validator->errors(), null), 422);
        }

        $replies = Reply::where('hp', $request->hp)->get();
        if (!$replies) {
            return response()->json(new CommonResource(false, "Reply tidak ditemukan", null), 404);
        }

        $res = new Collection([]);
        foreach ($replies as $reply) {
            switch ($reply->type) {
                case 'text':
                    $response = [
                        'id' => $reply->id,
                        'text' => json_decode($reply->reply)->text,
                        'keyword' => $reply->keyword,
                    ];
                    $res->push($response);
                    break;
                case 'image':
                    $response = [
                        'id' => $reply->id,
                        'keyword' => $reply->keyword,
                        'caption' => json_decode($reply->reply)->caption,
                        'image' => json_decode($reply->reply)->image->url,
                    ];
                    $res->push($response);
                    break;
                case 'button':
                    $response = [
                        'id' => $reply->id,
                        'keyword' => $reply->keyword,
                        'message' => json_decode($reply->reply)->text ?? json_decode($reply->reply)->caption,
                        'footer' => json_decode($reply->reply)->footer,
                        'buttons' => json_decode($reply->reply)->buttons,
                        'image' => json_decode($reply->reply)->image->url ?? null,
                    ];
                    $res->push($response);
                    break;
                default:
                    # code...
                    break;
            }
        }

        return new CommonResource(true, 'Reply Found!', $res);
    }
}
