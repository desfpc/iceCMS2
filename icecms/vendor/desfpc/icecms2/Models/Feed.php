<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Feed entity class
 */

namespace iceCMS2\Models;

/**
 * Class Queue
 *
 * @package iceCMS2\Models
 * @property int $id
 * @property int $author_id
 * @property int $type
 * @property int $target_id
 * @property string $created_time
 */
class Feed
{
    public const FEED_TYPES = [
        0 => 'system',
        1 => 'subscription',
    ];

    /** @var string Entity DB table name */
    protected string $_dbtable = 'feed';
}