<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * API v1 Admin Files Controller Class
 */

namespace app\Controllers\vendor\api\v1;

use iceCMS2\Controller\AbstractController;
use iceCMS2\Controller\ControllerInterface;
use iceCMS2\DTO\FileImageSizeListAdminDto;
use iceCMS2\Models\User;

class AdminImageSizes extends AbstractController implements ControllerInterface
{
    public function list(): void
    {
        $this->_authorizationCheckRole([User::ROLE_ADMIN]);
        $nullDto = new FileImageSizeListAdminDto();
    }
}