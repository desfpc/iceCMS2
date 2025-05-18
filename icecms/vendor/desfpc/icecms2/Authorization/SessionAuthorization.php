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
    public function authorizeRequest(?array $params = null): bool
    {
        if (isset($_REQUEST['email']) && isset($_REQUEST['password'])) {
            if ($this->_passwordAuth($_REQUEST['email'], $_REQUEST['password'])) {
                $_SESSION['user'] = self::$_user->get('id');
                $_SESSION['locale'] = self::$_user->get('language');
                return true;
            } else {
                $this->errors[] = 'Wrong email or password';
            }
        } elseif (isset($_SESSION['user'])) {
            $user = new User($this->_settings);
            if ($user->load((int)$_SESSION['user'])) {
                self::$_user = $user;
                return true;
            } else {
                $this->errors[] = 'Wrong session';
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function exitAuth(): bool
    {
        unset($_SESSION['user']);
        self::$_user = null;
        return true;
    }
}