<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User subscriber entity class
 */

namespace iceCMS2\Models;

use iceCMS2\Tools\Exception;

class UserSubscriber extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'user_subscribers';

    /** @var array|null Validators for values by key */
    protected ?array $_validators = [
        'parent_id' => 'int',
        'child_id' => 'int',
        'date_add' => 'string',
    ];

    /** @var array|string[]|null IDs columns for DB table */
    protected ?array $_idColumns = [
        'parent_id',
        'child_id',
    ];

    /**
     * Subscribe
     *
     * @param int $fromUser
     * @param int $toUser
     * @return bool
     * @throws Exception
     */
    public function subscribe(int $fromUser, int $toUser): bool
    {
        if ($fromUser === $toUser) {
            throw new Exception('You can\'t subscribe to yourself');
        }

        $res = $this->_db->queryBinded('SELECT * FROM user_subscribers WHERE parent_id = ? AND child_id = ?;', [
            0 => $fromUser,
            1 => $toUser,
        ]);

        //If record exist - work with statuses
        if (is_array($res) && count($res) > 0) {
            return true;
        }
        //Create new record
        else {
            $this->set([
                'parent_id' => $fromUser,
                'child_id' => $toUser,
            ]);

            return $this->save();
        }
    }

    /**
     * Unsubscribe
     *
     * @param int $fromUser
     * @param int $toUser
     * @return bool
     * @throws Exception
     */
    public function unSubscribe(int $fromUser, int $toUser): bool
    {
        if ($fromUser === $toUser) {
            throw new Exception('You can\'t unsubscribe from yourself');
        }

        $res = $this->_db->queryBinded('SELECT * FROM user_subscribers WHERE parent_id = ? AND child_id = ?;', [
            0 => $fromUser,
            1 => $toUser,
        ]);

        //If record exist - delete it!
        if (is_array($res) && count($res) > 0) {
            return $this->del();
        }
        //Do nothing
        else {
            return true;
        }
    }
}