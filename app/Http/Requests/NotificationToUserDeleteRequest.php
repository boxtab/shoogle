<?php

namespace App\Http\Requests;

use App\Support\ApiRequest\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class NotificationToUserDeleteRequest extends ApiRequest
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
            'notificationIDs' => 'required|array|min:1',
            "notificationIDs.*"  => [
                'required',     // null is not allowed
                'integer',      // input must be of type string
                'min:1',        // each string must have min 3 chars
                'distinct',     // members of the array must be unique
            ]
        ];
    }
}
