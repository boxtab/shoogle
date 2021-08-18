<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Helpers\Helper;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $profile = User::where('id', Auth::id())
            ->firstOrFail([
                'first_name',
                'last_name',
                'about',
                'rank',
                'profile_image',
            ]);
        $profile->profile_image = url('storage') . '/' . $profile->profile_image;

        return $profile;
    }

    public function updateProfile($request)
    {

    }
}
