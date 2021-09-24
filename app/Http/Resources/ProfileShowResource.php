<?php

namespace App\Http\Resources;

use App\Constants\RankConstant;
use App\Helpers\HelperAvatar;
use App\Helpers\HelperRank;
use App\Helpers\HelperReward;
use App\Models\UserHasReward;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileShowResource extends JsonResource
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
            'firstName'             => $this->resource->first_name,
            'lastName'              => $this->resource->last_name,
            'about'                 => $this->resource->about,
            'rank'                  => HelperRank::getRankByNumber( $this->resource->rank ),
            'profileImage'          => HelperAvatar::getURLProfileImage( $this->resource->profile_image ),
            'shooglesCount'         => $this->resource->profile_shoogles,
            'activeShooglesCount'   => $this->resource->profile_active,
            'inactiveShooglesCount' => $this->resource->profile_inactive,
            'rewards'               => UserHasRewardCollection::collection( HelperReward::getReward($this->resource->id) ),
        ];
    }
}
