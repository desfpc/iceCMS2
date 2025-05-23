<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Email Validator
 */
namespace iceCMS2\Validator;

use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class JsonValidator extends AbstractValidator implements ValidatorInterface
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function validate(DBInterface $db, mixed $value, ?Settings $settings = null, ?string $table = null,
        ?string $name = null): bool
    {
        if ($value === '{}' || $value === '[]' || $value === '') {
            return true;
        }

        if (is_array($value)) {
            return true;
        }

        if (json_decode($value)) {
            return true;
        }

        throw new Exception('Value `' .$name. '` (' .$value. ') is not valid json');
    }
}