<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShooglesUserListResource extends JsonResource
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
            'id' => $this->resource->shoogle_id,
            'title' => null,
            'coverImage' => null,
            'shooglersCount' => null,
            'buddiesCount' => null,
            'solosCount' => null,
            'buddyName' => null,
            'solo' => $this->resource->solo,
        ];
    }
}
