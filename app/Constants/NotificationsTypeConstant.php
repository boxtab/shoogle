<?php

namespace App\Constants;

/**
 * Class NotificationsTypeConstant
 * @package App\Constants
 */
class NotificationsTypeConstant
{
    const SCHEDULER_ID = 1;
    const BUDDY_REQUEST_ID = 2;
    const BUDDY_CONFIRM_ID = 3;
    const BUDDY_REJECT_ID = 4;
    const BUDDY_DISCONNECT_ID = 5;
    const REWARD_ASSIGN_ID = 6;

    const SCHEDULER_NAME = 'SCHEDULER';
    const BUDDY_REQUEST_NAME = 'BUDDY_REQUEST';
    const BUDDY_CONFIRM_NAME = 'BUDDY_CONFIRM';
    const BUDDY_REJECT_NAME = 'BUDDY_REJECT';
    const BUDDY_DISCONNECT_NAME = 'BUDDY_DISCONNECT';
    const REWARD_ASSIGN_NAME = 'REWARD_ASSIGN';
}
