<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User entity class
 */

namespace iceCMS2\Models;

class Log extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'logs';

    /** @var array|null Validators for values by key */
    protected ?array $_validators = [
        'alias' => 'string',
        'value' => 'string',
        'created_time' => 'unixtime',
        'updated_time' => 'unixtime',
    ];
}