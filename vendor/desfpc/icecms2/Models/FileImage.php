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
        }
        $this->_setByKeyAndValue('extension', $extension);

        return true;
    }

    /**
     * Method after success file saving via POST request ($this->savePostFile)
     *
     * @return bool
     */
    protected function _afterSavePostFile(): bool
    {
        return $this->_makeFavicon();
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
     * TODO Getting created image sizes
     *
     * @return array
     */
    public function getImageSizes(): array
    {
        return [];
    }
}