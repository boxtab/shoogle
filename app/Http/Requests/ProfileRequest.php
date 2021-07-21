<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'first_name' => 'required|min:2|max:255|unique:users|regex:/(^([a-zA-Z]+)(\d+)?$)/u',
            'last_name' => 'nullable|min:2|max:255',
            'about' => 'nullable|min:2|max:16384',
        ];
    }
}
