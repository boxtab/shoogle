<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

/**
 * Class HelperImage
 * @package App\Helpers
 */
class HelperImage
{

    public static $photosLabels = [
        'id_photo_label',
        'id_photo_general',
        'id_photo_plaque'
    ];

    /**
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
     * @param $base64
     * @param $label
     * @param $report
     * @return mixed
     */
    public static function processReportBase64Image($base64, $label, $report)
    {
        $photoDecoded = base64_decode(static::clearBase64Image($base64));

        /* uncomment to save to disk*/
        $info = getimagesizefromstring($photoDecoded);

        $fileExtension = self::getFileExtension($info['mime']);


        $filename = $label . '.' . $fileExtension;
        $basePath = "reports/{$report->id}/";
        $filepathOriginal = $basePath . "photos/original/";
        $filepathSmall = $basePath . "photos/small/";
        $fullPathOriginalWithFileName = $filepathOriginal . $filename;
        $fullPathSmallWithFileName = $filepathSmall . $filename;

        self::deleteAndStore($label, $fullPathOriginalWithFileName, $filepathOriginal, $photoDecoded);

        $img = Image::make($photoDecoded);
        self::optimize($img);
        $img->encode('data-url');
        $protoRawResized = Helper::clearBase64Image($img->getEncoded());
        $img->destroy();
        self::deleteAndStore($label, $fullPathSmallWithFileName, $filepathSmall, base64_decode($protoRawResized));
        return $protoRawResized;
    }

    /**
     * @param $reportId
     * @param $label
     */
    public static function deleteImage($reportId, $label)
    {
        Storage::disk('local')->delete([
            "reports/$reportId/photos/original/" . $label . '.' . 'png',
            "reports/$reportId/photos/original/" . $label . '.' . 'jpg',
            "reports/$reportId/photos/small/" . $label . '.' . 'png',
            "reports/$reportId/photos/small/" . $label . '.' . 'jpg'
        ]);
    }

    /**
     * @param $img
     */
    public static function optimize(&$img)
    {
        $maxSidePx = config('app.report_image_max_side_px');
        if ($img->getWidth() > $maxSidePx) {
            $img->resize($maxSidePx, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
    }

    /**
     * @param $label
     * @param $fullPathSmall
     * @param $filepath
     * @param $rawImage
     */
    public static function deleteAndStore($label, $fullPathSmall, $filepath,  $rawImage)
    {
        if (Storage::disk('local')->exists($filepath . $label . '.' . 'png')) {
            Storage::disk('local')->delete($filepath . $label . '.' . 'png');
        }
        if (Storage::disk('local')->exists($filepath  . $label . '.' . 'jpg')) {
            Storage::disk('local')->delete($filepath  . $label . '.' . 'jpg');
        }
        Storage::disk('local')->put($fullPathSmall, $rawImage);
    }

    /**
     * @param string $title
     * @return string
     */
    public static function getPdfTrans(string $title): string
    {
        return trans("admin.report.documents.pdf.$title");
    }
}
