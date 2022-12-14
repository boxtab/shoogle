<?php

namespace App\Helpers;

/**
 * Class HelperCalculate
 * @package App\Helpers
 */
class HelperCalculate
{
    /**
     * Rounds array elements.
     *
     * @param array|null $data
     * @param int|null $precision
     * @return array|null
     */
    public static function roundingArray(?array $data, ?int $precision): ?array
    {
        if ( is_null( $precision ) ) {
            return $data;
        }

        array_walk($data, function (&$element) use ($precision) {
            $element = round($element, $precision);
        });

        return $data;
    }

    /**
     * Rounding a number.
     *
     * @param float|null $data
     * @param int|null $precision
     * @return float|null
     */
    public static function roundingNumber(?float $data, ?int $precision): ?float
    {
        if ( is_null( $precision ) ) {
            return $data;
        }

        return round($data, $precision);
    }
}
