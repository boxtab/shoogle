<?php

namespace App\Repositories;

use App\Models\Invite;

interface InviteRepositoryInterface
{
    public function __construct( Invite $invite );
}
