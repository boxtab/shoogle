<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserHasShoogle
 * @package App\Models
 *
 * @property int id
 * @property int user_id
 * @property int shoogle_id
 * @property Carbon joined_at
 * @property Carbon|null left_at
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class UserHasShoogle extends Model
{
    use HasFactory;

    protected $table = 'user_has_shoogle';

    protected $fillable = [
        'id',
        'user_id',
        'shoogle_id',
        'joined_at',
        'left_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id'  => 'integer',
        'user_id' => 'integer',
        'shoogle_id' => 'integer',
        'joined_at' => 'datetime:Y-m-d h:i:s',
        'left_at' => 'datetime:Y-m-d h:i:s',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * When joined.
     * Escape: "2021-08-16T08:02:46.000000Z"
     *
     * @return mixed
     */
    public function getJoinedAtFormatAttribute()
    {
        return ( ! is_null($this->joined_at) ) ? $this->joined_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * When was the last action.
     * Escape: "2021-08-16T08:02:46.000000Z"
     *
     * @return mixed
     */
    public function getLeftAtFormatAttribute()
    {
        return ( ! is_null($this->left_at) ) ? $this->left_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * When created.
     * Escape: "2021-08-16T08:02:46.000000Z"
     *
     * @return mixed
     */
    public function getCreatedAttribute()
    {
        return ( ! is_null($this->created_at) ) ? $this->created_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * When changed.
     * Escape: "2021-08-16T08:02:46.000000Z"
     *
     * @return mixed
     */
    public function getUpdatedAttribute()
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
}
