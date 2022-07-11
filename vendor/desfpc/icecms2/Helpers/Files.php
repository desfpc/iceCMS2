<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * File Helpers
 */

namespace iceCMS2\Helpers;

use iceCMS2\Models\File;
use iceCMS2\Settings\Settings;

class Files
{
    /** @var array Extension types for getting icon class */
    private const EXTENSION_TYPES = [
        'pdf' => ['pdf'],
        'text' => ['txt', 'doc', 'docx', 'pages', 'rtf'],
        'table' => ['xls', 'xlsx', 'numbers'],
        'presentation' => ['ppt', 'pptx', 'pps', 'ppsx', 'pot', 'potx', 'keynote', 'key'],
        'video' => [
            'mov', '3gp', 'avi', 'mp4', 'mpg4', 'm4v', 'mpeg', 'mpg', 'wmv', 'm4v', 'mov', 'asf', 'vfw', 'mpe', 'm75',
            'm15', 'm2v', 'ts', 'qt', 'dif', 'mkv'
            ],
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'tiff', 'bmp', 'tga', 'jfif', 'ico', 'svg', 'webp'],
        'ai' => ['ai'],
        'archive' => [
            'zip', 'rar', '7z', 'arc', 'arj', 'dmg', 'egg', 'kgb', 'rzip', 'xar', 'tar', 'bzip2', 'gzip', 'lz4', 'lzip',
            'apk', 'rpm', 'msi', 'jar'
        ],
        'music' => [
            'aiff', 'flac', 'm4a', 'm4b', 'm4p', 'mmf', 'mp3', 'ogg', 'oga', 'mogg', 'ra', 'rm', 'vox', 'wav', 'wma',
            '8svx', 'mid', 'midi'
        ],
        'psd' => ['psd'],
        'code' => ['css', 'htm', 'html', 'xml', 'scss', 'php', 'cgi', 'js', 'go'],
        'book' => ['fb2', 'epub', 'djvu'],
    ];

    /** @var string Type for unknown file extension */
    private const EXTENSION_OTHER_TYPE = 'other';

    /**
     * Getting file icon class name
     *
     * @param File $file
     * @return string
     */
    public static function getIconClass(File $file): string
    {
        if (!$file->isLoaded) {
            return '';
        }

        $extension_type = self::EXTENSION_OTHER_TYPE;
        $file_extension = $file->get('extension');

        if ($file_extension !== '') {
            foreach (self::EXTENSION_TYPES as $type => $extensions) {
                if (in_array($file_extension, $extensions)) {
                    $extension_type = $type;
                    break;
                }
            }
        }

        return $extension_type;
    }

    /**
     * Getting file icon html
     *
     * @param File $file
     * @return string
     */
    public static function getIconHtml(File $file, string $moreClasses = ''): string
    {
        $class = self::getIconClass($file);
        return '<div class="file-icon ' . ($moreClasses === '' ? $class : $class . ' ' . $moreClasses) . '"></div>';
    }

    /**
     * Formated file size string
     *
     * @param int $size
     * @return string
     */
    public static function formatedSize(File $file): string
    {
        $size = $file->get('size');

        if ($size < 1024) {
            $size .= 'b';
        } elseif ($size < (1024 * 1024)) {
            $size = '<strong>' . round($size / 1024, 1) . '</strong>Kb';
        } elseif ($size < (1024 * 1024 * 1024)) {
            $size = '<strong>' . round($size / (1024 * 1024), 1) . '</strong>Mb';
        } else {
            $size = '<strong>' . round($size / (1024 * 1024 * 1024), 1) . '</strong>Gb';
        }

        return $size;
    }
}