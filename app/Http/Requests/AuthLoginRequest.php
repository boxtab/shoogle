<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

class AuthLoginRequest extends ApiRequest
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
            'email'     => 'required|email:rfc,dns|min:4|max:255|exists:users,email',
            'password'  => 'required|min:6|max:64',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
//    public function messages(): array
//    {
//        return [
//            'email.required'    => 'Please enter email.',
//            'email.exists'      => 'Email not registered.',
//            'email.email'       => 'Please enter valid email.',
//            'password.required' => 'Enter your password.',
//        ];
//    }
}
