<?php

namespace App\Http\Resources;

use App\Helpers\HelperBuddyRequest;
use App\Helpers\HelperNotific;
use App\Helpers\HelperNotificationBuddy;
use App\Helpers\HelperRank;
use App\Helpers\HelperReward;
use App\Helpers\HelperWellbeing;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            'id'                    => $this->resource->id,
            'typeNotificationText'  => $this->resource->typeNotificationLabel,
            'createdAt'             => $this->resource->createdAt,
            'reward'                => RewardResource::make( HelperReward::getAwarded($this->resource->id) ),
            'buddy'                 => HelperNotificationBuddy::getBuddyAndShoogle($this->resource->id),
            'reminder'              => HelperNotific::getRemainderScheduler( $this->resource->id, Auth::id() ),
            'wellbeing'             => HelperWellbeing::getNotification($this->resource->id, Auth::id()),
            'rank'                  => HelperRank::getNotification( $this->resource->id ),
        ];
    }
}
