<?php
namespace App\Http\Traits;

use App\Models\UserTemplate;
use Illuminate\Support\Facades\Http;

trait WhatsappTrait
{

    public function sendMessage($sender, $receiver, $msg)
    {
        $raw = '{"receiver": "' . $receiver . '", "message":' . $msg . '}';
        $encoded = json_encode($raw);
        $decoded = json_decode($encoded, true);
        $response = Http::withBody($decoded, 'application/json')->post(env('WA_URL') . '/chats/send?id=' . $sender);

        return json_decode($response, true);
    }

    public function sendTextMessage($sender, $receiver, $msg)
    {
        $raw = '{"receiver": "' . $receiver . '", "message": {"text": "' . $msg . '"}}';
        $encoded = json_encode($raw);
        $decoded = json_decode($encoded, true);
        $response = Http::withBody($decoded, 'application/json')->post(env('WA_URL') . '/chats/send?id=' . $sender);

        return json_decode($response, true);
    }

    public function getWaReply($hp, $template_id)
    {
        $userTemplate = UserTemplate::where('hp', $hp)
            ->where('template_id', $template_id)->first();

        //Return default reply
        if ($userTemplate == null) {
            $userTemplate = UserTemplate::where('hp', '1234567890987654321')
                ->where('template_id', $template_id)->first();
        }

        return json_decode($userTemplate->reply->reply);
    }

}
