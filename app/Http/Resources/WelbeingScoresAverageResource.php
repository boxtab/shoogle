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
            'social'        => isset($this->resource->social) ? $this->resource->social : null,
            'physical'      => isset($this->resource->physical) ? $this->resource->physical : null,
            'mental'        => isset($this->resource->mental) ? $this->resource->mental : null,
            'economical'    => isset($this->resource->economical) ? $this->resource->economical : null,
            'spiritual'     => isset($this->resource->spiritual) ? $this->resource->spiritual : null,
            'emotional'     => isset($this->resource->emotional) ? $this->resource->emotional : null,
        ];
    }
}
