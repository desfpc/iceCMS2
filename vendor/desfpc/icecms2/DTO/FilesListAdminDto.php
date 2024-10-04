<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Files List Admin DTO class
 */

namespace iceCMS2\DTO;

/**
 * @property int $id
 * @property string $name
 * @property string $filename
 * @property string $extension
 * @property string $filetype
 * @property int $size
 * @property string $url
 * @property int $image_width
 * @property int $image_height
 * @property int $user_id
 * @property string $user_nikname
 * @property bool $private
 * @property string $created_time
 */
class FilesListAdminDto extends AbstractDto implements DtoInterface
{
    /** @var array DTO params */
    public const PARAMS = [
        'id',
        'name',
        'filename',
        'extension',
        'filetype',
        'size',
        'url',
        'image_width',
        'image_height',
        'user_id',
        'user_nikname',
        'private',
        'created_time',
    ];

    /**
     * Additional data setter
     *
     * @param array $data
     * @return void
     */
    protected function _setAdditionalData(array $data): void
    {
        // do nothing
    }
}