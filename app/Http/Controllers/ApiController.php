<?php

namespace App\Http\Controllers;

use App\Accesses;
use Illuminate\Http\Request;
use App\Access;

class ApiController extends Controller
{
    protected $information    = null;
    protected $token          = null;
    protected $status         = true;
    protected $header         = "Header project";
    protected $whatsappUri 	  = "https://here_will_be_url.ru";
    protected $whatsappToken  = "24zwfk8g8xn32n5a";

    public function __construct(Request $request)
    {
        if(!empty($request->route()->parameters()))
        {
            $this->token = $request->route()->parameters()['token'];
        }
    }

    public function message(Request $request)
    {
        $body  = isset($request['body']) ? strval($request['body']) : '';
        $phone = isset($request['phone']) ? $request['phone'] : (isset($request['chatId']) ? stristr($request['chatId'], '@', true) : null);

        if($phone[0] == 7)
        {
            $params = array(
                'phone'  => $phone,
                'body'   => $body
            );

            return $this->call('sendMessage', $params);
        }

        $this->information  = 'Message was not sent: phone number must begin with 7.';
        $this->status       = false;

        return response()->json([
            'sent'    => $this->status,
            'message' => $this->information
        ], 200);
    }

    public function attachment(Request $request)
    {
        $phone      = isset($request['phone']) ? $request['phone'] : (isset($request['chatId']) ? stristr($request['chatId'], '@', true) : null);
        $body       = isset($request['body']) ? strval($request['body']) : null;
        $filename   = isset($request['filename']) ? strval($request['filename']) : null;
        $caption    = isset($request['caption']) ? strval($request['caption']): null;

        if($phone[0] == 7)
        {
            $params = array(
                'phone'     => $phone,
                'body'      => $body,
                'filename'  => $filename,
                'caption'   => $caption
            );

            return $this->call('sendFile', $params);
        }

        $this->information  = 'Message was not sent: phone number must begin with 7.';
        $this->status       = false;

        return response()->json([
            'sent'    => $this->status,
            'message' => $this->information
        ], 200);
    }

    public function webhook(Request $request)
    {
        $this->information  = 'Redirect uri not exist!';
        $redirect           = $request['webhookUri'];

        if(!isset($redirect))
        {
            return response()->json([
                "status"    => $this->status,
                "message"   => $this->information
            ], 200);
        }

        $update = Accesses::where('token', $this->token)->update(['redirect' => $redirect]);

        $this->information  = 'Webhook was installed at '.$redirect.'';

        if(!$update)
        {
            $this->status       = false;
            $this->information  = 'Webhook was not installed';
        }

        return response()->json([
            'status'    => $this->status,
            'message'   => $this->information
        ], 200);
    }

    public function gethook()
    {
        $this->information = Accesses::where('token', $this->token)->first()['redirect'];

        if(!$this->information)
        {
            $this->status = false;
        }

        return response()->json([
            'status'       => $this->status,
            'webhookUri'   => $this->information
        ], 200);
    }

    public function getdata(Request $request)
    {
        if(isset($request["messages"]))
        {
            return $this->calldata($request["messages"]);
        }

        return $this->status = false;;
    }

    private function calldata($params) {

        $this->information = Accesses::where('name', $this->header)->get();

        if(!count($this->information))
        {
            $this->status = false;
        }

        foreach($this->information as $info)
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $info->redirect);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

            curl_exec($ch);
            curl_close($ch);
        }
    }

    private function call($api_method = null, $params = null) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "{$this->whatsappUri}/{$api_method}?token={$this->whatsappToken}");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_exec($ch);
        curl_close ($ch);
    }
}
