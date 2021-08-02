<?php

namespace App\Enums;

use MadWeb\Enum\Enum;

/**
 * @method static BuddyRequestTypeEnum FOO()
 * @method static BuddyRequestTypeEnum BAR()
 * @method static BuddyRequestTypeEnum BAZ()
 */
final class BuddyRequestTypeEnum extends Enum
{
    const __default = self::INVITE;

    const INVITE = 'invite';
    const CONFIRM = 'confirm';
    const REJECT = 'reject';
    const DISCONNECT = 'disconnect';
}
