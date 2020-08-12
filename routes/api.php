<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [
	'uses' => 'UserController@login'
]);


Route::group(['middleware' => 'auth:api'], function() {
	// test route
	Route::post('/test', [
		'uses' => 'UserController@test'
	]);
	// users
	Route::get('/user/get', [
		'uses' => 'UserController@get_user'
	]);
	// notifications
	Route::get('/notifications/read', [
		'uses' => 'UserController@read_notifications'
	]);
	// friends
	Route::post('/friendable', [
		'uses' => 'FriendController@friendable_users'
	]);
	Route::post('/friend/requests/send', [
		'uses' => 'FriendController@send_request'
	]);
	Route::post('/friend/requests/cancel', [
		'uses' => 'FriendController@cancel_request'
	]);
	Route::post('/friend/requests/accept', [
		'uses' => 'FriendController@accept_request'
	]);
	Route::post('/friend/requests/decline', [
		'uses' => 'FriendController@decline_request'
	]);
	Route::post('/friend/remove', [
		'uses' => 'FriendController@remove_friend'
	]);
	// private messaging
	Route::post('/contacts', [
		'uses' => 'PrivateMessagingController@contacts'
	]);
	Route::post('/messages', [
		'uses' => 'PrivateMessagingController@messages'
	]);
	Route::post('/message/send', [
		'uses' => 'PrivateMessagingController@send'
	]);
	Route::post('/messages/mark-as-read', [
		'uses' => 'PrivateMessagingController@mark_as_read'
	]);
});