<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PrivateMessage as Message;
use App\Http\Resources\PrivateMessage as MessageResource;
use App\Events\NewPrivateMessage;
use App\Events\MessageRead;
use App\Helpers\Helpers;
use Carbon\Carbon;
use App\User;

class PrivateMessagingController extends Controller
{
    public function send(Request $request) {
        $request->validate([
            'to' => 'required|numeric',
            'content' => 'required|min:1|max:5000'
        ]);
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

    public function contacts(Request $request) {
        $request->validate([
            'exclude' => 'array',
            'exclude.*' => 'integer'
        ]);
        $exclude = $request->post('exclude') ? $request->post('exclude') : [];
        $friends = auth()->user()->friends()->whereNotIn('users.id', $exclude)->get();
        return ContactResource::collection($friends);
    }

    public function messages(Request $request) {
        $request->validate([
            'initial' => 'boolean',
            'contact_id' => 'required|integer'
        ]);
        $initial = $request->post('initial');
        $contact_id = $request->post('contact_id');
        // is friend
        $contact = auth()->user()->friends()->findOrFail($contact_id);
        $exclude = $request->post('exclude') ? $request->post('exclude') : [];
        $page = $request->post('page') ? $request->post('page') : 1;
        $message_history = Message::messageHistoryWith($contact_id)->whereNotIn('id', $exclude)->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        $paginated = Helpers::paginate($message_history, $page, config('constants.chat_messages_per_page'))->get();

        $result = $paginated->sortBy('created_at')
        ->values();
        $result = MessageResource::collection($result);
        $result = $result->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d-m-Y');
        });
        if($initial) {
            Message::unread($contact_id)->update(['seen' => true]);
            $info = [
                'from' => auth()->user()->id,
                'to' => $contact_id,
                'new' => false
            ];
            broadcast(new MessageRead($info));
        }
        return $result;
    }

    public function mark_as_read(Request $request) {
        $request->validate([
            'new' => 'boolean',
            'contact_id' => 'required|integer'
        ]);
        $contact_id = $request->post('contact_id');
        $new = $request->post('new') ? true : false;
        Message::unread($contact_id)->update(['seen' => true]);
        $info = [
            'from' => auth()->user()->id,
            'to' => $contact_id,
            'new' => $new
        ];
        broadcast(new MessageRead($info));
        return $info;
    }
}
