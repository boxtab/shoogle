<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PasswordReset
 * @package App\Models
 *
 * @property string email
 * @property string token
 * @property Carbon|null created_at
 *
 */
class PasswordReset extends BaseModel
{
    use HasFactory;

    protected $table = 'password_resets';

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected $casts = [
        'email' => 'string:255',
        'token' => 'string:255',
        'created_at' => 'datetime:Y-m-d h:i:s',
    ];
}
