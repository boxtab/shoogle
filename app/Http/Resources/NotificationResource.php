<?php

namespace App\Http\Resources;

use App\Helpers\HelperBuddyRequest;
use App\Helpers\HelperNotificationBuddy;
use App\Helpers\HelperReward;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class NotificationResource extends JsonResource
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
            'createdAt' => $this->resource->created,
            'reward' => RewardResource::make( HelperReward::getAwarded($this->resource->id) ),
            'buddy' => HelperNotificationBuddy::getBuddyAndShoogle($this->resource->id),
        ];
    }
}
