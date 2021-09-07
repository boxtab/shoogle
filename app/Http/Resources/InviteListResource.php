<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InviteListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($item) {
            return [
                'id' => $item->id,
                'email' => $item->email,
                'is_used' => $item->is_used,
                'companies_id' => $item->companies_id,
                'department' => $item->department,
                'created_at' => date('Y-m-d H:i:s', strtotime($item->created_at)),
            ];
        });
    }
}
