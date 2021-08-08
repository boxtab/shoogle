<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repositories
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var int|null
     */
    protected $companyId;

    /**
     * Repositories constructor.
     *
     * @param Model $model
     */
    public function __construct( Model $model )
    {
        $this->model = $model;
        $this->companyId = getCompanyIdFromJWT();
    }

    /**
     * Is the company selected.
     *
     * @return bool
     */
    public function noCompany(): bool
    {
        return is_null( $this->companyId ) ? true : false;
    }

    public function __call( $name, $arguments )
    {
        return call_user_func_array( [$this->model, $name], $arguments );
    }
}
