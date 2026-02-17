<?php
namespace App\Services\Image;

use Illuminate\Http\UploadedFile;

/**
 * Сервис для работы с изображениями
 * конвертация формата
 * изменения размера изображения
 */
abstract class BaseImage {

    /**
     * Свойство максимального размера
     * @var int
     */
    private int $__maxSize = 10000000;

    /**
     * Путь к существующему файлу
     * @var string
     */
    private string $__pathImg;

    /**
     * Путь к новому файло
     * @var string
     */
    protected string $_newPath;

    /**
     * Ресурс изображения
     * @var \GdImage
     */
    protected \GdImage $_resourceImg;

    /**
     * Новое изображение
     * @var \GdImage
     */
    protected \GdImage $_newImg;

    /**
     * Свойство минимального размера
     * @var int
     */
    private int $__minSize = 2;

    /**
     * @var UploadedFile|null
     */
    private ?UploadedFile $__img;

    /**
     * Сохраняем объект изображения
     * @param $img
     * @param $disk
     */
    public function __construct($img, $disk)
    {
        $this->__img = $img;
        $this->_newPath = $disk;
    }

    /**
     * Абстрактный метод реализуеться в потомках
     * @return mixed
     */
    abstract function convertFormat(): mixed;

    /**
     * Создаем источник изображения
     * @return \GdImage|resource
     * @throws \Exception
     */
    protected function _createSourceImg(): ?\GdImage
    {
        // получам реальный путь
        $this->__pathImg = $this->__img->getRealPath();

        // получаем новый путь
        $this->_newPath = tempnam(sys_get_temp_dir(), $this->_newPath) . '.webp';

        // Определяем тип файла по MIME
        $mime = $this->__img->getMimeType();

        // Проверяем допустимый размер
        $isCorrectSize = $this->__img->getSize() > $this->__minSize && $this->__img->getSize() < $this->__maxSize;

        if (!$isCorrectSize) {
            throw new \Exception('Bad size file');
        }

        // Делаем создание ресурса в зависимости от MIME
        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($this->__pathImg),
            'image/png' => imagecreatefrompng($this->__pathImg),
            'image/gif' => imagecreatefromgif($this->__pathImg),
            'image/webp' => imagecreatefromwebp($this->__pathImg),
            default => throw new \Exception("Unsupported image format: " . $mime),
        };

        if(!$image) {
            throw new \Exception("Failed to create image from file.");
        }

        return $image;
    }

    /**
     * Метод изменяет размер изображения
     * @param int $width
     * @param int $height
     * @return void
     * @throws \Exception
     */
    protected function convertSize(int $width, int $height): void
    {
        try{
            // создание подложки нужного размера
            $this->_newImg = imagecreatetruecolor($width, $height);
        } catch(\Exception $exception){
            echo 'Error create template img';
        }

        // получаем ресурс
        $this->_resourceImg = $this->_createSourceImg();

        // масштабирование изображения
        $scale = imagecopyresampled(
            $this->_newImg,
            $this->_resourceImg,
            0, 0, 0, 0,
            $width,
            $height,
            imagesx($this->_resourceImg),
            imagesy($this->_resourceImg)
        );
        if(!$scale) {
            throw new \Exception('Error scale img');
        }
    }
}
?>
