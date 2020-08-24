<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\Helpers;
use App\PrivateMessage;
use App\Http\Resources\PrivateMessage as MessageResource;

class Friend extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $last_private_message = PrivateMessage::messageHistoryWith($this->id)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile_picture_url' => $this->profile_picture_path != null ? Helpers::file_url($this->profile_picture_path, 'profile', 'small') : null,
            'last_private_message' => new MessageResource($last_private_message),
            'unread_count' => PrivateMessage::unread($this->id)->count(),
            // vuex preset
            'page' => 1,
            'typing' => false,
            'typing_timeout' => null,
            'initial_select' => true
        ];
    }
}
