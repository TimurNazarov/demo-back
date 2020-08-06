<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;
use Notification;
use Carbon\Carbon;
use App\Helpers\Helpers;
use App\User;
use App\PrivateMessage;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Friend as FriendResource;
use App\Notifications\Dummy as DummyNotification;

class UserController extends Controller
{
    public function test(Request $request) {
        // $msg = new PrivateMessage;
        // $msg->from = 2;
        // $msg->to = 1;
        // $msg->content = 'aDY*HW!@FH89auebhfdausbgasdughasd9ghdsghsdogdndos';
        // $msg->save();
        $contact_id = $request->post('contact_id');
        $exclude = $request->post('exclude');
        $contact_id = 2;
        // return auth()->user()->friends;
        $contact = auth()->user()->friends()->findOrFail($contact_id);
        $message_history = PrivateMessage::messageHistoryWith($contact_id)->orderBy('created_at', 'desc');
        $paginated = Helpers::paginate($message_history, 1, 100)
        ->get()
        ->sortBy('created_at')
        ->values()
        ->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('d-m-Y');
        });
        return $paginated;
        
        return 123;
    }

    public function get_user(Request $request) {
        return new UserResource(auth()->user());
    }

    public function login(Request $request) {
        $http = new HttpClient;

        $email = $request->post('email');
        $password = $request->post('password');
        // auth
        $parameters = [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('info.passport.client_id'),
                'client_secret' => config('info.passport.client_secret'),
                'username' => $email,
                'password' => $password
            ]
        ];
        $auth_response = $http->post(url('/') . '/oauth/token', $parameters);
        // to remove old tokens check passport events

        // --- get user
        
        $auth_response = json_decode($auth_response->getBody(), true);
        $bearer = 'Bearer ' . $auth_response['access_token'];

        $parameters = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $bearer
            ]
        ];

        $user = $http->get(url('/') . '/api/user/get', $parameters);

        $user_response = json_decode($user->getBody(), true);

        $response = [
            'auth' => $auth_response,
            'user' => $user_response
        ];
        return $response;
    }

    public function logout(Request $request) {
        return auth()->user()->tokens()->delete();
    }

    public function read_notifications() {
        return auth()->user()->unreadNotifications()->update(['read_at' => now()]);
    }
}
