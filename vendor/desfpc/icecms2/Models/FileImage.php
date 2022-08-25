<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * FileImage entity class
 */

namespace iceCMS2\Models;

use GdImage;
use iceCMS2\Tools\Exception;

class FileImage extends File
{
    /** @var string Favicon postfix in file name */
    protected const FAVICON_NAME = 'favicon';
    /** @var int Favicon image width */
    protected const FAVICON_WIDTH = 64;
    /** @var int Favicon image height */
    protected const FAVICON_HEIGHT = 64;
    /** @var string Default image extension (webp recommended) */
    protected const DEFAULT_IMG_FORMAT = 'webp';
    /** @var bool Flag - reformat or not original image to DEFAULT_IMG_FORMAT */
    protected const IF_REFORMAT_ORIGINAL = true;
    /** @var int Max image length (width or height). Original image resize to this size. If 0 - then no resize */
    protected const MAX_ORIGINAL_LENGTH = 1200;
    /** @var string File Type (enum: file, image, document) */
    protected string $_filetype = 'image';
    /** @var array|null Image sizes array*/
    private ?array $_imageSizes = null;

    /**
     * Check POST file for image
     *
     * @param array $file
     * @return bool
     * @throws Exception
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariables)
     */
    protected function _checkFileType(array $file): bool
    {
        [$width, $height, $type, $attr] = getimagesize($file['tmp_name']);

        $this->_setByKeyAndValue('width', (int)$width);
        $this->_setByKeyAndValue('height', (int)$height);

        switch ($type) {
            case '2':
                $extension = 'jpg';
                break;
            case '3':
                $extension = 'png';
                break;
            case '1':
                $extension = 'gif';
                break;
            case '6':
                $extension = 'bmp';
                break;
            case '18':
                $extension = 'webp';
                break;
            default:
                $this->errors[] = 'Transferred file is not an image or its format is not supported';
                return false;
        }
        $this->_setByKeyAndValue('extension', $extension);

        return true;
    }

    /**
     * Method after success file saving via POST request ($this->savePostFile)
     *
     * @return bool
     * @throws Exception
     */
    protected function _afterSavePostFile(): bool
    {
        return $this->_reformatOriginal() && $this->_makeFavicon();
    }

    /**
     * Reformat original image file
     *
     * @return bool
     * @throws Exception
     */
    private function _reformatOriginal(): bool
    {
        $doReformating = false;
        $doResizing = false;
        if (self::IF_REFORMAT_ORIGINAL && $this->get('extension') !== self::DEFAULT_IMG_FORMAT) {
            $doReformating = true;
            $newExtension = self::DEFAULT_IMG_FORMAT;
            $this->set('extension', $newExtension);
        } else {
            $newExtension = $this->get('extension');
        }
        if (
            self::MAX_ORIGINAL_LENGTH > 0 &&
            ($this->get('width') > self::MAX_ORIGINAL_LENGTH || $this->get('height') > self::MAX_ORIGINAL_LENGTH)
        ) {
            $doResizing = true;

            [$newX, $newY] = $this->_getMaxWidthAndHeight(
                $this->get('width'),
                $this->get('height'),
                self::MAX_ORIGINAL_LENGTH
            );
            $this->set('width', $newX);
            $this->set('height', $newY);
        } else {
            $newX = $this->get('width');
            $newY = $this->get('height');
        }

        if (!$doReformating && !$doResizing) {
            return true;
        }

        if ($this->save()) {
            $imgPath = $this->getPath();
            $this->saveImageSize($imgPath, $imgPath, $newX, $newY, $newExtension);
            return true;
        }

        return false;
    }

    /**
     * Return array of new width and height, resizing according to max length value
     *
     * @param $width
     * @param $height
     * @param $max
     * @return array
     */
    private function _getMaxWidthAndHeight($width, $height, $max): array
    {
        if ($width > $max && $width >= $height) {
            $height = $max * $height / $width;
            $width = $max;
        } elseif ($height > $max && $height >= $width) {
            $width = $max * $width / $height;
            $height = $max;
        }

        return [$width, $height];
    }

    /**
     * TODO Making image favicon file
     *
     * @return bool
     */
    private function _makeFavicon(): bool
    {
        return false;
    }

    /**
     * TODO Getting image file URL for web
     *
     * @param int|null $imageSize
     * @return string
     * @throws Exception
     */
    public function getUrl(?int $imageSize = null): string
    {
        if (is_null($imageSize)) {
            return parent::getUrl();
        }
    }

    /**
     * Getting file favicon URL
     *
     * @return string|null
     * @throws Exception
     */
    public function getFaviconUrl(): ?string
    {
        return $this->_getUrlDirectory() . $this->_getFaviconName();
    }

    /**
     * Getting Image favicon name
     *
     * @return string
     * @throws Exception
     */
    private function _getFaviconName(): string
    {
        return $this->_id . '_' . self::FAVICON_NAME . '.' . $this->get('extension');
    }

    /**
     * TODO Getting image file path in OS
     *
     * @param int|null $imageSize
     * @return string
     * @throws Exception
     */
    public function getPath(?int $imageSize = null): string
    {
        if (is_null($imageSize)) {
            return parent::getPath();
        }
    }

    /**
     * TODO Creating image variant by imageSize ID
     *
     * @param int $imageSize
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function createImageSize(int $imageSize): bool
    {
        return false;
    }

    /**
     * Getting created image sizes
     *
     * @return ?array
     * @throws Exception
     */
    public function getImageSizes(): ?array
    {
        if (!$this->isLoaded) {
            throw new Exception('Image object not loaded');
        }
        if (is_null($this->_imageSizes)) {
            $conditions = [
                'id' => [
                    'logic' => 'AND',
                    'sign' => 'IN',
                    'value' => '(SELECT image_size_id FROM file_image_sizes WHERE file_id = ' . $this->_id . ')'
                ],
            ];
            $order = ['id' => 'ASC'];
            $imageSizes = new ImageSizeList($this->_settings, $conditions, $order, 1, 1000);
            $this->_imageSizes = $imageSizes->get();
            if (!$this->_imageSizes) {
                $this->_imageSizes = null;
            }
        }
        return $this->_imageSizes;
    }

    /**
     * Create image from path and extension
     *
     * @param string $path
     * @param string $extension
     * @return GdImage|false
     * @throws Exception
     */
    private function _imageCreateFromExtension(string $path, string $extension): GdImage|false
    {
        return match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($path),
            'png' => imagecreatefrompng($path),
            'gif' => imagecreatefromgif($path),
            'bmp' => imagecreatefrombmp($path),
            'webp' => imagecreatefromwebp($path),
            default => throw new Exception('Wrong or not supported image type: ' . $extension),
        };
    }

    /**
     * Save image file with requested sizes, watermark and format
     *
     * @param string $from
     * @param string $to
     * @param int $newx
     * @param int $newy
     * @param string $extension
     * @param bool $crop
     * @param int $watermark
     * @param array|null $wparams
     * @throws Exception
     */
    public function saveImageSize (
        string $from,
        string $to,
        int $newx,
        int $newy,
        string $extension,
        bool $crop = false,
        int $watermark = 0,
        ?array $wparams = null
        )
    {
        //Calculating new image sizes
        $originalx = $this->get('width');
        $originaly = $this->get('height');
        $originalExtension = $this->get('extension');
        if ($newx == 0) {
            $newx = round($originalx * $newy / $originaly);
        } elseif ($newy == 0) {
            $newy = round($originaly * $newx / $originalx);
        }

        $im = $this->_imageCreateFromExtension($from, $originalExtension);
        $im1 = imagecreatetruecolor($newx, $newy);
        if ($extension == 'png') {
            imagealphablending($im1, false);
            imagesavealpha($im1, true);
        }

        if (!$crop) {
            imagecopyresampled($im1, $im, 0, 0, 0, 0, $newx, $newy, imagesx($im), imagesy($im));
        } else {
            $sootn1 = $newx / $newy;
            $sootn2 = imagesx($im) / imagesy($im);

            //crop by x
            if ($sootn1 >= $sootn2) {
                $ix = imagesx($im);
                $iy = round($newy * imagesx($im) / $newx);
            //crop by y
            } else {
                $iy = imagesy($im);
                $ix = round($newx * imagesy($im) / $newy);

            }

            //offsets
            $startx = (int)((imagesx($im) - $ix) / 2);
            $starty = 0;

            imagecopyresampled($im1, $im, 0, 0, $startx, $starty, $newx, $newy, $ix, $iy);
        }

        //Create watermark
        if ($watermark > 0 && !empty($wparams)) {
            $wimg = new FileImage($this->_settings, $watermark);
            $wimg->load();
            $stamp = $this->_imageCreateFromExtension($wimg->getPath(), $wimg->get('extension'));

            $sx = imagesx($stamp);
            $sy = imagesy($stamp);

            imagecopy(
                $im1,
                $stamp,
                imagesx($im1) - $sx - $wparams['width'],
                imagesy($im1) - $sy - $wparams['height'],
                $wparams['top'],
                $wparams['left'],
                imagesx($stamp),
                imagesy($stamp)
            );

        }

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($im1, $to, 100);
                break;
            case 'png':
                imagepng($im1, $to);
                break;
            case 'gif':
                imagegif($im1, $to);
                break;
            case 'bmp':
                imagebmp($im1, $to);
                break;
            case 'webp':
                imagewebp($im1, $to, 100);
                break;
        }
    }
}