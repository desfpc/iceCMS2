<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Abstract Authorization
 */

namespace iceCMS2\Authorization;

use iceCMS2\Models\User;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class AbstractAuthorization implements AuthorizationInterface
{
    /** @var Settings */
    protected Settings $_settings;

    /** @var User|null */
    protected static ?User $_user = null;

    /** @var array */
    public array $errors = [];

    /**
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->_settings = $settings;
    }

    /**
     * Authorize user by email and password
     *
     * @param string $email
     * @param string $password
     * @return bool
     * @throws Exception
     */
    protected function _passwordAuth(string $email, string $password): bool
    {
        $user = new User($this->_settings);
        if ($user->loadByParam('email', $email)) {
            if ($user->checkPassword($password)) {
                self::$_user = $user;
                return true;
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
                'refreshToken' => $refreshToken,
                'id' => self::$_user->get('id'),
            ];
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function authorizeRequest(?array $params = null): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getAuthStatus(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE){
            session_start();
        }

        if (is_null(self::$_user)) {
            if (!empty($_SESSION['user'])) {
                $user = new User($this->_settings);
                if ($user->load((int)$_SESSION['user'])) {
                    self::$_user = $user;
                }
            }
        }

        return !is_null(self::$_user);
    }

    /**
     * @inheritDoc
     */
    public function getUser(): ?User
    {
        return self::$_user;
    }

    /**
     * @inheritDoc
     */
    public function exitAuth(): bool
    {
        self::$_user = null;
        return true;
    }
}