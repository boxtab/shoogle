<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class PasswordRecoveryFacade
 * @package App\Facades
 */
class PasswordRecoveryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PasswordRecoveryService';
    }
}
