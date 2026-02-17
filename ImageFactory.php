<?php

namespace App\Services\Image;

use Illuminate\Http\UploadedFile;

class ImageFactory {

    /**
     * @param UploadedFile $file
     * @param string $disk
     * @param string $format
     * @return ImageJpeg|ImageWebp
     * @throws \Exception
     */
    public static function buildImg(UploadedFile $file, string $disk, string $format): ImageJpeg|ImageWebp
    {
        return match($format) {
            "webp" => new ImageWebp($file,$disk),
            "jpg" => new ImageJpeg($file,$disk),
//            "png" => ,
//            "bmp" => ,
            default => throw new \Exception("Unsupported image format: " . $format),
        };
    }
}
?>
