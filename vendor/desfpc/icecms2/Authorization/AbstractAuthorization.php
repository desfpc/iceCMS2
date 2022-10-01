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

class AbstractAuthorization implements AuthorizationInterface
{
    /** @var Settings */
    protected Settings $_settings;

    /** @var User|null */
    protected static ?User $_user = null;

    public function __construct(Settings $settings)
    {
        $this->_settings = $settings;
    }

    /**
     * @inheritDoc
     */
    public function authorizeRequest(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getAuthStatus(): bool
    {
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