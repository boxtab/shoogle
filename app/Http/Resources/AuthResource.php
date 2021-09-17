<?php

namespace App\Http\Resources;

use App\Helpers\HelperAvatar;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * @var string
     */
    private $token;

    /**
     * Set token.
     *
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
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
            'token'     => $this->token,
            'firstName' => $this->first_name,
            'lastName'  => $this->last_name,
            'email'     => $this->email,
            'role'      => count( $this->getRoleNames() ) !== 0 ? $this->getRoleNames()[0] : null,
            'avatar'    => HelperAvatar::getURLProfileImage( $this->profile_image ),
        ];
    }
}
