<?php

namespace App;

use App\Helpers\Helper;
use App\Mail\API\V1\InviteMail;
use App\Mail\API\V1\ResetPasswordMail;
use App\Models\Company;
use App\Models\Department;
use App\Models\Invite;
use App\Models\ModelHasRole;
use App\Models\NotificationToUser;
use App\Models\Rank;
use App\Models\Role;
use App\Models\Shoogle;
use App\Models\ShoogleViews;
use App\Models\UserHasShoogle;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
//use App\Notifications\ResetPasswordNotification;

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
 * @property string|null password_recovery_code
 * @property string|null remember_token
 * @property string|null avatar
 * @property int|null rank_id
 * @property string|null profile_image
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property Carbon|null deleted_at
 */

class User extends Authenticatable implements JWTSubject, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasMediaTrait, SoftDeletes;

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
        'password_recovery_code',
        'avatar',
        'rank_id',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
     * Attributes to be converted to date.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string the default authentication "guard".
     */
    protected $guard_name = 'api';

//    protected static function booted()
//    {
//        static::deleting(function ($resource) {
//        });
//
//        static::restoring(function ($resource) {
//
//
//        });
//    }

//    protected static function boot()
//    {
//        parent::boot();
//
//        static::deleted(function($inviteUser) {
//            $inviteUser->inviteUser()->delete();
//        });
//    }

//    public function delete()
//    {
//        $this->inviteUser()->delete();
//        return parent::delete();
//    }

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
     * @return BelongsTo
     */
    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class, 'rank_id', 'id')
            ->withDefault();
    }

    /**
     * @return HasMany
     */
    public function ranks(): HasMany
    {
        return $this->hasMany(Rank::class, 'rank_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function manyRole(): HasMany
    {
        return $this->hasMany(ModelHasRole::class, 'model_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function userHasShoogle(): HasMany
    {
        return $this->hasMany(UserHasShoogle::class);
    }

    /**
     * @return HasMany
     */
    public function shoogleViews(): hasMany
    {
        return $this->hasMany(ShoogleViews::class);
    }

    /**
     * @return HasMany
     */
    public function notificationsUser(): HasMany
    {
        return $this->hasMany(NotificationToUser::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function notificationsFromUser(): HasMany
    {
        return $this->hasMany(NotificationToUser::class, 'from_user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function inviteUser(): HasMany
    {
        return $this->hasMany(Invite::class, 'user_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function role(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }

    /**
     * Get the concatenation of first and last name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * The number of shoogles a user has.
     *
     * @return int|null
     */
    public function getProfileShooglesAttribute(): ?int
    {
        return Shoogle::on()
            ->where('owner_id', '=', $this->id)
            ->count();
    }

    /**
     * The number of active shoogles for the user.
     *
     * @return int|null
     */
    public function getProfileActiveAttribute(): ?int
    {
        return Shoogle::on()
            ->where('owner_id', '=', $this->id)
            ->where('active', '=', 1)
            ->count();
    }

    /**
     * The number of inactive shoogles for the user.
     *
     * @return int|null
     */
    public function getProfileInactiveAttribute(): ?int
    {
        return Shoogle::on()
            ->where('owner_id', '=', $this->id)
            ->where('active', '=', 0)
            ->count();
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

    /**
     * Send a notification that the password has been reset.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $link = Helper::getLinkResetPassword($token, $this->email);

        $resetPasswordMail = new ResetPasswordMail($link);
        $resetPasswordMail->to($this->email);
        Mail::send($resetPasswordMail);

//        $this->notify(new ResetPasswordNotification($url));
    }
}
