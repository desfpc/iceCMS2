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

use iceCMS2\Tools\Exception;

class UserFriend extends AbstractEntity
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_FRIEND = 'friend';
    public const STATUS_SUBSCRIBER = 'subscriber';
    public const STATUS_IGNORE = 'ignore';
    public const STATUS_DELETE = 'delete'; //NOT real status - command for delete record

    public const TYPE_FRIEND = 'friend';
    public const TYPE_FAMILY = 'family';
    public const TYPE_TEAMMATE = 'teammate';
    public const TYPE_OTHER = 'other';

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

    /** @var array|string[]|null IDs columns for DB table */
    protected ?array $_idKeys = [
        'parent_id',
        'child_id',
    ];

    /**
     * Subscribe TODO add notice to user, TODO tests
     *
     * @param int $fromUser
     * @param int $toUser
     * @param string $status
     * @return bool
     * @throws Exception
     */
    public function subscribe(int $fromUser, int $toUser, string $status = self::STATUS_PENDING): bool
    {
        if ($fromUser < $toUser) {
            $parentId = $fromUser;
            $childId = $toUser;
        } else {
            $parentId = $toUser;
            $childId = $fromUser;
        }

        $res = $this->_db->queryBinded('SELECT * FROM user_friends WHERE parent_id = ? AND child_id = ?;', [
            0 => $parentId,
            1 => $childId,
        ]);

        //If record exist - work with statuses
        if (is_array($res) && count($res) > 0) {

            $out = false;
            $this->set($res[0]);

            if ($status === self::STATUS_DELETE) {
                if ($res[0]['status'] === self::STATUS_IGNORE && $res[0]['initiator'] === $toUser) {
                    return false;
                }

                return $this->del();
            }

            if ($status === self::STATUS_IGNORE) {
                $this->set('status', $status);
                return $this->save();
            }

            switch ($res[0]['status']) {
                case self::STATUS_IGNORE:
                    //you may only delete record, if status is "ignore"
                    return false;
                case self::STATUS_PENDING:
                    if($res[0]['initiator'] === $fromUser) {
                        //you may only delete record or change to ignore, if status is "pending" and you are initiator
                        return false;
                    } else {
                        $this->set('status', $status);
                        $out = $this->save();
                    }
                    break;
                case self::STATUS_FRIEND:
                    if ($status === self::STATUS_FRIEND) {
                        $out = true;
                    } else {
                        if ($res[0]['initiator'] === $fromUser) {
                           $this->set('initiator', $toUser);
                           $this->set('status', self::STATUS_SUBSCRIBER);
                           $out = $this->save();
                        } else {
                            $this->set('status', self::STATUS_SUBSCRIBER);
                            $out = $this->save();
                        }
                    }
                    break;
                case self::STATUS_SUBSCRIBER:
                    if ($status === self::STATUS_SUBSCRIBER) {
                        $out = true;
                    } else {
                        if ($res[0]['initiator'] === $toUser) {
                            $this->set('status', self::STATUS_FRIEND);
                            $out = $this->save();
                        } else {
                            return false;
                        }
                    }
                    break;
            }

            return $out;
        }
        //Create new record
        else {
            if ($status === self::STATUS_DELETE) {
                return true;
            }

            if ($status !== self::STATUS_IGNORE) {
                $status = self::STATUS_PENDING;
            }

            $this->set([
                'parent_id' => $parentId,
                'child_id' => $childId,
                'status' => $status,
                'initiator' => $fromUser,
                'type' => self::TYPE_OTHER
            ]);

            return $this->save();
        }
    }
}