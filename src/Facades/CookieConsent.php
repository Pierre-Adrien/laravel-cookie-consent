<?php

namespace Pam\CookieConsent\Facades;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool hasConsented()
 * @method static bool isAllowed(string $key)
 * @method static bool isValidationByItem()
 * @method static bool isValidationByGroup()
 * @method static bool isDisplayByGroup()
 * @method static array getAllGroupConfigsNotLocked()
 * @method static array getAllCookieConfigsNotLocked()
 * @method static View|Factory getCookieConsentPopup()
 * @method static void checkConfig()
 * @method static mixed getCookieConsent()
 */
class CookieConsent extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cookie-consent';
    }
}