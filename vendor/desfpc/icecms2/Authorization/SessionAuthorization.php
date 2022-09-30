<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Session Authorization
 */

namespace iceCMS2\Authorization;

use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;

class SessionAuthorization extends AbstractAuthorization implements AuthorizationInterface
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function authorizeRequest(): bool
    {
        if (isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
            $user = new User($this->_settings);
            if ($user->loadByParam('email', $_REQUEST['email'])) {
                if ($user->checkPassword($_REQUEST['password'])) {
                    self::$_user = $user;
                    $_SESSION['user'] = $user->get('id');
                    return true;
                }
            }
        } elseif (isset($_SESSION['user'])) {
            $user = new User($this->_settings);
            if ($user->load((int)$_SESSION['user'])) {
                self::$_user = $user;
                return true;
            }
        }

        return false;
    }
}