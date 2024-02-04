<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Admin Users Controller Class
 */

namespace app\Controllers\vendor;


use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;

class AdminUsers extends AbstractController implements ControllerInterface
{
    public string $title = 'Users';

    /**
     * Default main method
     *
     * @throws Exception
     */
    public function main(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);

        $this->breadcrumbs = [
            ['title' => 'Admin dashboard', 'url' => '/admin/'],
            ['title' => 'Users', 'url' => '/users/']
        ];

        $this->renderTemplate('main');
    }
}