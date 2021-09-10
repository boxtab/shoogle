<?php

namespace App\Http\Resources;

use App\Models\Shoogle;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ShooglesSearchResultResource extends JsonResource
{
    /**
     * Community count.
     *
     * @return int
     */
    public function getCommunityCount(): int
    {
        $communityCount = 0;
        foreach ($this->resource as $shoogle) {
            $communityCount += $shoogle->shooglersCount;
        }
        return $communityCount;
    }

    /**
     * Buddies count.
     *
     * @return int
     */
    public function getBuddiesCount(): int
    {
        $buddiesCount = 0;
        foreach ($this->resource as $shoogle) {
            $buddiesCount += $shoogle->buddiesCount;
        }
        return $buddiesCount;
    }

    /**
     * Solos count.
     *
     * @return int
     */
    public function getSolosCount(): int
    {
        $solosCount = 0;
        foreach ($this->resource as $shoogle) {
            $solosCount += $shoogle->solosCount;
        }
        return $solosCount;
    }

    /**
     * Counting the total number of shoogles.
     *
     * @return int
     */
    public function getShoogleCount(): int
    {
        return (int)Shoogle::on()->count();
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'items' => ShooglesSearchItemsResource::collection($this->resource),
            'count' => $this->getShoogleCount(),
//            'count' => count($this->resource),
            'communityCount' => $this->getCommunityCount(),
            'buddiesCount' => $this->getBuddiesCount(),
            'solosCount' => $this->getSolosCount(),
        ];
    }
}
