<?php

if ( ! function_exists('replaceArraysOnStrings') ) {

    function replaceArraysOnStrings( object $objectContainsArrays )
    {
        $objectContainsStrings = new stdClass();
        foreach ($objectContainsArrays->toArray() as $key => $value) {
            $objectContainsStrings->$key = implode($value);
        }
        return $objectContainsStrings;
    }

}
