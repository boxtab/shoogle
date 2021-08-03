<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class DepartmentCreateRequest extends FormRequest
{
//    protected $errorBag;

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
            'companyId' => 'required|integer',
            'departmentName' => 'required|min:2|max:255',
        ];
    }

//    protected function failedValidation( \Illuminate\Contracts\Validation\Validator $validator )
//    {
//        return response()->json([
//            'success' => false,
//            'errors' => 'asd',
//        ], 422);
//    }

//    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
//    {
//        $response = new Response(['error' => $validator->errors()->first()], 422);
//        throw new ValidationException($validator, $response);
//    }
}
