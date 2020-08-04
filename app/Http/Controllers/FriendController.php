<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FriendRequest;
use App\User;
use App\Notifications\FriendRequest as FriendRequestNotification;
use App\Http\Resources\Friend as FriendResource;
use App\Http\Resources\FriendRequest as FriendRequestResource;
// events
use App\Events\NewFriendRequest;
use App\Events\FriendRequestCancelled;
use App\Events\FriendRequestAccepted;
use App\Events\FriendRequestDeclined;
use App\Events\FriendRemoved;

class FriendController extends Controller
{

    public function friendable_users() {
        $user = auth()->user();

        $users = User::whereDoesntHave('friends', function($query) use ($user) {
            $query->where('friend_id', $user->id);
        })
        ->whereDoesntHave('incoming_friend_requests', function($query) use ($user) {
            $query->where('to', '!=', $user->id);
        })
        ->whereDoesntHave('outgoing_friend_requests', function($query) use ($user) {
            $query->where('from', '!=', $user->id);
        })
        ->where('id', '!=', $user->id)
        ->get();
        return FriendResource::collection($users);
    }

    public function send_request(Request $request) {
    	$user = auth()->user();
    	$to = $request->post('to');
    	$message = $request->post('message');
    	//check if exists
    	$exists = FriendRequest::where([
    		['from', $user->id],
    		['to', $to],
    		['complete', false]
    	])->exists();
    	if($exists) {
    		return 1;
    	} else {
	    	$friend_request = new FriendRequest;
	    	$friend_request->from = $user->id;
	    	$friend_request->to = $to;
	    	$friend_request->message = $message ? $message : null;
	    	$friend_request->save();

	    	$to_user = User::findOrFail($to);

            broadcast(new NewFriendRequest(new FriendRequestResource($friend_request)));
        	$to_user->notify(new FriendRequestNotification($friend_request));

	    	return new FriendRequestResource($friend_request);
    	}
    }

    public function cancel_request(Request $request) {
        $user = auth()->user();
        $to = $request->post('to');
        $friend_request = $user->outgoing_friend_requests()->where('to', $to)->first();
        $request_id = $friend_request->id;
        FriendRequest::destroy($request_id);

        broadcast(new FriendRequestCancelled(new FriendRequestResource($friend_request)));
        return $request_id;
    }

    public function accept_request(Request $request) {
        $user = auth()->user();
        $from = $request->post('from');
        $from_user = User::findOrFail($from);
        $friend_request = $user->incoming_friend_requests()->where('from', $from)->first();
        // set friendship
        $user->friends()->syncWithoutDetaching($from_user->id);
        $from_user->friends()->syncWithoutDetaching($user->id);

        $friend_request->complete = true;
        $friend_request->save();

        broadcast(new FriendRequestAccepted(new FriendRequestResource($friend_request)));
        return new FriendResource($from_user);
    }

    public function decline_request(Request $request) {
        $user = auth()->user();
        $from = $request->post('from');
        $from_user = User::findOrFail($from);
        $friend_request = $user->incoming_friend_requests()->where('from', $from)->first();
        $friend_request->complete = true;
        $friend_request->save();
        broadcast(new FriendRequestDeclined(new FriendRequestResource($friend_request)));
    }

    public function remove_friend(Request $request) {
        $user = auth()->user();
        $friend_id = $request->post('friend_id');
        $friend = $user->friends()->findOrFail($friend_id);

        // $user->friends()->detach($friend->id);
        // $friend->friends()->detach($user->id);
        $info = [
            'removed_friend_id' => $friend_id,
            'removing_friend' => new FriendResource($user)
        ];

        broadcast(new FriendRemoved($info));

        return 1;
    }
}
