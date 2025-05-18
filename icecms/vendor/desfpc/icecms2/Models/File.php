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
use iceCMS2\Types\UnixTime;

use function PHPUnit\Framework\isInstanceOf;

class File extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'files';

    /** @var string File Type (enum: file, image, document) */
    protected string $_filetype = 'file';

    /** @var string|null New image extension */
    protected ?string $_newExtension = null; //TODO change getUrl() extension to newExtension if not null

    /**
     * Getting file extension
     *
     * @param string $filename
     * @return string
     */
    public static function getFileExtension(string $filename): string
    {
        $pathInfo = pathinfo($filename);

        if (!isset($pathInfo['extension'])) {
            return '';
        }
        return $pathInfo['extension'];
    }

    /**
     * Check POST file for class specification
     *
     * @param array $file
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    protected function _checkFileType(array $file): bool
    {
        return true;
    }

    /**
     * Function before delete
     *
     * @return bool
     * @throws Exception
     */
    protected function _beforeDel(): bool
    {
        $path = $this->getPath();
        if (file_exists($path)) {
            unlink($path);
        }
        
        return true;
    }

    /**
     * Method after success file saving via POST request ($this->savePostFile)
     *
     * @return bool
     */
    protected function _afterSavePostFile(): bool
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

        $tmpName = $file['tmp_name'];
        $extension = File::getFileExtension($tmpName);

        //Setting entity params from File
        $this->_setByKeyAndValue('name', $file['name'], false);
        $this->_setByKeyAndValue('filename', $file['name'], false);
        $this->_setByKeyAndValue('extension', $extension, false);
        $this->_setByKeyAndValue('size', (int)$file['size'], false);
        $this->_setByKeyAndValue('filetype', $this->_filetype, false);
        $this->_setByKeyAndValue('private', (int)$private, false);
        $this->_setByKeyAndValue('created_time', new UnixTime(), false);

        if (!is_null($userId)) {
            $this->_setByKeyAndValue('user_id', $userId, false);
        }

        //Setting entity params from POST values
        $this->set($_POST, null, true);

        if (!$this->_checkFileType($file)) {
            throw new Exception('Transferred file have incorrect type');
        }

        if ($extension !== $this->get('extension')) {
            $extension = $this->get('extension');
        }

        //Creating a file record in DB
        if (!$this->save()) {
            throw new Exception('Error in saving File Entity');
        }

        //Creating a server route for storing file
        $fileVsPath = $this->_createPath($private) . $this->_id;
        if (!empty($extension)) {
            $fileVsPath .= '.' . $extension;
        }

        //Store file on server
        if (!rename($tmpName, $fileVsPath)) {
            $this->del();
            throw new Exception('Error in saving File on server');
        }

        //Updating file Entity URL
        $this->_setByKeyAndValue('url', $this->getUrl());

        if (!$this->save()) {
            $this->del();
            throw new Exception('Error in saving Entity');
        }

        if (!$this->_afterSavePostFile()) {
            $this->del();
            throw new Exception('Error in saving Entity (after save function)');
        }

        return true;
    }

    /**
     * Getting file path in OS
     *
     * @return string
     * @throws Exception
     */
    public function getPath(): string
    {
        $this->_needLoaded();

        $dirs = $this->_getPathDirectory((bool)$this->_values['private']);
        $path = $dirs[1] . $this->_id;
        if (!empty($this->_values['extension'])) {
            $path .= '.' . $this->_values['extension'];
        }

        return $path;
    }

    /**
     * Getting file URL for web
     *
     * @return string
     * @throws Exception
     */
    public function getUrl(): string
    {
        $this->_needLoaded();
        $url = $this->_getUrlDirectory();

        $url .= $this->_id;
        if (!empty($this->_values['extension'])) {
            if (is_null($this->_newExtension)) {
                $url .= '.' . $this->_values['extension'];
            } else {
                $url .= '.' . $this->_newExtension;
            }
        }

        return $url;
    }

    /**
     * Getting file favicon URL
     *
     * @return string|null
     */
    public function getFaviconUrl(): ?string
    {
        return null;
    }

    /**
     * Getting directory for URL and Path
     *
     * @param ?bool $private
     * @return string
     */
    protected function _getUrlDirectory(?bool $private = null): string
    {
        if (is_null($private)) {
            $private = (bool)$this->_values['private'];
        }

        if (isset($this->_settings->testMode)) {
            $url = DIRECTORY_SEPARATOR . 'files_test' . DIRECTORY_SEPARATOR;
        } else {
            $url = DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
        }

        if ($private) {
            $url .= 'private' . DIRECTORY_SEPARATOR;
        }

        $time = $this->_values['created_time'];
        if (!($time instanceof UnixTime)) {
            $time = new UnixTime($this->_values['created_time']);
        }

        $url .= date('Ym', $time->get()) . DIRECTORY_SEPARATOR;

        return $url;
    }

    /**
     * Getting url and path directories array
     *
     * @param bool $private
     * @return array
     */
    protected function _getPathDirectory(bool $private = false): array
    {
        $url = $this->_getUrlDirectory($private);
        $dirpatch = $this->_settings->path . 'web' . $url;

        return [$url, $dirpatch];
    }

    /**
     *  Create file patch directory
     *
     * @param bool $private
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function _createPath(bool $private = false): string
    {
        [$url, $dirpatch] = $this->_getPathDirectory($private);

        if (!is_dir($dirpatch)) {
            if (!$private) {
                mkdir($dirpatch, 0750);
            } else {
                mkdir($dirpatch, 0640);
            }
        }

        return $dirpatch;
    }
}