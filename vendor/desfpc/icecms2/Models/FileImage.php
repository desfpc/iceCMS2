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

class FileImage extends File
{
    /**
     * Create favicon for image file
     *
     * @return bool
     */
    private function _createFavicon(): bool
    {

    }

    /**
     * Check POST file for image
     *
     * @param array $file
     * @return bool
     */
    protected function _checkFileType(array $file): bool
    {
        switch ($imgtype) {
            case '2':
                $extension = 'jpg';
                break;
            case '3':
                $extension = 'png';
                break;
            case '1':
                $extension = 'gif';
                break;
            default:
                $this->errors[] = 'Transferred file is not an image or its format is not supported';
                return false;
                break;
        }
        return true;
    }

    /**
     * Creating image variant by width/height/waterMark
     *
     * @param int|null $x
     * @param int|null $y
     * @param int|null $waterMark
     * @return bool
     */
    public function createImageSize(?int $x = null, ?int $y = null, ?int $waterMark = null): bool
    {

    }
}