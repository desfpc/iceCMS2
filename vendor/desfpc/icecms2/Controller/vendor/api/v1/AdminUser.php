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

        $this->requestParameters->getRequestValues(['page','']);

        $userList = new UserList($this->settings);
        $users = $userList->get();

        $this->renderJson([$users], true);
    }
}