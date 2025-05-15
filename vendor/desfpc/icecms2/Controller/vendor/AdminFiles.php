<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Admin Files Controller Class
 */

namespace app\Controllers\vendor;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;

class AdminFiles extends AbstractController implements ControllerInterface
{
    /** @var bool Is full width layout */
    protected const IS_FULL_WIDTH = true;

    /** @var string */
    public string $title = 'Files';

    /**
     * Default main method
     *
     * @throws Exception
     */
    public function main(): void
    {
        $this->_authorizationCheckRole([User::ROLE_MODERATOR, User::ROLE_ADMIN]);

        $this->breadcrumbs = [
            ['title' => 'Admin dashboard', 'url' => '/admin/'],
            ['title' => 'Files', 'url' => '/files/']
        ];

        $this->renderTemplate('main');
    }

    /**
     * Edit user method
     *
     * @return void
     * @throws Exception
     */
    public function edit(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);

        $id = (int) $this->routing->pathInfo['query_vars']['id'];
        if (empty($id)) {
            $this->_redirect('/404');
        }

        $this->templateData['id'] = $id;

        $this->breadcrumbs = [
            ['title' => 'Admin dashboard', 'url' => '/admin/'],
            ['title' => 'Files', 'url' => '/admin/files/'],
            ['title' => 'Edit file', 'url' => '/files/edit/']
        ];

        $this->renderTemplate('edit');
    }
}