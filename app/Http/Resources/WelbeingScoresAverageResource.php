<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WelbeingScoresAverageResource extends JsonResource
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
            'social'        => isset($this->resource->social) ? round($this->resource->social, 2) : null,
            'physical'      => isset($this->resource->physical) ? round($this->resource->physical, 2) : null,
            'mental'        => isset($this->resource->mental) ? round($this->resource->mental, 2) : null,
            'economical'    => isset($this->resource->economical) ? round($this->resource->economical, 2) : null,
            'spiritual'     => isset($this->resource->spiritual) ? round($this->resource->spiritual, 2) : null,
            'emotional'     => isset($this->resource->emotional) ? round($this->resource->emotional, 2) : null,
        ];
    }
}
