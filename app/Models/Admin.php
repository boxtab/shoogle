<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Admin
 * @package App\Models
 *
 * @property int id
 * @property boolean superadmin
 * @property string|null name
 * @property string|null password
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class Admin extends BaseModel
{
    use HasFactory;

    protected $table = 'admins';

    protected $fillable = [
        'id',
        'superadmin',
        'company_id',
        'name',
        'password',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'superadmin' => 'boolean',
        'company_id' => 'integer',
        'name' => 'string:45',
        'password' => 'string:45',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')->withDefault();
    }
}
