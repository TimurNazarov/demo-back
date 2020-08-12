<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    // scopes

    public function scopeMessageHistoryWith($query, $contact_id) {
        return $query->where([['from', auth()->user()->id], ['to', $contact_id]])->orWhere([['from', $contact_id], ['to', auth()->user()->id]]);
    }

    public function scopeUnread($query, $contact_id) {
        return $query->where([['to', auth()->user()->id], ['from', $contact_id], ['seen', false]]);
    }
}
