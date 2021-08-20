<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

class ShooglesCreateRequest extends ApiRequest
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
            'wellbeingCategoryId'   => ['required', 'integer', 'exists:wellbeing_categories,id'],
            'active'                => ['required', 'boolean'],
            'title'                 => ['nullable', 'min:2', 'max:45'],
            'reminder'              => ['required', 'min:2', 'date_format:"Y-m-d H:i:s"'],
            'reminderInterval'      => ['nullable', 'string', 'max:1024'],
            'description'           => ['nullable', 'min:2', 'max:9086'],
            'coverImage'            => ['required', 'min:2', 'max:256'],
        ];
    }
}
