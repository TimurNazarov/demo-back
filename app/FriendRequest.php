<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    public function from_user() {
    	return $this->belongsTo('App\User', 'from');
    }
    public function to_user() {
    	return $this->belongsTo('App\User', 'to');
    }
}
