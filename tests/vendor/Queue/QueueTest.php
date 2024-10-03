<?php

declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * Logger Test class
 */

namespace vendor\Queue;

use DateTime;
use iceCMS2\Caching\CachingFactory;
use iceCMS2\Caching\Redis;
use iceCMS2\Logger\LoggerFactory;
use iceCMS2\Messages\FakeEmailTransport;
use iceCMS2\Messages\MessageFactory;
use iceCMS2\Tests\Ice2CMSTestCase;
use iceCMS2\Tools\Exception;

class QueueTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected static array $_dbTables = ['queues'];

    /**
     * Test Logger class
     *
     * @return void
     * @throws Exception
     */
    public function testQueue(): void
    {
        #example createQueue
//        QueueFactory::queue('mysql')->create('mail', ['a' => 1, 'b' => 2]);
//        QueueFactory::queue('redis')->create('mail', ['a' => 3, 'b' => 4]);
//        QueueFactory::queue('redis')->create('mail_test', ['mail' => 'test@tets.test', 'name' => 'Ivan']);
//        QueueFactory::queue('mysql')->create('mail_test', ['mail' => 'test@tets.test', 'name' => 'Ivan']);
//        QueueFactory::queue()->create('mail', ['a' => 5, 'b' => 6]);
        #example getDataInQueue
//        QueueFactory::queue('redis')->getDataByQueueName('mail_test');
//        QueueFactory::queue('mysql')->getDataByQueueName('mail_test');
        #example getDataInTask
//        QueueFactory::queue('redis')->getTaskByIndex('mail_test', 2);
//        $taskId = QueueFactory::queue('mysql')->getTaskByIndex('mail_test', 5);
        #example getStatusInQueue
//        QueueFactory::queue('redis')->getStatusByTaskId('6649e8bdb81c8');
//        QueueFactory::queue('mysql')->getStatusByTaskId('6649e8bdb81c8');
        #example runQueue
//        QueueFactory::queue('redis')->runQueue('mail_test');
//        $taskId =QueueFactory::queue('mysql')->runQueue('mail_test');
        #setStatus
//        QueueFactory::queue('redis')->setStatus('6649e8bdb81c8', 'failed', ['mail' => 'test@tets.test', 'name' => 'Ivan']);
//        QueueFactory::queue('redis')->setStatus('6649e8bf6e913');

//        $taskId = QueueFactory::queue('mysql')->setStatus('664a06d04c62e', 'failed', ['mail' => 'test@tets.test', 'name' => 'Ivan']);
//      $taskId =  QueueFactory::queue('mysql')->setStatus('6649e8bf6e913');
        #getTasksByStatus
        //$taskId = QueueFactory::queue('redis')->getTasksByStatus('in process');
//        $taskId = QueueFactory::queue('mysql')->getTasksByStatus('in process');

    }
}