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

use iceCMS2\Commands\Logs\ClearLogs;
use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\Locale\LocaleText;
use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;
use iceCMS2\Tools\FlashVars;

class AdminLogs extends AbstractController implements ControllerInterface
{
    /** @var string */
    public string $title = 'Logs';

    /** @var string */
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
     * @throws Exception
     */
    public function instance(): void
    {
        match ($this->settings->logs->type) {
            'db' => $this->getLogInDB(),
            default => $this->getLogNameFiles()
        };
    }

    /**
     * @return void
     * @throws Exception
     */
    public function getLogInDB(): void
    {
        $query = 'SELECT DATE(created_time) AS created_time, alias FROM logs GROUP BY DATE(created_time), alias';

        $res = $this->_db->query($query);

        $i = 0;
        foreach ($res as $item) {
            echo "<p id='log-$i' class='log' 
                    data-alias='{$item['alias']}'
                    data-created_time='{$item['created_time']}'>
                    {$item['alias']} - {$item['created_time']}</p>";
            $i++;
        }

        if (0 === $i) {
            $notice = LocaleText::get($this->settings, 'log/notices/No logs yet', [], $this->settings->locale);
            echo "<p class='mt-5 alert alert-danger'>$notice</p>";
        }
    }

    /**
     * @return void
     * @throws Exception
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

        if (0 === $i) {
            $notice = LocaleText::get($this->settings, 'log/notices/No logs yet', [], $this->settings->locale);
            echo "<p class='mt-5 alert alert-danger'>$notice</p>";
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function clearAllLogs(): void
    {
        $result = ClearLogs::clearAllLogs();

        $this->getMessage($result, 'Clear All Logs');

        $this->_redirect('/admin/logs/');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function clearOnPeriodLogs(): void
    {
        $result = ClearLogs::ClearOnPeriodLogs();

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

        if (is_bool($result) && $result) {
            $flashVars->set('success', $message);
        } else {
            $flashVars->set('error', 'Sorry, but you don`t have logs.');
        }
    }
}