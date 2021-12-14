<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Abuse
 * @package App\Models
 *
 * @property int id
 * @property string date_abuse
 * @property int from_user_id
 * @property int to_user_id
 * @property int company_admin_id
 * @property string|null message_id
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Abuse extends BaseModel
{
    use HasFactory;

    protected $table = 'abuses';

    protected $fillable = [
        'id',
        'date_abuse',
        'from_user_id',
        'to_user_id',
        'company_admin_id',
        'message_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'date_abuse' => 'string:255',
        'from_user_id' => 'integer',
        'to_user_id' => 'integer',
        'company_admin_id' => 'integer',
        'message_id' => 'string:255',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id', 'id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id', 'id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function companyAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_admin_id', 'id')->withDefault();
    }
}
