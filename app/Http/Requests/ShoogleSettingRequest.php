<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShoogleSettingRequest extends FormRequest
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
            'reminder'          => 'nullable|date_format:Y-m-d H:i:s',
            'reminderInterval'  => 'nullable|string:1024',
            'buddy'             => 'nullable|boolean',
            'isReminder'        => 'nullable|boolean',
        ];
    }
}
