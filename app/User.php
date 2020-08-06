<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\PrivateMessage;
use Illuminate\Notifications\DatabaseNotification;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /// --- relationships

    public function friends() {
        return $this->belongsToMany('App\User', 'user_friends_pivot', 'user_id', 'friend_id')->withTimestamps();
    }

    // public function friend_requests() {
    //     return $this->hasMany('App\FriendRequest', 'to')->orWhere('')->where('complete', false);
    // }

    public function incoming_friend_requests() {
        return $this->hasMany('App\FriendRequest', 'to')->where('complete', false);
    }

    public function outgoing_friend_requests() {
        return $this->hasMany('App\FriendRequest', 'from')->where('complete', false);
    }

    public function messages() {
        return PrivateMessage::where('from', $this->id)->orWhere('to', $this->id);
    }

    // ----

    public function receivesBroadcastNotificationsOn() {
        return 'user.'.$this->id;
    }
    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable'); // orderBy('created_at', 'desc') default overrite
    }
}
