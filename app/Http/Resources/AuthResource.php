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
     * @var string
     */
    private $streamToken;

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
     * @return array
     */
    public function toArray($request)
    {
        return [
            'token'         => $this->token,
            'userId'        => $this->id,
            'firstName'     => $this->first_name,
            'lastName'      => $this->last_name,
            'email'         => $this->email,
            'role'          => count( $this->getRoleNames() ) !== 0 ? $this->getRoleNames()[0] : null,
            'about'         => $this->about,
            'avatar'        => HelperAvatar::getURLProfileImage( $this->profile_image ),
            'streamToken'   => $this->streamToken,
        ];
    }
}
