<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

class AuthPasswordResetRequest extends ApiRequest
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
//        $this->request->set('password_confirmation', $this->request->get('passwordConfirmation'));
        return [
            'email' => 'required|email:rfc,dns|min:5|max:255',
            'token' => 'required',
//            'token' => ['required', 'exists:password_resets,token'],
            'password' => 'required|min:6|max:64|required_with:passwordConfirmation|same:passwordConfirmation',
            'passwordConfirmation' => 'required|min:6|max:64',
        ];
    }
}
