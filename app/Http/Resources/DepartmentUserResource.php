<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class DepartmentUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($item) {
            return [
                'id' => $item->id,
                'firstName' => $item->first_name,
                'lastName' => $item->last_name,
                'email' => $item->email,
                'profileImage' => $item->profile_image,
            ];
        });
    }
}
