<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * FileDocument entity class
 */

namespace iceCMS2\Models;

class FileDocument extends File
{
    /** @var string File Type (enum: file, image, document) */
    protected string $_filetype = 'document';
}