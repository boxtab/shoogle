<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
//        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'     =>'required|email',
            'password'  =>'required',

//            'email'     =>'required|email|exists:users',
//            'password'  =>'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Please enter email.',
            'email.exists'      => 'Email not registered.',
            'email.email'       => 'Please enter valid email.',
            'password.required' => 'Enter your password.',
        ];
    }
}
