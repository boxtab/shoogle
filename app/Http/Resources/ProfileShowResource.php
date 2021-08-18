<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileShowResource extends JsonResource
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
            'firstName'             => $this->resource->first_name,
            'lastName'              => $this->resource->last_name,
            'about'                 => $this->resource->about,
            'rank'                  => $this->resource->rank,
            'profileImage'          => $this->resource->profile_image,
            'activeShooglesCount'   => $this->resource->activeShooglesCount,
            'inactiveShooglesCount' => $this->resource->inactiveShooglesCount,
            'rewards' => [
//                  { icon, givenByUserId, createdAt }
//                  { icon, givenByUserId, createdAt }
//                  { icon, givenByUserId, createdAt }
            ],
        ];
    }
}
