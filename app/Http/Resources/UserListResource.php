<?php

namespace App\Http\Resources;

use App\Models\Shoogle;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
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
        // photo, lastName, firstName, email, rating = 0, shoogles, department

        return $this->resource->map(function ($item) {
            return [
                'id' => $item->id,
                'photo' => $item->avatar,
                'firstName' => $item->first_name,
                'lastName' => $item->last_name,
                'department' => null,
                'email' => $item->email,
                'rating' => 0,
                'shoogles' => Shoogle::where('owner_id', $item->id)->count(),
            ];
        });
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
