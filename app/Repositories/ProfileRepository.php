<?php

namespace App\Repositories;

use App\Constants\ImageConstant;
use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\Helpers\HelperAvatar;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPUnit\TextUI\Help;
use Exception;

/**
 * Class ProfileRepository
 * @package App\Repositories
 */
class ProfileRepository extends Repositories
{
    /**
     * @var User
     */
    protected $model;

    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Returns the user profile.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public function getProfile(int $userId)
    {
        return $this->model->on()
            ->where('id', '=', $userId)
            ->firstOrFail([
                'id',
                'first_name',
                'last_name',
                'about',
                'rank_id',
                'profile_image',
            ]);
    }

    /**
     * Update profile.
     *
     * @param int $userId
     * @param bool $profileImageTransmitted
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $about
     * @param string|null $profileImage
     * @throws Exception
     */
    public function updateProfile(int $userId, bool $profileImageTransmitted, ?string $firstName, ?string $lastName, ?string $about, ?string $profileImage)
    {
        $user = User::on()
            ->where('id', '=', $userId )
            ->first();

        if ( is_null($user) ) {
            throw new Exception('Your profile was not found.', Response::HTTP_NOT_FOUND);
        }

        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->about = $about;
        $user->save();

        if ( $profileImageTransmitted ) {
            if ( is_null( $profileImage ) ) {
                HelperAvatar::deleteAvatar($user);
            } else {
                HelperAvatar::saveAvatar($profileImage, $user);
            }
        }
    }
}
