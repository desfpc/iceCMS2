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
use PHPUnit\Framework\TestCase;

class RoutingTest extends TestCase
{
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
    }
}