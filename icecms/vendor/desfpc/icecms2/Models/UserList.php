<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User entity list class
 */

namespace iceCMS2\Models;

class UserList extends AbstractEntityList
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'users';
}