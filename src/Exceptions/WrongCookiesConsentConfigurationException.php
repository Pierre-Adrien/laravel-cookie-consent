<?php

namespace Pam\CookieConsent\Exceptions;

use Exception;
use Throwable;

/**
 * Class WrongCookiesConsentConfigurationException
 * @package Pam\CookieConsent\Exceptions
 */
class WrongCookiesConsentConfigurationException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct("Error in CookieConsent module : " . $message, $code, $previous);
    }
}