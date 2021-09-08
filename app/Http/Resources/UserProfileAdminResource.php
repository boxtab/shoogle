<?php

namespace App\Http\Resources;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
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
            'profile_image'     => $this->resource->profile_image,
            'firstName'         => $this->resource->first_name,
            'lastName'          => $this->resource->last_name,
            'departmentId'      => $this->resource->department_id,
            'email'             => $this->resource->email,
            'rating'            => $this->resource->rank,
            'shoogles'          => Shoogle::where('owner_id', $this->resource->id)->count(),
            'isCompanyAdmin'    => (Helper::getRole($this->resource->id) == RoleConstant::COMPANY_ADMIN) ? true : false,
            'shooglesList'      => Shoogle::where('owner_id', $this->resource->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'wellbeingCategory' => $item->wellbeingCategory->name,
                        'shooglersCount' => UserHasShoogle::where('shoogle_id', $item->id)->count(),
                    ];
                })
                ->toArray(),
        ];
    }
}
