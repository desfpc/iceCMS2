<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Caching Interface
 */

namespace iceCMS2\Caching;

interface CachingInterface
{
    /**
     * Checking if the key is in the cache
     *
     * @param string $key
     * @return bool
     * @throws \Exception
     */
    public function has(string $key): bool;

    /**
     * Finding Keys in the Cache
     *
     * @param string $pattern
     * @return false|array
     */
    public function findKeys(string $pattern): mixed;

    /**
     * Get value by key
     *
     * @param string $key
     * @param bool $decode
     * @return mixed
     * @throws \Exception
     */
    public function get(string $key, bool $decode = false): mixed;

    /**
     * Setting a value by key
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $expired
     * @return bool
     * @throws \Exception
     */
    public function set(string $key, mixed $value, ?int $expired = null): bool;

    /**
     * Deleting a value by key
     *
     * @param string $key
     * @return bool
     * @throws \Exception
     */
    public function del(string $key): bool;
}