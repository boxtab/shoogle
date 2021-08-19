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
        return $this->resource->map(function ($item) {
            return [
                'id'            => $item->id,
                'photo'         => $item->avatar,
                'firstName'     => $item->first_name,
                'lastName'      => $item->last_name,
                'department'    => $item->department->name,
                'email'         => $item->email,
                'role'          => $item->role[0]->name,
                'rating'        => $item->rank,
                'shoogles'      => Shoogle::where('owner_id', $item->id)->count(),
            ];
        });
    }
}
