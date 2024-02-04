<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * DTO Interface class
 */

namespace iceCMS2\DTO;

interface DtoInterface
{
    public function setData(array $data): void;

    public function getParams(): array;
}