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

    /**
     * @param User $adminCompany
     */
    public function setAdminCompany(User $adminCompany)
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
            'companyName' => $this->resource->name,
            'firstName' => $this->adminCompany->first_name,
            'lastName' => $this->adminCompany->last_name,
            'email' => $this->adminCompany->email,
        ];
    }
}
