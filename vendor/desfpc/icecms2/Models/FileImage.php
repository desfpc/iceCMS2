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
    /** @var string File Type (enum: file, image, document) */
    protected string $_filetype = 'image';

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
                break;
        }
        $this->_setByKeyAndValue('extension', $extension);

        return true;
    }

    /**
     * Getting image file URL for web
     *
     * @param int|null $x Width of image file
     * @param int|null $y Height of image file
     * @param int|null $waterMark Image WaterMark file ID
     * @return string
     */
    public function getUrl(?int $x = null, ?int $y = null, ?int $waterMark = null): string
    {
        if (is_null($x) && is_null($y) && is_null($waterMark)) {
            return parent::getUrl();
        }
    }

    /**
     * Getting image file path in OS
     *
     * @param int|null $x Width of image file
     * @param int|null $y Height of image file
     * @param int|null $waterMark Image WaterMark file ID
     * @return string
     */
    public function getPath(?int $x = null, ?int $y = null, ?int $waterMark = null): string
    {
        if (is_null($x) && is_null($y) && is_null($waterMark)) {
            return parent::getPath();
        }
    }

    /**
     * Creating image variant by width/height/waterMark
     *
     * @param int|null $x
     * @param int|null $y
     * @param int|null $waterMark
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function createImageSize(?int $x = null, ?int $y = null, ?int $waterMark = null): bool
    {
        return false;
    }
}