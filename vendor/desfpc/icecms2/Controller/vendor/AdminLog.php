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
use iceCMS2\Commands\Logs\ClearAllLogs;
use iceCMS2\Commands\Logs\ClearOnPeriodLogs;
use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;
use iceCMS2\Tools\FlashVars;

class AdminLog extends AbstractController implements ControllerInterface
{
    /** @var string  */
    public string $title = 'Logs';
    
    /** @var string  */
    private const PATH = '../logs';

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
            ['title' => 'Logs', 'url' => '/logs/']
        ];

        $this->renderTemplate('main');
    }

    /**
     * @return void
     */
    public function getLogNameFiles(): void
    {
        $dir = dir(self::PATH);
        
        $i = 0;
        while (false !== ($files = $dir->read())) {
            if ($files === '.' || $files === '..' || $files === '.gitkeep') {
                continue;
            }
            echo "<p id='log-$i' class='log' data-file='$files'>$files</p>";
            $i++;
        }

        if(0 === $i ){
            echo "<p class='mt-5 alert alert-danger'>Логов пока нет</p>";
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function clearAllLogs(): void
    {
        $result = ClearAllLogs::main();

        $this->getMessage($result, 'Clear All Logs');

        $this->_redirect('/admin/logs/');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function clearOnPeriodLogs(): void
    {
        $result = ClearOnPeriodLogs::main();

        $this->getMessage($result, 'Cleared the logs for the period');

        $this->_redirect('/admin/logs/');
    }

    /**
     * @param bool $result
     * @param string $message
     *
     * @return void
     */
    private function getMessage(bool $result, string $message): void
    {
        $flashVars = new FlashVars();

        if($result){
            $flashVars->set('success', $message);
        } else {
            $flashVars->set('error', 'Sorry, but you don`t have logs.');
        }
    }
}