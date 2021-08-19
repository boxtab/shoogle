<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Role
 * @package App\Models
 *
 * @property int id
 * @property string name
 * @property string guard_name
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Role extends BaseModel
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'id',
        'name',
        'guard_name',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string:255',
        'guard_name' => 'string:255',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * @return HasMany
     */
    public function models(): hasMany
    {
        return $this->hasMany(ModelHasRole::class);
    }
}
