<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * UserSubscriber Model Tests
 */

namespace vendor\Models\UserSubscriber;

use iceCMS2\Models\User;
use iceCMS2\Models\UserSubscriber;
use iceCMS2\Tests\Ice2CMSTestCase;
use iceCMS2\Tools\Exception;

class UserSubscriberTest extends Ice2CMSTestCase
{
    /**
     * DB Tables used for testing
     */
    protected static array $_dbTables = ['users', 'user_subscribers'];

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        if (session_id() === ''){
            session_start();
        }
        parent::__construct();
    }

    /**
     * Test UserSubscriber Entity class
     *
     * @return void
     * @throws Exception
     */
    public function testSubscribeUnsubscribe(): void
    {
        //Check user exist
        $user1 = new User(self::$_testSettings);
        $this->assertTrue($user1->load(1));

        $subscription = new UserSubscriber(self::$_testSettings);

        //Check subscribe
        try {
            $subscription->subscribe(1, 1);
        } catch (Exception $e) {
            $this->assertSame('You can\'t subscribe to yourself', $e->getMessage());
        }

        $this->assertTrue($subscription->subscribe(1, 2));

        $query = 'SELECT * FROM user_subscribers';
        $res = self::$_db->query($query);

        $resRow = $res[0];
        unset($resRow['date_add']);

        $this->assertSame(['parent_id' => '1', 'child_id' => '2'], $resRow);

        //Check unsubscribe
        $this->assertTrue($subscription->unsubscribe(1, 2));

        $query = 'SELECT * FROM user_subscribers';
        $res = self::$_db->query($query);

        $this->assertEmpty($res);
    }
}