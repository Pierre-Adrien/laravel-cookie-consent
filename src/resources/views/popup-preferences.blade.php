<?php
    use Pam\CookieConsent\ViewModels\CookieConsentViewModel;
    /** @var CookieConsentViewModel $model */
?>

<div id="{{ $model->ihmIds['popupPreferencesId'] }}" class="cookie-consent-popup-preferences cookie-consent-popup-tab cookie-consent-popup-hider">
    <form action="{{ url(config('cookie-consent.routes.savePreferences')) }}" method="POST">
        <div class="cookie-consent-display-preferences">
            @foreach($model->cookies as $cookie)
                @include('cookie-consent::_cookie-row', [
                    'cookieModel' => $cookie,
                    'isGroupDisplayMode' => $model->isGroupDisplayMode,
                    'isGroupValidationMode' => $model->isGroupValidationMode
                ])
            @endforeach
        </div>
    </form>
</div>