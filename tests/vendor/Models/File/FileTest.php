<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * File Model Classes Tests
 */

namespace vendor\Models\File;

use iceCMS2\Tests\Ice2CMSTestCase;

class FileTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected static array $_dbTables = ['users', 'files'];

    public function testConnect(): void
    {
        $this->assertEquals(true, static::$_DB->getConnected());
    }

}