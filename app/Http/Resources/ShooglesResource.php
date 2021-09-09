<?php

namespace App\Http\Resources;

use App\Models\Buddie;
use App\Models\BuddyRequest;
use App\Models\Shoogle;
use App\Models\UserHasShoogle;
use Illuminate\Http\Resources\Json\JsonResource;

class ShooglesResource extends JsonResource
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
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'photo' => $this->cover_image,
            'creator' => [
                'email' => $this->resource->owner->email,
                'firstName' => $this->resource->owner->first_name,
                'lastName' => $this->resource->owner->last_name,
                'profileImage' => $this->resource->owner->profile_image,
            ],
            'createdAt' => $this->resource->created,
            'lastActivity' => $this->resource->updated,
            'wellbeingCategory' => $this->resource->wellbeingCategory->name,

            'shooglersCount' => UserHasShoogle::where('shoogle_id', $this->resource->id)->count(),
            'shooglersList' => UserHasShoogle::where('shoogle_id', $this->resource->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'profile_image' => $item->user->profile_image,
                        'id' => $item->user->id,
                        'firstName' => $item->user->first_name,
                        'lastName' => $item->user->last_name,
                        'lastActivity' => $item->left_at_format,
                    ];
                })
                ->toArray(),

            'buddiesCount' => Buddie::where('shoogle_id', $this->resource->id)->count(),
            'buddiesList' => Buddie::where('shoogle_id', $this->resource->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'user1' => [
                            'firstName' => $item->user1->first_name,
                            'lastName' => $item->user1->last_name,
                            'profileImage' => $item->user1->profile_image,
                        ],
                        'user2' => [
                            'firstName' => $item->user2->first_name,
                            'lastName' => $item->user2->last_name,
                            'profileImage' => $item->user2->profile_image,
                        ],
                    ];
                })
                ->toArray(),
        ];
    }
}
