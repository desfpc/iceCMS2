<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * User Model Classes Tests
 */

namespace vendor\Models\User;

use iceCMS2\Models\FileImage;
use iceCMS2\Models\User;
use iceCMS2\Tools\Exception;
use iceCMS2\Tests\Ice2CMSTestCase;

class UserTest extends Ice2CMSTestCase
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
     * Create User Entity
     *
     * @throws Exception
     */
    public function testUser(): void
    {
        //Creating avatar image
        $testFilePath = self::getTestClassDir() . 'avatar.webp';
        $testFilePathNew = self::getTestClassDir() . 'avatarNew.webp';

        copy($testFilePath, $testFilePathNew);
        chmod($testFilePathNew, 0666);

        //Simulating File transfer
        $_FILES['testFile'] = [
            'tmp_name' => $testFilePathNew,
            'name' => 'avatarNew.webp',
            'size' => filesize($testFilePathNew),
        ];

        //Simulating POST transfer
        $_POST = [
            'anons' => 'Beautiful user avatar',
        ];

        $imageFile = new FileImage(self::$_testSettings);
        $this->assertTrue($imageFile->savePostFile('testFile'));
        $avatarId = $imageFile->get('id');

        $this->assertEquals(1, $avatarId);

        //Creating user
        $userPass = '123#@$%#_VCff';
        $user = new User(self::$_testSettings);
        $user->set([
            'email' => 'test@email.com',
            'phone' => '+7 (999) 999-99-99',
            'telegram' => 'testTelegram',
            'language' => 'ru',
            'name' => 'Test User',
            'nikname' => 'TestUser',
            'status' => 'created',
            'role' => 'user',
            'rating' => 0,
            'avatar' => $avatarId,
            'sex' => 'female',
            'password' => $userPass,
        ]);

        $this->assertTrue($user->save());
        $this->assertNotEquals($userPass, $user->get('password'));

        $avatarUrl = $user->avatarUrl;
        $this->assertNotNull($avatarUrl);
        $this->assertStringContainsString('1_avatar.webp', $avatarUrl);
    }
}