<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

class AuthSignupRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'         => 'required|email:rfc,dns|unique:users,email|min:5|max:255',
            'password'      => 'required|min:6|max:64',
            'firstName'     => 'nullable|min:1|max:255',
            'lastName'      => 'nullable|min:1|max:255',
            'profileImage'  => 'nullable',
            'about'         => 'nullable|min:1|max:16384',
        ];
    }
}
