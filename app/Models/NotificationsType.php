<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class NotificationsType
 * @package App\Models
 *
 * @property int id
 * @property string name
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 *
 */
class NotificationsType extends BaseModel
{
    use HasFactory;

    protected $table = 'notifications_type';

    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string:256',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * Get the notifications to user for the type.
     *
     * @return HasMany
     */
    public function notificationsToUser(): HasMany
    {
        return $this->hasMany(NotificationToUser::class, 'type_id', 'id');
    }
}
