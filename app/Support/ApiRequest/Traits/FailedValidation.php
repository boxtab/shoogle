<?php


namespace App\Support\ApiRequest\Traits;

use App\Support\ApiResponse\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * Trait FailedValidation
 * @package App\Http\Requests\Admin\Traits
 */
trait FailedValidation
{
    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator) : void
    {
        $response = ApiResponse::returnError($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new ValidationException($validator, $response);
    }
}
