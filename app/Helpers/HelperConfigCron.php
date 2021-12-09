<?php

namespace App\Helpers;

use App\Models\ConfigCronLastFlagProcessedAt;
use Illuminate\Support\Facades\DB;

/**
 * Class HelperConfigCron
 * @package App\Helpers
 */
class HelperConfigCron
{
    /**
     * Get last flag.
     *
     * @return string|null
     */
    public static function getLastFlagProcessed(): ?string
    {
        $configCronLastFlagProcessedAt = ConfigCronLastFlagProcessedAt::on()->first();

        if ( is_null($configCronLastFlagProcessedAt) ) {
            return null;
        }

        return $configCronLastFlagProcessedAt->value;
    }

    /**
     * Set last flag.
     *
     * @param string|null $value
     */
    public static function setLastFlagProcessed(?string $value)
    {
        DB::transaction(function () use($value) {
            ConfigCronLastFlagProcessedAt::on()->delete();
            ConfigCronLastFlagProcessedAt::on()->insert(['value' => $value]);
        });
    }
}
