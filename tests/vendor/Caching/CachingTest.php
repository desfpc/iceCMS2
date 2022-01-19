<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Caching Class Tests
 */

namespace vendor\Caching;

use Exception;
use iceCMS2\Caching\CachingFactory;
use iceCMS2\Tests\Ice2CMSTestCase;

class CachingTest extends Ice2CMSTestCase
{
    /**
     * Test Redis Caching system
     *
     * @return void
     * @throws Exception
     */
    public function testCaching(): void
    {
        $cacher = CachingFactory::instance(self::$_settings);

        $key1 = 'iceCMS2_test_Key1';
        $value1 = 'test valUe1';

        $this->assertFalse($cacher->has($key1));
        $this->assertTrue($cacher->set($key1, $value1));
        $val = $cacher->get($key1);
        $this->assertEquals($value1, $val);
        $this->assertTrue($cacher->del($key1));

        $this->assertTrue($cacher->set($key1, $value1, 2));
        $val = $cacher->get($key1);
        $this->assertEquals($value1, $val);
        sleep(2);
        $this->assertFalse($cacher->has($key1));
    }
}