<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Admin Caches Controller Class
 */

namespace app\Controllers\vendor;


use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;

class AdminCaches extends AbstractController implements ControllerInterface
{
    public string $title = 'Caches';

    /** Default main method
     * @throws Exception
     */
    public function main(): void
    {
        $this->_authorizationCheckRole([User::ROLE_MODERATOR, User::ROLE_ADMIN]);

        $this->breadcrumbs = [
            ['title' => 'Main', 'url' => '/'],
            ['title' => 'Caches', 'url' => '/caches/']
        ];

        $this->renderTemplate('main');
    }
}