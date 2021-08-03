<?php


namespace App\Support\ApiRequest\Traits;

use App\Exceptions\RequestAuthorizeException;
use Exception;

/**
 * Trait FailedAuthorization
 * @package App\Http\Requests\Admin\Traits
 */
trait FailedAuthorization
{
    /**
     * Handle a failed validation attempt.
     *
     * @return void
     *
     * @throws Exception
     */
    protected function failedAuthorization() : void
    {
        throw new RequestAuthorizeException('You are not allowed for this action!');
    }
}
