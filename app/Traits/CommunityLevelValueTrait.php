<?php

namespace App\Traits;

/**
 * Trait CommunityLevelValueTrait
 * @package App\Traits
 */
trait CommunityLevelValueTrait
{
    /**
     * Average value of indicators of wellbeing.
     *
     * @param array|null $userIDs
     * @param string $periodBegin
     * @param string $periodEnd
     * @return array|null
     */
    private function getValue(?array $userIDs, string $periodBegin, string $periodEnd): ?array
    {
        return null;
    }
}
