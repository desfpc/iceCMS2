<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * FileList Model Classes Tests
 */

namespace vendor\Models\FileList;

use iceCMS2\Models\FileList;
use iceCMS2\Tools\Exception;
use iceCMS2\Models\File;
use iceCMS2\Tests\Ice2CMSTestCase;

class FileListTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected static array $_dbTables = ['users', 'files'];

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
     * Adding test file
     *
     * @param string $fileName
     * @return File
     * @throws Exception
     */
    private function _addTestFile(string $fileName): File
    {
        $fileNameArr = explode('.', $fileName);
        $newFileName = $fileNameArr[0] . 'NEW.' . $fileNameArr[1];
        $file = new File(self::$_testSettings);
        $file->clearCache();
        $testFilePath = self::getTestClassDir() . $fileName;
        $testFilePathNew = self::getTestClassDir() . $newFileName;
        copy($testFilePath, $testFilePathNew);
        chmod($testFilePathNew, 0666);

        //Simulating File transfer
        $_FILES['testFile'] = [
            'tmp_name' => $testFilePathNew,
            'name' => $fileName,
            'size' => filesize($testFilePath),
        ];
        //Simulating POST transfer
        $_POST = [
            'anons' => $fileName . ' description text',
        ];

        $file = new File(self::$_testSettings);
        $this->assertTrue($file->savePostFile('testFile'));

        return $file;
    }

    /**
     * Create File Entity from $_POST and $_FILE global arrays
     *
     * @throws Exception
     */
    public function testFileList(): void
    {
        //Adding File 1
        $file1 = $this->_addTestFile('LICENSE.txt');
        //Adding File 2
        $file2 = $this->_addTestFile('LICENSE2.txt');
        //Adding File 3
        $file3 = $this->_addTestFile('LICENSE3.txt');

        $fileList = new FileList(self::$_testSettings);
        $cnt = $fileList->getCnt();
        $this->assertEquals(3, $cnt);

        $rows = $fileList->get();
        $this->assertCount(3, $rows);

        //TODO check FileList with $conditions, $order, $page and $size
        $conditions = ['id' => 2];
        $fileList = new FileList(self::$_testSettings, $conditions);
        $rows = $fileList->get();
        $this->assertCount(1, $rows);

        $conditions = ['name' =>
            [
                'logic' => 'AND',
                'sign' => 'LIKE',
                'value' => '%SE%',
            ]
        ];
        $order = ['name' => 'DESC', 'id' => 'ASC'];
        $fileList = new FileList(self::$_testSettings, $conditions, $order);
        $rows = $fileList->get();
        $this->assertCount(3, $rows);

        $fileList = new FileList(self::$_testSettings, $conditions, $order, 1, 2);
        $rows = $fileList->get();
        $this->assertCount(2, $rows);
        $this->assertEquals('LICENSE3.txt', $rows[0]['name']);

        $this->assertTrue($file1->del());
        $this->assertTrue($file2->del());
        $this->assertTrue($file3->del());
    }
}