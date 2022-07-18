<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * HelpersStrings Class Tests
 */

namespace vendor\Helpers;

use iceCMS2\Helpers\Strings;
use PHPUnit\Framework\TestCase;

class StringsTest extends TestCase
{
    /**
     * Test iceCMS2\Helpers\Strings::camelToSnake
     *
     * @return void
     */
    public function testCamelToSnake(): void
    {
        $this->assertEquals([
            'first_camel',
            'second_camel',
            'third_camel_xxl',
        ], [
            Strings::camelToSnake('firstCamel'),
            Strings::camelToSnake('SecondCamel'),
            Strings::camelToSnake('ThirdCamelXXL'),
        ]);
    }

    /**
     * Test iceCMS2\Helpers\Strings::snakeToCamel
     *
     * @return void
     */
    public function testSnakeToCamel(): void
    {
        $this->assertEquals([
            'firstCamel',
            'secondCamel',
            'thirdCamelXxl',
            'UpperCamel',
        ], [
            Strings::snakeToCamel('first_camel'),
            Strings::snakeToCamel('second_camel'),
            Strings::snakeToCamel('third_camel_xxl'),
            Strings::snakeToCamel('upperCamel', false),
        ]);
    }
}