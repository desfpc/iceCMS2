<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * File entity class
 */

namespace iceCMS2\Models;

use iceCMS2\Tools\Exception;

//TODO refactor for parent File -> childs ImageFile/DocumentFile classes
class File extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'files';

    /**
     * Getting file extension
     *
     * @param string $filename
     * @return string
     */
    public static function getFileExtension(string $filename): string
    {
        $path_info = pathinfo($filename);
        if (!isset($path_info['extension'])) {
            return '';
        }
        return $path_info['extension'];
    }

    /**
     * Set Entity from POST value
     *
     * @param string $paramName
     * @param int|null $userId
     * @param bool|null $private
     * @return bool
     * @throws Exception
     */
    public function saveFromPost(string $paramName, string $filetype = 'auto', ?int $userId = null, bool $private = false): bool
    {
        if ($paramName == '' || empty($_FILES[$paramName])) {
            return false;
        }

        $tmp_name = $_FILES[$paramName]["tmp_name"];
        $name = $_FILES[$paramName]['name'];
        $extension = File::getFileExtension($name);
        $size = $_FILES[$paramName]['size'];
        list($width, $height, $imgtype, $attr) = getimagesize($tmp_name);
        
        if (is_null($imgtype) && $filetype === 'image') {
            throw new Exception('Transferred file is not an image');
        }

        if ($filetype === 'auto' && is_null($imgtype)) {
            $filetype = 'file';
        } else {
            $filetype = 'image';
        }

        //Check real extension of image file
        if ($filetype == 'image') {
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
        }

        $url = $this->_createPath($private);
    }

    /**
     * Getting File/Image favicon image url
     *
     * @return string
     */
    public function getFaviconUrl(): string
    {

    }

    /**
     * Create favicon for image file
     *
     * @return bool
     */
    public function createFavicon(): bool
    {

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

    /**
     * Getting file path in OS
     *
     * @param int|null $x Width of image file
     * @param int|null $y Height of image file
     * @param int|null $waterMark Image WaterMark file ID
     * @return string
     */
    public function getPath(?int $x = null, ?int $y = null, ?int $waterMark = null): string
    {
        
    }

    /**
     * Getting file URL for web
     *
     * @param int|null $x Width of image file
     * @param int|null $y Height of image file
     * @param int|null $waterMark Image WaterMark file ID
     * @return string
     */
    public function getUrl(?int $x = null, ?int $y = null, ?int $waterMark = null): string
    {

    }

    /**
     *  Create file patch directory
     *
     * @param bool $private
     * @return string
     */
    private function _createPath(bool $private = false): string
    {
        $url = '/files/';
        if ($private) {
            $url .= 'private/';
        }

        $url .= date('Ym') . '/';
        $dirpatch = $this->_settings->path . '/web' . $url;

        if (!is_dir($dirpatch)) {
            if (!$private) {
                mkdir($dirpatch, 0750);
            } else {
                mkdir($dirpatch, 0640);
            }
        }

        return $url;
    }
}