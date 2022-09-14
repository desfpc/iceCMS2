<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * File Model Classes Tests
 */

namespace vendor\Models\FileImage;

use iceCMS2\Models\FileImage;
use iceCMS2\Models\ImageSize;
use iceCMS2\Models\ImageSizeList;
use iceCMS2\Tools\Exception;
use iceCMS2\Tests\Ice2CMSTestCase;

class FileImageTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected static array $_dbTables = ['users', 'files', 'image_sizes', 'file_image_sizes'];

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
     * File Image all entity test
     * @throws Exception
     */
    public function testFileImage(): void
    {
        //Creating watermark image
        $testFilePath = self::getTestClassDir() . 'logofw.png';
        $testFilePathNew = self::getTestClassDir() . 'logofwnew.png';

        copy($testFilePath, $testFilePathNew);
        chmod($testFilePathNew, 0666);

        //Simulating File transfer
        $_FILES['testFile'] = [
            'tmp_name' => $testFilePathNew,
            'name' => 'logofwnew.png',
            'size' => filesize($testFilePathNew),
        ];

        //Simulating POST transfer
        $_POST = [
            'noInTableKey' => 'someValue',
            'anons' => 'File description text',
        ];

        $watermarkFile = new FileImage(self::$_testSettings);
        $this->assertTrue($watermarkFile->savePostFile('testFile'));
        $watermarkId = $watermarkFile->get('id');

        //Creating test image sizes
        $imageSizeArr = [
            [
                'width' => 0,
                'height' => 300,
                'string_id' => '0_300',
            ],
            [
                'width' => 300,
                'height' => 0,
                'string_id' => '300_0',
            ],
            [
                'width' => 300,
                'height' => 300,
                'string_id' => '300',
            ],
            [
                'width' => 800,
                'height' => 400,
                'string_id' => '800_400_' . $watermarkId,
                'watermark_id' => $watermarkId,
                'watermark_width' => 100,
                'watermark_height' => 200,
                'watermark_top' => -10,
                'watermark_left' => 10,
                'watermark_units' => 'px',
            ],
        ];

        foreach ($imageSizeArr as $item) {
            $imageSize = new ImageSize(self::$_testSettings);
            $imageSize->set($item);
            $imageSizeSaved = $imageSize->save();
            if (!$imageSizeSaved) {
                print_r($item);
            }
            $this->assertTrue($imageSizeSaved);
        }

        $query = 'SELECT * FROM image_sizes';
        $res = self::$_db->query($query);
        $this->assertCount(4, $res);

        //Creating test image
        $testFilePath = self::getTestClassDir() . 'testImg.jpg';
        $testFilePathNew = self::getTestClassDir() . 'testImgNew.jpg';

        copy($testFilePath, $testFilePathNew);
        chmod($testFilePathNew, 0666);

        //Simulating File transfer
        $_FILES['testFile'] = [
            'tmp_name' => $testFilePathNew,
            'name' => 'testImgNew.jpg',
            'size' => filesize($testFilePathNew),
        ];

        //Simulating POST transfer
        $_POST = [
            'noInTableKey' => 'someValue',
            'anons' => 'File description text',
        ];

        $imageFile = new FileImage(self::$_testSettings);
        $this->assertTrue($imageFile->savePostFile('testFile'));

        //Adding imageSizes to Image
        $sizes = $imageFile->getImageSizes();
        $this->assertNull($sizes);

        $imageSizes = new ImageSizeList(self::$_testSettings);
        $imageSizesArr = $imageSizes->get();
        $this->assertCount(4, $imageSizesArr);

        foreach ($imageSizesArr as $item) {
            $imageFile->createImageSize($item['id']);
        }

        //TODO Deleting imageSizes

        //TODO Deleting Image
    }
}