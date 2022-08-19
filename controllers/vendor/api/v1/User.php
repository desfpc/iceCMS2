<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * API v1 User Controller Class
 */

namespace app\Controllers\vendor\api\v1;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;

class User extends AbstractController implements ControllerInterface
{
    public string $title = 'User';

    /**
     * TODO Return list of users JSON
     *
     * @return void
     */
    public function list(): void
    {
        $list = [1,2,3];
        $this->renderJson($list, true);
    }

    /**
     * TODO Return User by ID
     *
     * @return void
     */
    public function get(): void
    {
        if (!isset($this->routing->pathInfo['query_vars']['id'])) {
            $this->renderJson(['message' => 'No User ID passed'], false);
            return;
        }

        $id = $this->routing->pathInfo['query_vars']['id'];
        $this->renderJson(['userId' => $id], true);
    }
}