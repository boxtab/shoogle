<?php

namespace App\Http\Resources;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Helpers\HelperAvatar;
use App\Models\Shoogle;
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
            'departmentId'      => $this->resource->department_id,
            'email'             => $this->resource->email,
            'rating'            => $this->resource->rank,
        ];
    }
}
