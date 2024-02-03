<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * API v1 Admin User Controller Class
 */

namespace app\Controllers\vendor\api\v1;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\DTO\UserListAdminDto;
use iceCMS2\Helpers\Strings;
use iceCMS2\Models\User;
use iceCMS2\Models\User as UserModel;
use iceCMS2\Models\UserList;
use iceCMS2\Tools\Exception;

class AdminUser extends AbstractController implements ControllerInterface
{
    private const ROWS_COUNT = 20;

    /**
     * Delete User by ID
     *
     * @return void
     * @throws Exception
     */
    public function delete(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);

        if (!isset($this->routing->pathInfo['query_vars']['id'])) {
            $this->renderJson(['message' => 'No User ID passed'], false);
            return;
        }

        $userId = (int)$this->routing->pathInfo['query_vars']['id'];

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

        if ($user->del()) {
            $this->renderJson(['message' => 'User deleted'], true);
        } else {
            $this->renderJson(['message' => 'User not deleted'], false);
        }
    }

    /**
     * Admin User list
     *
     * @return void
     * @throws Exception
     */
    public function list(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $nullDto = new UserListAdminDto();

        // Columns
        $out = [
            'cols' => [
                [
                    'id' => 'id',
                    'name' => 'ID',
                    'ordered' => true,
                    'action' => 'link',
                    'actionUrl' => '/admin/user/{id}/edit/',
                    'icon' => 'arrow-return-right',
                ],
                [
                    'id' => 'email',
                    'name' => 'Email',
                    'ordered' => true,
                ],
                [
                    'id' => 'telegram',
                    'name' => 'Telegram',
                    'ordered' => true,
                    'editable' => true,
                    'editUrl' => '/api/v1/admin/user/{id}/edit',
                    'inputType' => 'input',
                ],
                [
                    'id' => 'role',
                    'name' => 'Role',
                    'ordered' => true,
                    'editable' => true,
                    'editUrl' => '/api/v1/admin/user/{id}/edit',
                    'inputType' => 'select',
                    'selectArray' => [
                        'user' => 'User',
                        'moderator' => 'Moderator',
                        'admin' => 'Admin',
                    ],
                ],
                [
                    'id' => 'status',
                    'name' => 'Status',
                    'ordered' => true,
                    'editable' => true,
                    'editUrl' => '/api/v1/admin/user/{id}/edit',
                    'inputType' => 'select',
                    'selectArray' => [
                        'created' => 'Created',
                        'active' => 'Active',
                        'deleted' => 'Deleted',
                    ],
                ],
                [
                    'id' => 'created_time',
                    'name' => 'Created Time',
                    'ordered' => true,
                ],
                [
                    'id' => 'actions',
                    'name' => 'Actions',
                    'ordered' => false,
                    'buttons' => [
                        [
                            'name' => '',
                            'icon' => 'eye',
                            'action' => 'link',
                            'actionUrl' => '/user/{id}',
                            'class' => 'btn btn-primary btn-sm me-1',
                            'description' => 'View user profile',
                        ],
                        [
                            'name' => '',
                            'icon' => 'pencil',
                            'action' => 'link',
                            'actionUrl' => '/admin/user/{id}/edit',
                            'class' => 'btn btn-warning btn-sm me-1',
                            'description' => 'Edit user',
                        ],
                        [
                            'name' => '',
                            'icon' => 'trash',
                            'action' => 'ajax',
                            'actionUrl' => '/api/v1/admin/user/{id}/delete',
                            'class' => 'btn btn-danger btn-sm',
                            'description' => 'Delete user',
                            'confirm' => 'Are you sure?',
                        ],
                    ],
                ],
            ]
        ];

        $this->requestParameters->getRequestValues(['page','filters','limit','order']);

        if (!empty($this->requestParameters->values->page)) {
            $page = (int)$this->requestParameters->values->page;
        } else {
            $page = 1;
        }

        // Filters
        $conditionsArr = [];
        $filters = [
            'search' => [
                'id' => 'search',
                'name' => 'Search',
                'type' => 'string',
                'value' => '',
            ],
            'role' => [
                'id' => 'role',
                'name' => 'Role',
                'type' => 'select',
                'value' => '',
                'array' => [
                    'all' => ['name' => 'All', 'value' => ''],
                    'user' => ['name' => 'User', 'value' => 'user'],
                    'moderator' => ['name' => 'Moderator', 'value' => 'moderator'],
                    'admin' => ['name' => 'Admin', 'value' => 'admin'],
                ],
            ],
            'status' => [
                'id' => 'status',
                'name' => 'Status',
                'type' => 'select',
                'value' => '',
                'array' => [
                    'all' => ['name' => 'All', 'value' => ''],
                    'created' => ['name' => 'Created', 'value' => 'created'],
                    'active' => ['name' => 'Active', 'value' => 'active'],
                    'deleted' => ['name' => 'Deleted', 'value' => 'deleted'],
                ],
            ],
        ];

        if (
            !empty($this->requestParameters->values->filters)
            && ($filtersArr = Strings::paramStringToJson($this->requestParameters->values->filters))
        )
        {
            foreach ($filtersArr as $filterKey => $filterValue) {
                if (isset($filters[$filterKey]) && !empty($filterValue['value'])) {

                    $filters[$filterKey]['value'] = $filterValue['value'];

                    $this->_changeConditionsArr($conditionsArr, $filterKey, $filterValue['value']);
                }
            }
        }

        // Order
        $orderQuery = [
            'col' => 'id',
            'order' => 'DESC',
        ];

        if (
            !empty($this->requestParameters->values->order)
            && ($orderArr = Strings::paramStringToJson($this->requestParameters->values->order))
            && !empty($orderArr['col'])
            && !empty($orderArr['order'])
        ) {
            $orderQuery = $orderArr;
        }

        $order = [$orderQuery['col'] => $orderQuery['order']];

        $offset = ($page - 1) * self::ROWS_COUNT;

        $userList = new UserList(
            $this->settings, $this->_makeConditions($conditionsArr), $order, $offset, (self::ROWS_COUNT + 1), 0, true
        );
        $users = $userList->getDtoFields($nullDto);

        if (count($users) === (self::ROWS_COUNT + 1)) {
            $out['nextPage'] = $page + 1;
            array_pop($users);
        } else {
            $out['nextPage'] = null;
        }

        $out['rows'] = $users;
        $out['order'] = $orderQuery;
        $out['filters'] = $filters;

        $this->renderJson($out, true);
    }

    /**
     * @param array $conditionsArr
     * @param string $filterKey
     * @param string $filterValue
     * @return void
     */
    private function _changeConditionsArr(array &$conditionsArr, string $filterKey, string $filterValue): void
    {
        if ($filterValue !== '') {
            $conditionsArr[$filterKey] = $filterValue;
        }
    }

    /**
     * @param array $conditionsArr
     * @return array
     */
    private function _makeConditions(array $conditionsArr): array
    {
        $conditions = [];

        if (!empty($conditionsArr['search'])) {
            $conditions['email'] = [
                'logic' => 'AND',
                'sign' => 'LIKE',
                'value' => '%' . mb_strtolower($conditionsArr['search'], 'UTF-8') . '%',
            ];
        }

        if (!empty($conditionsArr['role'])) {
            $conditions['role'] = $conditionsArr['role'];
        }

        if (!empty($conditionsArr['status'])) {
            $conditions['status'] = $conditionsArr['status'];
        }

        return $conditions;
    }
}