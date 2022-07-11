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
     * Check POST file for class specification
     *
     * @param array $file
     * @return bool
     */
    protected function _checkFileType(array $file): bool
    {
        return true;
    }

    /**
     * Function before delete
     *
     * @return bool
     */
    protected function _beforeDel()
    {
        
        return true;
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
    public function savePostFile(string $paramName, ?int $userId = null, bool $private = false): bool
    {
        if ($paramName == '' || empty($_FILES[$paramName])) {
            return false;
        }

        $file = $_FILES[$paramName];

        $tmp_name = $file["tmp_name"];

        //Setting entity params from File
        $this->_setByKeyAndValue('name', $file['name'], false);
        $this->_setByKeyAndValue('extension', File::getFileExtension($file['name']), false);
        $this->_setByKeyAndValue('size', (int)$file['size'], false);

        //Setting entity params from POST values
        $this->set($_POST, null, true);
        
        if (!$this->_checkFileType($file)) {
            throw new Exception('Transferred file have incorrect type');
        }

        //Creating a file record in DB
        if (!$this->save()) {
            throw new Exception('Error in saving File Entity');
        }

        //Creating a server route for storing file
        $fileVsPath = $this->_createPath($private) . $this->_id;
        if (!empty($this->_values['extension'])) {
            $fileVsPath .= '.' . $this->_values['extension'];
        }

        //Store file on server
        if (!move_uploaded_file($tmp_name, $fileVsPath)) {
            $this->del();
            throw new Exception('Error in saving File on server');
        }

        //Updating file Entity URL
        $this->_setByKeyAndValue('url', $this->getUrl());
        $this->save();

        return true;
    }

    /**
     * Getting favicon image url
     *
     * @return string
     */
    public function getFaviconUrl(): string
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
     * @return string
     */
    public function getUrl(): string
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