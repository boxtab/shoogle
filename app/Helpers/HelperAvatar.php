<?php

namespace App\Helpers;

use App\Constants\ImageConstant;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

/**
 * Class HelperAvatar
 * @package App\Helpers
 */
class HelperAvatar
{
    /**
     * Removing metadata from an image.
     *
     * @param $base64
     * @return string|string[]
     */
    public static function clearBase64Image($base64)
    {
        $base64 = str_replace('data:image/png;base64,', '', $base64);
        $base64 = str_replace('data:image/jpg;base64,', '', $base64);
        $base64 = str_replace('data:image/jpeg;base64,', '', $base64);
        $base64 = str_replace('data:image/gif;base64,', '', $base64);
        $base64 = str_replace(' ', '+', $base64);
        return $base64;
    }

    /**
     * Determine the file extension.
     *
     * @param $mime
     * @return string
     */
    public static function getFileExtension($mime)
    {
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                return 'jpg';
            default:
                return 'png';
        }
    }

    /**
     * Deleting images in all formats.
     *
     * @param string $filePath
     */
    public static function deleteBase64Image(string $filePath): void
    {
        $filePath = substr($filePath, 0, -4);
        if (Storage::disk('local')->exists($filePath. '.' . 'png')) {
            Storage::disk('local')->delete($filePath. '.' . 'png');
        }

        if (Storage::disk('local')->exists($filePath . '.' . 'jpg')) {
            Storage::disk('local')->delete($filePath . '.' . 'jpg');
        }
    }

    /**
     * Saving an image.
     *
     * @param string $filePath
     * @param $base64
     */
    public static function putBase64Image(string $filePath, $base64): void
    {
        Storage::disk('local')->put($filePath, $base64);
    }

    /**
     * Returns the path to the image.
     *
     * @param string $fileName
     * @return string
     */
    public static function getPath(string $fileName): string
    {
        return ImageConstant::BASE_PATH_AVATAR_INTERNAL . '/' . $fileName;
    }

    /**
     * Removes the avatar of the transferred user.
     *
     * @param User $user
     */
    public static function deleteAvatar(User $user): void
    {
//        if ( is_null( $user->profile_image ) ) {
//            return;
//        }

        $filePath = static::getPath($user->profile_image);
        static::deleteBase64Image($filePath);

        $user->profile_image = null;
        $user->save();
    }

    /**
     * Save user avatar.
     *
     * @param $base64
     * @param User $user
     */
    public static function saveAvatar($base64, User $user): void
    {
        self::deleteAvatar($user);

        $photoDecoded = base64_decode(static::clearBase64Image($base64));
        $info = getimagesizefromstring($photoDecoded);
        $fileExtension = self::getFileExtension($info['mime']);
        $fileName = 'id' . Auth::id() . '-' . Str::uuid()->toString() . '.' . $fileExtension;

        $user->profile_image = $fileName;
        $user->save();

        $filePath = static::getPath($fileName);
        static::putBase64Image($filePath, $photoDecoded);
    }
}
