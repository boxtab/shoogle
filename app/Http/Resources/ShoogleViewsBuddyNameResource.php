<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoogleViewsBuddyNameResource extends JsonResource
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
            'profileImage'  => HelperAvatar::getURLProfileImage( $this->resource->profile_image ),
        ];
    }
}
