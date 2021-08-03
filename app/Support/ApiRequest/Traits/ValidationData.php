<?php


namespace App\Support\ApiRequest\Traits;


/**
 * Trait ValidationData
 * @package App\Http\Requests\Admin\Traits
 */
trait ValidationData
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData() : array
    {
        return array_merge($this->all(),$this->route()->parameters());
    }
}
