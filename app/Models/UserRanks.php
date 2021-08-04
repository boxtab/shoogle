<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserRanks
 * @package App\Models
 *
 * @property int id
 * @property int user_id
 * @property int rank
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class UserRanks extends Model
{
    use HasFactory;

    protected $table = 'user_ranks';

    protected $fillable = [
        'id',
        'user_id',
        'rank',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'rank' => 'integer',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * When created.
     *
     * @return mixed
     */
    public function getCreatedAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    /**
     * When changed.
     *
     * @return mixed
     */
    public function getUpdatedAttribute()
    {
        return $this->updated_at->format('Y-m-d H:i:s');
    }

    /**
     * Whose rank.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->withDefault();
    }
}
