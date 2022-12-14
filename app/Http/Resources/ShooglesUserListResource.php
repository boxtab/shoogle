<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

/**
 * Class ShooglesUserListResource
 * @package App\Http\Resources
 */
class ShooglesUserListResource extends JsonResource
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
            'id'                => $this->resource->id,
            'title'             => $this->resource->title,
            'coverImage'        => $this->resource->coverImage,
            'shooglersCount'    => $this->resource->shooglersCount,
            'buddiesCount'      => $this->resource->buddiesCount,
            'solosCount'        => $this->resource->solosCount,
            'buddyName'         => $this->resource->buddyName,
            'solo'              => $this->resource->solo,
            'owner'             => $this->resource->owner,
            'chatNameCommon'    => $this->resource->chatNameCommon,
            'chatNameWithBuddy' => $this->resource->chatNameWithBuddy,
        ];
    }
}
