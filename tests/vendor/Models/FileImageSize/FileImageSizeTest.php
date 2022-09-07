<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * File Model Classes Tests
 */

namespace vendor\Models\FileImageSize;

use iceCMS2\Models\FileImageSize;
use iceCMS2\Tests\Ice2CMSTestCase;
use iceCMS2\Tools\Exception;

class FileImageSizeTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected static array $_dbTables = ['files', 'image_sizes', 'file_image_sizes'];

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        if (session_id() === ''){
            session_start();
        }
        parent::__construct();
    }

    /**
     * Test FileImageSize Entity class
     *
     * @return void
     * @throws Exception
     */
    public function testFileImageSizeEntity(): void
    {
        $fileImageSize = new FileImageSize(self::$_testSettings);
        $fileImageSize->set('file_id', 1);
        $fileImageSize->set('image_size_id', 1);
        $fileImageSize->set('is_created', 0);

        $this->assertTrue($fileImageSize->save());

        $idKeys = [
            'file_id' => 1,
            'image_size_id' => 1,
        ];
        $fileImageSizeLoad = new FileImageSize(self::$_testSettings, $idKeys);
        $this->assertTrue($fileImageSizeLoad->load());
        $this->assertEquals(1, $fileImageSizeLoad->get('file_id'));
        $this->assertEquals(1, $fileImageSizeLoad->get('image_size_id'));
        $this->assertEquals(0, $fileImageSizeLoad->get('is_created'));

        $this->assertTrue($fileImageSizeLoad->del());
        $this->assertFalse($fileImageSizeLoad->load());
    }

}