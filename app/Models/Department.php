<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Department
 * @package App\Models
 *
 * @property int id
 * @property int company_id
 * @property string name
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = [
        'id',
        'company_id',
        'name',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'name' => 'string:255',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h-i-s',
    ];

    /**
     * Which company does the department belong to.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')
            ->withDefault();
    }

    /**
     * Users from this department.
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }
}
