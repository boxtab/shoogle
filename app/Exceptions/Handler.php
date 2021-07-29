<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
//        if ( $exception ) {
//            // log the error
//            return response()->json([
//                'success' => false,
//                'data' => [
//                    'status' => $exception->getStatusCode(),
//                    'error' => $exception->getMessage(),
//                ],
//            ]);
//        }

        return parent::render($request, $exception);
    }

    public function register()
    {
        // reportable
        $this->renderable(function (Throwable $e) {
            return response([
                'success' => false,
                'reportableError' => $e->getMessage()],
                $e->getCode() ?: 400
            );
//            return response(['error123' => $e->getMessage()], $e->getCode() ?: 400);
        });
    }
}
