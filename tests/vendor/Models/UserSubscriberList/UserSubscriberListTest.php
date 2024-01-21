<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * UserSubscriberList Model Tests
 */

namespace vendor\Models\UserSubscriberList;

use iceCMS2\Models\User;
use iceCMS2\Models\UserSubscriberList;
use iceCMS2\Tests\Ice2CMSTestCase;
use iceCMS2\Tools\Exception;

class UserSubscriberListTest extends Ice2CMSTestCase
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

        $userSubscriberList = new UserSubscriberList(self::$_testSettings);
        $cnt = $userSubscriberList->getCnt();
        $this->assertEquals(2, $cnt);

        $userSubscriberList2 = new UserSubscriberList(self::$_testSettings);
        $res = $userSubscriberList2->getSubscribers(1);
        $this->assertEquals(1, count($res));

        $row = $res[0];
        unset($row['date_add']);
        $this->assertSame([
            'parent_id' => 2,
            'child_id' => 1,
        ], $row);

        $userSubscriberList3 = new UserSubscriberList(self::$_testSettings);
        $res = $userSubscriberList3->getSubscriptions(1);
        $this->assertEquals(1, count($res));

        $row = $res[0];
        unset($row['date_add']);
        $this->assertSame([
            'parent_id' => 1,
            'child_id' => 2,
        ], $row);
    }
}