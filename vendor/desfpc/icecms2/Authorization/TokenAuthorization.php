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

class TokenAuthorization implements AuthorizationInterface
{

    /**
     * @inheritDoc
     */
    public function authorizeRequest(): bool
    {
        // TODO: Implement authorizeRequest() method.
    }

    /**
     * @inheritDoc
     */
    public function getAuthStatus(): bool
    {
        // TODO: Implement getAuthStatus() method.
    }

    /**
     * @inheritDoc
     */
    public function getAuthUser(): ?User
    {
        // TODO: Implement getAuthUser() method.
    }

    /**
     * @inheritDoc
     */
    public function exitAuth(): bool
    {
        // TODO: Implement exitAuth() method.
    }
}