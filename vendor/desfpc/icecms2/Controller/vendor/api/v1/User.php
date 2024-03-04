<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * API v1 User Controller Class
 */

namespace app\Controllers\vendor\api\v1;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\FileImage;
use iceCMS2\Tools\Exception;
use iceCMS2\Models\User as UserModel;

class User extends AbstractController implements ControllerInterface //TODO create USER service, move logic to it
{
    public const LOGIC_STATUS_FRIENDS = 'friends';
    public const LOGIC_STATUS_SUBSCRIBERS = 'subscribers';
    public const LOGIC_STATUS_SUBSCRIPTIONS = 'subscriptions';
    public const LOGIC_STATUS_IGNORE = 'ignore';
    public const LOGIC_STATUS_REQUESTS = 'requests';
    public const LOGIC_STATUS_CONFIRMATIONS = 'confirmations';
    public const LOGIC_STATUSES = [
        self::LOGIC_STATUS_FRIENDS,
        self::LOGIC_STATUS_SUBSCRIBERS,
        self::LOGIC_STATUS_SUBSCRIPTIONS,
        self::LOGIC_STATUS_IGNORE,
        self::LOGIC_STATUS_REQUESTS,
        self::LOGIC_STATUS_CONFIRMATIONS,
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_FRIEND = 'friend';
    public const STATUS_SUBSCRIBER = 'subscriber';
    public const STATUS_IGNORE = 'ignore';
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_FRIEND,
        self::STATUS_SUBSCRIBER,
        self::STATUS_IGNORE,
    ];

    public const TYPE_FRIEND = 'friend';
    public const TYPE_FAMILY = 'family member';
    public const TYPE_TEAMMATE = 'teammate';
    public const TYPE_OTHER = 'other';
    public const TYPES = [
        self::TYPE_FRIEND,
        self::TYPE_FAMILY,
        self::TYPE_TEAMMATE,
        self::TYPE_OTHER,
    ];

    /** @var array Logic Status rules for real db user_friends status and initiator  */
    public const LOGIC_STATUS_RULES = [
        self::LOGIC_STATUS_FRIENDS => [
            [
                'status' => self::STATUS_FRIEND,
                'ids' => true,
            ],
        ],
        self::LOGIC_STATUS_SUBSCRIBERS => [
            [
                'status' => self::STATUS_SUBSCRIBER,
                'initiator' => false,
            ],
            [
                'status' => self::STATUS_PENDING,
                'initiator' => false,
            ],
        ],
        self::LOGIC_STATUS_SUBSCRIPTIONS => [
            [
                'status' => self::STATUS_SUBSCRIBER,
                'initiator' => true,
            ],
            [
                'status' => self::STATUS_PENDING,
                'initiator' => true,
            ],
        ],
        self::LOGIC_STATUS_IGNORE => [
            [
                'status' => self::STATUS_IGNORE,
                'initiator' => true,
            ],
        ],
        self::LOGIC_STATUS_REQUESTS => [
            [
                'status' => self::STATUS_PENDING,
                'initiator' => true,
            ],
        ],
        self::LOGIC_STATUS_CONFIRMATIONS => [
            [
                'status' => self::STATUS_PENDING,
                'initiator' => false,
            ],
        ],
    ];

    public string $title = 'User';

    /**
     * Get SQL query by logic status for getting user connections list TODO move to User model
     *
     * @param string|null $logicStatus
     * @param int $userId
     * @return array
     * @throws Exception
     */
    private function _makeLogicStatusRulesQuery(?string $logicStatus, int $userId): array
    {
        $query = '';
        $bindValues = [];

        if (!is_null($logicStatus)) {
            if (empty(self::LOGIC_STATUS_RULES[$logicStatus])) {
                throw new Exception('Wrong logic status');
            }

            $rules = self::LOGIC_STATUS_RULES[$logicStatus];

            foreach ($rules as $ruleKey => $rule) {
                $prefix = $logicStatus . '_' . $ruleKey;

                if (!empty($query)) {
                    $query .= ' UNION ALL ';
                }

                $query .= '(SELECT `' . $prefix . '`.`parent_id`, `' . $prefix . '`.`child_id`, `' . $prefix . '`.`initiator`
                FROM `user_friends` `' . $prefix . '`
            WHERE 1 = 1';

                foreach ($rule as $key => $value) {

                    $addQuery = true;

                    if ($key === 'status') {
                        $operand = '=';
                        $bindValues[] = $value;
                    } elseif ($key === 'ids') {
                        $addQuery = false;
                        $query .= ' AND (`' . $prefix . '`.`parent_id` = ' . $userId . ' OR `' . $prefix . '`.`child_id` = ' . $userId . ')';
                    }
                    else {
                        if (!is_null($value)) {
                            if ($value === true) {
                                $operand = '=';
                            } else {
                                $operand = '<>';
                            }
                            $bindValues[] = $userId;
                        } else {
                            $addQuery = false;
                        }
                    }

                    if ($addQuery) {
                        $query .= ' AND `' . $prefix . '`.`' . $key . '` ' . $operand . ' ?';
                    }
                }

                $query .= ')';
            }
        }

        $query = 'SELECT `t`.*, 
       `p`.`nikname` `parent_nik`, `p`.`avatar` `parent_avatar`,
       `c`.`nikname` `child_nik`, `c`.`avatar` `child_avatar`
        FROM (' . $query . ') `t`
        INNER JOIN `users` `p` ON `p`.`id` = `t`.`parent_id`
        INNER JOIN `users` `c` ON `c`.`id` = `t`.`child_id`;';

        return [$query, $bindValues];
    }

    /**
     * Return list of user friends (or pendings/subscribers/ignore)
     *
     * @return void
     * @throws Exception
     */
    public function friends(): void
    {
        $this->_authorizationCheck();

        /** @var UserModel $user */
        $user = $this->authorization->getUser();

        $this->requestParameters->getRequestValues(['logicStatus', 'type']);

        if (empty($this->requestParameters->values->logicStatus)) {
            $logicStatus = null;
        } else {
            $logicStatus = $this->requestParameters->values->logicStatus;
        }

        if (empty($this->requestParameters->values->type)) {
            $type = null;
        } else {
            $type = $this->requestParameters->values->type;
        }

        if (is_null($logicStatus) || !in_array($logicStatus, self::LOGIC_STATUSES)) {
            throw new Exception('Wrong logic status');
        }

        if (!is_null($type) && !in_array($type, self::TYPES)) {
            throw new Exception('Wrong type');
        }

        try {
            [$query, $values] = $this->_makeLogicStatusRulesQuery($logicStatus ?? self::LOGIC_STATUS_FRIENDS, (int)$user->get('id'));

            /*var_dump($query, $values);
            die();*/

            if ($friends = $this->_db->queryBinded($query, $values)) {
                $this->renderJson($friends, true);
            } else {
                $this->renderJson(['message' => $this->_db->getErrorText()], false);
            }
        } catch (\Throwable $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
        }
    }

    /**
     * Return list of users (full entities) by IDs //TODO caching
     *
     * @return void
     */
    public function list(): void
    {
        $this->requestParameters->getRequestValues(['users']);
        if (empty($this->requestParameters->values->users)) {
            $this->renderJson(['message' => 'Empty users ids string'], false);
            return;
        }

        $list = explode(',', $this->requestParameters->values->users);

        try {
            $list = array_map(function ($userId) {
                $user = new UserModel($this->settings);
                if (!$user->load((int)$userId)) {
                    throw new Exception('Wrong User ID');
                }
                $userValues = $user->get();
                unset($userValues['password']);
                return $userValues;
            }, $list);
        } catch (Exception $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
            return;
        }

        $this->renderJson($list, true);
    }

    /**
     * Return User by ID
     *
     * @return void
     * @throws Exception
     */
    public function get(): void
    {
        if (!isset($this->routing->pathInfo['query_vars']['id'])) {
            $this->renderJson(['message' => 'No User ID passed'], false);
            return;
        }

        $userId = $this->routing->pathInfo['query_vars']['id'];

        try {
            $user = new UserModel($this->settings);
        } catch (Exception $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
            return;
        }

        if (!$user->load((int)$userId)) {
            $this->renderJson(['message' => 'Wrong User ID'], false);
            return;
        }

        $out = $user->get();
        unset($out['password']);

        $this->renderJson($out, true);
    }

    /**
     * Upload user avatar
     *
     * @return void
     * @throws Exception
     */
    public function uploadAvatar(): void
    {
        $this->_authorizationCheck();

        /** @var UserModel $user */
        $user = $this->authorization->getUser();

        $file = new FileImage($this->settings);

        if ($file->savePostFile('file')) {
            if (!is_null($user->get('avatar'))) {
                $oldAvatar = new FileImage($this->settings);
                $oldAvatar->load((int)$user->get('avatar'));
                $oldAvatar->del();
            }

            $fileId = $file->get('id');

            $user->set('avatar', $fileId);
            $user->save();
            $user->loadAvatar();
            $this->renderJson(['file' => $fileId, 'url' => $user->avatarUrl], true);
        } else {
            $this->renderJson(['message' => 'Error in avatar uploading'], false);
        }
    }

    /**
     * Update user password
     *
     * @return void
     * @throws Exception
     */
    public function changePassword(): void
    {
        $this->_authorizationCheck();

        /** @var UserModel $user */
        $user = $this->authorization->getUser();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data) || empty($data['old']) || empty($data['new'])) {
            $this->renderJson(['message' => 'Empty passwords', 'input' => $data], false);
            return;
        }

        if ($data['old'] === $data['new']) {
            $this->renderJson(['message' => 'Old and new passwords are equal'], false);
            return;
        }

        if (!$user->checkPassword($data['old'])) {
            $this->renderJson(['message' => 'Wrong old password'], false);
            return;
        }

        try {
            $user->set('password', $data['new']);
            if ($user->save()) {
                $this->renderJson(['message' => 'Password updated'], true);
            } else {
                $this->renderJson(['message' => 'Error in password updating'], false);
            }
        } catch (Exception $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
        }
    }

    /**
     * Update user profile
     *
     * @return void
     */
    public function updateProfile(): void
    {
        $this->_authorizationCheck();

        /** @var UserModel $user */
        $user = $this->authorization->getUser();
        $data = json_decode(file_get_contents('php://input'), true);
        $unsetArr = ['avatar', 'email_approve_code', 'email_approved', 'email_send_time', 'phone_approve_code',
            'password', 'phone_approved', 'phone_send_time', 'created_at',];

        foreach ($unsetArr as $unset) {
            if (isset($data[$unset])) {
                unset($data[$unset]);
            }
        }

        if (isset($data['contacts'])) {
            try {
                $data['contacts'] = json_encode($data['contacts']);
            } catch (\Throwable $e) {
                $data['contacts'] = [];
            }
        }

        try {
            $user->set($data);
            if ($user->save()) {
                $this->renderJson(['message' => 'Profile updated'], true);
            } else {
                $this->renderJson(['message' => 'Error in profile updating', 'errors' => $user->errors], false);
            }
        } catch (Exception $e) {
            $this->renderJson(['message' => $e->getMessage()], false);
            return;
        }
    }
}