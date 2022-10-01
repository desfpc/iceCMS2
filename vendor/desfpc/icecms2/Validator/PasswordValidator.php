<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Password Validator
 */
namespace iceCMS2\Validator;

use iceCMS2\Helpers\Strings;
use iceCMS2\Tools\Exception;

class PasswordValidator implements ValidatorInterface
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function validate(mixed $value): bool
    {
        if (Strings::getPassError($value) === 0) {
            return true;
        }

        throw new Exception('Password is not valid');
    }
}