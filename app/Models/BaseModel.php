<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    /**
     * Formatted creation date.
     * Escape: "2021-08-16T08:02:46.000000Z"
     *
     * @return mixed
     */
    public function getCreatedAttribute()
    {
        return ( ! is_null($this->created_at) ) ? $this->created_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Formatted Editing Date.
     * Escape: "2021-08-16T08:02:46.000000Z"
     *
     * @return mixed
     */
    public function getUpdatedAttribute()
    {
        return ( ! is_null($this->updated_at) ) ? $this->updated_at->format('Y-m-d H:i:s') : null;
    }
}
