<?php

namespace App\Models;

use App\Scopes\BuddiesScope;
use App\Scopes\NotificationToUserScope;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class NotificationToUser
 * @package App\Models
 *
 * @property int id
 * @property int user_id
 * @property int type_id
 * @property boolean viewed
 * @property string|null notification
 * @property int|null shoogle_id
 * @property int|null from_user_id
 * @property string|null from_message
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property Carbon|null deleted_at
 *
 */
class NotificationToUser extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'notifications_to_user';

    protected $fillable = [
        'id',
        'user_id',
        'type_id',
        'viewed',
        'notification',
        'shoogle_id',
        'from_user_id',
        'from_message',
        'buddy_request_id',
        'buddy_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'type_id' => 'integer',
        'viewed' => 'boolean',
        'notification' => 'string:8192',
        'shoogle_id' => 'integer',
        'from_user_id' => 'integer',
        'from_message' => 'string',
        'buddy_request_id' => 'integer',
        'buddy_id' => 'integer',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
        'deleted_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * Default value for model attribute.
     *
     * @var array
     */
    protected $attributes = [
        'viewed' => 0,
    ];

    /**
     * Linking to the users table.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(NotificationsType::class, 'type_id', 'id')
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
     * @return BelongsTo
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id', 'id')
            ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function buddyRequest(): BelongsTo
    {
        return $this->belongsTo(BuddyRequest::class, 'buddy_request_id', 'id')
            ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function buddie(): BelongsTo
    {
        return $this->belongsTo(Buddie::class, 'buddy_id', 'id')
            ->withDefault();
    }

    /**
     * @param $value
     */
    public function setViewedAttribute($value)
    {
        if ( $value === false || $value === 0 ) {
            $this->attributes['viewed'] = 0;
        } else {
            $this->attributes['viewed'] = 1;
        }
    }

    /**
     * @return mixed|null
     */
    public function getTypeNotificationTextAttribute()
    {
        if ( is_null( $this->type_id ) ) {
            return null;
        } else {
            $typeNotification = NotificationsType::on()
                ->where('id', '=', $this->type_id)
                ->first();
            if ( is_null($typeNotification) ) {
                return null;
            } else {
                return $typeNotification->name;
            }
        }
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new NotificationToUserScope);
    }
}
