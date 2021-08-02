<?php

namespace App\Repositories;

use App\Models\Company;

interface TestRepositoryInterface
{
    public function __construct( Company $company );
}
