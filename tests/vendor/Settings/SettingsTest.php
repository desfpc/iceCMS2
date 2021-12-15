<?php
declare(strict_types=1);

use iceCMS2\Settings\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    public function testInvalidSettings()
    {
        // empty array
        $settings = new Settings([]);
        $this->assertEquals('Failed to load settings: Settings file error - there is no required field: path', $settings->errors->text);

        // wrong array with single value
        $wrongSettings = [];
        $wrongSettings['path'] = 'test/path';
        $settings = new Settings($wrongSettings);
        $this->assertEquals($wrongSettings['path'], $settings->path);
        $this->assertEquals('Failed to load settings: Settings file error - there is no required field: template', $settings->errors->text);

        // TODO wrong array without one value
    }
}