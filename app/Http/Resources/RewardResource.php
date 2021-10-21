<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use App\Helpers\HelperReward;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
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
            'profileImage' => HelperAvatar::getURLProfileImage($this->resource->profileImage),
            'firstName' => $this->resource->firstName,
            'lastName' => $this->resource->lastName,
            'rewardName' => $this->resource->rewardName,
            'rewardIcon' => HelperReward::getURLReward( $this->resource->rewardIcon ),
        ];
    }
}
