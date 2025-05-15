<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Queue entity class
 */

namespace iceCMS2\Models;


/**
 * Class Queue
 *
 * @package iceCMS2\Models
 * @property int $id
 * @property string $name
 * @property int $workers
 * @property int $interval
 * @property string $status
 * @property string $created_time
 * @property string $updated_time
 */
class Queue
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'queues';
}