<?php


namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Class AuthorizeException
 */
class RequestAuthorizeException extends Exception
{
    /**
     * The status code to use for the response.
     *
     * @var integer
     */
    public $status = 403;

    /**
     * Create a new exception instance.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }

    /**
     * In Laravel 5.5, you can render your exceptions directly from the exception class
     * itself, allowing you to handle them they way you want to.
     *
     * @param $request
     * @return JsonResponse|RedirectResponse
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return $this->handleAjax();
        }

        return redirect()->back()
            ->withInput()
            ->withErrors($this->getMessage());
    }

    /**
     * Handle an ajax response.
     */
    private function handleAjax()
    {
        return response()->json([
            'success'   => true,
            'error' => $this->getMessage(),
        ], $this->status);
    }
}
