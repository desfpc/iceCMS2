<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * LocaleText Test class
 */

namespace vendor\Locale;

use iceCMS2\Caching\CachingFactory;
use iceCMS2\Locale\LocaleText;
use iceCMS2\Tests\Ice2CMSTestCase;
use iceCMS2\Tools\Exception;

class LocaleTextTest extends Ice2CMSTestCase
{
    /**
     * LocaleTextTest Get Tests
     *
     * @return void
     * @throws Exception
     */
    public function testLocaleTextGet(): void
    {
        //Clear Cache
        $cacher = CachingFactory::instance(self::$_testSettings);
        $keys = $cacher->findKeys(self::$_testSettings->db->name . '*');
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $cacher->del($key);
            }
        }

        $this->assertEquals(
            'Test string value1',
            LocaleText::get(
                self::$_testSettings,
                'user/noInUser/Test string {key1}',
                ['key1' => 'value1'],
            'ru')
        );

        $this->assertEquals(
            'телефона',
            LocaleText::get(
                self::$_testSettings,
                'user/approve/genitiveСase/phone',
                [],
                'ru'
            )
        );

        $this->assertEquals(
            'Код для подтверждения 1234',
            LocaleText::get(
                self::$_testSettings,
                'user/approve/Code for approve {codeType}',
                ['codeType' => '1234'],
                'ru'
            )
        );
    }
}