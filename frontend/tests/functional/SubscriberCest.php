<?php
namespace frontend\tests\functional;

use common\fixtures\SubscriberFixture;
use common\fixtures\UserFixture;
use common\models\Subscriber;
use frontend\tests\FunctionalTester;


class SubscriberCest
{
    private const VIDEO_ID = 10;
    private const VIEWER_USERNAME = 'like_user';
    private const VIEWER_PASSWORD = 'test123456';
    private const SUBSCRIBE_URL = '/channel/subscribe';
    private const CHANNEL_USERNAME = 'video_owner';

    /**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'channel_subscribe_user.php',
            ],
            'subscriber' => [
                'class' => SubscriberFixture::class,
                'dataFile' => codecept_data_dir() . 'subscriber.php',
            ],
        ];
    }
    // before
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function testGuestCannotSubscribe(FunctionalTester $I)
    {
        $channel = $I->grabFixture('user', 'channel');

        $I->amOnRoute('channel/subscribe', ['username' => $channel->username]);
        $I->see('Login', 'h1');
        $I->seeInCurrentUrl('/site/login');
        $I->dontSeeRecord(Subscriber::class, [
            'user_id' => $I->grabFixture('user', 'viewer')->id,
            'channel_id' => $channel->id,
        ]);
    }

    public function testUserCanSubscribe(FunctionalTester $I)
    {
        $channel = $I->grabFixture('user', 'channel');
        $viewer = $I->grabFixture('user', 'viewer');

        $I->amLoggedInAs($viewer);
        $I->amOnRoute('channel/subscribe', ['username' => $channel->username]);
        $I->seeRecord(Subscriber::class, [
            'user_id' => $viewer->id,
            'channel_id' => $channel->id,
        ]);
    }
}
