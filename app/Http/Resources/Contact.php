<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\Helpers;
use App\PrivateMessage;
use App\Http\Resources\PrivateMessage as MessageResource;

class Contact extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $last_private_message = PrivateMessage::where([['from', auth()->user()->id], ['to', $this->id]])
                                        ->orWhere([['to', auth()->user()->id], ['from', $this->id]])->orderBy('created_at', 'desc')->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile_picture_url' => Helpers::file_url($this->profile_picture_path),
            'last_private_message' => new MessageResource($last_private_message)
        ];
    }
}
