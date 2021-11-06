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
                'rank',
                'profile_image',
            ]);
    }

    /**
     * Update profile.
     *
     * @param Request $request
     * @throws \Exception
     */
    public function updateProfile(Request $request)
    {
        $profile = User::on()->where('id', '=', Auth::id() )->first();

        if ( is_null($profile) ) {
            throw new Exception('The authenticated user profile was not found.', Response::HTTP_NOT_FOUND);
        }

        $profile->update(
            Helper::formatSnakeCase(
                $request->except(['profileImage'])
            )
        );

        if ( $request->exists('profileImage') ) {
            $profileImage = $request->input('profileImage');

            if ( empty( $profileImage ) ) {
                HelperAvatar::deleteAvatar($profile);
            } else {
                HelperAvatar::saveAvatar($profileImage, $profile);
            }
        }
    }
}
