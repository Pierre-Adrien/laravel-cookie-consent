<?php

namespace Pam\CookieConsent\Tests\ViewModels;

use Illuminate\Support\Collection;
use Pam\CookieConsent\Tests\TestCase;
use Pam\CookieConsent\ViewModels\CookieConsentViewModel;
use Pam\CookieConsent\ViewModels\CookieViewModel;

class CookieConsentViewModelTest extends TestCase
{
    /**
     * Test if a list display viewModel is filled correctly.
     */
    /** @test */
    public function newVm_listDisplay_should_fillCorrectly(): void {
        $ihmIdsTest = ['test1' => 'test1'];
        $updatePreferencesButtonIds = 'updatePreferenceId';
        $customClassTest = 'test1';
        $cookies = [[
            'key' => 'testCookie1',
            'title' => 'testCookie1-title',
            'description' => 'testCookie1-description'
        ], [
            'key' => 'testCookie2',
            'title' => 'testCookie2-title',
            'description' => 'testCookie2-description'
        ]];

        $vm = new CookieConsentViewModel(
            false,
            false,
            false,
            $ihmIdsTest,
            $updatePreferencesButtonIds,
            $customClassTest,
            'bottom',
            $cookies
        );

        $this->assertEquals(false, $vm->hasConsented);
        $this->assertEquals(false, $vm->isGroupDisplayMode);
        $this->assertEquals(false, $vm->isGroupValidationMode);
        $this->assertEquals($ihmIdsTest, $vm->ihmIds);
        $this->assertEquals([$updatePreferencesButtonIds], $vm->updatePreferencesButtonIds);
        $this->assertEquals($customClassTest, $vm->customClass);
        $this->assertEquals('cookie-consent-position-bottom', $vm->positionClass);

        $formattedCookies = $vm->cookies;
        $this->assertInstanceOf(Collection::class, $formattedCookies);
        $this->assertCount(2, $formattedCookies);

        $firstCookie = $formattedCookies->get(0);
        $this->assertInstanceOf(CookieViewModel::class, $firstCookie);
        /** @var CookieViewModel $firstCookie */
        $this->assertFalse($firstCookie->isGroup);
        $this->assertFalse($firstCookie->isLocked);

        $secondCookie = $formattedCookies->get(1);
        $this->assertInstanceOf(CookieViewModel::class, $secondCookie);
        /** @var CookieViewModel $secondCookie */
        $this->assertFalse($secondCookie->isGroup);
        $this->assertFalse($secondCookie->isLocked);
    }

    /**
     * Test if a group display viewModel is filled correctly.
     */
    /** @test */
    public function newVm_groupDisplay_should_fillCorrectly(): void {
        $ihmIdsTest = ['test1' => 'test1'];
        $updatePreferencesButtonIds = ['updatePreferenceId1', 'updatePreferenceId2'];
        $customClassTest = 'test1';
        $cookies = [[
            'key' => 'testCookie1',
            'title' => 'testCookie1-title',
            'description' => 'testCookie1-description',
            'group' => 'testGroup1'
        ], [
            'key' => 'testCookie2',
            'title' => 'testCookie2-title',
            'description' => 'testCookie2-description',
            'group' => 'testGroup1'
        ]];
        $groups = [[
            'key' => 'testGroup1',
            'title' => 'testGroup1-title',
            'description' => 'testGroup1-description'
        ]];

        $vm = new CookieConsentViewModel(
            true,
            true,
            true,
            $ihmIdsTest,
            $updatePreferencesButtonIds,
            $customClassTest,
            'top',
            $cookies,
            $groups
        );

        $this->assertEquals(true, $vm->hasConsented);
        $this->assertEquals(true, $vm->isGroupDisplayMode);
        $this->assertEquals(true, $vm->isGroupValidationMode);
        $this->assertEquals($ihmIdsTest, $vm->ihmIds);
        $this->assertEquals($updatePreferencesButtonIds, $vm->updatePreferencesButtonIds);
        $this->assertEquals($customClassTest, $vm->customClass);
        $this->assertEquals('cookie-consent-position-top', $vm->positionClass);

        $groupedCookies = $vm->cookies;
        $this->assertInstanceOf(Collection::class, $groupedCookies);
        $this->assertCount(1, $groupedCookies);

        $formattedGroup = $groupedCookies->get(0);
        $this->assertInstanceOf(CookieViewModel::class, $formattedGroup);

        /** @var CookieViewModel $formattedGroup */
        $this->assertTrue($formattedGroup->isGroup);
        $this->assertFalse($formattedGroup->isLocked);

        $groupCookies = $formattedGroup->cookies;
        $this->assertInstanceOf(Collection::class, $groupCookies);
        $this->assertCount(2, $groupCookies);

        $firstCookie = $groupCookies->get(0);
        $this->assertInstanceOf(CookieViewModel::class, $firstCookie);
        /** @var CookieViewModel $firstCookie */
        $this->assertFalse($firstCookie->isGroup);
        $this->assertFalse($firstCookie->isLocked);

        $secondCookie = $groupCookies->get(1);
        $this->assertInstanceOf(CookieViewModel::class, $secondCookie);
        /** @var CookieViewModel $secondCookie */
        $this->assertFalse($secondCookie->isGroup);
        $this->assertFalse($secondCookie->isLocked);
    }
}