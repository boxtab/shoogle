<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

class WellbeingScoresStoreRequest extends ApiRequest
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
            'social'        => 'required|integer|min:1|max:10',
            'physical'      => 'required|integer|min:1|max:10',
            'mental'        => 'required|integer|min:1|max:10',
            'economical'    => 'required|integer|min:1|max:10',
            'spiritual'     => 'required|integer|min:1|max:10',
            'emotional'     => 'required|integer|min:1|max:10',
            'intellectual'  => 'required|integer|min:1|max:10',
        ];
    }
}
