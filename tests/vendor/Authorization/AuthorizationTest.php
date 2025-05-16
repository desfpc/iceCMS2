<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Authorization Tests
 */

namespace vendor\Authorization;

use iceCMS2\Authorization\SessionAuthorization;
use iceCMS2\Authorization\TokenAuthorization;
use iceCMS2\Models\User;
use iceCMS2\Tests\Ice2CMSTestCase;
use iceCMS2\Tools\Exception;

class AuthorizationTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected static array $_dbTables = ['users'];

    protected function setUp(): void
    {
        if (session_id() === '') {
            session_start();
        }
        parent::setUp();
    }

    /**
     * Test Authorization tests
     * @throws Exception
     */
    public function testAuthorization(): void
    {
        $user = new User(self::$_testSettings);
        $testEmail = 'test@email.com';
        $testPassword = 'testPassword_123';
        $user->set([
            'email' => $testEmail,
            'phone' => '+7 (999) 999-99-99',
            'telegram' => 'testTelegram',
            'language' => 'ru',
            'name' => 'Test User',
            'nikname' => 'TestUser',
            'status' => 'created',
            'role' => 'user',
            'rating' => 0,
            'sex' => 'female',
            'password' => $testPassword,
        ]);

        $this->assertTrue($user->save());

        $sessionAuthorization = new SessionAuthorization(self::$_testSettings);
        $_REQUEST['email'] = $testEmail;
        $_REQUEST['password'] = $testPassword;
        $this->assertTrue($sessionAuthorization->authorizeRequest());

        $user = $sessionAuthorization->getUser();
        $this->assertNotNull($user);
        $this->assertEquals($testEmail, $user->get('email'));

        unset($_REQUEST['email']);
        unset($_REQUEST['password']);
        $this->assertTrue($sessionAuthorization->authorizeRequest());

        $this->assertTrue($sessionAuthorization->exitAuth());
        $this->assertNull($sessionAuthorization->getUser());

        $tokenAuthorization = new TokenAuthorization(self::$_testSettings);
        $token = $tokenAuthorization->getTokens($testEmail, $testPassword);
        $this->assertNotFalse($token);

        $_SERVER['HTTP_ACCESS_TOKEN'] = $token['accessToken'];
        $this->assertTrue($tokenAuthorization->authorizeRequest());

        $_SERVER['HTTP_REFRESH_TOKEN'] = $token['refreshToken'];
        $newTokens = $tokenAuthorization->refreshToken();
        $this->assertIsArray($newTokens);

        $_SERVER['HTTP_ACCESS_TOKEN'] = $newTokens['accessToken'];
        $this->assertTrue($tokenAuthorization->authorizeRequest());
    }
}