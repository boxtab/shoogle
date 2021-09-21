<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use App\Helpers\HelperShoogleStatistic;
use App\Helpers\HelperShooglesViews;
use App\Models\UserHasShoogle;
use Illuminate\Http\Resources\Json\JsonResource;

class ShooglesViewsResource extends JsonResource
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
            'photo' => $this->resource->cover_image,
            'creator' => [
                'id' => $this->resource->owner->id,
                'avatar' => HelperAvatar::getURLProfileImage( $this->resource->owner->profile_image ),
            ],
            'createdAt' => $this->resource->created,
            'lastActivity' => $this->resource->updated,
            // lastActivityBy - this plug
//            'lastActivityBy' => [
//                'id' => $this->resource->owner->id,
//                'avatar' => $this->resource->owner->avatar,
//            ],
            'lastActivityBy' => HelperShooglesViews::getLastActivityBy($this->resource->id),
            // mostActiveShooglers - this plug
            'mostActiveShooglers' => UserHasShoogle::where('shoogle_id', $this->resource->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->user->id,
                        'avatar' => HelperAvatar::getURLProfileImage( $item->user->profile_image ),
                    ];
                })
                ->toArray(),
            // mostActiveShooglersCount - this plug
            'mostActiveShooglersCount' => UserHasShoogle::where('shoogle_id', $this->resource->id)->count(),
            'views' => $this->resource->views,
            'shooglersCount' => HelperShoogleStatistic::getShooglersCount($this->resource->id),
            'buddiesCount' => HelperShoogleStatistic::getBuddiesCount($this->resource->id),
            'solosCount' => HelperShoogleStatistic::getSolosCount($this->resource->id),
        ];
    }
}
