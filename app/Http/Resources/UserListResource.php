<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use App\Models\Shoogle;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($item) {
            return [
                'id'            => $item->id,
                'profileImage'  => HelperAvatar::getURLProfileImage( $item->profile_image ),
                'firstName'     => $item->first_name,
                'lastName'      => $item->last_name,
                'department'    => $item->department->name,
                'email'         => $item->email,
                'role'          => ( count($item->role) > 0 ) ? $item->role[0]->name : 'Warning: no role',
                'rating'        => $item->rank,
                'shoogles'      => Shoogle::where('owner_id', $item->id)->count(),
            ];
        });
    }
}
