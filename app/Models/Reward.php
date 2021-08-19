<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Reward
 * @package App\Models
 *
 * @property int id
 * @property string name
 * @property string|null icon
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Reward extends BaseModel
{
    use HasFactory;

    protected $table = 'rewards';

    protected $fillable = [
        'id',
        'name',
        'icon',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string:45',
        'icon' => 'string:256',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

}
