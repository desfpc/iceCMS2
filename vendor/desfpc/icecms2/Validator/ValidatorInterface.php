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

use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;

interface ValidatorInterface
{
    /**
     * @param DBInterface $db
     * @param mixed $value
     * @param Settings|null $settings
     * @param string|null $table
     * @param string|null $name
     * @return bool
     */
    public static function validate(DBInterface $db, mixed $value, ?Settings $settings = null, ?string $table = null,
        ?string $name = null): bool;
}