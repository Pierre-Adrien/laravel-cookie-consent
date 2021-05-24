<?php

namespace Pam\CookieConsent\Tests\Http\Controllers;

use Illuminate\Http\Request;
use Mockery;
use Illuminate\Support\Facades\Config;
use Pam\CookieConsent\Facades\CookieConsent;
use Pam\CookieConsent\Http\Controllers\CookieConsentController;
use Pam\CookieConsent\Tests\TestCase;

class CookieConsentControllerTest extends TestCase
{
    const TEST_COOKIE_NAME = 'testCookieName';

    private Mockery\MockInterface $cookieConsentFacadeMock;

    #region TEST_DATA
    const COOKIE_TEST_NO_GROUP_1 =  [
        'key' => 'no_group_1',
        'title' => 'title-no_group_1',
        'description' => 'description-no_group_1'
    ];
    const COOKIE_TEST_NO_GROUP_2 =  [
        'key' => 'no_group_2',
        'title' => 'title-no_group_2',
        'description' => 'description-no_group_2'
    ];
    const GROUP_TEST_1 = [
        'key' => 'group_1',
        'title' => 'title-group_1',
        'description' => 'description-group_1'
    ];
    const GROUP_TEST_2 = [
        'key' => 'group_2',
        'title' => 'title-group_2',
        'description' => 'description-group_2'
    ];
    const COOKIE_TEST_WITH_GROUP_1 =  [
        'key' => 'with_group_1',
        'title' => 'title-with_group_1',
        'description' => 'description-with_group_1',
        'group' => 'group_1'
    ];
    const COOKIE_TEST_WITH_GROUP_2 =  [
        'key' => 'with_group_2',
        'title' => 'title-with_group_2',
        'description' => 'description-with_group_2',
        'group' => 'group_1'
    ];
    const COOKIE_TEST_WITH_GROUP_3 =  [
        'key' => 'with_group_3',
        'title' => 'title-with_group_3',
        'description' => 'description-with_group_3',
        'group' => 'group_2'
    ];
    #endregion TEST_DATA

    public function setUp(): void {
        parent::setUp();

        Config::set('cookie-consent.cookie_name', self::TEST_COOKIE_NAME);
        Config::set('cookie-consent.cookie_lifetime', 365 * 24 * 60);

        $this->cookieConsentFacadeMock = Mockery::mock();
        CookieConsent::swap($this->cookieConsentFacadeMock);
    }

    #region ACCEPT_ALL_COOKIES
    /**
     * Test if all the cookies are set to valid in the cookie-consent cookie in display mode list.
     */
    /** @test */
    public function acceptAllCookies_validationByItem_shouldSetValue(): void {
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('getAllGroupConfigsNotLocked');
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()->andReturn([self::COOKIE_TEST_NO_GROUP_1, self::COOKIE_TEST_NO_GROUP_2]);

        $controller = new CookieConsentController();
        $response = $controller->acceptAllCookies();

        $this->assertEquals(302, $response->getStatusCode());

        $cookiesFiltered = array_filter($response->headers->getCookies(), function($c) {
            return $c->getName() === self::TEST_COOKIE_NAME;
        });
        $this->assertNotEmpty($cookiesFiltered);

        $cookie = $cookiesFiltered[0];
        $this->assertNotNull($cookie);

        $expectedValue = [
            'no_group_1' => true,
            'no_group_2' => true
        ];

        $this->assertEquals($expectedValue, json_decode($cookie->getValue(), true));
    }

    /**
     * Test if all the cookies and groups are set to valid in the cookie-consent cookie in display mode group.
     */
    /** @test */
    public function acceptAllCookies_validationByGroup_shouldSetValue(): void {
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('getAllGroupConfigsNotLocked')
            ->once()->andReturn([self::GROUP_TEST_1]);
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()->andReturn([self::COOKIE_TEST_WITH_GROUP_1, self::COOKIE_TEST_WITH_GROUP_2]);

        $controller = new CookieConsentController();
        $response = $controller->acceptAllCookies();

        $this->assertEquals(302, $response->getStatusCode());

        $cookiesFiltered = array_filter($response->headers->getCookies(), function($c) {
            return $c->getName() === self::TEST_COOKIE_NAME;
        });
        $this->assertNotEmpty($cookiesFiltered);

        $cookie = $cookiesFiltered[0];
        $this->assertNotNull($cookie);

        $expectedValue = [
            'group_1' => true,
            'with_group_1' => true,
            'with_group_2' => true
        ];

        $this->assertEquals($expectedValue, json_decode($cookie->getValue(), true));
    }
    #endregion ACCEPT_ALL_COOKIES

    #region REFUSE_ALL_COOKIES
    /**
     * Test if all the cookies are set to not valid in the cookie-consent cookie in display mode list.
     */
    /** @test */
    public function refuseAllCookies_validationByItem_shouldSetValue(): void {
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('getAllGroupConfigsNotLocked');
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()->andReturn([self::COOKIE_TEST_NO_GROUP_1, self::COOKIE_TEST_NO_GROUP_2]);

        $controller = new CookieConsentController();
        $response = $controller->refuseAllCookies();

        $this->assertEquals(302, $response->getStatusCode());

        $cookiesFiltered = array_filter($response->headers->getCookies(), function($c) {
            return $c->getName() === self::TEST_COOKIE_NAME;
        });
        $this->assertNotEmpty($cookiesFiltered);

        $cookie = $cookiesFiltered[0];
        $this->assertNotNull($cookie);

        $expectedValue = [
            'no_group_1' => false,
            'no_group_2' => false
        ];

        $this->assertEquals($expectedValue, json_decode($cookie->getValue(), true));
    }

    /**
     * Test if all the cookies and groups are set to not valid in the cookie-consent cookie in display mode group.
     */
    /** @test */
    public function refuseAllCookies_validationByGroup_shouldSetValue(): void {
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('getAllGroupConfigsNotLocked')
            ->once()->andReturn([self::GROUP_TEST_1]);
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()->andReturn([self::COOKIE_TEST_WITH_GROUP_1, self::COOKIE_TEST_WITH_GROUP_2]);

        $controller = new CookieConsentController();
        $response = $controller->refuseAllCookies();

        $this->assertEquals(302, $response->getStatusCode());

        $cookieName = self::TEST_COOKIE_NAME;
        $cookiesFiltered = array_filter($response->headers->getCookies(), function($c) use ($cookieName) {
            return $c->getName() === $cookieName;
        });
        $this->assertNotEmpty($cookiesFiltered);

        $cookie = $cookiesFiltered[0];
        $this->assertNotNull($cookie);

        $expectedValue = [
            'group_1' => false,
            'with_group_1' => false,
            'with_group_2' => false
        ];

        $this->assertEquals($expectedValue, json_decode($cookie->getValue(), true));
    }
    #endregion REFUSE_ALL_COOKIES

    #region SAVE_COOKIES_PREFERENCES
    /**
     * Test if all the cookies are set according to the request in the cookie-consent cookie in display mode list.
     */
    /** @test */
    public function saveCookiesPreferences_validationByItem_shouldSetValue(): void {
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByItem')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('getAllGroupConfigsNotLocked');
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()->andReturn([self::COOKIE_TEST_NO_GROUP_1, self::COOKIE_TEST_NO_GROUP_2]);

        $request = Request::create('/saveCookiesPreferences', 'POST', [
            'no_group_1' => true
        ]);

        $controller = new CookieConsentController();
        $response = $controller->saveCookiesPreferences($request);

        $this->assertEquals(302, $response->getStatusCode());

        $cookieName = self::TEST_COOKIE_NAME;
        $cookiesFiltered = array_filter($response->headers->getCookies(), function($c) use ($cookieName) {
            return $c->getName() === $cookieName;
        });
        $this->assertNotEmpty($cookiesFiltered);

        $cookie = $cookiesFiltered[0];
        $this->assertNotNull($cookie);

        $expectedValue = [
            'no_group_1' => true,
            'no_group_2' => false
        ];

        $this->assertEquals($expectedValue, json_decode($cookie->getValue(), true));
    }

    /**
     * Test if all the cookies and groups are set according to the request in the cookie-consent cookie in display mode group.
     */
    /** @test */
    public function saveCookiesPreferences_validationByGroup_shouldSetValue(): void {
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByItem')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('getAllGroupConfigsNotLocked')
            ->once()->andReturn([self::GROUP_TEST_1, self::GROUP_TEST_2]);
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()->andReturn([self::COOKIE_TEST_WITH_GROUP_1, self::COOKIE_TEST_WITH_GROUP_2, self::COOKIE_TEST_WITH_GROUP_3]);

        $request = Request::create('/saveCookiesPreferences', 'POST', [
            'group_2' => true
        ]);

        $controller = new CookieConsentController();
        $response = $controller->saveCookiesPreferences($request);

        $this->assertEquals(302, $response->getStatusCode());

        $cookieName = self::TEST_COOKIE_NAME;
        $cookiesFiltered = array_filter($response->headers->getCookies(), function($c) use ($cookieName) {
            return $c->getName() === $cookieName;
        });
        $this->assertNotEmpty($cookiesFiltered);

        $cookie = $cookiesFiltered[0];
        $this->assertNotNull($cookie);

        $expectedValue = [
            'with_group_1' => false,
            'with_group_2' => false,
            'with_group_3' => true,
            'group_1' => false,
            'group_2' => true
        ];

        $this->assertEquals($expectedValue, json_decode($cookie->getValue(), true));
    }
    #endregion SAVE_COOKIES_PREFERENCES
}