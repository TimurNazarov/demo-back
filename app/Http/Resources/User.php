<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Notification;
use App\Http\Resources\Friend;
use App\Http\Resources\FriendRequest;
use App\Helpers\Helpers;

class User extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'profile_picture_small' => $this->profile_picture_path != null ? Helpers::file_url($this->profile_picture_path, 'profile', 'small') : null,
            'profile_picture_average' => $this->profile_picture_path != null ? Helpers::file_url($this->profile_picture_path, 'profile', 'average') : null,
            'friends' => [
                'loaded' => Friend::collection($this->friends),
                'new' => []
            ],
            'friend_requests' => [
                'incoming' => [
                    'loaded' => FriendRequest::collection($this->incoming_friend_requests),
                    'new' => []
                ],
                'outgoing' => FriendRequest::collection($this->outgoing_friend_requests),
            ],
            'notifications' => Notification::collection($this->notifications()->orderBy('created_at', 'desc')->get()),
        ];
    }
}
