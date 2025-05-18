<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * File Image Sizes List Admin DTO class
 */

namespace iceCMS2\DTO;

/**
 * @property int $id
 * @property int $width
 * @property int $height
 * @property bool $is_crop
 * @property string $string_id
 * @property ?int $watermark_id
 */
class FileImageSizeListAdminDto extends AbstractDto implements DtoInterface
{
    /** @var array DTO params */
    public const PARAMS = [
        'id',
        'width',
        'height',
        'is_crop',
        'string_id',
        'watermark_id',
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