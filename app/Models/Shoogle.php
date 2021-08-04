<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property string|null description
 * @property string cover_image
 * @property bool accept_buddies
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Shoogle extends Model
{
    use HasFactory;

    protected $table = 'shoogles';

    protected $fillable = [
        'id',
        'owner_id',
        'wellbeing_category_id',
        'active',
        'title',
        'reminder',
        'description',
        'cover_image',
        'accept_buddies',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'owner_id' => 'integer',
        'wellbeing_category_id' => 'integer',
        'active' => 'boolean',
        'title' => 'string:45',
        'reminder' => 'datetime',
        'description' => 'string',
        'cover_image' => 'string:256',
        'accept_buddies' => 'boolean',
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
     * Formatted creation date.
     *
     * @return mixed
     */
    public function getCreatedAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    /**
     * Formatted Editing Date.
     *
     * @return mixed
     */
    public function getUpdatedAttribute()
    {
        return $this->updated_at->format('Y-m-d H:i:s');
    }

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
     * Shoogle users.
     *
     * @return HasMany
     */
    public function userHasShoogle(): HasMany
    {
        return $this->hasMany(UserHasShoogle::class, 'shoogle_id', 'id');
    }
}
