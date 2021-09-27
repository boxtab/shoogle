<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class AuthLoginResource extends JsonResource
{
    /**
     * @var string
     */
    private $streamToken;

    /**
     * Set stream token.
     *
     * @param string|null $streamToken
     */
    public function setStreamToken(?string $streamToken)
    {
        $this->streamToken = $streamToken;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'token'         => $this->resource,
            'userId'        => auth()->user()->id,
            'firstName'     => auth()->user()->first_name,
            'lastName'      => auth()->user()->last_name,
            'email'         => auth()->user()->email,
            'role'          => count( auth()->user()->getRoleNames() ) !== 0 ? auth()->user()->getRoleNames()[0] : null,
            'avatar'        => HelperAvatar::getURLProfileImage( auth()->user()->profile_image ),
            'streamToken'   => $this->streamToken,
        ];
    }
}
