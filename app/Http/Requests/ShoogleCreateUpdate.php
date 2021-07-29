<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShoogleCreateUpdate extends FormRequest
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
            'wellbeingCategoryId' => ['required', 'integer', 'exists:wellbeing_categories,id'],
            'active' => ['required', 'boolean'],
            'title' => ['nullable', 'min:2', 'max:45'],
            'description' => ['nullable', 'min:2', 'max:9086'],
            'coverImage' => ['required', 'min:2', 'max:256'],
            'acceptBuddies' => ['required', 'boolean'],
        ];
    }
}
