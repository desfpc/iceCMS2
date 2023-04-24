<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * UniqString Validator
 */
namespace iceCMS2\Validator;

use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;
use iceCMS2\Tools\Exception;

class UniqueStringValidator extends AbstractValidator implements ValidatorInterface
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function validate(DBInterface $db, mixed $value, ?Settings $settings = null, ?string $table = null,
        ?string $name = null): bool
    {
        if (!is_null($table) && !is_null($name)) {
            $sql = 'SELECT ' . $name . ' FROM ' . $table . ' WHERE ' . $name . ' = ?';
            $params = [$value];
            $result = $db->queryBinded($sql, $params);
            if (count($result) > 0) {
                return false;
            }
            return true;
        }

        throw new Exception('Wrong parameters for UniqueStringValidator');
    }
}