<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Notifications Controller Class
 */

namespace app\Controllers\vendor;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;
use iceCMS2\Tools\FlashVars;

class Profile extends AbstractController implements ControllerInterface
{
    /** @var FlashVars Flashvars for alerts and errors */
    private FlashVars $flashVars;

    public string $title = 'Profile';

    public function main(): void
    {
        $this->flashVars = new FlashVars();

        if (!$this->authorization->getAuthStatus()) {
            $this->flashVars->set('error', 'Only authorized users can view this page');
            $this->_redirect('/authorize');
        }

        $this->_redirect('/profile/' . $this->authorization->getUser()->get('id'));
    }

    /**
     * @throws Exception
     */
    public function user(): void
    {
        $this->flashVars = new FlashVars();

        if (!$this->authorization->getAuthStatus()) {
            $this->flashVars->set('error', 'Only authorized users can view this page');
            $this->_redirect('/authorize');
        }
        $this->flashVars = new FlashVars();

        $id = (int) $this->routing->pathInfo['query_vars']['id'];
        if (empty($id)) {
            $this->flashVars->set('error', 'User not found');
            $this->_redirect('/404');
        }

        $user = new User($this->settings);
        try {
            if (!$user->load($id)) {
                $this->flashVars->set('error', 'User not found');
                $this->_redirect('/404');
            }
            $user->loadAvatar();
        } catch (Exception $e) {
            $this->flashVars->set('error', $e->getMessage());
            $this->_redirect('/500');
        }
        $this->templateData['user'] = $user;
        $this->renderTemplate('main');
    }
}