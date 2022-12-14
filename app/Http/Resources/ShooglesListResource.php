<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use App\Helpers\HelperShoogle;
use App\Models\Shoogle;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ShooglesListResource extends JsonResource
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
                'id'            => $item->shoogle_id,
                'title'         => $item->shoogle_title,
                'active'        => (boolean) $item->shoogle_active,
                'lastActivity'  => $item->shoogle_last_activity,
                'firstName'     => $item->users_first_name,
                'lastName'      => $item->users_last_name,
                'profileImage'  => HelperAvatar::getURLProfileImage( $item->users_profile_image ),
                'shooglers'     => HelperShoogle::getShooglersCount($item->shoogle_id),
                'department'    => $item->departments_name,
            ];
        });
    }
}
