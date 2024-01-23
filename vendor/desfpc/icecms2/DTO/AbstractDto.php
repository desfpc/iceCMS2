<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Abstract DTO class
 */

namespace iceCMS2\DTO;

use Exception;
use iceCMS2\Helpers\Strings;

abstract class AbstractDto implements DtoInterface
{
    /** @var array DTO params */
    public const PARAMS = [];

    /** @var array DTO data */
    protected array $_data = [];

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
        $this->_setAdditionalData($data);
    }

    /**
     * Get DTO params
     *
     * @return array
     */
    public function getParams(): array
    {
        return static::PARAMS;
    }

    /**
     * Additional data setter
     *
     * @param array $data
     * @return void
     */
    protected abstract function _setAdditionalData(array $data): void;

    /**
     * DTO params setter
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        $this->_data = array_intersect($data, static::PARAMS);
        $this->_data = array_merge($this->_data, array_fill_keys(static::PARAMS, null));
    }

    /**
     * DTO params getter
     *
     * @param string $key
     * @return mixed|null
     * @throws Exception
     */
    public function __get(string $key)
    {
        $snakeCaseKey = Strings::camelToSnake($key);

        if (isset($this->_data[$snakeCaseKey])) {
            return $this->_data[$snakeCaseKey];
        }

        throw new Exception('Undefined property: ' . $key);
    }
}