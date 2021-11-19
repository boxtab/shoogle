<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Rank
 * @package App\Models
 *
 * @property int id
 * @property string name
 * @property string|null icon
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 *
 */
class Rank extends BaseModel
{
    use HasFactory;

    protected $table = 'ranks';

    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string:255',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'rank_id', 'id');
    }
}
