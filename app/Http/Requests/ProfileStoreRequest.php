<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

class ProfileStoreRequest extends ApiRequest
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
            'firstName'     => 'nullable|min:2|max:255',
            'lastName'      => 'nullable|min:2|max:255',
            'about'         => 'nullable|min:2|max:16384',
//            'profileImage'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2048 Kb
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'about.min' => 'Please say a few words about yourself',
        ];
    }
}
