<?php

namespace App\Http\Resources;

use App\Models\Shoogle;
use App\Models\UserHasShoogle;
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
            'department'    => $this->resource->department_id,
            'email'         => $this->resource->email,
            'rating'        => UserRanks::where('user_id', $this->resource->id)->count(),
            'shoogles'      => Shoogle::where('owner_id', $this->resource->id)->count(),
            'shooglesList'  => Shoogle::where('owner_id', $this->resource->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'wellbeingCategory' => $item->wellbeingCategory->name,
                        'shooglersCount' => UserHasShoogle::where('shoogle_id', $item->id)->count(),
                    ];
                })
                ->toArray(),
        ];
    }
}
