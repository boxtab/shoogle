<?php

namespace App\Http\Resources;

use App\Models\Shoogle;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // id, photo, firstName, lastName, department, email, rating, shoogles
        return [
            'id'            => $this->resource->id,
            'photo'         => $this->resource->avatar,
            'firstName'     => $this->resource->first_name,
            'lastName'      => $this->resource->last_name,
            'department'    => null,
            'email'         => $this->resource->email,
            'rating'        => 0,
            'shoogles'      => Shoogle::where('owner_id', $this->resource->id)->count(),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'success' => true,
        ];
    }
}
