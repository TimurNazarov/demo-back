<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PrivateMessage as Message;
use App\Http\Resources\PrivateMessage as MessageResource;
use App\Http\Resources\Contact as ContactResource;
use App\Events\NewPrivateMessage;
use App\Helpers\Helpers;
use Carbon\Carbon;

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

    public function messages(Request $request) {
        $contact_id = $request->post('contact_id');
        // is friend
        $contact = auth()->user()->friends()->findOrFail($contact_id);
        $exclude = $request->post('exclude') ? $request->post('exclude') : [];
        $page = $request->post('page') ? $request->post('page') : 1;
        $message_history = Message::messageHistoryWith($contact_id)->whereNotIn('id', $exclude)->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        $paginated = Helpers::paginate($message_history, $page, config('constants.chat_messages_per_page'))->get();

        $result = $paginated->sortBy('created_at')
        ->values()
        // group by day
        ->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d-m-Y');
        });
        return $result;
    }
}
