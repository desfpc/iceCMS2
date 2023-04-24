<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Enum Validator
 */
namespace iceCMS2\Validator;

use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class EnumValidator extends AbstractValidator implements ValidatorInterface
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function validate(DBInterface $db, mixed $value, ?Settings $settings = null, ?string $table = null,
        ?string $name = null): bool
    {
        if (!is_null($table) && !is_null($name)) {

            $enumArr = $db->getEnumValues($table, $name);

            if (empty($enumArr)) {
                throw new Exception('Enum values not found for table ' . $table . ' and field ' . $name);
            }

            if (in_array($value, $enumArr)) {
                return true;
            }

            return false;
        }

        throw new Exception('Wrong parameters for EnumValidator');
    }
}