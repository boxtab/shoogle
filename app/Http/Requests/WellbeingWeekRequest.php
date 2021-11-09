<?php

namespace App\Http\Requests;

use App\Support\ApiRequest\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class WellbeingWeekRequest extends ApiRequest
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
            'from'          => 'nullable|string|date_format:Y-m-d',
            'to'            => 'nullable|string|date_format:Y-m-d',
            'departmentId'  => 'nullable|integer|exists:departments,id',
        ];
    }
}
