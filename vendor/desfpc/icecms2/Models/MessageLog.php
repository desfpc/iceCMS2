<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * MessageLog entity class
 */

namespace iceCMS2\Models;

class MessageLog extends AbstractLogEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'message_log';
}