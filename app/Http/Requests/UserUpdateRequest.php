<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

class UserUpdateRequest extends ApiRequest
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
            'firstName'         => 'nullable|min:2|max:255',
            'lastName'          => 'nullable|min:2|max:255',
            'email'             => 'nullable|email:rfc,dns|min:4|max:255',
            'departmentId'      => 'nullable|integer',
            'isCompanyAdmin'    => 'nullable|boolean',
        ];
    }
}
