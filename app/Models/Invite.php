<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Invite
 * @package App\Models
 *
 * @property int id
 * @property string email
 * @property boolean is_used
 * @property int|null created_by
 * @property int|null companies_id
 * @property int|null department_id
 * @property int|null user_id
 * @property int status
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Invite extends BaseModel
{
    use HasFactory;

    protected $table = 'invites';

    protected $fillable = [
        'id',
        'email',
        'is_used',
        'created_by',
        'companies_id',
        'department_id',
        'user_id',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'email'  => 'string:45',
        'is_used' => 'boolean',
        'created_by' => 'integer',
        'companies_id' => 'integer',
        'department_id' => 'integer',
        'user_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'companies_id', 'id')
            ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')
            ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->withDefault();
    }
}
