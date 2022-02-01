<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * HelpersStrings Class Tests
 */

namespace vendor\Routing;

use iceCMS2\Routing\Routing;
use iceCMS2\Settings\Settings;
use PHPUnit\Framework\TestCase;

class RoutingTest extends TestCase
{
    /**
     * Testing parsing URL from request
     */
    public function testParseURL(): void
    {
        $routing = new Routing();

        $_SERVER['REQUEST_URI'] = '/test1/test2/test3/?test4=val1&test5=val2';
        $_SERVER['SCRIPT_NAME'] = '/script_name/';

        $routing->parseURL();
        $this->assertEquals([
            'base' => '',
            'call_utf8' => 'test1/test2/test3/',
            'call' => 'test1/test2/test3/',
            'call_parts' => [
                0 => 'test1',
                1 => 'test2',
                2 => 'test3',
            ],
            'query_utf8' => 'test4=val1&test5=val2',
            'query' => 'test4=val1&test5=val2',
            'query_vars' => [
                'test4' => 'val1',
                'test5' => 'val2',
            ],
        ], $routing->pathInfo);

        $_SERVER['REQUEST_URI'] = '/тест1/тест2/?тест3=val1&тест4=знач2';
        $_SERVER['SCRIPT_NAME'] = '/script_name/';

        $routing->parseURL();
        $this->assertEquals([
            'base' => '',
            'call_utf8' => 'тест1/тест2/',
            'call' => '????1/????2/',
            'call_parts' => [
                0 => 'тест1',
                1 => 'тест2',
            ],
            'query_utf8' => 'тест3=val1&тест4=знач2',
            'query' => '????3=val1&????4=????2',
            'query_vars' => [
                'тест3' => 'val1',
                'тест4' => 'знач2',
            ],
        ], $routing->pathInfo);
    }

    /**
     * Testing getting route from pathInfo and Settings
     */
    public function testGettingRoute(): void
    {
        /** @var array $settings */
        require_once('testSettings.php');

        $settings['routes'] = [
            'test1/test2/test3' => 'controller3',
            'test1/test2' => 'controller2',
            'test1' => 'controller1'
        ];

        $_SERVER['SCRIPT_NAME'] = '/script_name/';

        $settings1 = new Settings($settings);
        $_SERVER['REQUEST_URI'] = '/test5/test2/test3/?test4=val1&test5=val2';

        $routing = new Routing();
        $routing->parseURL();
        $routing->getRoute($settings1, false);
        $this->assertEquals([
            'controller' => 'NotFound',
            'method' => 'main',
            'parts' => [],
        ], $routing->route
        );

        $_SERVER['REQUEST_URI'] = '/test1/test2/?test4=val1&test5=val2';
        $routing->parseURL();
        $routing->getRoute($settings1, false);
        $this->assertEquals([
            'controller' => 'controller2',
            'method' => 'main',
            'parts' => [],
        ], $routing->route
        );

        $_SERVER['REQUEST_URI'] = '/test1/test-test5/test3/test2/?test4=val1&test5=val2';
        $routing->parseURL();
        $routing->getRoute($settings1, false);
        $this->assertEquals([
            'controller' => 'controller1',
            'method' => 'testTest5',
            'parts' => [
                0 => 'test3',
                1 => 'test2',
            ],
        ], $routing->route
        );

        $settings['routes'] = [
            'test1/test2/$id/$action' => ['controller' => 'controller1', 'method' => 'get'],
            'test1/test2' => ['controller' => 'controller5', 'method' => 'set'],
            'test2/test3/$id' => 'controller2',
            'test4/$id' => 'controller4'
        ];
        $settings2 = new Settings($settings);
        $_SERVER['REQUEST_URI'] = '/test1/test2/10/save/?test4=val1&test5=val2';
        $routing->parseURL();
        $routing->getRoute($settings2, false);
        $this->assertEquals([
            'controller' => 'controller1',
            'method' => 'get',
            'parts' => [],
        ], $routing->route
        );
        $this->assertEquals([
            'base' => '',
            'call_utf8' => 'test1/test2/10/save/',
            'call' => 'test1/test2/10/save/',
            'call_parts' => [
                0 => 'test1',
                1 => 'test2',
                2 => '10',
                3 => 'save',
            ],
            'query_utf8' => 'test4=val1&test5=val2',
            'query' => 'test4=val1&test5=val2',
            'query_vars' => [
                'test4' => 'val1',
                'test5' => 'val2',
                'id' => '10',
                'action' => 'save'
            ],
        ], $routing->pathInfo);

        $_SERVER['REQUEST_URI'] = '/test1/test2/?test4=val1&test5=val2';
        $routing->parseURL();
        $routing->getRoute($settings2, false);
        $this->assertEquals([
            'controller' => 'controller5',
            'method' => 'set',
            'parts' => [],
        ], $routing->route
        );
    }
}