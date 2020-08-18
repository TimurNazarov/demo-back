<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\Helpers;

class Notification extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
    $type = str_replace('App\\Notifications\\', '', $this->type);
    
        return [
          'id' => $this->id,
          'type' => $type,
          'read_at' => $this->read_at,
          'created_at' => $this->created_at->format('H:i d-m-Y'),
          'data' => $this->data,
        ];
    }
}
