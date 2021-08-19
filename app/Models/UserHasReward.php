<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserHasReward
 * @package App\Models
 *
 * @property int id
 * @property int user_id
 * @property int reward_id
 * @property int given_by_user_id
 */

class UserHasReward extends BaseModel
{
    use HasFactory;

    protected $table = 'user_has_reward';

    protected $fillable = [
        'id',
        'user_id',
        'reward_id',
        'given_by_user_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'reward_id' => 'integer',
        'given_by_user_id' => 'integer',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class, 'reward_id', 'id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function givenByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'given_by_user_id', 'id')->withDefault();
    }
}
