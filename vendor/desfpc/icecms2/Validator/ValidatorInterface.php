<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Validator Interface
 */

namespace iceCMS2\Validator;

interface ValidatorInterface
{
    /**
     * @param mixed $value
     * @return bool
     */
    public static function validate(mixed $value): bool;
}