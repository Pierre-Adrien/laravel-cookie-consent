<?php

namespace Pam\CookieConsent\Tests;

use Illuminate\Support\Facades\Config;
use Mockery;
use Pam\CookieConsent\Exceptions\WrongCookiesConsentConfigurationException;
use Pam\CookieConsent\Facades\CookieConsent;
use Symfony\Component\DomCrawler\Crawler;

class CookieConsentTest extends TestCase
{
    private Mockery\MockInterface $cookieConsentFacadeMock;

    public function setUp(): void {
        parent::setUp();

        $this->cookieConsentFacadeMock = Mockery::mock();
        CookieConsent::swap($this->cookieConsentFacadeMock);
    }

    #region getCookieConsentPopup
    private static array $IHM_IDS = [
        'authorizeAllButtonId' => 'cookie-consent-authorize-all',
        'backButtonId' => 'cookie-consent-back-button',
        'cookieConsentPopupContainerId' => 'cookie-consent-popup-container',
        'closeButtonId' => 'cookie-consent-close-button',
        'openPreferencesLinkId' => 'cookie-consent-open-preferences',
        'popupNoticeId' => 'cookie-consent-popup-notice',
        'popupPreferencesId' => 'cookie-consent-popup-preferences',
        'refuseAllButtonId' => 'cookie-consent-refuse-all',
        'savePreferencesButtonId' => 'cookie-consent-save-preferences'
    ];
    private static string $UPDATE_PREFERENCES_BUTTON_ID = 'update-preferences';
    private static string $CUSTOM_CLASS = 'custom-class';
    private static string $POSITION = 'bottom';

    /**
     * Test the dom elements with display list and cookies not allowed.
     */
    /** @test */
    public function view_shouldContainDomElements_displayList_cookiesNotAllowed(): void {
        $cookies = [[
            'key' => 'key_cookie_1',
            'title' => 'title_cookie_1',
            'description' => 'description_cookie_1'
        ],[
            'key' => 'key_cookie_2',
            'title' => 'title_cookie_2',
            'description' => 'description_cookie_2'
        ]];

        $this->cookieConsentFacadeMock->shouldReceive('checkConfig')
            ->once();
        $this->cookieConsentFacadeMock->shouldReceive('hasConsented')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isDisplayByGroup')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isAllowed')
            ->withAnyArgs()
            ->twice()->andReturn(false);

        Config::set('cookie-consent.ihmIds', self::$IHM_IDS);
        Config::set('cookie-consent.updatePreferencesButtonIds', self::$UPDATE_PREFERENCES_BUTTON_ID);
        Config::set('cookie-consent.custom-class', self::$CUSTOM_CLASS);
        Config::set('cookie-consent.position', self::$POSITION);
        Config::set('cookie-consent.cookies', $cookies);
        Config::set('cookie-consent.cookies-group', []);
        Config::set('cookie-consent.routes.refuseAll', 'refuseAllCookies');
        Config::set('cookie-consent.routes.acceptAll', 'acceptAllCookies');
        Config::set('cookie-consent.routes.savePreferences', 'savePreferencesCookies');

        $result = \Pam\CookieConsent\CookieConsent::getCookieConsentPopup();
        $html = $result->render();
        $crawler = new Crawler($html);

        foreach (self::$IHM_IDS as $key => $id) {
            if ($key === 'closeButtonId') {
                $this->assertCount(0, $crawler->filter('#'.$id));
            } else {
                $this->assertCount(1, $crawler->filter('#'.$id));
            }
        }

        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item'));
        $this->assertCount(0, $crawler->filter('.cookie-consent-cookie-group'));

        foreach ($cookies as $cookie) {
            $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item-detail input#'.$cookie['key']));
            $this->assertCount(0, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item-detail input#'.$cookie['key'].':checked'));
        }
    }

    /**
     * Test the dom elements with display list and cookies allowed.
     */
    /** @test */
    public function view_shouldContainDomElements_displayList_cookiesAllowed(): void {
        $cookies = [[
            'key' => 'key_cookie_1',
            'title' => 'title_cookie_1',
            'description' => 'description_cookie_1'
        ],[
            'key' => 'key_cookie_2',
            'title' => 'title_cookie_2',
            'description' => 'description_cookie_2'
        ]];

        $this->cookieConsentFacadeMock->shouldReceive('checkConfig')
            ->once();
        $this->cookieConsentFacadeMock->shouldReceive('hasConsented')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isDisplayByGroup')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isAllowed')
            ->withAnyArgs()
            ->twice()->andReturn(true);

        Config::set('cookie-consent.ihmIds', self::$IHM_IDS);
        Config::set('cookie-consent.updatePreferencesButtonIds', self::$UPDATE_PREFERENCES_BUTTON_ID);
        Config::set('cookie-consent.custom-class', self::$CUSTOM_CLASS);
        Config::set('cookie-consent.position', self::$POSITION);
        Config::set('cookie-consent.cookies', $cookies);
        Config::set('cookie-consent.cookies-group', []);
        Config::set('cookie-consent.routes.refuseAll', 'refuseAllCookies');
        Config::set('cookie-consent.routes.acceptAll', 'acceptAllCookies');
        Config::set('cookie-consent.routes.savePreferences', 'savePreferencesCookies');

        $result = \Pam\CookieConsent\CookieConsent::getCookieConsentPopup();
        $html = $result->render();
        $crawler = new Crawler($html);

        foreach (self::$IHM_IDS as $key => $id) {
            if ($key === 'backButtonId') {
                $this->assertEquals(0, $crawler->filter('#'.$id)->count());
            } else {
                $this->assertEquals(1, $crawler->filter('#'.$id)->count());
            }
        }

        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item'));
        $this->assertCount(0, $crawler->filter('.cookie-consent-cookie-group'));

        foreach ($cookies as $cookie) {
            $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item-detail input#'.$cookie['key']));
            $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item-detail input#'.$cookie['key'].':checked'));
        }
    }

    /**
     * Test the dom elements with display group, validation group and cookies not allowed.
     */
    /** @test */
    public function view_shouldContainDomElements_displayGroup_validationGroup_cookiesNotAllowed(): void {
        $cookies = [[
            'key' => 'key_cookie_1',
            'title' => 'title_cookie_1',
            'description' => 'description_cookie_1',
            'group' => 'group_key'
        ],[
            'key' => 'key_cookie_2',
            'title' => 'title_cookie_2',
            'description' => 'description_cookie_2',
            'group' => 'group_key'
        ]];

        $groups = [[
            'key' => 'group_key',
            'title' => 'title_group',
            'description' => 'description_group'
        ]];

        $this->cookieConsentFacadeMock->shouldReceive('checkConfig')
            ->once();
        $this->cookieConsentFacadeMock->shouldReceive('hasConsented')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isDisplayByGroup')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isAllowed')
            ->withAnyArgs()
            ->times(3)->andReturn(false);

        Config::set('cookie-consent.ihmIds', self::$IHM_IDS);
        Config::set('cookie-consent.updatePreferencesButtonIds', self::$UPDATE_PREFERENCES_BUTTON_ID);
        Config::set('cookie-consent.custom-class', self::$CUSTOM_CLASS);
        Config::set('cookie-consent.position', self::$POSITION);
        Config::set('cookie-consent.cookies', $cookies);
        Config::set('cookie-consent.cookies-group', $groups);
        Config::set('cookie-consent.routes.refuseAll', 'refuseAllCookies');
        Config::set('cookie-consent.routes.acceptAll', 'acceptAllCookies');
        Config::set('cookie-consent.routes.savePreferences', 'savePreferencesCookies');

        $result = \Pam\CookieConsent\CookieConsent::getCookieConsentPopup();
        $html = $result->render();
        $crawler = new Crawler($html);

        foreach (self::$IHM_IDS as $key => $id) {
            if ($key === 'closeButtonId') {
                $this->assertCount(0, $crawler->filter('#'.$id));
            } else {
                $this->assertCount(1, $crawler->filter('#'.$id));
            }
        }

        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item:not(.cookie-consent-cookie-group)'));
        $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-group'));

        $group = $groups[0];
        $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item-detail input#'.$group['key']));
        $this->assertCount(0, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item-detail input#'.$group['key'].':checked'));

        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item'));

        foreach ($cookies as $cookie) {
            $this->assertCount(0, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item input#'.$cookie['key']));
        }
    }

    /**
     * Test the dom elements with display group, validation group and cookies allowed.
     */
    /** @test */
    public function view_shouldContainDomElements_displayGroup_validationGroup_cookiesAllowed(): void {
        $cookies = [[
            'key' => 'key_cookie_1',
            'title' => 'title_cookie_1',
            'description' => 'description_cookie_1',
            'group' => 'group_key'
        ],[
            'key' => 'key_cookie_2',
            'title' => 'title_cookie_2',
            'description' => 'description_cookie_2',
            'group' => 'group_key'
        ]];

        $groups = [[
            'key' => 'group_key',
            'title' => 'title_group',
            'description' => 'description_group'
        ]];

        $this->cookieConsentFacadeMock->shouldReceive('checkConfig')
            ->once();
        $this->cookieConsentFacadeMock->shouldReceive('hasConsented')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isDisplayByGroup')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isAllowed')
            ->withAnyArgs()
            ->times(3)->andReturn(true);

        Config::set('cookie-consent.ihmIds', self::$IHM_IDS);
        Config::set('cookie-consent.updatePreferencesButtonIds', self::$UPDATE_PREFERENCES_BUTTON_ID);
        Config::set('cookie-consent.custom-class', self::$CUSTOM_CLASS);
        Config::set('cookie-consent.position', self::$POSITION);
        Config::set('cookie-consent.cookies', $cookies);
        Config::set('cookie-consent.cookies-group', $groups);
        Config::set('cookie-consent.routes.refuseAll', 'refuseAllCookies');
        Config::set('cookie-consent.routes.acceptAll', 'acceptAllCookies');
        Config::set('cookie-consent.routes.savePreferences', 'savePreferencesCookies');

        $result = \Pam\CookieConsent\CookieConsent::getCookieConsentPopup();
        $html = $result->render();
        $crawler = new Crawler($html);

        foreach (self::$IHM_IDS as $key => $id) {
            if ($key === 'backButtonId') {
                $this->assertCount(0, $crawler->filter('#'.$id));
            } else {
                $this->assertCount(1, $crawler->filter('#'.$id));
            }
        }

        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item:not(.cookie-consent-cookie-group)'));
        $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-group'));

        $group = $groups[0];
        $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item-detail input#'.$group['key'].':checked'));

        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item'));

        foreach ($cookies as $cookie) {
            $this->assertCount(0, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item input#'.$cookie['key']));
        }
    }

    /**
     * Test the dom elements with display group, validation item and cookies not allowed.
     */
    /** @test */
    public function view_shouldContainDomElements_displayGroup_validationItem_cookiesNotAllowed(): void {
        $cookies = [[
            'key' => 'key_cookie_1',
            'title' => 'title_cookie_1',
            'description' => 'description_cookie_1',
            'group' => 'group_key'
        ],[
            'key' => 'key_cookie_2',
            'title' => 'title_cookie_2',
            'description' => 'description_cookie_2',
            'group' => 'group_key'
        ]];

        $groups = [[
            'key' => 'group_key',
            'title' => 'title_group',
            'description' => 'description_group'
        ]];

        $this->cookieConsentFacadeMock->shouldReceive('checkConfig')
            ->once();
        $this->cookieConsentFacadeMock->shouldReceive('hasConsented')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isDisplayByGroup')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isAllowed')
            ->withAnyArgs()
            ->times(2)->andReturn(false);

        Config::set('cookie-consent.ihmIds', self::$IHM_IDS);
        Config::set('cookie-consent.updatePreferencesButtonIds', self::$UPDATE_PREFERENCES_BUTTON_ID);
        Config::set('cookie-consent.custom-class', self::$CUSTOM_CLASS);
        Config::set('cookie-consent.position', self::$POSITION);
        Config::set('cookie-consent.cookies', $cookies);
        Config::set('cookie-consent.cookies-group', $groups);
        Config::set('cookie-consent.routes.refuseAll', 'refuseAllCookies');
        Config::set('cookie-consent.routes.acceptAll', 'acceptAllCookies');
        Config::set('cookie-consent.routes.savePreferences', 'savePreferencesCookies');

        $result = \Pam\CookieConsent\CookieConsent::getCookieConsentPopup();
        $html = $result->render();
        $crawler = new Crawler($html);

        foreach (self::$IHM_IDS as $key => $id) {
            if ($key === 'closeButtonId') {
                $this->assertCount(0, $crawler->filter('#'.$id));
            } else {
                $this->assertCount(1, $crawler->filter('#'.$id));
            }
        }

        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item:not(.cookie-consent-cookie-group)'));
        $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-group'));

        $group = $groups[0];
        $this->assertCount(0, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item-detail input#'.$group['key']));
        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item'));

        foreach ($cookies as $cookie) {
            $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item input#'.$cookie['key']));
        }
    }

    /**
     * Test the dom elements with display group, validation item and cookies allowed.
     */
    /** @test */
    public function view_shouldContainDomElements_displayGroup_validationItem_cookiesAllowed(): void {
        $cookies = [[
            'key' => 'key_cookie_1',
            'title' => 'title_cookie_1',
            'description' => 'description_cookie_1',
            'group' => 'group_key'
        ],[
            'key' => 'key_cookie_2',
            'title' => 'title_cookie_2',
            'description' => 'description_cookie_2',
            'group' => 'group_key'
        ]];

        $groups = [[
            'key' => 'group_key',
            'title' => 'title_group',
            'description' => 'description_group'
        ]];

        $this->cookieConsentFacadeMock->shouldReceive('checkConfig')
            ->once();
        $this->cookieConsentFacadeMock->shouldReceive('hasConsented')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isDisplayByGroup')
            ->once()->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()->andReturn(false);
        $this->cookieConsentFacadeMock->shouldReceive('isAllowed')
            ->withAnyArgs()
            ->times(2)->andReturn(true);

        Config::set('cookie-consent.ihmIds', self::$IHM_IDS);
        Config::set('cookie-consent.updatePreferencesButtonIds', self::$UPDATE_PREFERENCES_BUTTON_ID);
        Config::set('cookie-consent.custom-class', self::$CUSTOM_CLASS);
        Config::set('cookie-consent.position', self::$POSITION);
        Config::set('cookie-consent.cookies', $cookies);
        Config::set('cookie-consent.cookies-group', $groups);
        Config::set('cookie-consent.routes.refuseAll', 'refuseAllCookies');
        Config::set('cookie-consent.routes.acceptAll', 'acceptAllCookies');
        Config::set('cookie-consent.routes.savePreferences', 'savePreferencesCookies');

        $result = \Pam\CookieConsent\CookieConsent::getCookieConsentPopup();
        $html = $result->render();
        $crawler = new Crawler($html);

        foreach (self::$IHM_IDS as $key => $id) {
            if ($key === 'backButtonId') {
                $this->assertCount(0, $crawler->filter('#'.$id));
            } else {
                $this->assertCount(1, $crawler->filter('#'.$id));
            }
        }

        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item:not(.cookie-consent-cookie-group)'));
        $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-group'));

        $group = $groups[0];
        $this->assertCount(0, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item-detail input#'.$group['key']));
        $this->assertCount(2, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item'));

        foreach ($cookies as $cookie) {
            $this->assertCount(1, $crawler->filter('.cookie-consent-cookie-item .cookie-consent-cookie-item input#'.$cookie['key'].':checked'));
        }
    }
    #endregion getCookieConsentPopup

    #region hasConsented
    /**
     * Test if we get false when the cookie is not set.
     * We should receive false.
     */
    /** @test */
    public function hasConsented_shouldReturnFalse_withEmptyCookieConsent(): void {
        $this->cookieConsentFacadeMock->shouldReceive('getCookieConsent')
            ->once()
            ->andReturn(null);
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('getAllCookieConfigsNotLocked');
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('isValidationByGroup');
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('getAllGroupConfigsNotLocked');

        $result = \Pam\CookieConsent\CookieConsent::hasConsented();

        $this->assertFalse($result);
    }

    /**
     * Test if a cookie is missing in the consented ones.
     * We should receive false.
     */
    /** @test */
    public function hasConsented_shouldReturnFalse_withMissingCookie(): void {
        $this->cookieConsentFacadeMock->shouldReceive('getCookieConsent')
            ->once()
            ->andReturn(json_decode("{\"cookie_1\":true}"));
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()
            ->andReturn([[
                'key' => 'cookie_1',
                'title' => 'cookie_1_title',
                'description' => 'cookie_1_description'
            ],[
                'key' => 'cookie_2',
                'title' => 'cookie_2_title',
                'description' => 'cookie_2_description'
            ]]);
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('isValidationByGroup');
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('getAllGroupConfigsNotLocked');

        $result = \Pam\CookieConsent\CookieConsent::hasConsented();

        $this->assertFalse($result);
    }

    /**
     * Test if all cookies are in the consented ones without a group validation.
     * We should receive true.
     */
    /** @test */
    public function hasConsented_shouldReturnTrue_withAllCookies_and_withoutGroupValidation(): void {
        $this->cookieConsentFacadeMock->shouldReceive('getCookieConsent')
            ->once()
            ->andReturn(json_decode("{\"cookie_1\":true, \"cookie_2\":false}"));
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()
            ->andReturn([[
                'key' => 'cookie_1',
                'title' => 'cookie_1_title',
                'description' => 'cookie_1_description'
            ],[
                'key' => 'cookie_2',
                'title' => 'cookie_2_title',
                'description' => 'cookie_2_description'
            ]]);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()
            ->andReturn(false);
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('getAllGroupConfigsNotLocked');

        $result = \Pam\CookieConsent\CookieConsent::hasConsented();

        $this->assertTrue($result);
    }

    /**
     * Test if all cookies are in the consented ones but not all groups with a group validation.
     * We should receive false.
     */
    /** @test */
    public function hasConsented_shouldReturnFalse_withMissingGroup_and_withGroupValidation(): void {
        $this->cookieConsentFacadeMock->shouldReceive('getCookieConsent')
            ->once()
            ->andReturn(json_decode("{\"cookie_1\":true, \"cookie_2\":false}"));
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()
            ->andReturn([[
                'key' => 'cookie_1',
                'title' => 'cookie_1_title',
                'description' => 'cookie_1_description',
                'group' => 'group_1'
            ],[
                'key' => 'cookie_2',
                'title' => 'cookie_2_title',
                'description' => 'cookie_2_description',
                'group' => 'group_1'
            ]]);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()
            ->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('getAllGroupConfigsNotLocked')
            ->once()
            ->andReturn([[
                'key' => 'group_1',
                'title' => 'group_1_title',
                'description' => 'group_1_description'
            ]]);

        $result = \Pam\CookieConsent\CookieConsent::hasConsented();

        $this->assertFalse($result);
    }

    /**
     * Test if all cookies and groups are in the consented ones with a group validation.
     * We should receive true.
     */
    /** @test */
    public function hasConsented_shouldReturnTrue_withAllGroups_and_withGroupValidation(): void {
        $this->cookieConsentFacadeMock->shouldReceive('getCookieConsent')
            ->once()
            ->andReturn(json_decode("{\"cookie_1\":false, \"cookie_2\":false, \"group_1\":false}"));
        $this->cookieConsentFacadeMock->shouldReceive('getAllCookieConfigsNotLocked')
            ->once()
            ->andReturn([[
                'key' => 'cookie_1',
                'title' => 'cookie_1_title',
                'description' => 'cookie_1_description',
                'group' => 'group_1'
            ],[
                'key' => 'cookie_2',
                'title' => 'cookie_2_title',
                'description' => 'cookie_2_description',
                'group' => 'group_1'
            ]]);
        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->once()
            ->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('getAllGroupConfigsNotLocked')
            ->once()
            ->andReturn([[
                'key' => 'group_1',
                'title' => 'group_1_title',
                'description' => 'group_1_description'
            ]]);

        $result = \Pam\CookieConsent\CookieConsent::hasConsented();

        $this->assertTrue($result);
    }
    #endregion hasConsented

    #region isAllowed
    /**
     * Test if the cookies are not consented.
     * We should receive false.
     */
    /** @test */
    public function isAllowed_shouldReturnFalse_ifCookiesNotConsented(): void {
        $this->cookieConsentFacadeMock->shouldReceive('hasConsented')
            ->once()
            ->andReturn(false);
        $this->cookieConsentFacadeMock->shouldNotHaveReceived('getCookieConsent');

        $result = \Pam\CookieConsent\CookieConsent::isAllowed('cookie_1');

        $this->assertFalse($result);
    }

    /**
     * Test if the cookies are consented and refused for the given key.
     * We should receive false.
     */
    /** @test */
    public function isAllowed_shouldReturnFalse_ifCookiesConsented_refusedKey(): void {
        $this->cookieConsentFacadeMock->shouldReceive('hasConsented')
            ->once()
            ->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('getCookieConsent')
            ->once()
            ->andReturn(json_decode("{\"cookie_1\":false}"));

        $result = \Pam\CookieConsent\CookieConsent::isAllowed('cookie_1');

        $this->assertFalse($result);
    }

    /**
     * Test if the cookies are consented and accepted for the given key.
     * We should receive false.
     */
    /** @test */
    public function isAllowed_shouldReturnFalse_ifCookiesConsented_acceptedKey(): void {
        $this->cookieConsentFacadeMock->shouldReceive('hasConsented')
            ->once()
            ->andReturn(true);
        $this->cookieConsentFacadeMock->shouldReceive('getCookieConsent')
            ->once()
            ->andReturn(json_decode("{\"cookie_1\":true}"));

        $result = \Pam\CookieConsent\CookieConsent::isAllowed('cookie_1');

        $this->assertTrue($result);
    }
    #endregion isAllowed

    #region isDisplayByGroup
    /**
     * Test if the config 'preferences-display-mode' is set to 'group'.
     * Should return true.
     */
    /** @test */
    public function isDisplayByGroup_shouldReturnTrue(): void {
        Config::set('cookie-consent.preferences-display-mode', 'group');

        $result = \Pam\CookieConsent\CookieConsent::isDisplayByGroup();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertTrue($result);
    }

    /**
     * Test if the config 'preferences-display-mode' is set to any other string than group.
     * Should return false.
     */
    /** @test */
    public function isDisplayByGroup_shouldReturnFalse_withAnyOtherStringThanGroup(): void {
        Config::set('cookie-consent.preferences-display-mode', 'other');

        $result = \Pam\CookieConsent\CookieConsent::isDisplayByGroup();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertFalse($result);
    }
    #endregion isDisplayByGroup

    #region isValidationByItem
    /**
     * Test if the config 'preferences-display-mode' is set to 'list'.
     * Should return true.
     */
    /** @test */
    public function isValidationByItem_shouldReturnTrue_withListDisplayMode(): void {
        Config::set('cookie-consent.preferences-display-mode', 'list');

        $result = \Pam\CookieConsent\CookieConsent::isValidationByItem();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertTrue($result);
    }

    /**
     * Test if the config 'preferences-display-mode' is not set to 'list'
     * and if the config 'preferences-validation-mode' is set to 'item'.
     * Should return true.
     */
    /** @test */
    public function isValidationByItem_shouldReturnTrue_withoutListDisplayMode_and_withItemValidation(): void {
        Config::set('cookie-consent.preferences-display-mode', 'other');
        Config::set('cookie-consent.preferences-validation-mode', 'item');

        $result = \Pam\CookieConsent\CookieConsent::isValidationByItem();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertTrue($result);
    }

    /**
     * Test if the config 'preferences-display-mode' is not set to 'list'
     * and if the config 'preferences-validation-mode' is set to 'item'.
     * Should return false.
     */
    /** @test */
    public function isValidationByItem_shouldReturnFalse_withoutListDisplayMode_and_withoutItemValidation(): void {
        Config::set('cookie-consent.preferences-display-mode', 'other');
        Config::set('cookie-consent.preferences-validation-mode', 'other');

        $result = \Pam\CookieConsent\CookieConsent::isValidationByItem();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertFalse($result);
    }
    #endregion isValidationByItem

    #region isValidationByGroup
    /**
     * Test if the config 'preferences-display-mode' is not set to 'group'.
     * Should return false.
     */
    /** @test */
    public function isValidationByGroup_shouldReturnFalse_withoutGroupDisplayMode(): void {
        Config::set('cookie-consent.preferences-display-mode', 'other');

        $result = \Pam\CookieConsent\CookieConsent::isValidationByGroup();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertFalse($result);
    }

    /**
     * Test if the config 'preferences-display-mode' is set to 'group'
     * and the config 'preferences-validation-mode' is not set to 'group'.
     * Should return false.
     */
    /** @test */
    public function isValidationByGroup_shouldReturnFalse_withGroupDisplayMode_and_withoutGroupValidationMode(): void {
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.preferences-validation-mode', 'other');

        $result = \Pam\CookieConsent\CookieConsent::isValidationByGroup();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertFalse($result);
    }

    /**
     * Test if the config 'preferences-display-mode' is set to 'group'
     * and the config 'preferences-validation-mode' is set to 'group'.
     * Should return false.
     */
    /** @test */
    public function isValidationByGroup_shouldReturnTrue_withGroupDisplayMode_and_withGroupValidationMode(): void {
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.preferences-validation-mode', 'group');

        $result = \Pam\CookieConsent\CookieConsent::isValidationByGroup();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertTrue($result);
    }
    #endregion isValidationByGroup

    #region getAllCookieConfigsNotLocked
    /**
     * Test with locked cookie.
     * Should return an empty array.
     */
    /** @test */
    public function getAllCookieConfigsNotLocked_shouldReturnAnEmptyArray(): void {
        Config::set('cookie-consent.cookies', [[
            'key' => 'cookie_locked',
            'title' => 'cookie_locked_title',
            'description' => 'cookie_locked_description',
            'locked' => true
        ]]);
        Config::set('cookie-consent.cookies-group', []);

        $result = \Pam\CookieConsent\CookieConsent::getAllCookieConfigsNotLocked();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertEquals([], $result);
    }

    /**
     * Test without group validation with a locked cookie and a not locked one.
     * Should return the not locked cookie.
     */
    /** @test */
    public function getAllCookieConfigsNotLocked_shouldReturnAnArrayOfCookiesNotLocked_notValidationByGroup(): void {
        Config::set('cookie-consent.cookies', [[
            'key' => 'cookie_locked',
            'title' => 'cookie_locked_title',
            'description' => 'cookie_locked_description',
            'locked' => true
        ], [
            'key' => 'cookie_1',
            'title' => 'cookie_1_title',
            'description' => 'cookie_1_description'
        ],[
            'key' => 'cookie_2',
            'title' => 'cookie_2_title',
            'description' => 'cookie_2_title'
        ]]);
        Config::set('cookie-consent.cookies-group', []);

        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->twice()
            ->andReturn(false);

        $result = \Pam\CookieConsent\CookieConsent::getAllCookieConfigsNotLocked();

        $this->assertEquals([[
            'key' => 'cookie_1',
            'title' => 'cookie_1_title',
            'description' => 'cookie_1_description'
        ],[
            'key' => 'cookie_2',
            'title' => 'cookie_2_title',
            'description' => 'cookie_2_title'
        ]], $result);
    }

    /**
     * Test with group validation with a locked group and an unlocked one.
     * Should return all the cookies except the ones in the locked group.
     */
    /** @test */
    public function getAllCookieConfigsNotLocked_shouldReturnAnArrayOfCookiesNotLocked_validationByGroup_withLockedGroup(): void {
        Config::set('cookie-consent.cookies', [[
            'key' => 'cookie_1',
            'title' => 'cookie_1_title',
            'description' => 'cookie_1_description',
            'group' => 'locked_group'
        ],[
            'key' => 'cookie_2',
            'title' => 'cookie_2_title',
            'description' => 'cookie_2_title',
            'group' => 'group_1'
        ]]);
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'locked_group',
            'title' => 'locked_group_title',
            'description' => 'locked_group_description',
            'locked' => true
        ],[
            'key' => 'group_1',
            'title' => 'group_1_title',
            'description' => 'group_1_description'
        ]]);

        $this->cookieConsentFacadeMock->shouldReceive('isValidationByGroup')
            ->twice()
            ->andReturn(true);

        $result = \Pam\CookieConsent\CookieConsent::getAllCookieConfigsNotLocked();

        $this->assertEquals([[
            'key' => 'cookie_2',
            'title' => 'cookie_2_title',
            'description' => 'cookie_2_title',
            'group' => 'group_1'
        ]], $result);
    }
    #endregion getAllCookieConfigsNotLocked

    #region getAllGroupConfigsNotLocked
    /**
     * Test without not locked group.
     * Should return an empty array.
     */
    /** @test */
    public function getAllGroupConfigsNotLocked_shouldReturnAnEmptyArray(): void {
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'locked_group',
            'title' => 'locked_group_title',
            'description' => 'locked_group_description',
            'locked' => true
        ]]);

        $result = \Pam\CookieConsent\CookieConsent::getAllGroupConfigsNotLocked();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertEquals([], $result);
    }

    /**
     * Test with a locked group and a not locked one.
     * Should return the not locked group.
     */
    /** @test */
    public function getAllGroupConfigsNotLocked_shouldReturnAnArrayOfGroupsNotLocked(): void {
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'locked_group',
            'title' => 'locked_group_title',
            'description' => 'locked_group_description',
            'locked' => true
        ],[
            'key' => 'group_1',
            'title' => 'group_1title',
            'description' => 'group_1description'
        ]]);

        $result = \Pam\CookieConsent\CookieConsent::getAllGroupConfigsNotLocked();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertEquals([[
            'key' => 'group_1',
            'title' => 'group_1title',
            'description' => 'group_1description'
        ]], $result);
    }
    #endregion getAllGroupConfigsNotLocked

    #region getCookieConsent
    /**
     * Test if we get the cookie-consent content.
     * Should return cookie consent content
     */
    /** @test */
    public function getCookieConsent_shouldReturnCookieConsentContent(): void {
        $cookieValue = 'test_cookie_value';
        $cookieNameTest = 'cookie-name-test';

        Config::set('cookie-consent.cookie_name', $cookieNameTest);

        $_COOKIE[$cookieNameTest] = json_encode($cookieValue);

        $result = \Pam\CookieConsent\CookieConsent::getCookieConsent();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
        $this->assertEquals($cookieValue, $result);
    }
    #endregion getCookieConsent

    #region checkConfig
    /**
     * Test if the position is invalid.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_invalidPosition(): void {
        Config::set('cookie-consent.position', 'invalid');

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The "position" configuration can only be set to "top" or "bottom".');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if the display-mode is invalid.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_invalidDisplayMode(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'invalid');

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The "preferences-display-mode" configuration can only be set to "list" or "group".');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if the cookies is not an array.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_cookiesNotArray(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'list');
        Config::set('cookie-consent.cookies', 'invalid');

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The "cookies" property must be an array.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if the cookies are empty.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_cookiesEmpty(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'list');
        Config::set('cookie-consent.cookies');

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The "cookies" property must be an array.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a cookie has no key.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_cookiesWithoutKey(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'list');
        Config::set('cookie-consent.cookies', [[
            'title' => 'title',
            'description' => 'description'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The cookies configuration is not well formatted. A "key", a "title" and a "description" must be provided.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a cookie has no title.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_cookiesWithoutTitle(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'list');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key',
            'description' => 'description'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The cookies configuration is not well formatted. A "key", a "title" and a "description" must be provided.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a cookie has no description.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_cookiesWithoutDescription(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'list');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key',
            'title' => 'title'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The cookies configuration is not well formatted. A "key", a "title" and a "description" must be provided.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a cookie key is not unique.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_cookiesWithSameKey(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'list');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key',
            'title' => 'title',
            'description' => 'description'
        ],[
            'key' => 'key',
            'title' => 'title',
            'description' => 'description'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The cookies keys must be unique.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if the validation-mode is invalid in group display mode.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_displayGroup_invalidValidationMode(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key',
            'title' => 'title',
            'description' => 'description'
        ]]);
        Config::set('cookie-consent.preferences-validation-mode', 'invalid');

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The "preferences-validation-mode" configuration can only be set to "item" or "group".');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a group has no key in group display mode.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_groupWithoutKey(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key_cookie',
            'title' => 'title_cookie',
            'description' => 'description_cookie'
        ]]);
        Config::set('cookie-consent.preferences-validation-mode', 'group');
        Config::set('cookie-consent.cookies-group', [[
            'title' => 'title_group',
            'description' => 'description_group'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The groups configuration is not well formatted. A "key", a "title" and a "description" must be provided.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a group has no title in group display mode.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_groupWithoutTitle(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key_cookie',
            'title' => 'title_cookie',
            'description' => 'description_cookie'
        ]]);
        Config::set('cookie-consent.preferences-validation-mode', 'group');
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'key_group',
            'description' => 'description_group'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The groups configuration is not well formatted. A "key", a "title" and a "description" must be provided.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a group has no description in group display mode.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_groupWithoutDescription(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key_cookie',
            'title' => 'title_cookie',
            'description' => 'description_cookie'
        ]]);
        Config::set('cookie-consent.preferences-validation-mode', 'group');
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'key_group',
            'title' => 'title_group'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The groups configuration is not well formatted. A "key", a "title" and a "description" must be provided.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a group key is not unique in group display mode.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_groupsWithSameKey(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key_cookie',
            'title' => 'title_cookie',
            'description' => 'description_cookie'
        ]]);
        Config::set('cookie-consent.preferences-validation-mode', 'group');
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'key_group',
            'title' => 'title_group',
            'description' => 'description_group'
        ],[
            'key' => 'key_group',
            'title' => 'title_group',
            'description' => 'description_group'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The groups keys must be unique and cannot be the same as a cookie\'s one.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a group key already exists as a cookie's key in group display mode.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_groupsWithSameKeyAsCookie(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key',
            'title' => 'title_cookie',
            'description' => 'description_cookie'
        ]]);
        Config::set('cookie-consent.preferences-validation-mode', 'group');
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'key',
            'title' => 'title_group',
            'description' => 'description_group'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The groups keys must be unique and cannot be the same as a cookie\'s one.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a cookie has no group in group display mode.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_cookieWithoutGroup(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key_cookie',
            'title' => 'title_cookie',
            'description' => 'description_cookie'
        ]]);
        Config::set('cookie-consent.preferences-validation-mode', 'group');
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'key_group',
            'title' => 'title_group',
            'description' => 'description_group'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The cookies configuration is not well formatted for a "group" display. A "group" must be provided.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test if a cookie has a not existing group in group display mode.
     * We should get an exception
     */
    /** @test */
    public function checkConfig_shouldThrow_cookieWithNotExistingGroup(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key_cookie',
            'title' => 'title_cookie',
            'description' => 'description_cookie',
            'group' => 'not_existing_group'
        ]]);
        Config::set('cookie-consent.preferences-validation-mode', 'group');
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'key_group',
            'title' => 'title_group',
            'description' => 'description_group'
        ]]);

        $this->expectException(WrongCookiesConsentConfigurationException::class);
        $this->expectExceptionMessage('The cookies configuration is not well formatted for a "group" display. The provided "group" must be present in the "cookies-group" configuration.');

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test with a valid configuration in list display mode.
     * We shouldn't get an exception
     */
    /** @test
     * @throws WrongCookiesConsentConfigurationException
     */
    public function checkConfig_shouldNotThrow_displayList(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'list');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key_cookie',
            'title' => 'title_cookie',
            'description' => 'description_cookie'
        ]]);

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }

    /**
     * Test with a valid configuration in group display mode.
     * We shouldn't get an exception
     */
    /** @test
     * @throws WrongCookiesConsentConfigurationException
     */
    public function checkConfig_shouldNotThrow_displayGroup(): void {
        Config::set('cookie-consent.position', 'bottom');
        Config::set('cookie-consent.preferences-display-mode', 'group');
        Config::set('cookie-consent.cookies', [[
            'key' => 'key_cookie',
            'title' => 'title_cookie',
            'description' => 'description_cookie',
            'group' => 'key_group'
        ]]);
        Config::set('cookie-consent.preferences-validation-mode', 'group');
        Config::set('cookie-consent.cookies-group', [[
            'key' => 'key_group',
            'title' => 'title_group',
            'description' => 'description_group'
        ]]);

        \Pam\CookieConsent\CookieConsent::checkConfig();

        $this->cookieConsentFacadeMock->shouldNotHaveBeenCalled();
    }
    #endregion checkConfig
}