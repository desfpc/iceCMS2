<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Logger Test class
 */

namespace vendor\Logger;

use DateTime;
use iceCMS2\Caching\CachingFactory;
use iceCMS2\Caching\Redis;
use iceCMS2\Logger\DBLogger;
use iceCMS2\Logger\FileLogger;
use iceCMS2\Logger\LoggerFactory;
use iceCMS2\Messages\FakeEmailTransport;
use iceCMS2\Messages\MessageFactory;
use iceCMS2\Models\MessageLog;
use iceCMS2\Tests\Ice2CMSTestCase;
use iceCMS2\Tools\Exception;

class LoggerTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected static array $_dbTables = ['message_log'];

    /**
     * Test Logger class
     *
     * @return void
     * @throws Exception
     */
    public function testLogger(): void
    {
        /** @var FakeEmailTransport $message */
        $message = MessageFactory::instance(self::$_testSettings, 'fake');
        $message->setFrom('test@email.com', 'Test Sender')
            ->setTo('to@test.com','Test Recipient')
            ->setTheme('Test Subject')
            ->setText('Test Body')
            ->send();

        //File log test
        self::$_settings->logs->type = 'file';

        /** @var FileLogger $logger */
        $logger = LoggerFactory::instance(self::$_testSettings);
        $this->assertSame('iceCMS2\Logger\FileLogger', get_class($logger));

        $dateTime = new DateTime();
        $testLogPath = self::$_testSettings->path . '/logs/test_' . $dateTime->format('Y-m') . '.log';
        if (file_exists($testLogPath)) {
            unlink($testLogPath);
        }
        $this->assertTrue($logger::log(self::$_settings, 'test', $message));
        $this->assertTrue(file_exists($testLogPath));

        $logContent = file_get_contents($testLogPath);
        $this->assertStringContainsString('Test Subject', $logContent);
        $this->assertStringContainsString('Test Body', $logContent);
        $this->assertStringContainsString('test@email.com', $logContent);
        $this->assertStringContainsString('to@test.com', $logContent);

        //DB log test
        self::$_settings->logs->type = 'db';

        /** @var DBLogger $logger */
        $logger = LoggerFactory::instance(self::$_testSettings);
        $this->assertSame('iceCMS2\Logger\DBLogger', get_class($logger));

        $messageLogObj = new MessageLog(self::$_testSettings);

        $messageArray = json_decode(json_encode($message->__serialize()), true);
        $messageArray['from_name'] = $messageArray['fromName'];
        unset($messageArray['fromName']);
        $messageArray['to_name'] = $messageArray['toName'];
        unset($messageArray['toName']);
        $messageLogObj->set($messageArray);

        $this->assertTrue($logger::log(self::$_settings, 'Message', $messageLogObj));
        $this->assertTrue($logger::log(self::$_settings, 'Message', $messageArray));

        $query = 'SELECT * FROM message_log_' . date('Ym') . ' ORDER BY id ASC';
        $res = self::$_db->query($query);

        $this->assertCount(2, $res);

        //clear log table
        $this->assertTrue(self::$_db->query('DROP TABLE message_log_' . date('Ym') . ';'));
        /** @var Redis $cacher */
        $cacher = CachingFactory::instance(self::$_testSettings);
        $keys = $cacher->findKeys(self::$_testSettings->db->name . '*');
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $this->assertTrue($cacher->del($key));
            }
        }
    }
}