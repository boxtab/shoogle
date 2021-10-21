<?php

namespace App\Http\Resources;

use App\Helpers\HelperBuddyRequest;
use App\Helpers\HelperReward;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationListResource extends JsonResource
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
            'id' => $this->resource->id,
            'typeNotificationText' => $this->resource->typeNotificationText,
            'createdAt' => $this->resource->createdAt,
            'reward' => RewardResource::make( HelperReward::getAwarded($this->resource->id) ),
            'buddy' => HelperBuddyRequest::getNotification($this->resource->id),
        ];
    }
}
