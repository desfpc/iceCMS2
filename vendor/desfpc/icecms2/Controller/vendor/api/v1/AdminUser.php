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
use iceCMS2\Models\User;
use iceCMS2\Models\UserList;
use iceCMS2\Tools\Exception;

class AdminUser extends AbstractController implements ControllerInterface
{
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

        $out = [
            'cols' => [
                [
                    'id' => 'id',
                    'name' => 'ID',
                    'ordered' => true,
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
                ],
            ]
        ];

        $this->requestParameters->getRequestValues(['page','filters','limit','order']);

        if (!empty($this->requestParameters->values->page)) {
            $page = (int)$this->requestParameters->values->page;
        } else {
            $page = 1;
        }

        $conditions = [];
        $orderQuery = [
            'col' => 'id',
            'order' => 'DESC',
        ];

        if (
            !empty($this->requestParameters->values->order)
            && ($orderArr = $this->_paramStringToJson($this->requestParameters->values->order))
            && !empty($orderArr['col'])
            && !empty($orderArr['order'])
        ) {
            $orderQuery = $orderArr;
        }

        $order = [$orderQuery['col'] => $orderQuery['order']];

        $userList = new UserList($this->settings, $conditions, $order, $page, 2);
        $users = $userList->getDtoFields($nullDto);

        $out['rows'] = $users;
        $out['order'] = $orderQuery;

        $this->renderJson($out, true);
    }

    /**
     * Decode GET param string to JSON
     *
     * @param string $param
     * @return false|array
     */
    private function _paramStringToJson(string $param): false|array
    {
        $out = [];
        if (!empty($param)) {
            $out = json_decode(htmlspecialchars_decode(urldecode($param)), true);
        }
        return $out;
    }
}