<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserHasShoogleLog
 * @package App\Models
 *
 *
 * @property int id
 * @property int|null user_id
 * @property int|null shoogle_id
 * @property int|null user_has_shoogle_id
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
class UserHasShoogleLog extends BaseModel
{
    use HasFactory;

    protected $table = 'user_has_shoogle_log';

    protected $fillable = [
        'id',
        'user_id',
        'shoogle_id',
        'user_has_shoogle_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id'  => 'integer',
        'user_id' => 'integer',
        'shoogle_id' => 'integer',
        'user_has_shoogle_id' => 'integer',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * Formatted created_at.
     *
     * @return string|null
     */
    public function getCreatedAtFormattedAttribute(): ?string
    {
        return ( ! is_null($this->created_at) ) ? $this->created_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Formatted updated_at.
     *
     * @return string|null
     */
    public function getUpdatedAtFormattedAttribute(): ?string
    {
        return ( ! is_null($this->updated_at) ) ? $this->updated_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Users shoogles.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->withDefault();
    }

    /**
     * Shoogle for users.
     *
     * @return BelongsTo
     */
    public function shoogle(): BelongsTo
    {
        return $this->belongsTo(Shoogle::class, 'shoogle_id', 'id')
            ->withDefault();
    }

    /**
     * User has shoogle.
     *
     * @return BelongsTo
     */
    public function userHasShoogle(): BelongsTo
    {
        return $this->belongsTo(UserHasShoogle::class, 'user_has_shoogle', 'id')
            ->withDefault();
    }
}
