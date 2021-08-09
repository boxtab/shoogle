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
            'social'        => null,
            'physical'      => null,
            'mental'        => null,
            'economical'    => null,
            'spiritual'     => null,
            'emotional'     => null,
        ];
    }
}
