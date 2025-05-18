<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Modificator Factory
 */
namespace iceCMS2\Modificator;

use iceCMS2\Tools\Exception;

class ModificatorFactory
{
    /**
     * Choose validator and validate
     *
     * @param string $modificator
     * @param mixed $value
     * @return void
     * @throws Exception
     */
    public static function modify(string $modificator, mixed &$value): void
    {
        switch ($modificator) {
            case 'password':
                PasswordModificator::modify($value);
                break;
            default:
                if (class_exists($modificator)) {
                    /** @var ModificatorInterface $validatorObj */
                    $modificatorObj = new $modificator();
                    $modificatorObj::modify($value);
                    break;
                }

                throw new Exception('Modificator ' . $modificator . ' not found');
        }
    }
}