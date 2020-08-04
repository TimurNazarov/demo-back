<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;
use Notification;
use App\User;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Friend as FriendResource;
use App\Notifications\Dummy as DummyNotification;

class UserController extends Controller
{
    public function test(Request $request) {
        mail('asd@dasd.sd', 'asdas', json_encode(auth()->user()));
        // $user = User::find(1);
        // $user2 = User::find(2);
        // $friend_request = new \App\FriendRequest;
        // $to = 1;
        // $friend_request->from = 2;
        // $friend_request->to = $to;
        // $friend_request->message = null;
        // $friend_request->save();

        // $to_user = User::findOrFail($to);

        // $to_user->notify(new DummyNotification($friend_request));
        //---
        // $user->friends()->syncWithoutDetaching($user2->id);
        // $user2->friends()->syncWithoutDetaching($user->id);
        //---
        
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
