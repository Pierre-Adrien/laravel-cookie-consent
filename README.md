[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <h3 align="center">
    <a href="https://github.com/Pierre-Adrien/laravel-cookie-consent">
        Laravel Cookie-Consent
    </a>
  </h3>

  <div align="center">
    This package provides a fully customizable cookie-consent popup for a Laravel project. 
    <br />
    <br />
    <a href="https://github.com/Pierre-Adrien/laravel-cookie-consent/issues">Report Bug</a>
    ·
    <a href="https://github.com/Pierre-Adrien/laravel-cookie-consent/issues">Request Feature</a>
  </div>
</div>

<!-- TABLE OF CONTENTS -->
## Table of Contents
<ol>
  <li>
    <a href="#about-the-project">About The Project</a>
  </li>
  <li>
    <a href="#installation">Installation</a>
  </li>
  <li><a href="#usage">Usage</a></li>
  <li><a href="#configuration">Configuration</a></li>
  <li><a href="#contributing">Contributing</a></li>
  <li><a href="#license">License</a></li>
  <li><a href="#contact">Contact</a></li>
</ol>

<!-- ABOUT THE PROJECT -->
## About The Project

Passed in the 2002 and amended in 2009, the ePrivacy Directive (EPD) has become known as the “cookie law” since its most notable 
effect was the proliferation of cookie consent pop-ups after it was passed. It supplements (and in some cases, overrides) the GDPR, 
addressing crucial aspects about the confidentiality of electronic communications and the tracking of Internet users more broadly.

To comply with the regulations governing cookies under the GDPR and the ePrivacy Directive you must:

* Receive users’ consent before you use any cookies except strictly necessary cookies.
* Provide accurate and specific information about the data each cookie tracks and its purpose in plain language before consent is received.
* Document and store consent received from users.
* Allow users to access your service even if they refuse to allow the use of certain cookies.
* Make it as easy for users to withdraw their consent as it was for them to give their consent in the first place.

This package provides a fully configurable cookie-consent popup for a [Laravel](https://laravel.com) project.

Here is a standard use of it. All possibilities are described in this documentation.

Validation popup home
![Cookie consent popup not validated home][notValidated_1-screenshot]

Validation popup preferences (not validated)
![Cookie consent popup not validated preferences][notValidated_2-screenshot]

Validation popup preferences (validated)
![Cookie consent popup validated][validated-screenshot]

<!-- INSTALLATION -->
## Installation

1. The package can be installed via composer :
   ```sh 
   composer require pierre-adrien/laravel-cookie-consent
   ```

2. Publish files :

   
Configuration : allow you to modify the configuration of the displayed popup.
   ```sh 
   php artisan vendor:publish --provider="Pam\CookieConsent\CookieConsentServiceProvider" --tag="config"
   ```
Assets : REQUIRED to proper execution.
   ```sh 
   php artisan vendor:publish --provider="Pam\CookieConsent\CookieConsentServiceProvider" --tag="assets"
   ```
Languages : allow you to set the translations. 
   ```sh 
   php artisan vendor:publish --provider="Pam\CookieConsent\CookieConsentServiceProvider" --tag="lang"
   ```
Views : allow you to modify the displayed popup.
   ```sh 
   php artisan vendor:publish --provider="Pam\CookieConsent\CookieConsentServiceProvider" --tag="views"
   ```

<!-- USAGE -->
## Usage

Add the cookie-consent popup at the end of your "body" tag :
  ```sh 
  {{ CookieConsent::getCookieConsentPopup() }}
  ```

Add content depending on a consent for a cookie key or a group key :
  ```sh 
  @if(CookieConsent::isAllowed('key'))
    {{-- Do some stuff --}}
  @endif
  ```
You can also use the ```isAllowed``` method in your php code with the ```CookieConsent``` facade.

<!-- CONFIGURATION -->
## Configuration

All the configurations of the published "config/cookie-consent.php" file are listed and explained here.

#### Cookie lifetime
* Define the user cookie consent's duration.
* Configuration key : "cookie_lifetime".
* Default value : 1 year. 
* Unit : minutes.

#### Cookie name
* Define the user cookie consent's cookie name.
* Configuration key : "cookie_name".
* Default value : "cookie-consent".
* Unit : string.

#### Cookies
* Define the cookies the user has to consent.
* Configuration key : "cookies".
* Default value : random cookies configurations, change it with what you need.
* Unit : array.
* Each cookie configuration contains some keys :
   * key : key of the cookie, must be unique.
   * title : translation key for the title.
   * description : translation key for the description.
   * group : a group key (only used with a group display, see "Preferences display mode").
   * locked : if set to "true", this cookie is only displayed for information, it is not part of the validation process.

#### Groups
* Define the cookie groups the user has to consent (only used with a group display, see "Preferences display mode").
* Configuration key : "cookies-group".
* Default value : random cookies groups configurations, change it with what you need.
* Unit : array.
* Each cookie group configuration contains some keys :
   * key : key of the group, must be unique and not the same of a cookie's key.
   * title : translation key for the title.
   * description : translation key for the description.
   * locked : if set to "true", this group is only displayed for information, it is not part of the validation process.

#### Custom class
* Define one, or many class to customize the popup design.
* Configuration key : "custom-class".
* Default value : empty.
* Unit : string.

#### IHM ids
* Used to override the ids of the popup if already exists in your website.
* Configuration key : "ihmIds".
* Default value : default ids.
* Unit : array.

#### Position
* Set the position of the popup. Can be set to "top" or "bottom".
* Configuration key : "position".
* Default value : "bottom".
* Unit : string.

#### Preferences display mode
* Set the preferences popup display mode. Can be set to "list" or "group".
* Configuration key : "preferences-display-mode".
* Default value : "list".
* Unit : string.

List display
![Cookie consent popup not validated preferences_list_display][notValidated_2-screenshot]

Group display
![Cookie consent popup not validated preferences_group_display][notValidated_3-screenshot]

#### Preferences validation mode
* Set the preferences popup validation mode (only used with a group display, see "Preferences display mode"). Can be set to "item" or "group".
* Configuration key : "preferences-validation-mode".
* Default value : "group".
* Unit : string.

Group validation
![Cookie consent popup not validated preferences_group_validation][notValidated_3-screenshot]

Item validation
![Cookie consent popup not validated preferences_item_validation][notValidated_4-screenshot]

#### Routes
* Used to override the routes of the package if already exists in your website.
* Configuration key : "routes".
* Default value : default routes.
* Unit : array.

#### Update preferences button ids
* One or many ids of your present in your website to reopen the validation popup.
* Configuration key : "updatePreferencesButtonIds".
* Default value : "update-preferences".
* Unit : string or array.

<!-- CONTRIBUTING -->
## Contributing

To contribute you can :
* <a href="https://github.com/Pierre-Adrien/laravel-cookie-consent/issues">Open an issue</a>
* Create a Pull Request with your modifications and corresponding unit tests.
   * Fork the Project
   * Create your Feature Branch (`git checkout -b feature/myFeature`)
   * Commit your Changes (`git commit -m 'message'`)
   * Push to the Branch (`git push origin feature/myFeature`)
   * Open a Pull Request

<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE` for more information.

<!-- CONTACT -->
## Contact

Project Link: [https://github.com/Pierre-Adrien/laravel-cookie-consent](https://github.com/Pierre-Adrien/laravel-cookie-consent)

<!-- MARKDOWN LINKS & IMAGES -->
[contributors-shield]: https://img.shields.io/github/contributors/Pierre-Adrien/laravel-cookie-consent.svg?style=for-the-badge
[contributors-url]: https://github.com/Pierre-Adrien/laravel-cookie-consent/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/Pierre-Adrien/laravel-cookie-consent.svg?style=for-the-badge
[forks-url]: https://github.com/Pierre-Adrien/laravel-cookie-consent/network/members
[stars-shield]: https://img.shields.io/github/stars/Pierre-Adrien/laravel-cookie-consent.svg?style=for-the-badge
[stars-url]: https://github.com/Pierre-Adrien/laravel-cookie-consent/stargazers
[issues-shield]: https://img.shields.io/github/issues/Pierre-Adrien/laravel-cookie-consent.svg?style=for-the-badge
[issues-url]: https://github.com/Pierre-Adrien/laravel-cookie-consent/issues
[license-shield]: https://img.shields.io/github/license/Pierre-Adrien/laravel-cookie-consent?style=for-the-badge
[license-url]: https://github.com/Pierre-Adrien/laravel-cookie-consent/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://www.linkedin.com/in/pierre-adrien-maison-a8626a108/
[notValidated_1-screenshot]: images/notValidated_1.png
[notValidated_2-screenshot]: images/notValidated_2.png
[notValidated_3-screenshot]: images/notValidated_3.png
[notValidated_4-screenshot]: images/notValidated_4.png
[validated-screenshot]: images/validated.png