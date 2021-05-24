<?php

namespace Pam\CookieConsent;

use Illuminate\Support\ServiceProvider;

class CookieConsentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'cookie-consent');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'cookie-consent');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        if ($this->app->runningInConsole()) {
            // Publish translations.
            $this->publishes([
                __DIR__.'/resources/lang' => resource_path('lang/vendor/cookie-consent'),
            ], 'lang');
            //php artisan vendor:publish --provider="Pam\CookieConsent\CookieConsentServiceProvider" --tag="lang"

            // Publish config.
            $this->publishes([
                __DIR__.'/config/config.php' => config_path('cookie-consent.php'),
            ], 'config');
            //php artisan vendor:publish --provider="Pam\CookieConsent\CookieConsentServiceProvider" --tag="config"

            // Publish views.
            $this->publishes([
                __DIR__.'/resources/views' => resource_path('views/vendor/cookie-consent'),
            ], 'views');
            //php artisan vendor:publish --provider="Pam\CookieConsent\CookieConsentServiceProvider" --tag="views"

            // Publish assets.
            $this->publishes([
                __DIR__.'/resources/assets' => public_path('cookie-consent'),
            ], 'assets');
            //php artisan vendor:publish --provider="Pam\CookieConsent\CookieConsentServiceProvider" --tag="assets"
        }
    }

    public function register()
    {
        $this->app->bind('cookie-consent', function() {
            return new CookieConsent();
        });

        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'cookie-consent');
    }
}