<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Modificator Interface
 */

namespace iceCMS2\Modificator;

interface ModificatorInterface
{
    /**
     * @param mixed $value
     * @return void
     */
    public static function modify(mixed &$value): void;
}