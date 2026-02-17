<?php

namespace App\Services\Image;

/**
 * Класс работы с изображениями формата jpeg
 */
class ImageJpeg extends BaseImage
{

    public function __construct($img, $disk)
    {
        parent::__construct($img, $disk);
    }
    /**
     * Конвертация изображения в формат jpeg
     * @return string|null
     * @throws \Exception
     */
    public function convertFormat(): string|null
    {
        $image = $this->_createSourceImg();
        try {
            imagejpeg($image, $this->_newPath, 90);
            imagedestroy($image);
            return $this->_newPath;
        } catch(\Exception $e) {
            return null;
        }
    }

    /**
     * Сохранение нового изображения в формате jpeg
     * @param int $width
     * @param int $height
     * @return string
     * @throws \Exception
     */
    public function saveNewImg(int $width, int $height):string
    {
        $this->convertSize($width, $height);
        imagejpeg($this->_newImg, $this->_newPath);
        imagedestroy($this->_newImg);
        imagedestroy($this->_resourceImg);
        return $this->_newPath;
    }
}
?>
