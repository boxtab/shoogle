<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Company
 * @package App\Models
 *
 * @property int id
 * @property string name
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property Carbon|null deleted_at
 */

class Company extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string:45',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
        'deleted_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * Attributes to be converted to date.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Company departments.
     *
     * @return HasMany
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'company_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_id', 'id')->select(['first_name', 'last_name', 'email']);
    }
}
