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

use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class ValidatorFactory
{
    /**
     * Choose validator and validate
     *
     * @param DBInterface $db
     * @param Settings $settings
     * @param string $validator
     * @param mixed $value
     * @param string|null $table
     * @param string|null $name
     * @return bool
     * @throws Exception
     */
    public static function validate(DBInterface $db, Settings $settings, string $validator, mixed $value, ?string $table = null, ?string $name = null): bool
    {
        $validatorName = str_replace('|empty', '', $validator);

        if ($validator !== $validatorName) {
            $empty = true;
        } else {
            $empty = false;
        }

        if ($empty && empty($value)) {
            return true;
        }

        switch ($validatorName) {
            case 'password':
                return PasswordValidator::validate($db, $value);
            case 'email':
                return EmailValidator::validate($db, $value);
            case 'uniqueString':
                return UniqueStringValidator::validate($db, $value, $settings, $table, $name);
            case 'language':
                return LanguageValidator::validate($db, $value, $settings);
            case 'enum':
                return EnumValidator::validate($db, $value, $settings, $table, $name);
            case 'unixtime':
                return UnixTimeValidator::validate($db, $value);
            case 'int':
                return IntValidator::validate($db, $value, $settings, $table, $name);
            default:
                if (class_exists($validator)) {
                    /** @var ValidatorInterface $validatorObj */
                    $validatorObj = new $validator();
                    return $validatorObj::validate($db, $value, $settings, $table, $name);
                }

                throw new Exception('Validator ' . $validatorName . ' not found');
        }
    }
}