<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class DepartmentDetailResource extends JsonResource
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
            'name'              => $this->resource->name,
            'shooglersCount'    => User::on()->where('department_id', '=', $this->resource->id)->count(),
            'shooglers'         => new DepartmentUserResource(User::on()->where('department_id', $this->resource->id)->get()),
        ];
    }
}
