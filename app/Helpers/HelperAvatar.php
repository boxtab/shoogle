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

    public static function deleteBase64Image(string $filePath): void
    {
        if (Storage::disk('local')->exists($filePath. '.' . 'png')) {
            Storage::disk('local')->delete($filePath. '.' . 'png');
        }

        if (Storage::disk('local')->exists($filePath . '.' . 'jpg')) {
            Storage::disk('local')->delete($filePath . '.' . 'jpg');
        }
    }

    public static function putBase64Image(string $filePath, string $base64): void
    {
        Storage::disk('local')->put($filePath, $base64);
    }

    public static function deleteAvatar(User $user): void
    {
        $filePath = ImageConstant::BASE_PATH_AVATAR . '/' . substr($user->profile_image, 0, -4);
        static::deleteBase64Image($filePath);
    }

    public static function saveAvatar(string $base64, User $user): void
    {
        $photoDecoded = base64_decode(static::clearBase64Image($base64));
        $info = getimagesizefromstring($photoDecoded);
        $fileExtension = self::getFileExtension($info['mime']);
        $fileName = 'id' . Auth::id() . '-' . Str::uuid()->toString() . '.' . $fileExtension;

        $user->profile_image = $fileName;
        $user->save();


        $filepath = ImageConstant::BASE_PATH_AVATAR . '/' . $fileName;

        if (Storage::disk('local')->exists($filepath. '.' . 'png')) {
            Storage::disk('local')->delete($filepath. '.' . 'png');
        }

        if (Storage::disk('local')->exists($filepath . '.' . 'jpg')) {
            Storage::disk('local')->delete($filepath . '.' . 'jpg');
        }


    }

}
