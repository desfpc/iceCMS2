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
                $this->get('height')
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
     * @return array
     */
    private function _getMaxWidthAndHeight($width, $height): array
    {
        if ($width > self::MAX_ORIGINAL_LENGTH && $width >= $height) {
            $height = self::MAX_ORIGINAL_LENGTH * $height / $width;
            $width = self::MAX_ORIGINAL_LENGTH;
        } elseif ($height > self::MAX_ORIGINAL_LENGTH && $height >= $width) {
            $width = self::MAX_ORIGINAL_LENGTH * $width / $height;
            $height = self::MAX_ORIGINAL_LENGTH;
        }

        return [$width, $height];
    }

    /**
     * Making image favicon file
     *
     * @return bool
     * @throws Exception
     */
    private function _makeFavicon(): bool
    {
        return $this->saveImageSize(
            $this->getPath(),
            $this->_getFaviconPath(),
            self::FAVICON_WIDTH,
            self::FAVICON_HEIGHT,
            $this->get('extension'),
            true
        );
    }

    /**
     * Getting image file URL for web
     *
     * @param int|ImageSize|null $imageSize
     * @return string
     * @throws Exception
     */
    public function getUrl(int|ImageSize|null $imageSize = null): string
    {
        if (is_null($imageSize)) {
            return parent::getUrl();
        }
        return $this->_getUrlDirectory() . $this->_getImageSizeName($imageSize);
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
     * Getting Image Size name
     *
     * @param int|ImageSize $imageSize
     * @return string
     * @throws Exception
     */
    private function _getImageSizeName(int|ImageSize $imageSize): string
    {
        if (is_int($imageSize)) {
            $imageSize = new ImageSize($this->_settings, $imageSize);
            $imageSize->load();
        }
        return $imageSize->get('string_id');
    }

    /**
     * Getting image file path in OS
     *
     * @param int|ImageSize|null $imageSize
     * @return string
     * @throws Exception
     */
    public function getPath(int|ImageSize|null $imageSize = null): string
    {
        if (is_null($imageSize)) {
            return parent::getPath();
        }
        $dirs = $this->_getPathDirectory((bool)$this->_values['private']);
        $path = $dirs[1] . $this->_getImageSizeName($imageSize);
        if (!empty($this->_values['extension'])) {
            $path .= '.' . $this->_values['extension'];
        }

        return $path;
    }

    /**
     * Getting image favicon path in OS
     *
     * @return string
     * @throws Exception
     */
    private function _getFaviconPath(): string
    {
        $dirs = $this->_getPathDirectory((bool)$this->_values['private']);
        $path = $dirs[1] . $this->_getFaviconName();
        if (!empty($this->_values['extension'])) {
            $path .= '.' . $this->_values['extension'];
        }

        return $path;
    }

    /**
     * Delete FileImageSize by ImageSize Id
     *
     * @param int $imageSizeId
     * @return bool
     * @throws Exception
     */
    public function deleteImageSize(int $imageSizeId): bool
    {
        $fileImageSize = new FileImageSize($this->_settings, [
            'file_id' => $this->_id,
            'image_size_id' => $imageSizeId
        ]);
        if ($fileImageSize->load()) {
            if ($fileImageSize->get('is_created') === 1) {
                unlink($this->getPath($imageSizeId));
            }
            $this->_imageSizes = null;
            return $fileImageSize->del();
        }
        return false;
    }

    /**
     * Adding ImageSize to file without creating file
     *
     * @param int $imageSizeId
     * @return bool
     * @throws Exception
     */
    public function addImageSize(int $imageSizeId): bool
    {
        $imageSize = new ImageSize($this->_settings, $imageSizeId);
        if ($imageSize->load()) {
            $fileImageSize = new FileImageSize($this->_settings);
            $fileImageSize->set([
                'file_id' => $this->_id,
                'image_size_id' => $imageSizeId,
                'is_created' => 0,
            ]);
            if (!$fileImageSize->save(true)) {
                unlink($this->getPath($imageSize));
                return false;
            };
            $this->_imageSizes = null;
            return true;
        }
        return false;
    }

    /**
     * Creating ImageSize files
     *
     * @param bool $recreateExists
     * @return bool
     * @throws Exception
     */
    public function buildImageSizeFiles(bool $recreateExists = false): bool
    {
        if (!is_null($this->_imageSizes)) {
            $imageSizes = $this->_imageSizes;
        } else {
            $imageSizes = $this->getImageSizes();
        }

        $errors = [];
        if (!empty($imageSizes)) {
            foreach ($imageSizes as $imageSize) {
                if ($recreateExists || $imageSize['is_created'] === 0) {
                    if (!$this->createImageSize($imageSize['id'])) {
                        $errors[] = 'Error when creating ImageSize ' . $imageSize['id'];
                    }
                }
            }

            if (!empty($errors)) {
                $this->errors = array_merge($this->errors, $errors);
                return false;
            }
        }
        return true;
    }

    /**
     * Creating image variant by imageSize ID
     *
     * @param int $imageSizeId
     * @return bool
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function createImageSize(int $imageSizeId): bool
    {
        $imageSize = new ImageSize($this->_settings, $imageSizeId);
        if ($imageSize->load()) {

            if ($imageSize->get('width') === 0 || $imageSize->get('height') === 0) {
                $crop = false;
            } else {
                $crop = true;
            }

            if (!is_null($imageSize->get('watermark_id')) && $imageSize->get('watermark_id') > 0) {
                $wparams = [
                    'width' => $imageSize->get('watermark_width'),
                    'height' => $imageSize->get('watermark_height'),
                    'top' => $imageSize->get('watermark_top'),
                    'left' => $imageSize->get('watermark_left'),
                    'units' => $imageSize->get('watermark_units'),
                ];
            } else {
                $wparams = null;
            }

            if ($this->saveImageSize(
                $this->getPath(),
                $this->getPath($imageSize),
                $imageSize->get('width'),
                $imageSize->get('height'),
                self::DEFAULT_IMG_FORMAT,
                $crop,
                $imageSize->get('watermark_id'),
                $wparams
            )) {
                $fileImageSize = new FileImageSize($this->_settings);
                $fileImageSize->set([
                    'file_id' => $this->_id,
                    'image_size_id' => $imageSizeId,
                    'is_created' => 1,
                ]);
                if (!$fileImageSize->save(true)) {
                    unlink($this->getPath($imageSize));
                    return false;
                };
                $this->_imageSizes = null;
                return true;
            }
        }

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
            $conditions = ['file_id' => $this->_id];
            $order = ['id' => 'ASC'];
            $imageSizes = new ImageSizeList($this->_settings, $conditions, $order, 1, null);
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
     * Overlay watermark
     *
     * @param int $watermark
     * @param GdImage $image
     * @param array $wparams
     * @return void
     * @throws Exception
     */
    private function _overlayWatermark(int $watermark, GdImage $image, array $wparams): void
    {
        $wimg = new FileImage($this->_settings, $watermark);
        $wimg->load();
        $stamp = $this->_imageCreateFromExtension($wimg->getPath(), $wimg->get('extension'));

        $sx = imagesx($stamp);
        $sy = imagesy($stamp);

        if ($sx !== $wparams['width'] || $sy !== $wparams['height']) {
            $temp = imagecreatetruecolor($wparams['width'], $wparams['height']);
            imagecopyresampled(
                $temp,
                $stamp,
                0,
                0,
                0,
                0,
                $wparams['width'],
                $wparams['height'],
                $sx,
                $sy
            );
            $stamp = $temp;
        }

        if ($wparams['left'] >= 0) {
            $dstX = $wparams['left'];
        } else {
            $dstX = imagesx($image) + $wparams['left'] - $wparams['width'];
        }

        if ($wparams['top'] >= 0) {
            $dstY = $wparams['top'];
        } else {
            $dstY = imagesy($image) + $wparams['top'] - $wparams['height'];
        }

        imagecopy(
            $image,
            $stamp,
            $dstX,
            $dstY,
            0,
            0,
            $wparams['width'],
            $wparams['height']
        );

        imagedestroy($stamp);
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
     * @return bool
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
        ): bool
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
            $this->_overlayWatermark($watermark, $im1, $wparams);
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
        return true;
    }
}