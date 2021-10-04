<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

class BuddyDisconnectRequest extends ApiRequest
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
            'buddyId'   => 'required|integer|exists:users,id',
            'shoogleId' => 'required|integer|exists:shoogles,id',
            'message'   => 'nullable|string:1024',
        ];
    }
}
