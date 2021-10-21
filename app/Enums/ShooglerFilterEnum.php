<?php

namespace App\Enums;

use MadWeb\Enum\Enum;
use ReflectionClass;

/**
 * Class ShooglerEnum
 * @package App\Enums
 */
final class ShooglerFilterEnum extends Enum
{
    const RECENTLY_JOINED = 'RECENTLY_JOINED';
    const AVAILABLE = 'AVAILABLE';
    const SOLO = 'SOLO';
    const BUDDIED = 'BUDDIED';

    /**
     * Return constants as an array.
     *
     * @return array
     * @throws \ReflectionException
     */
    private static function getArray(): array
    {
        $reflector = new ReflectionClass(__CLASS__);
        $response = $reflector->getConstants();
        unset($response['__default']);
        return $response;
    }

    /**
     * Return constants as an associative array.
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getArrayKey()
    {
        return self::getArray();
    }

    /**
     * Return constants as an indexed array.
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getArrayIndex()
    {
        return array_values( self::getArray() );
    }
}
