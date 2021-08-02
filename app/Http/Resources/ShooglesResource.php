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
            ],
            'createdAt' => $this->resource->created,
            'lastActivity' => $this->resource->updated,
            'wellbeingCategory' => $this->resource->wellbeingCategory->name,
            'shooglersCount' => UserHasShoogle::where('shoogle_id', $this->resource->id)->count(),
            'buddiesCount' => BuddyRequest::where('shoogle_id', $this->resource->id)->count(),
            'shooglersList' => UserHasShoogle::where('shoogle_id', $this->resource->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'photo' => $item->user->avatar,
                        'firstName' => $item->user->first_name,
                        'lastName' => $item->user->last_name,
                        'lastActivity' => $item->left_at_format,
                    ];
                })
                ->toArray(),
            'buddiesList' => BuddyRequest::where('shoogle_id', $this->resource->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'photo' => $item->user->avatar,
                        'firstName' => $item->user1->first_name,
                        'lastName' => $item->user1->last_name,
                    ];
                })
                ->toArray(),
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
