<?php

namespace Pam\CookieConsent\Tests\ViewModels;

use Illuminate\Support\Collection;
use Pam\CookieConsent\Tests\TestCase;
use Pam\CookieConsent\ViewModels\CookieViewModel;

class CookieViewModelTest extends TestCase
{
    public string $cookieKey = 'cookie-key';
    public string $cookieTitleKey = 'cookie-titleKey';
    public string $cookieDescriptionKey = 'cookie-descriptionKey';

    /**
     * Test if the view model is filled correctly.
     */
    /** @test */
    public function newVm_should_filledCorrectly(): void {
        $vm = new CookieViewModel(
            $this->cookieKey,
            $this->cookieTitleKey,
            $this->cookieDescriptionKey,
            false,
            false
        );

        $this->assertEquals($this->cookieKey, $vm->key);
        $this->assertEquals($this->cookieTitleKey, $vm->titleKey);
        $this->assertEquals($this->cookieDescriptionKey, $vm->descriptionKey);
        $this->assertFalse($vm->isLocked);
        $this->assertFalse($vm->isAllowed);
        $this->assertFalse($vm->isGroup);
        $this->assertInstanceOf(Collection::class, $vm->cookies);
        $this->assertEmpty($vm->cookies);
    }

    /**
     * AddCookie should add a cookie to the collection.
     */
    /** @test */
    public function addCookie_should_addCookie(): void {
        $vm = new CookieViewModel($this->cookieKey, $this->cookieTitleKey, $this->cookieDescriptionKey, false, true);

        $vm->addCookie(
            new CookieViewModel(
                $this->cookieKey,
                $this->cookieTitleKey,
                $this->cookieDescriptionKey,
                false,
                false
            )
        );

        $this->assertInstanceOf(Collection::class, $vm->cookies);
        $this->assertNotEmpty($vm->cookies);
        $this->assertCount(1, $vm->cookies);
    }
}