<?php

namespace App\Models;

use App\Scopes\BuddiesScope;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Buddie
 * @package App\Models
 *
 * @property int id
 * @property int shoogle_id
 * @property int user1_id
 * @property int user2_id
 * @property Carbon connected_at
 * @property Carbon|null disconnect_at
 * @property string|null chat_id
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Buddie extends BaseModel
{
    use HasFactory;

    protected $table = 'buddies';

    protected $fillable = [
        'id',
        'shoogle_id',
        'user1_id',
        'user2_id',
        'connected_at',
        'disconnected_at',
        'chat_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'shoogle_id' => 'integer',
        'user1_id' => 'integer',
        'user2_id' => 'integer',
        'connected_at' => 'datetime:Y-m-d h:i:s',
        'disconnected_at' => 'datetime:Y-m-d h:i:s',
        'chat_id' => 'string',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id', 'id')
            ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id', 'id')
            ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function shoogle(): BelongsTo
    {
        return $this->belongsTo(Shoogle::class, 'shoogle_id', 'id')
            ->withDefault();
    }

    /**
     * @return HasMany
     */
    public function buddy(): HasMany
    {
        return $this->hasMany(NotificationToUser::class, 'buddy_id', 'id');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new BuddiesScope);
    }
}
