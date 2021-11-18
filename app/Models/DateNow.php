<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class DateNow
 * @package App\Models
 *
 * @property string date_time_now
 */

class DateNow extends BaseModel
{
    use HasFactory;

    protected $table = 'date_now';

    protected $fillable = [
        'date_time_now',
    ];

    protected $casts = [
        'date_time_now' => 'string:64',
    ];
}
