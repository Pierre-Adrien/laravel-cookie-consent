<?php

use Illuminate\Support\Facades\Route;
use Pam\CookieConsent\Http\Controllers\CookieConsentController;

Route::post(config('cookie-consent.routes.acceptAll'), [CookieConsentController::class, 'acceptAllCookies'])->name('acceptAllCookies');
Route::post(config('cookie-consent.routes.refuseAll'), [CookieConsentController::class, 'refuseAllCookies'])->name('refuseAllCookies');
Route::post(config('cookie-consent.routes.savePreferences'), [CookieConsentController::class, 'saveCookiesPreferences'])->name('saveCookiesPreferences');