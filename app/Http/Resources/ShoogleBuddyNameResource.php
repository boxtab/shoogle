<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoogleBuddyNameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ( is_null($this->resource) ) {
            return null;
        }

        return [
            'id'            => $this->resource->id,
            'firstName'     => $this->resource->first_name,
            'lastName'      => $this->resource->last_name,
            'profileImage'  => HelperAvatar::getURLProfileImage( $this->resource->profile_image ),
        ];
    }
}
