<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PrivateMessage extends JsonResource
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
            'from' => $this->from,
            'to' => $this->to,
            'content' => $this->content,
            'seen' => $this->seen,
            'date' => $this->created_at->format('d-m-Y'),
            'time' => $this->created_at->format('H:i')
            // 'date' => Carbon::createFromTimestamp($this->created_at)->format('m-d-Y'),
            // 'time' => Carbon::createFromTimestamp($this->created_at)->format('H:i'),
        ];
    }
}
