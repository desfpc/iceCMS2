<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * DateTime type class
 */

namespace iceCMS2\Types;

class UnixTime
{
    /** @var int|null Timestamp int value */
    private ?int $_timestamp = null;

    /**
     * Constructor method
     *
     * @param int|string|null $time
     */
    public function __construct(int|string|null $time = null)
    {
        $this->set($time);
    }

    /**
     * Get int timestamp
     *
     * @return false|int|null
     */
    public function get()
    {
        return $this->_timestamp;
    }

    /**
     * Setting timestamp
     *
     * @param int|string|null $time
     * @return void
     */
    public function set(int|string|null $time = null)
    {
        if (is_null($time)) {
            $time = time();
        } elseif (is_string($time)) {
            $time = strtotime($time);
        }

        $this->_timestamp = $time;
    }

    /**
     * Magick to string convertion
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->get();
    }
}