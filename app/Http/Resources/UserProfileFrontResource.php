<?php

namespace App\Http\Resources;

use App\Constants\RankConstant;
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
        return [
            'id'                    => $this->resource->id,
            'profileImage'          => HelperAvatar::getURLProfileImage( $this->resource->profile_image ),
            'firstName'             => $this->resource->first_name,
            'lastName'              => $this->resource->last_name,
            'rank'                  => HelperRank::getRankByNumber( $this->resource->rank ),
            'rewards'               => UserHasRewardCollection::collection( HelperReward::getReward($this->resource->id) ),
            'shooglesCount'         => $this->resource->profile_shoogles,
            'activeShooglesCount'   => $this->resource->profile_active,
            'inactiveShooglesCount' => $this->resource->profile_inactive,
            'shoogles'              => HelperShoogleProfile::getShooglesByUserID($this->resource->id),
        ];
    }
}
