<?php

namespace App\Http\Resources;

use App\Helpers\HelperReward;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class UserHasRewardCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'icon'          => HelperReward::getURLReward( $this->resource->reward->icon ),
            'givenByUserId' => $this->resource->given_by_user_id,
            'createdAt'     => $this->resource->created,
        ];
    }
}
