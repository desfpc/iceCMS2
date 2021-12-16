<?php
declare(strict_types=1);

use iceCMS2\Settings\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    public function testSettings()
    {
        // empty array
        $settings = new Settings([]);
        $this->assertEquals('Failed to load settings: Settings file error - there is no required field: path', $settings->errors->text);

        // valid settings array
        $validSettings = [
            'path' => 'text/path',
            'template' => 'testTemplate',
            'dev' => true,
            'secret' => 'verySecretSecret',
            'db' => [
                'type' => 'mySql',
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
                'primary_domain' => 'test.site',
                'redirect_to_primary_domain' => true,
                'language_subdomain' => true,
            ],
            'cache' => [
                'use_redis' => true,
                'redis_host' => '127.0.0.1',
                'redis_port' => 6379,
            ],
            'routes' => [
                '404' => '404',
                '500' => '500',
                'someURL' => 'someRouteControllerAndMethod'
            ],
        ];

        $settings = new Settings($validSettings);
        $this->assertEquals(0, $settings->errors->flag);
        $this->assertEquals($validSettings['routes']['someURL'], $settings->routes['someURL']);

        // wrong settings #1
        $wrongSettings = $validSettings;
        unset($wrongSettings['site']);
        $settings = new Settings($wrongSettings);
        $this->assertEquals(1, $settings->errors->flag);
        $this->assertEquals('Failed to load settings: Settings file error - there is no required field: site', $settings->errors->text);

        // wrong settings #2
        $wrongSettings = $validSettings;
        unset($wrongSettings['site']['redirect_to_primary_domain']);
        $settings = new Settings($wrongSettings);
        $this->assertEquals(1, $settings->errors->flag);
        $this->assertEquals('Failed to load settings: Settings file error - there is no required field: site-redirect_to_primary_domain', $settings->errors->text);
    }
}