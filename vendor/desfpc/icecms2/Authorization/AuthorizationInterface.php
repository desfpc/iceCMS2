<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Authorization Interface
 */

namespace iceCMS2\Authorization;

use iceCMS2\Models\User;
use iceCMS2\Settings\Settings;

interface AuthorizationInterface
{
    /**
     * Constructor class
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings);

    /**
     * Authorize request
     *
     * @return bool
     */
    public function authorizeRequest(): bool;

    /**
     * Getting authorize status
     *
     * @return bool
     */
    public function getAuthStatus(): bool;

    /**
     * Getting authorized user
     *
     * @return User|null
     */
    public function getUser(): ?User;

    /**
     * Exit from authorized user
     *
     * @return bool
     */
    public function exitAuth(): bool;
}