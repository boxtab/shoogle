<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunityLevelStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'social'        => $this->resource['social'],
            'physical'      => $this->resource['physical'],
            'mental'        => $this->resource['mental'],
            'financial'     => $this->resource['economical'],
            'spiritual'     => $this->resource['spiritual'],
            'emotional'     => $this->resource['emotional'],
            'intellectual'  => $this->resource['intellectual'],
        ];
    }
}
