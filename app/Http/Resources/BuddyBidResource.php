<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
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
                    profile_image as profileImage,
                    first_name as firstName,
                    last_name as lastName,
                    about as about
                '))
                ->where('id', $this->resource->buddy)
                ->get()
                ->map(function ($item) {
                    $item['profileImage'] = HelperAvatar::getURLProfileImage($item['profileImage']);
                    return  $item;
                })->first(),
            'shoogle' => Shoogle::on()
                ->select(DB::raw('
                    id as id,
                    title as title,
                    cover_image as coverImage
                '))
                ->where('id', $this->resource->shoogle_id)
                ->first(),
            'created_at' => $this->resource->created,
            'message' => $this->resource->message,
        ];
    }
}
