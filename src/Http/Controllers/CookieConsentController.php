<?php

namespace Pam\CookieConsent\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Pam\CookieConsent\Facades\CookieConsent;

class CookieConsentController extends Controller
{
    /**
     * Accept all cookies.
     *
     * @return RedirectResponse
     */
    public function acceptAllCookies(): RedirectResponse
    {
        return self::setAllCookies(true);
    }

    /**
     * Refuse all cookies.
     *
     * @return RedirectResponse
     */
    public function refuseAllCookies(): RedirectResponse
    {
        return self::setAllCookies(false);
    }

    /**
     * Save the cookies preferences sent in the request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function saveCookiesPreferences(Request $request): RedirectResponse
    {
        $cookieConsentArray = [];

        if (CookieConsent::isValidationByGroup()) {
            foreach (CookieConsent::getAllGroupConfigsNotLocked() as $group) {
                if ($request->has($group['key'])) {
                    $cookieConsentArray[$group['key']] = true;
                } else {
                    $cookieConsentArray[$group['key']] = false;
                }
            }
        }

        $isValidationByItem = CookieConsent::isValidationByItem();
        foreach (CookieConsent::getAllCookieConfigsNotLocked() as $cookie) {
            if (($isValidationByItem && $request->has($cookie['key']) || (!$isValidationByItem && $request->has($cookie['group'])))){
                $cookieConsentArray[$cookie['key']] = true;
            } else {
                $cookieConsentArray[$cookie['key']] = false;
            }
        }

        return self::setCookie($cookieConsentArray);
    }

    #region PRIVATE METHODS
    /**
     * Set all the cookies consent with the same value.
     *
     * @param bool $consent
     * @return RedirectResponse
     */
    private function setAllCookies(bool $consent) : RedirectResponse {
        $cookieConsentArray = self::setAllGroup($consent);

        foreach (CookieConsent::getAllCookieConfigsNotLocked() as $cookie) {
            $cookieConsentArray[$cookie['key']] = $consent;
        }

        return self::setCookie($cookieConsentArray);
    }

    /**
     * Set all the groups consent with the same value
     *
     * @param bool $consent
     * @return array
     */
    private function setAllGroup(bool $consent): array {
        $cookieConsentArray = [];

        if (CookieConsent::isValidationByGroup()) {
            foreach (CookieConsent::getAllGroupConfigsNotLocked() as $group) {
                $cookieConsentArray[$group['key']] = $consent;
            }
        }

        return $cookieConsentArray;
    }

    /**
     * Set the value of the cookie-consent cookies in the response.
     *
     * @param array $value
     * @return RedirectResponse
     */
    private function setCookie(array $value): RedirectResponse
    {
        $cookie = cookie(config('cookie-consent.cookie_name'), json_encode($value), config('cookie-consent.cookie_lifetime'));

        return redirect()->back()->withCookie($cookie);
    }
    #endregion PRIVATE METHODS
}