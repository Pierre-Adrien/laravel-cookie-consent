<?php

namespace Pam\CookieConsent\ViewModels;

use Illuminate\Support\Collection;

class CookieViewModel
{
    public string $key;
    public string $titleKey;
    public string $descriptionKey;
    public bool $isLocked;
    public bool $isAllowed;
    public bool $isGroup;
    public Collection $cookies;

    public function __construct(string $key, string $titleKey, string $descriptionKey, bool $isLocked, bool $isAllowed, bool $isGroup = false) {
        $this->key = $key;
        $this->titleKey = $titleKey;
        $this->descriptionKey = $descriptionKey;
        $this->isLocked = $isLocked;
        $this->isAllowed = $isAllowed;
        $this->isGroup = $isGroup;
        $this->cookies = new Collection();
    }

    /**
     * Add a sub-cookie.
     *
     * @param CookieViewModel $cookie
     */
    public function addCookie(CookieViewModel $cookie) {
        $this->cookies->add($cookie);
    }
}