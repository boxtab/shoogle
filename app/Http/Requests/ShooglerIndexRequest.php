<?php

namespace App\Http\Requests;

use App\Enums\ShooglerFilterEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Support\ApiRequest\ApiRequest;

class ShooglerIndexRequest extends ApiRequest
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
     * @throws \ReflectionException
     */
    public function rules()
    {
        return [
            'search'     => ['nullable', 'string'],
//            'filter'    => ['nullable', Rule::in(ShooglerFilterEnum::getArrayIndex())],
            'filter'    => ['nullable', Rule::in(['recentlyJoined', 'available', 'solo', 'buddied']),],
        ];
    }
}
