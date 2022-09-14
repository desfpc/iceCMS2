<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * FileImageSize entity class
 */

namespace iceCMS2\Models;

class FileImageSize extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'file_image_sizes';

    /** @var array|null columns for ID */
    protected ?array $_idColumns = ['file_id', 'image_size_id'];
}