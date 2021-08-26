<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InviteShowResource extends JsonResource
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
            'email' => $this->resource->email,
            'is_used' => $this->resource->is_used,
            'created_by' => $this->resource->created_by,
            'companies_id' => $this->resource->companies_id,
            'department_id' => $this->resource->department_id,
        ];
    }
}
