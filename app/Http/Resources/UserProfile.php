<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\Helpers;
use App\Http\Resources\Friend;


class UserProfile extends JsonResource
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
            'profile_picture_url' => $this->profile_picture_path != null ? Helpers::file_url($this->profile_picture_path, 'profile', 'small') : null,
            'profile_picture_average' => $this->profile_picture_path != null ? Helpers::file_url($this->profile_picture_path, 'profile', 'average') : null,
            'friends' => Friend::collection($this->friends),
            'posts' => Helpers::get_user_posts($this->id)
        ];
    }
}
