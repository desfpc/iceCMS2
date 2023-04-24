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

use iceCMS2\DB\DBInterface;
use iceCMS2\Settings\Settings;

abstract class AbstractValidator implements ValidatorInterface
{
    /** @var string Setting - if empty value is valid */
    const SETTING_EMPTY = 'empty';

    /** @var string[] Available settings array */
    const SETTINGS = [
        self::SETTING_EMPTY
    ];

    /**
     * @var array Settings of validated value
     */
    protected static array $valueSettings = [];

    /**
     * @var mixed Prepared for DB value
     */
    protected static mixed $workValue = null;

    /**
     * @inheritDoc
     */
    public static function validate(DBInterface $db, mixed $value, ?Settings $settings = null, ?string $table = null,
        ?string $name = null): bool
    {
        self::prepareValue($value);

        return true;
    }

    /**
     * @return bool
     */
    static protected function validateValueSettings(): bool
    {
        if (!empty(static::$valueSettings)) {
            foreach (static::$valueSettings as $setting => $value) {
                switch ($setting) {
                    case self::SETTING_EMPTY:
                        if (empty($value)) {
                            return true;
                        }
                        break;
                }
            }
        }

        return false;
    }

    /**
     * Prepare value for validation
     *
     * @param string $value
     * @return void
     */
    protected static function prepareValue(string $value): void
    {
        $valueArr = explode('|', $value);

        if (count($valueArr) > 1) {
            foreach ($valueArr as $valueItem) {
                if (is_null(static::$workValue)) {
                    static::$workValue = $valueItem;
                } else {
                    static::$valueSettings[$valueItem] = true;
                }
            }
        } else {
            static::$workValue = $value;
        }
    }
}