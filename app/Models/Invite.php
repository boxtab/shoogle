<?php

namespace App\Models;

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
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Invite extends Model
{
    use HasFactory;

    protected $table = 'invites';

    protected $fillable = [
        'id',
        'email',
        'is_used',
        'created_by',
        'companies_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'email'  => 'string:45',
        'is_used' => 'boolean',
        'created_by' => 'integer',
        'companies_id' => 'integer',
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
}
