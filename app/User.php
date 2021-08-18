<?php

namespace App;

use App\Models\Company;
use App\Models\Department;
use App\Models\ModelHasRole;
use App\Models\Role;
use App\Models\UserRanks;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @package App
 *
 * @property int id
 * @property int|null company_id
 * @property int|null department_id
 * @property string|null first_name
 * @property string|null last_name
 * @property string|null about
 * @property string email
 * @property Carbon|null email_verified_at
 * @property string password
 * @property string|null remember_token
 * @property string|null avatar
 * @property string|null profile_image
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */

class User extends Authenticatable implements JWTSubject, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'company_id',
        'department_id',
        'first_name',
        'last_name',
        'about',
        'email',
        'password',
        'avatar',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @var string the default authentication "guard".
     */
    protected $guard_name = 'api';

    /**
     * User from the company.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')
            ->withDefault();
    }

    /**
     * User from the department.
     *
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')
            ->withDefault();
    }

    /**
     * User rank.
     *
     * @return HasMany
     */
    public function userRanks(): HasMany
    {
        return $this->hasMany(UserRanks::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function manyRole(): HasMany
    {
        return $this->hasMany(ModelHasRole::class);
    }

    /**
     * @return BelongsToMany
     */
    public function role(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }

    /**
     * Average user rating.
     *
     * @return false|float
     */
    public function getAverageUserRankAttribute()
    {
        return round(UserRanks::where('user_id', $this->id)->avg('rank'),2);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
