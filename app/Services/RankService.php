<?php

namespace App\Services;

use App\User;

/**
 * Class RankService
 * @package App\Services
 */
class RankService
{
    private $userId = null;

    private $countShoogleViews = 0;

    private $countWellbeingScores = 0;

    private $countReward = 0;

    /**
     * RankService constructor.
     * @param int|null $userId
     */
    public function __construct(?int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * User found or not found.
     *
     * @return bool
     */
    public function isUserFound(): bool
    {
        if ( is_null($this->userId) ) {
            return false;
        }

        $user = User::on()
            ->where('id', '=', $this->userId)
            ->first();

        if ( is_null($user) ) {
            return false;
        }

        return true;
    }

    public function fetchCountShoogleViews()
    {
        $this->countShoogleViews = 1;
    }

    public function fetchCountWellbeingScores()
    {
        $this->countWellbeingScores = 1;
    }

    public function fetchCountReward()
    {
        $this->countReward = 1;
    }
}
