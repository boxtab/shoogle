<?php

namespace App\Models;

use App\Scopes\UserHasShoogleScope;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserHasShoogle
 * @package App\Models
 *
 * @property int id
 * @property int user_id
 * @property int shoogle_id
 * @property Carbon joined_at
 * @property Carbon|null left_at
 * @property boolean solo
 * @property Carbon|null reminder
 * @property string|null reminder_interval
 * @property bool|null is_reminder
 * @property Carbon|null last_notification
 * @property Carbon|null in_process
 * @property string|null chat_id
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property Carbon|null deleted_at
 */

class UserHasShoogle extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_has_shoogle';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'id',
        'user_id',
        'shoogle_id',
        'joined_at',
        'left_at',
        'solo',
        'reminder',
        'reminder_interval',
        'is_reminder',
        'last_notification',
        'in_process',
        'chat_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id'  => 'integer',
        'user_id' => 'integer',
        'shoogle_id' => 'integer',
        'joined_at' => 'datetime:Y-m-d h:i:s',
        'left_at' => 'datetime:Y-m-d h:i:s',
        'solo' => 'boolean',
        'reminder' => 'datetime:Y-m-d h:i:s',
        'reminder_interval' => 'string:1024',
        'is_reminder' => 'boolean',
        'last_notification' => 'datetime:Y-m-d h:i:s',
        'in_process' => 'datetime:Y-m-d h:i:s',
        'chat_id' => 'string:1024',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
        'deleted_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
//    protected $dateFormat = 'U';

    /**
     * When joined.
     * Escape: "2021-08-16T08:02:46.000000Z"
     *
     * @return string|null
     */
    public function getJoinedAtFormatAttribute(): ?string
    {
        return ( ! is_null($this->joined_at) ) ? $this->joined_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * When was the last action.
     * Escape: "2021-08-16T08:02:46.000000Z"
     *
     * @return string|null
     */
    public function getLeftAtFormatAttribute(): ?string
    {
        return ( ! is_null($this->left_at) ) ? $this->left_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Formatted reminder time.
     *
     * @return string|null
     */
    public function getReminderFormattedAttribute(): ?string
    {
        return ( ! is_null($this->reminder) ) ? $this->reminder->format('Y-m-d H:i:s') : null;
    }

    /**
     * Formatted last notification.
     *
     * @return string|null
     */
    public function getLastNotificationFormattedAttribute(): ?string
    {
        return ( ! is_null($this->last_notification) ) ? $this->last_notification->format('Y-m-d H:i:s') : null;
    }

    /**
     * Formatted in process.
     *
     * @return string|null
     */
    public function getInProcessFormattedAttribute(): ?string
    {
        return ( ! is_null($this->in_process) ) ? $this->in_process->format('Y-m-d H:i:s') : null;
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
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new UserHasShoogleScope);
    }
}
