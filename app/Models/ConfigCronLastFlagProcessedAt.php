<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ConfigCronLastFlagProcessedAt
 * @package App\Models
 *
 * @property int id
 * @property string value
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
class ConfigCronLastFlagProcessedAt extends BaseModel
{
    use HasFactory;

    protected $table = 'config_cron_last_flag_processed_at';

    protected $fillable = [
        'id',
        'value',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'value' => 'string:1024',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];
}
