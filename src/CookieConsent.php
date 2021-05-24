<?php

namespace Pam\CookieConsent;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Pam\CookieConsent\Exceptions\WrongCookiesConsentConfigurationException;
use Pam\CookieConsent\ViewModels\CookieConsentViewModel;

class CookieConsent
{
    public static string $LIST_MODE = 'list';
    public static string $GROUP_MODE = 'group';

    public static string $GROUP_VALIDATION_ITEM = 'item';
    public static string $GROUP_VALIDATION_GROUP = 'group';

    public static string $POSITION_TOP = 'top';
    public static string $POSITION_BOTTOM = 'bottom';

    /**
     * Returns the Html of the Cookie-consent popup.
     *
     * @return View|Factory
     */
    public static function getCookieConsentPopup()
    {
        Facades\CookieConsent::checkConfig();

        $viewModel = new CookieConsentViewModel(
            Facades\CookieConsent::hasConsented(),
            Facades\CookieConsent::isDisplayByGroup(),
            Facades\CookieConsent::isValidationByGroup(),
            config('cookie-consent.ihmIds'),
            config('cookie-consent.updatePreferencesButtonIds'),
            config('cookie-consent.custom-class'),
            config('cookie-consent.position'),
            config('cookie-consent.cookies'),
            config('cookie-consent.cookies-group')
        );

        return view('cookie-consent::popup-container')
            ->with('model', $viewModel);
    }

    /**
     * Returns if the cookie-consent has already been given.
     *
     * It can return false if
     *   - new cookies can be used by the website and we want the user to give his consent for them.
     *   - we change from an item validation to a group validation and we want the user to give his consent for the groups.
     *
     * @return bool
     */
    public static function hasConsented(): bool
    {
        $cookieConsent = Facades\CookieConsent::getCookieConsent();

        if (empty($cookieConsent)) return false;

        foreach (Facades\CookieConsent::getAllCookieConfigsNotLocked() as $cookie) {
            if (!property_exists($cookieConsent, $cookie['key'])) return false;
        }

        if (Facades\CookieConsent::isValidationByGroup()) {
            foreach (Facades\CookieConsent::getAllGroupConfigsNotLocked() as $group) {
                if (!property_exists($cookieConsent, $group['key'])) return false;
            }
        }

        return true;
    }

    /**
     * Check if the user gave his permission for a key.
     *
     * @param string $key.
     *
     * @return bool
     */
    public static function isAllowed(string $key): bool
    {
        if (!Facades\CookieConsent::hasConsented())
            return false;

        $userCookiesConsent = Facades\CookieConsent::getCookieConsent();

        return $userCookiesConsent->{$key};
    }

    #region HELPERS

    /**
     * Check if the configuration is in a group display.
     *
     * @return bool
     */
    public static function isDisplayByGroup(): bool {
        return config('cookie-consent.preferences-display-mode') === self::$GROUP_MODE;
    }

    /**
     * Check if the configuration is in a item validation.
     *
     * @return bool
     */
    public static function isValidationByItem(): bool {
        return config('cookie-consent.preferences-display-mode') === self::$LIST_MODE
            || config('cookie-consent.preferences-validation-mode') === self::$GROUP_VALIDATION_ITEM;
    }

    /**
     * Check if the configuration is in a group validation.
     *
     * @return bool
     */
    public static function isValidationByGroup(): bool {
        return config('cookie-consent.preferences-display-mode') === self::$GROUP_MODE
            && config('cookie-consent.preferences-validation-mode') === self::$GROUP_VALIDATION_GROUP;
    }

    /**
     * Return an array of cookies.
     * Are excluded the ones with the property locked to true.
     * Are excluded the ones linked with a locked group.
     *
     * @return array
     */
    public static function getAllCookieConfigsNotLocked(): array {
        $cookies = config('cookie-consent.cookies');
        $groups = config('cookie-consent.cookies-group');

        $cookiesNotLocked = [];

        foreach ($cookies as $cookie) {
            if (!array_key_exists('locked', $cookie) || !$cookie['locked']) {
                if (Facades\CookieConsent::isValidationByGroup()) {
                    $cookieGroup = $cookie['group'];
                    $groupFiltered = array_filter($groups, function($g) use ($cookieGroup) {
                        return $g['key'] === $cookieGroup;
                    });

                    $group = array_values($groupFiltered)[0];

                    if (!array_key_exists('locked', $group) || !$group['locked']) {
                        array_push($cookiesNotLocked, $cookie);
                    }
                } else {
                    array_push($cookiesNotLocked, $cookie);
                }
            }
        }

        return $cookiesNotLocked;
    }

    /**
     * Return an array of groups.
     * Are excluded the ones with the property locked to true.
     *
     * @return array
     */
    public static function getAllGroupConfigsNotLocked(): array {
        $groups = config('cookie-consent.cookies-group');

        $groupsNotLocked = [];

        foreach ($groups as $group) {
            if (!array_key_exists('locked', $group) || !$group['locked']) {
                array_push($groupsNotLocked, $group);
            }
        }

        return $groupsNotLocked;
    }

    /**
     * Returns the decoded Cookie or null.
     *
     * @return mixed
     */
    public static function getCookieConsent()
    {
        return json_decode($_COOKIE[config('cookie-consent.cookie_name')] ?? '');
    }

    /**
     * Check the configuration file to ensure proper operation.
     *
     * @throws WrongCookiesConsentConfigurationException
     */
    public static function checkConfig()
    {
        $position = config('cookie-consent.position');
        if (!in_array($position, [self::$POSITION_BOTTOM, self::$POSITION_TOP])) {
            throw new WrongCookiesConsentConfigurationException('The "position" configuration can only be set to "top" or "bottom".');
        }

        $displayMode = config('cookie-consent.preferences-display-mode');

        if (!in_array($displayMode, [self::$LIST_MODE, self::$GROUP_MODE])) {
            throw new WrongCookiesConsentConfigurationException('The "preferences-display-mode" configuration can only be set to "list" or "group".');
        }

        $cookies = config('cookie-consent.cookies');
        $keys = [];

        if (!is_array($cookies)) {
            throw new WrongCookiesConsentConfigurationException('The "cookies" property must be an array.');
        } else {
            foreach ($cookies as $cookie) {
                if(!array_key_exists('key', $cookie) || !array_key_exists('title', $cookie) || !array_key_exists('description', $cookie)) {
                    throw new WrongCookiesConsentConfigurationException('The cookies configuration is not well formatted. A "key", a "title" and a "description" must be provided.');
                } else if (in_array($cookie['key'], $keys)) {
                    throw new WrongCookiesConsentConfigurationException('The cookies keys must be unique.');
                }

                array_push($keys, $cookie['key']);
            }
        }

        if ($displayMode === self::$GROUP_MODE) {
            if (!in_array(config('cookie-consent.preferences-validation-mode'), [self::$GROUP_VALIDATION_ITEM, self::$GROUP_VALIDATION_GROUP])) {
                throw new WrongCookiesConsentConfigurationException('The "preferences-validation-mode" configuration can only be set to "item" or "group".');
            }

            $groups = config('cookie-consent.cookies-group');

            foreach ($groups as $group) {
                if(!array_key_exists('key', $group) || !array_key_exists('title', $group) || !array_key_exists('description', $group)) {
                    throw new WrongCookiesConsentConfigurationException('The groups configuration is not well formatted. A "key", a "title" and a "description" must be provided.');
                } else if (in_array($group['key'], $keys)) {
                    throw new WrongCookiesConsentConfigurationException('The groups keys must be unique and cannot be the same as a cookie\'s one.');
                }

                array_push($keys, $group['key']);
            }

            foreach ($cookies as $cookie) {
                if (!array_key_exists('group', $cookie)) {
                    throw new WrongCookiesConsentConfigurationException('The cookies configuration is not well formatted for a "group" display. A "group" must be provided.');
                } else if (array_search($cookie['group'], array_column($groups, 'key')) === false) {
                    throw new WrongCookiesConsentConfigurationException('The cookies configuration is not well formatted for a "group" display. The provided "group" must be present in the "cookies-group" configuration.');
                }
            }
        }
    }
    #endregion HELPERS
}