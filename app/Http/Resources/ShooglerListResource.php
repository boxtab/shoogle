<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ShooglerListResource extends JsonResource
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
            'id'            => $this->resource->id,
            'profile_image' => HelperAvatar::getURLProfileImage( $this->resource->profile_image ),
            'firstName'     => $this->resource->firstName,
            'lastName'      => $this->resource->lastName,
            'about'         => $this->resource->about,
            'baddies'       => $this->resource->baddies,
            'solo'          => $this->resource->solo,
            'joinedAt'      => $this->resource->joinedAt,
        ];
    }
}
