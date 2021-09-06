<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class AuthLoginResource extends JsonResource
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
            'token'     => $this->resource,
            'firstName' => auth()->user()->first_name,
            'lastName'  => auth()->user()->last_name,
            'email'     => auth()->user()->email,
            'role'      => count( auth()->user()->getRoleNames() ) !== 0 ? auth()->user()->getRoleNames()[0] : null,
            'avatar'    => auth()->user()->avatar,
        ];
    }
}