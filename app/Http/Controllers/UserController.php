<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
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
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function get_user(Request $request) {
        return new UserResource(auth()->user());
    }

    public function register(Request $request) {
        // validate
        $validator = Validator::make($request->all(), [
            'locale' => 'required|min:2|max:2',
            'email' => ['required', 'unique:users', 'regex:/^[\w._]+@\w+\.\w+$/'],
            'name' => ['required', 'regex:/^[a-zA-Zа-яА-Я\- ]{3,36}$/'],
            'password' => ['required', 'regex:/^[\w!@#$%^&*]{8,42}$/'],
            'profile_picture_file' => 'sometimes|image|mimes:jpeg,png|max:10000',
        ]);
        if ($validator->fails() && in_array('validation.unique', $validator->errors()->get('email'))) {
            // email exists
            return ['demo_error' => 903];
        } else if($validator->fails()) {
            // something went wrong
            return ['demo_error' => 900]; 
        }
        // set input values
        $locale = $request->post('locale');
        $name = $request->post('name');
        $email = $request->post('email');
        $password = $request->post('password');
        $profile_picture = $request->profile_picture_file;

        if($profile_picture) {
            $profile_picture_path = Helpers::handle_image_resize($profile_picture, 'profile', ['small', 'average']);
        } else {
            $profile_picture_path = null;
        }

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
        // disabled for testing
        // // validate
        // $validator = Validator::make($request->all(), [
        //     'email' => ['required', 'regex:/^[\w._]+@\w+\.\w+$/'],
        //     'password' => ['required', 'regex:/^[\w!@#$%^&*]{8,42}$/'],
        // ]);
        // if ($validator->fails()) {
        //     // something went wrong
        //     return ['demo_error' => 900]; 
        // }

        // set input values
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
        $http = new HttpClient;
        try {
            $auth_response = $http->post(url('/') . '/oauth/token', $parameters);
        } catch (ClientException $e) {
            // user was not found
            return ['demo_error' => 901];
        }
        $user = User::where('email', $email)->first();
        if($user->email_verified_at == null) {
            // unverified email
            return ['demo_error' => 902];
        }
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
            return redirect(config('app.frontend_url') . '/banner-message/verify/success');
        }
    }
}
