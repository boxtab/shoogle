<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentListResource extends JsonResource
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
                'id'        => $item->department_id,
                'name'      => $item->department_name,
                'shooglers' => $item->shooglers,
            ];
        });
    }
}
