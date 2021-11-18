<?php

namespace App\Http\Resources;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Helpers\HelperAvatar;
use App\Helpers\HelperRank;
use App\Helpers\HelperReward;
use App\Helpers\HelperShoogleProfile;
use App\Models\Shoogle;
use App\Models\UserHasReward;
use App\Models\UserHasShoogle;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserProfileFrontResource extends JsonResource
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
        $shoogles = $helperShoogleProfile->getShoogles();
        $shooglesCount = $helperShoogleProfile->getShooglesCount();
        $activeShooglesCount = $helperShoogleProfile->getActiveShooglesCount();
        $inactiveShooglesCount = $helperShoogleProfile->getInactiveShooglesCount();

        return [
            'id'                    => $this->resource->id,
            'profileImage'          => HelperAvatar::getURLProfileImage( $this->resource->profile_image ),
            'firstName'             => $this->resource->first_name,
            'lastName'              => $this->resource->last_name,
            'rank'                  => HelperRank::getRankByNumber( $this->resource->rank ),
            'rewards'               => UserHasRewardCollection::collection( HelperReward::getReward($this->resource->id) ),
            'shooglesCount'         => $shooglesCount,
            'activeShooglesCount'   => $activeShooglesCount,
            'inactiveShooglesCount' => $inactiveShooglesCount,
            'shoogles'              => $shoogles,
        ];
    }
}
