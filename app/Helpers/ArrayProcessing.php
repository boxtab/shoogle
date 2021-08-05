<?php

use Illuminate\Support\Facades\Log;

if ( ! function_exists('replaceArraysOnStrings') ) {

    function replaceArraysOnStrings( $objectContainsArrays )
    {
        if ( gettype( $objectContainsArrays ) === 'string' ) {
            return $objectContainsArrays;
        }
        Log::info('test');
        $objectContainsStrings = new stdClass();
        foreach ($objectContainsArrays->toArray() as $key => $value) {
            $objectContainsStrings->$key = implode($value);
        }
        return $objectContainsStrings;
    }

}
