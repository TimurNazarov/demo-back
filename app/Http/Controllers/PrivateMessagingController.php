<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PrivateMessage as Message;
use App\Http\Resources\PrivateMessage as MessageResource;
use App\Http\Resources\Contact as ContactResource;
use App\Events\NewPrivateMessage;

class PrivateMessagingController extends Controller
{
    public function send(Request $request) {
    	$from = auth()->user()->id;
    	$to = $request->post('to');
    	$content = $request->post('content');
    	$message = new Message;
    	$message->from = $from;
    	$message->to = $to;
    	$message->content = $content;
    	$message->seen = false;
    	$message->save();

    	$message = new MessageResource($message);

    	broadcast(new NewPrivateMessage($message));
    	return $message;
    }

    public function contacts() {
        $friends = auth()->user()->friends;
        return ContactResource::collection($friends);
    }
}
