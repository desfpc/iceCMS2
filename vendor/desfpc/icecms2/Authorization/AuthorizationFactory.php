<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Authorization Factory
 */

namespace iceCMS2\Authorization;

use iceCMS2\Settings\Settings;

class AuthorizationFactory
{
    /**
     * Get authorization object by type
     *
     * @param Settings $settings
     * @param string $type
     * @return AuthorizationInterface
     */
    public static function instance(Settings $settings, string $type): AuthorizationInterface
    {
        return match ($type) {
            'token' => new TokenAuthorization($settings),
            default => new SessionAuthorization($settings),
        };
    }
}