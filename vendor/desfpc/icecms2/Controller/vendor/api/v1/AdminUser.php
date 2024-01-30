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
use iceCMS2\Models\UserList;
use iceCMS2\Tools\Exception;

class AdminUser extends AbstractController implements ControllerInterface
{
    private const ROWS_COUNT = 2;

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
                    'id' => 'role',
                    'name' => 'Role',
                    'ordered' => true,
                ],
                [
                    'id' => 'status',
                    'name' => 'Status',
                    'ordered' => true,
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

        $userList = new UserList(
            $this->settings, $this->_makeConditions($conditionsArr), $order, $page, (self::ROWS_COUNT + 1)
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