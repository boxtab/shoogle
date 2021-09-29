<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Shoogle
 * @package App\Models
 *
 * @property int id
 * @property int owner_id
 * @property int wellbeing_category_id
 * @property bool active
 * @property string|null title
 * @property Carbon reminder
 * @property string|null reminder_interval
 * @property bool|null is_reminder
 * @property bool|null is_repetitive
 * @property string|null description
 * @property string cover_image
 * @property int views
 * @property string|null chat_id
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Shoogle extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'shoogles';

    protected $fillable = [
        'id',
        'owner_id',
        'wellbeing_category_id',
        'active',
        'title',
        'reminder',
        'reminder_interval',
        'is_reminder',
        'is_repetitive',
        'description',
        'cover_image',
        'views',
        'chat_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'owner_id' => 'integer',
        'wellbeing_category_id' => 'integer',
        'active' => 'boolean',
        'title' => 'string:45',
        'reminder' => 'datetime:Y-m-d h:i:s',
        'reminder_interval' => 'string:1024',
        'is_reminder' => 'boolean',
        'is_repetitive' => 'boolean',
        'description' => 'string',
        'cover_image' => 'string:256',
        'views' => 'integer',
        'chat_id' => 'string',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * Shoogle creator.
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id')
            ->withDefault();
    }

    /**
     * Shoogle category.
     *
     * @return BelongsTo
     */
    public function wellbeingCategory(): BelongsTo
    {
        return $this->belongsTo(WellbeingCategory::class, 'wellbeing_category_id', 'id')
            ->withDefault();
    }

    /**
     * @return HasMany
     */
    public function shoogleViews(): HasMany
    {
        return $this->hasMany(ShoogleViews::class);
    }

    /**
     * Shoogle users.
     *
     * @return HasMany
     */
    public function userHasShoogle(): HasMany
    {
        return $this->hasMany(UserHasShoogle::class, 'shoogle_id', 'id');
    }

    /**
     * Formatted reminder time.
     *
     * @return string|null
     */
    public function getReminderFormattedAttribute(): ?string
    {
        return Carbon::create($this->reminder)->toTimeString();
    }
}
