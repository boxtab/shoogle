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
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $profile->profile_image = url('storage') . '/' . $profile->profile_image;

        return $profile;
    }

    /**
     * Update profile.
     *
     * @param Request $request
     */
    public function updateProfile(Request $request)
    {
        $profile = User::where('id', Auth::id())->firstOrFail();
        $profile->update(
            Helper::formatSnakeCase(
                $request->except(['profileImage', '_method'])
            )
        );

        if ( $request->has('profileImage') ) {
            $uniqueFilename = Str::uuid()->toString() . '.' . $request->file('profileImage')->extension();

            $profile->clearMediaCollection($profile->id);
            $profile->addMediaFromRequest('profileImage')
                ->usingFileName($uniqueFilename)
                ->toMediaCollection($profile->id);

            $mediaId = DB::table('media')->where('file_name', $uniqueFilename)->get('id')[0]->id;

            $profile->profile_image = $mediaId . '/' . $uniqueFilename;
            $profile->save();
        }
    }
}
