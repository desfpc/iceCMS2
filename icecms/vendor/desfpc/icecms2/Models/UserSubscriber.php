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

/**
 * Class UserSubscriber
 *
 * @package iceCMS2\Models
 * @property int $user_id
 * @property int $target_id
 * @property string $created_time
 */
class UserSubscriber extends AbstractEntity
{
    /** @var string Entity DB table name */
    protected string $_dbtable = 'user_subscribers';

    /** @var array|null Validators for values by key */
    protected ?array $_validators = [
        'user_id' => 'int',
        'target_id' => 'int',
        'created_time' => 'unixtime',
    ];

    /** @var array|string[]|null IDs columns for DB table */
    protected ?array $_idColumns = [
        'user_id',
        'target_id',
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

        $res = $this->_db->queryBinded('SELECT * FROM user_subscribers WHERE user_id = ? AND target_id = ?;', [
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
                'user_id' => $fromUser,
                'target_id' => $toUser,
            ]);

            return $this->save(); //TODO cerate send notification task
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

        $res = $this->_db->queryBinded('SELECT * FROM user_subscribers WHERE user_id = ? AND target_id = ?;', [
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