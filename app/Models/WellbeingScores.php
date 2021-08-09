<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WellbeingScores
 * @package App\Models
 *
 * @property int id
 * @property int user_id
 * @property int|null social
 * @property int|null physical
 * @property int|null mental
 * @property int|null economical
 * @property int|null spiritual
 * @property int|null emotional
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class WellbeingScores extends Model
{
    use HasFactory;

    protected $table = 'wellbeing_scores';

    protected $fillable = [
        'id',
        'user_id',
        'social',
        'physical',
        'mental',
        'economical',
        'spiritual',
        'emotional',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'social' => 'integer',
        'physical' => 'integer',
        'mental' => 'integer',
        'economical' => 'integer',
        'spiritual' => 'integer',
        'emotional' => 'integer',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];
}
