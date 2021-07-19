<?php

namespace App\Models;

use App\User;
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

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id', 'id')
            ->withDefault();
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id', 'id')
            ->withDefault();
    }

    public function shoogle(): BelongsTo
    {
        return $this->belongsTo(Shoogle::class, 'shoogle_id', 'id')
            ->withDefault();
    }
}
