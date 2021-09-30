<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\ApiRequest\ApiRequest;

/**
 * Class ShooglesEntryRequest
 * @package App\Http\Requests
 */
class ShooglesEntryRequest extends ApiRequest
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
            'shoogleId'         => 'required|exists:shoogles,id',
            'reminder'          => 'required|date_format:Y-m-d H:i:s',
            'reminderInterval'  => 'required|string|max:1024',
            'isReminder'        => 'required|boolean',
            'buddy'             => 'required|boolean',
            'note'              => 'nullable|string',
        ];
    }
}
