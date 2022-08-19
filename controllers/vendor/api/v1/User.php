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
     * Return list of users JSON
     *
     * @return void
     */
    public function list(): void
    {
        $list = [1,2,3];
        $this->renderJson($list, true);
    }
}