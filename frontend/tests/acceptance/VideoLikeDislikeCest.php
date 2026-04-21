<?php
namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;

class VideoLikeDislikeCest
{
    private const VIDEO_ID = 8;
    private const VIEWER_USERNAME = 'like_user';
    private const VIEWER_PASSWORD = 'test123456';
    private const LIKE_FORM_SELECTOR = '[data-testid="video-like-form"]';
    private const DISLIKE_FORM_SELECTOR = '[data-testid="video-dislike-form"]';
    private const LIKE_BUTTON_SELECTOR = '[data-testid="video-like-button"]';
    private const DISLIKE_BUTTON_SELECTOR = '[data-testid="video-dislike-button"]';
    private const LIKE_COUNT_SELECTOR = '[data-testid="video-like-count"]';
    private const DISLIKE_COUNT_SELECTOR = '[data-testid="video-dislike-count"]';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => \common\fixtures\UserFixture::class,
                'dataFile' => codecept_data_dir() . 'video_like_user.php',
            ],
            'video' => [
                'class' => \common\fixtures\VideosFixture::class,
                'dataFile' => codecept_data_dir() . 'video_like_video.php',
            ],
            'video_like' => [
                'class' => \common\fixtures\VideoLikeFixture::class,
                'dataFile' => codecept_data_dir() . 'video_like.php',
            ],
        ];
    }

    public function testGuestCannotLikeVideo(AcceptanceTester $I)
    {
        $I->amOnPage('/video/view?id=' . self::VIDEO_ID);
        $I->waitForElementVisible(self::LIKE_BUTTON_SELECTOR, 5);
        $I->submitForm(self::LIKE_FORM_SELECTOR, []);

        $I->waitForElementVisible('h1', 5);
        $I->see('Login', 'h1');
        $I->seeInCurrentUrl('/site/login');
    }

    public function testAuthenticatedUserCanToggleVideoLike(AcceptanceTester $I)
    {
        $this->loginAsViewer($I);
        $this->assertToggleReaction(
            $I,
            self::LIKE_FORM_SELECTOR,
            self::LIKE_BUTTON_SELECTOR,
            self::LIKE_COUNT_SELECTOR
        );
    }

    public function testAuthenticatedUserCanToggleVideoDislike(AcceptanceTester $I)
    {
        $this->loginAsViewer($I);
        $this->assertToggleReaction(
            $I,
            self::DISLIKE_FORM_SELECTOR,
            self::DISLIKE_BUTTON_SELECTOR,
            self::DISLIKE_COUNT_SELECTOR
        );
    }

    private function loginAsViewer(AcceptanceTester $I): void
    {
        $I->amOnPage('/site/login');
        $I->fillField('LoginForm[username]', self::VIEWER_USERNAME);
        $I->fillField('LoginForm[password]', self::VIEWER_PASSWORD);
        $I->click('button[name="login-button"]');
        $I->wait(5);
        $I->dontSeeInCurrentUrl('/site/login');
    }

    private function assertToggleReaction(
        AcceptanceTester $I,
        string $formSelector,
        string $buttonSelector,
        string $countSelector
    ): void {
        $I->amOnPage('/video/view?id=' . self::VIDEO_ID);
        $state = $this->readReactionButtonState($I, $buttonSelector, $countSelector);

        $I->waitForElementVisible($buttonSelector, 5);
        $I->seeElement($buttonSelector . '.' . ($state['isActive'] ? 'btn-outline-primary' : 'btn-outline-secondary'));
        $I->submitForm($formSelector, []);

        $expectedCount = $state['isActive'] ? ($state['count'] - 1) : ($state['count'] + 1);
        $expectedClass = $state['isActive'] ? 'btn-outline-secondary' : 'btn-outline-primary';

        $I->waitForElementVisible($buttonSelector, 5);
        $I->waitForText((string) $expectedCount, 5, $countSelector);
        $I->seeElement($buttonSelector . '.' . $expectedClass);
    }

    private function readReactionButtonState(AcceptanceTester $I, string $buttonSelector, string $countSelector): array
    {
        $I->waitForElementVisible($buttonSelector, 5);

        $count = (int) $I->grabTextFrom($countSelector);
        $class = (string) $I->grabAttributeFrom($buttonSelector, 'class');

        return [
            'count' => $count,
            'isActive' => strpos($class, 'btn-outline-primary') !== false,
        ];
    }
}
