<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Password Modificator
 */
namespace iceCMS2\Modificator;

class PasswordModificator implements ModificatorInterface
{

    /**
     * @inheritDoc
     */
    public static function modify(mixed &$value): void
    {
        $value = self::_getPasswordHash($value);
    }

    /**
     * Get password hash
     *
     * @param string $password
     * @return string
     */
    private static function _getPasswordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}