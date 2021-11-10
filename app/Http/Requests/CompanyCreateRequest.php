<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

class CompanyCreateRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'companyName'   => 'required|min:2|max:45',
//            'companyName'   => 'required|unique:companies,name|min:2|max:45',
            'firstName'     => 'required|min:2|max:255',
            'lastName'      => 'min:2|max:255',
            'email'         => 'required|email:rfc,dns|min:5|max:255',
//            'email'         => 'required|email:rfc,dns|unique:users,email|min:5|max:255',
            'password'      => 'required|min:6|max:64',
        ];
    }
}
