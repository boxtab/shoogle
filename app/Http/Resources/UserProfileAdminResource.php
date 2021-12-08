<?php

namespace App\Http\Resources;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Helpers\HelperAvatar;
use App\Helpers\HelperShoogle;
use App\Helpers\HelperShoogleList;
use App\Helpers\HelperWellbeing;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserProfileAdminResource extends JsonResource
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
            'profileImage'      => HelperAvatar::getURLProfileImage( $this->resource->profile_image ),
            'firstName'         => $this->resource->first_name,
            'lastName'          => $this->resource->last_name,
            'departmentId'      => $this->resource->department_id,
            'email'             => $this->resource->email,
            'rating'            => $this->resource->rank_id,
            'shoogles'          => HelperShoogle::getShoogleCount($this->resource->id, true),
            'isCompanyAdmin'    => (Helper::getRole($this->resource->id) == RoleConstant::COMPANY_ADMIN) ? true : false,
            'shooglesList'      => HelperShoogleList::getList($this->resource->id, false),
            'wellbeingLastTime' => HelperWellbeing::getLastTime( $this->resource->id ),
        ];
    }
}
