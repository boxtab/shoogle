<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use App\Helpers\HelperRank;
use App\Helpers\HelperReward;
use App\Helpers\HelperShoogleProfile;
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
        $helperShoogleProfile = new HelperShoogleProfile($this->resource->id);
        $shooglesCount = $helperShoogleProfile->getShooglesCount();
        $activeShooglesCount = $helperShoogleProfile->getActiveShooglesCount();
        $inactiveShooglesCount = $helperShoogleProfile->getInactiveShooglesCount();

        return [
            'firstName'             => $this->resource->first_name,
            'lastName'              => $this->resource->last_name,
            'about'                 => $this->resource->about,
            'rank'                  => HelperRank::getRankNameByRankId( $this->resource->rank_id ),
            'profileImage'          => HelperAvatar::getURLProfileImage( $this->resource->profile_image ),
            'shooglesCount'         => $shooglesCount,
            'activeShooglesCount'   => $activeShooglesCount,
            'inactiveShooglesCount' => $inactiveShooglesCount,
            'rewards'               => UserHasRewardCollection::collection( HelperReward::getReward($this->resource->id) ),
        ];
    }
}
