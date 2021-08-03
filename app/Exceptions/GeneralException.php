<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class GeneralException extends Exception
{
    /**
     * The status code to use for the response.
     *
     * @var integer
     */
    public $status = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * Create a new exception instance.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        Log::channel('errorlog')->info('---------------- START ERROR ---------------------------');
        Log::channel('errorlog')->info($message);
        Log::channel('errorlog')->info('----------------- END ERROR ----------------------------');

        parent::__construct($message);
    }

    public function render()
    {
        return response()->json([
            'success'   => false,
            'error'     => $this->getMessage(),
        ], $this->status);
    }
}
