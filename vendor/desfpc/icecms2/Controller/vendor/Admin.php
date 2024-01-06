<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Admin Controller Class
 */

namespace app\Controllers\vendor;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;

class Admin extends AbstractController implements ControllerInterface
{
    public string $title = 'Admin dashboard';

    /** Default main method - only render default template
     * @throws Exception
     */
    public function main(): void
    {
        $this->_authorizationCheckRole([User::ROLE_MODERATOR, User::ROLE_ADMIN]);
        //$this->_authorizationCheck(); //for authorization check without special role(s)

        $this->breadcrumbs = [
            ['title' => 'Admin dashboard', 'url' => '/'],
            ['title' => 'Admin dashboard', 'url' => '/admin/']
        ];

        $this->renderTemplate('main');
    }
}