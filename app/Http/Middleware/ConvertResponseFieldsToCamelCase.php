<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConvertResponseFieldsToCamelCase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $content = $response->getContent();

        if ($request->wantsJson()) {
            try {

                $json = json_decode($content, true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    if (isset($json['forceUpperCase'])) {
                        unset($json['forceUpperCase']);
                        $response->setContent(json_encode($json, 15));
                    } else {
                        $replaced = $this->camelCaseResponse($json);
                        $response->setContent(json_encode($replaced, 15));
                    }
                }
            } catch (\Exception $e) {
                // you can log an error here if you want
            }
        }

        return $response;
    }

    protected function camelCaseResponse($data)
    {
        if (is_array($data)) {
            return $this->encodeArray($data);
        } else if (is_object($data)) {
            return $this->encodeArray((array) $data);
        } else {
            return $data;
        }
    }

    protected function encodeArray($array)
    {
        $newArray = [];
        foreach ($array as $key => $val) {
            $newArray[Str::camel($key)] = $this->camelCaseResponse($val);
        }
        return $newArray;
    }

}
