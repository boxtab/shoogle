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
     * @return mixed
     */
    public function getProfile()
    {
        $profile = $this->model->where('id', Auth::id())
            ->firstOrFail([
                'id',
                'first_name',
                'last_name',
                'about',
                'rank',
                'profile_image',
            ]);
        if ( ! is_null($profile->profile_image) ) {
            $profile->profile_image = url(ImageConstant::BASE_PATH_AVATAR_EXTERNAL) . '/' . $profile->profile_image;
        }

        return $profile;
    }

    /**
     * Update profile.
     *
     * @param Request $request
     * @throws \Exception
     */
    public function updateProfile(Request $request)
    {
        $profile = User::on()->find( Auth::id() )->first();

        if ( is_null($profile) ) {
            throw new Exception('The authenticated user profile was not found.', Response::HTTP_NOT_FOUND);
        }

        $profile->update([
            'first_name' => $request->input('firstName'),
            'last_name' => $request->input('lastName'),
            'about' => $request->input('about'),
            'rank' => $request->input('rank'),
        ]);


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
