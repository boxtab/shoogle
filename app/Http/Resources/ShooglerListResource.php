<?php

namespace App\Http\Resources;

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
            'id' => $this->resource->id,
            'photo' => $this->resource->photo,
            'firstName' => $this->resource->firstName,
            'lastName' => $this->resource->lastName,
            'about' => $this->resource->about,
            'buddied' => ( $this->resource->buddied !== 0) ? true : false,
            'solo' => ( $this->resource->solo !== 0) ? true : false,
            'joinedAt' => $this->resource->joinedAt,
        ];
    }
}
