<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Friend;

class FriendRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // 'from' => $this->when($this->from != auth()->user()->id, new Friend($this->from_user)),
            // 'to' => $this->when($this->from == auth()->user()->id, new Friend($this->to_user))
            'from' => new Friend($this->from_user),
            'to' => new Friend($this->to_user)
        ];
    }
}
