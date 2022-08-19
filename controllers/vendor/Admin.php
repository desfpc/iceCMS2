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

class Admin extends AbstractController implements ControllerInterface
{
    public string $title = 'Admin';
}