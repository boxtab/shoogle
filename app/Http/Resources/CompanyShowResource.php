<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyShowResource extends JsonResource
{
    /**
     * @var User
     */
    private $adminCompany;

    public function setAdminCompany($adminCompany)
    {
        $this->adminCompany = $adminCompany;
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
            'companyName'   => $this->resource->name,
            'firstName'     => ( ! is_null($this->adminCompany) ) ? $this->adminCompany->first_name : null,
            'lastName'      => ( ! is_null($this->adminCompany) ) ? $this->adminCompany->last_name : null,
            'email'         => ( ! is_null($this->adminCompany) ) ? $this->adminCompany->email : null,
        ];
    }
}
