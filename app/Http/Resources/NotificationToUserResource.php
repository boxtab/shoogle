<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationToUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->resource->id,
            'user_id'           => $this->resource->user_id,
            'full_name'         => $this->resource->first_name . ' ' . $this->resource->last_name,
            'type_notification' => $this->resource->type,
            'notification'      => $this->resource->notification,
            'created_at'        => $this->resource->created,
        ];
    }
}
