<?php

namespace App\Helpers;

use App\Http\Requests\ShooglesPaginationRequest;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

/**
 * Class HelperRequest
 * @package App\Helpers
 */
class HelperRequest
{
    /**
     * Create custom object request.
     *
     * @param array $args
     * @return Request
     */
    public static function make(array $args): Request
    {
//        return new ShooglesPaginationRequest([$args]);
        return new Request([$args]);
    }
}
