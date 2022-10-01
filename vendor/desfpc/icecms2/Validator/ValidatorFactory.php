<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Validator Factory
 */
namespace iceCMS2\Validator;

use iceCMS2\Tools\Exception;

class ValidatorFactory
{
    /**
     * Choose validator and validate
     *
     * @param string $validator
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    public static function validate(string $validator, mixed $value): bool
    {
        switch ($validator) {
            case 'password':
                return PasswordValidator::validate($value);
            default:
                if (class_exists($validator)) {
                    /** @var ValidatorInterface $validatorObj */
                    $validatorObj = new $validator();
                    return $validatorObj::validate($value);
                }

                throw new Exception('Validator ' . $validator . ' not found');
        }
    }
}