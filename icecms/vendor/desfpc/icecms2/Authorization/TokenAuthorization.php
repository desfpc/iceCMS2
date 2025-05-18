<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Token Authorization
 */

namespace iceCMS2\Authorization;

use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;

class TokenAuthorization extends AbstractAuthorization implements AuthorizationInterface
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function authorizeRequest(?array $params = null): bool
    {
        if (isset($_SERVER['HTTP_TOKEN'])) {

            if ($payload = JWT::checkJWT($this->_settings, $_SERVER['HTTP_TOKEN'], 'access')) {
                $user = new User($this->_settings);
                if ($user->loadByParam('id', $payload->getUid())) {
                    self::$_user = $user;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Update Token
     *
     * @return bool|array
     * @throws Exception
     */
    public function refreshToken(): bool|array
    {
        if (isset($_SERVER['HTTP_TOKEN'])) {
            if ($payload = JWT::checkJWT($this->_settings, $_SERVER['HTTP_TOKEN'], 'refresh')) {
                $accessToken = JWT::getJWT($this->_settings, $payload->getUid(), 'access');
                $refreshToken = JWT::getJWT($this->_settings, $payload->getUid(), 'refresh');

                return [
                    'accessToken' => $accessToken,
                    'refreshToken' => $refreshToken
                ];
            }
        }

        return false;
    }
}