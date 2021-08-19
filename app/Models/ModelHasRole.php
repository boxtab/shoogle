<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModelHasRole
 * @package App\Models
 *
 * @property int role_id
 * @property  string model_type
 * @property int model_id
 */

class ModelHasRole extends BaseModel
{
    use HasFactory;

    protected $table = 'model_has_roles';

    protected $fillable = [
        'role_id',
        'model_type',
        'model_id',
    ];

    protected $casts = [
        'role_id' => 'integer',
        'model_type' => 'string:255',
        'model_id' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id')
            ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function model(): BelongsTo
    {
        return $this->belongsTo(User::class, 'model_id', 'id')
            ->withDefault();
    }
}
