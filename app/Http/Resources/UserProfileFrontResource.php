<?php

namespace App\Http\Resources;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Helpers\HelperAvatar;
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
            'id'                => $this->resource->id,
            'photo'             => HelperAvatar::getURLProfileImage( $this->resource->profile_image ),
            'firstName'         => $this->resource->first_name,
            'lastName'          => $this->resource->last_name,
            'companyId'         => $this->resource->company_id,
            'company'           => $this->resource->company->name,
            'departmentId'      => $this->resource->department_id,
            'department'        => $this->resource->department->name,
            'email'             => $this->resource->email,
            'rating'            => $this->resource->rank,
            'rewards'           => UserHasRewardCollection::collection( HelperReward::getReward($this->resource->id) ),
            'shoogles'          => $this->resource->profile_shoogles,
            'active'            => $this->resource->profile_active,
            'inactive'          => $this->resource->profile_inactive,
            'followingShoogle'  => HelperShoogleProfile::getFollowing(null),
            'otherShoogles'     => null,
        ];
    }
}
