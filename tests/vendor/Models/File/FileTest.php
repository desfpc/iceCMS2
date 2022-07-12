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

use iceCMS2\Tools\Exception;
use iceCMS2\Models\File;
use iceCMS2\Tests\Ice2CMSTestCase;

class FileTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected static array $_dbTables = ['users', 'files'];

    /**
     *
     *
     * @throws Exception
     */
    public function testCreatingFileFromPost(): void
    {
        $file = new File(self::$_testSettings);
        $this->assertEquals(false, $file->isLoaded);
        $this->assertFalse($file->load(1));
    }

}