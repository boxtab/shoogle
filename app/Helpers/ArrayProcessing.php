<?php

if ( ! function_exists('replaceArraysOnStrings') ) {

    function replaceArraysOnStrings( $objectContainsArrays )
    {
        if ( gettype( $objectContainsArrays ) === 'string' ) {
            return $objectContainsArrays;
        }

        $objectContainsStrings = new stdClass();
        foreach ($objectContainsArrays->toArray() as $key => $value) {
            $objectContainsStrings->$key = implode($value);
        }
        return $objectContainsStrings;
    }

}
