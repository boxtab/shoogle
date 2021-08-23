<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ShoogleViews
 * @package App\Models
 *
 * @property int id
 * @property int shoogle_id
 * @property int user_id
 * @property Carbon last_view
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
class ShoogleViews extends BaseModel
{
    use HasFactory;

    protected $table = 'shoogles_views';

    protected $fillable = [
        'id',
        'shoogle_id',
        'user_id',
        'last_view',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id'            => 'integer',
        'shoogle_id'    => 'integer',
        'user_id'       => 'integer',
        'last_view'     => 'datetime:Y-m-d h:i:s',
        'created_at'    => 'datetime:Y-m-d h:i:s',
        'updated_at'    => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function shoogle(): BelongsTo
    {
        return $this->belongsTo(Shoogle::class, 'owner_id', 'id')
            ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id')
            ->withDefault();
    }
}
