<?php

namespace App\Http\Resources;

use App\Models\Shoogle;
use App\Models\UserRanks;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

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
        return [
            'id'            => $this->resource->id,
            'photo'         => $this->resource->avatar,
            'firstName'     => $this->resource->first_name,
            'lastName'      => $this->resource->last_name,
            'department'    => null,
            'email'         => $this->resource->email,
            'rating'        => UserRanks::where('user_id', $this->resource->id)->count(),
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
