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


use iceCMS2\Caching\CachingFactory;
use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;
use iceCMS2\Tools\FlashVars;

class AdminCaches extends AbstractController implements ControllerInterface
{
    /** @var bool Is full width layout */
    protected const IS_FULL_WIDTH = true;

    public string $title = 'Caches';

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
            ['title' => 'Caches', 'url' => '/caches/']
        ];

        $this->renderTemplate('main');
    }

    /**
     * Clear all caches
     *
     * @return void
     * @throws Exception
     */
    public function clearAllCaches(): void
    {
        $this->_authorizationCheckRole([User::ROLE_MODERATOR, User::ROLE_ADMIN]);

        $cacher = CachingFactory::instance($this->settings);
        $keys = $cacher->findKeys($this->settings->db->name . '*');

        $errors = [];

        if (!empty($keys)) {
            foreach ($keys as $key) {

                if (!$cacher->del($key)) {
                    $errors[] = $key;
                }
            }
        }

        $flashVars = new FlashVars();

        if (empty($errors)) {
            $flashVars->set('success', 'All caches cleared');
        } else {
            $flashVars->set('error', 'Some caches (' . count($errors) . ') not cleared');
        }

        $this->_redirect('/admin/caches/');
    }

    /**
     * Clear PHP caches
     *
     * @return void
     * @throws Exception
     */
    public function clearPHPCaches(): void
    {
        $this->_authorizationCheckRole([User::ROLE_MODERATOR, User::ROLE_ADMIN]);

        opcache_reset();
        clearstatcache();



        $flashVars = new FlashVars();
        $flashVars->set('success', 'PHP caches cleared');

        $this->_redirect('/admin/caches/');
    }
}