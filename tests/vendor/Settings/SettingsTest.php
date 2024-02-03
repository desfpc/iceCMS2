<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Settings Class Tests
 */

namespace vendor\Settings;

use iceCMS2\Settings\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    /**
     * Test iceCMS2\Settings\Settings
     *
     * @return void
     */
    public function testSettings(): void
    {
        // empty array
        $settings = new Settings([]);
        $this->assertEquals(
            'Failed to load settings: Settings file error - there is no required field: path',
            $settings->errors->text
        );

        // valid settings array
        $validSettings = [
            'path' => 'text/path',
            'template' => 'testTemplate',
            'dev' => true,
            'secret' => 'verySecretSecret',
            'db' => [
                'type' => 'MySQL',
                'name' => 'testDB',
                'host' => '127.0.0.1',
                'port' => '3306',
                'login' => 'testLogin',
                'pass' => 'testPass',
                'encoding' => 'UTF8',
            ],
            'dbTest' => [
                'type' => 'MySQL',
                'name' => 'testDB',
                'host' => '127.0.0.1',
                'port' => '3306',
                'login' => 'testLogin',
                'pass' => 'testPass',
                'encoding' => 'UTF8',
            ],
            'email' => [
                'mail' => 'test@domain',
                'port' => '465',
                'signature' => 'Test mail system',
                'pass' => 'testPass',
                'smtp' => 'smtp.domain',
            ],
            'sms' => null,
            'site' => [
                'title' => 'Test Site Name',
                'primaryDomain' => 'test.site',
                'redirectToPrimaryDomain' => true,
                'localeSubdomain' => true,
                'cssScriptsVersion' => '1',
                'jsScriptsVersion' => '1',
            ],
            'locales' => [
                'en',
                'ru',
            ],
            'logs' => [
                'period' => 'month',
                'period_clear' => 'day',
                'type' => 'db',
            ],
            'cache' => [
                'useRedis' => true,
                'redisHost' => '127.0.0.1',
                'redisPort' => 6379,
            ],
            'routes' => [
                '404' => '404',
                '500' => '500',
                'someURL' => 'someRouteControllerAndMethod'
            ],
            'isUseCms' => true,
        ];

        $settings = new Settings($validSettings);
        $this->assertEquals(0, $settings->errors->flag);
        $this->assertEquals($validSettings['routes']['someURL'], $settings->routes['someURL']);

        // wrong settings #1
        $wrongSettings = $validSettings;
        unset($wrongSettings['site']);
        $settings = new Settings($wrongSettings);
        $this->assertEquals(1, $settings->errors->flag);
        $this->assertEquals(
            'Failed to load settings: Settings file error - there is no required field: site',
            $settings->errors->text
        );

        // wrong settings #2
        $wrongSettings = $validSettings;
        unset($wrongSettings['site']['redirectToPrimaryDomain']);
        $settings = new Settings($wrongSettings);
        $this->assertEquals(1, $settings->errors->flag);
        $this->assertEquals(
            'Failed to load settings: Settings file error - there is no required field: site-redirectToPrimaryDomain',
            $settings->errors->text
        );
    }
}