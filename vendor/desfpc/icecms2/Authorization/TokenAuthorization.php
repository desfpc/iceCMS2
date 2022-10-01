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
    public function authorizeRequest(): bool
    {
        if (isset($_SERVER['HTTP_ACCESS_TOKEN'])) {
            if ($payload = JWT::checkJWT($this->_settings, $_SERVER['HTTP_ACCESS_TOKEN'], 'access')) {
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
        if (isset($_SERVER['HTTP_REFRESH_TOKEN'])) {
            if ($payload = JWT::checkJWT($this->_settings, $_SERVER['HTTP_REFRESH_TOKEN'], 'refresh')) {
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

    /**
     * Get tokens by password
     *
     * @param string $email
     * @param string $password
     * @return bool|array
     * @throws Exception
     */
    public function getTokens(string $email, string $password): bool|array
    {
        if ($this->_passwordAuth($email, $password)) {
            $accessToken = JWT::getJWT($this->_settings, (int)self::$_user->get('id'), 'access');
            $refreshToken = JWT::getJWT($this->_settings, (int)self::$_user->get('id'), 'refresh');

            return [
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken
            ];
        }

        return false;
    }
}