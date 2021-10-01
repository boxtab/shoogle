<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use App\Helpers\HelperMember;
use App\Helpers\HelperShoogleStatistic;
use App\Helpers\HelperShooglesViews;
use App\Models\UserHasShoogle;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ShooglesViewsResource
 * @package App\Http\Resources
 */
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
            'lastActivityBy' => HelperShooglesViews::getLastActivityBy($this->resource->id),

            'mostActiveShooglers' => HelperShooglesViews::getMostActiveShooglers($this->resource->id),
            'mostActiveShooglersCount' => count( HelperShooglesViews::getMostActiveShooglers($this->resource->id) ),

            'views' => $this->resource->views,

            'shooglersCount' => HelperMember::getMemberCount($this->resource->id),
            'buddiesCount' => HelperShoogleStatistic::getBuddiesCount($this->resource->id),
            'solosCount' => HelperShoogleStatistic::getSolosCount($this->resource->id),
        ];
    }
}
