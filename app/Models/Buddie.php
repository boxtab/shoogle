<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Buddie extends Model
{
    use HasFactory;

    protected $table = 'buddies';

    protected $fillable = [
        'id',
        'shoogle_id',
        'user1_id',
        'user2_id',
        'connected_at',
        'disconnected_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'shoogle_id' => 'integer',
        'user1_id' => 'integer',
        'user2_id' => 'integer',
        'connected_at' => 'datetime:Y-m-d h:i:s',
        'disconnected_at' => 'datetime:Y-m-d h:i:s',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];


}
