<?php

namespace Pam\CookieConsent\ViewModels;

use Illuminate\Support\Collection;
use Pam\CookieConsent\Facades\CookieConsent as FacadeCookieConsent;

class CookieConsentViewModel
{
    public bool $hasConsented;
    public bool $isGroupDisplayMode;
    public bool $isGroupValidationMode;
    public array $ihmIds;
    public array $updatePreferencesButtonIds;
    public string $customClass;
    public string $positionClass;

    private array $cookiesNotFormatted;
    private array $groupsNotFormatted;

    public Collection $cookies;

    /**
     * CookieConsentViewModel constructor.
     *
     * @param bool $hasConsented
     * @param bool $isDisplayByGroup
     * @param bool $isValidationByGroup
     * @param array $ihmIds
     * @param mixed $updatePreferencesButtonIds
     * @param string $customClass
     * @param string $positionClass
     * @param array $cookiesNotFormatted
     * @param array $groupsNotFormatted
     */
    public function __construct(
        bool $hasConsented,
        bool $isDisplayByGroup,
        bool $isValidationByGroup,
        array $ihmIds,
        $updatePreferencesButtonIds,
        string $customClass,
        string $positionClass,
        array $cookiesNotFormatted,
        array $groupsNotFormatted = []
    ) {
        $this->hasConsented = $hasConsented;
        $this->isGroupDisplayMode = $isDisplayByGroup;
        $this->isGroupValidationMode = $isValidationByGroup;
        $this->ihmIds = $ihmIds;
        $this->updatePreferencesButtonIds = is_array($updatePreferencesButtonIds) ? $updatePreferencesButtonIds : [$updatePreferencesButtonIds];
        $this->customClass = $customClass;
        $this->positionClass = $positionClass === 'top' ? 'cookie-consent-position-top' : 'cookie-consent-position-bottom';

        $this->cookiesNotFormatted = $cookiesNotFormatted;
        $this->groupsNotFormatted = $groupsNotFormatted;

        $this->cookies = $this->getFormattedCookies();
    }

    /**
     * Return the cookie configuration in a collection well formatted.
     *
     * @return Collection
     */
    private function getFormattedCookies(): Collection {
        $cookiesCollection = new Collection();

        $cookies = $this->cookiesNotFormatted;

        if ($this->isGroupDisplayMode) {
            $groups = $this->groupsNotFormatted;

            foreach ($groups as $group) {
                $lockedGroup = array_key_exists('locked', $group) ? $group['locked'] : false;
                $item = new CookieViewModel($group['key'], $group['title'], $group['description'], $lockedGroup, $lockedGroup || !$this->isGroupValidationMode ? true : FacadeCookieConsent::isAllowed($group['key']), true);

                foreach ($cookies as $cookie) {
                    if($cookie['group'] === $group['key']) {
                        $lockedCookie = array_key_exists('locked', $cookie) ? $cookie['locked'] : false;
                        $cookie = new CookieViewModel($cookie['key'], $cookie['title'], $cookie['description'], $lockedCookie, $lockedCookie ? true : FacadeCookieConsent::isAllowed($cookie['key']));
                        $item->addCookie($cookie);
                    }
                }

                $cookiesCollection->add($item);
            }
        } else {
            foreach ($cookies as $cookie) {
                $lockedCookie = array_key_exists('locked', $cookie) ? $cookie['locked'] : false;
                $cookie = new CookieViewModel($cookie['key'], $cookie['title'], $cookie['description'], $lockedCookie, $lockedCookie ? true : FacadeCookieConsent::isAllowed($cookie['key']));
                $cookiesCollection->add($cookie);
            }
        }

        return $cookiesCollection;
    }
}