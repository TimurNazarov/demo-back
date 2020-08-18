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
use App\Events\UserRegistered;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function get_user(Request $request) {
        return new UserResource(auth()->user());
    }

    public function register(Request $request) {
        $locale = $request->post('locale');
        $name = $request->post('name');
        $email = $request->post('email');
        $password = $request->post('password');
        $profile_picture = $request->profile_picture_file;
        $profile_picture_path = $profile_picture->store('profile', 'public');
        // only file name in database
        $profile_picture_path = str_replace('profile/', '', $profile_picture_path);

        $new_user = new User;
        $new_user->name = $name;
        $new_user->email = $email;
        $new_user->password = bcrypt($password);
        $new_user->email_confirmation_code = Str::random(64);
        $new_user->profile_picture_path = $profile_picture_path;
        $new_user->save();
        event(new UserRegistered(['user' => $new_user, 'locale' => $locale]));
    }

    public function login(Request $request) {
        $http = new HttpClient;

        $email = $request->post('email');
        $password = $request->post('password');

        $user = User::where('email', $email)->firstOrFail();
        if($user->email_verified_at == null) {
            // error(not verified email)
            return 321;
        }
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
        Auth::attempt(['email' => $email, 'password' => $password]);
        $user_response = new UserResource($user);

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

    public function verify_email($confirmation_code) {
        $user = User::where('email_confirmation_code', $confirmation_code)->firstOrFail();
        if($user->email_verified_at == null) {
            $user->email_verified_at = now();
            $user->save();
        }
    }
}
