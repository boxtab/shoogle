<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $token;

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
            'lastName'  => $this->first_name,
            'email'     => $this->email,
            'role'      => count( $this->getRoleNames() ) !== 0 ? $this->getRoleNames()[0] : null,
            'avatar'    => $this->avatar,
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
//    public function withResponse($request, $response)
//    {
//        $response->header('Authorization', 'Bearer ' . $request);
//    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'success' => true,
        ];
    }
}
