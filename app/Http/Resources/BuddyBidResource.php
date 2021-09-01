<?php

namespace App\Http\Resources;

use App\Models\Shoogle;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class BuddyBidResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'buddy' => User::on()
                ->select(DB::raw('
                    id as id,
                    avatar as photo,
                    first_name as firstName,
                    last_name as lastName,
                    about as about
                '))
                ->where('id', $this->resource->buddy)
                ->first(),
            'shoogle' => Shoogle::on()
                ->select(DB::raw('
                    id as id,
                    title as title,
                    cover_image as coverImage
                '))
                ->where('id', $this->resource->shoogle_id)
                ->first(),
            'message' => $this->resource->message,
        ];
    }
}
