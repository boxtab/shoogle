<?php

namespace App\Support\ApiRequest;

use App\Support\ApiRequest\Traits\FailedValidation;
use App\Support\ApiRequest\Traits\ValidationData;
use App\Support\ApiRequest\Traits\FailedAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    use FailedValidation;
    use FailedAuthorization;
    use ValidationData;
}
