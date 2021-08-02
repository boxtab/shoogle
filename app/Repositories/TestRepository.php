<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TestRepository extends Repositories implements TestRepositoryInterface
{
    /**
     * @var Company
     */
    protected $model;

    /**
     * TestRepository constructor.
     *
     * @param Company $model
     */
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }
}
