<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User friend entity class
 */

namespace iceCMS2\Models;

class UserFriend extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'user_friends';

    /** @var array|null Validators for values by key */
    protected ?array $_validators = [
        'parent_id' => 'int',
        'child_id' => 'int',
        'status' => 'enum',
        'initiator' => 'int',
        'type' => 'enum',
        'date_add' => 'string',
        'date_edit' => 'string|empty',
    ];
}