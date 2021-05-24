<?php
    use Pam\CookieConsent\ViewModels\CookieConsentViewModel;
    /** @var CookieConsentViewModel $model */
?>

<div class="cookie-consent-popup-actions">
    <button id="{{ $model->ihmIds['openPreferencesLinkId'] }}" class="cookie-consent-manage-preferences-btn cookie-consent-popup-hider">{{ trans('cookie-consent::global.managePreferences') }}</button>

    <div class="action-buttons">
        @if(!$model->hasConsented)
            <button id="{{ $model->ihmIds['backButtonId'] }}" type="button" class="cookie-consent-btn cookie-consent-back-btn cookie-consent-popup-hider">{{ trans('cookie-consent::global.backButton') }}</button>
        @endif

        <form class="cookie-consent-popup-hider" action="{{ url(config('cookie-consent.routes.refuseAll')) }}" method="POST">
            <button id="{{ $model->ihmIds['refuseAllButtonId'] }}" class="cookie-consent-btn" type="submit">{{ trans('cookie-consent::global.refuseAll') }}</button>
        </form>

        <form class="cookie-consent-popup-hider" action="{{ url(config('cookie-consent.routes.acceptAll')) }}" method="POST">
            <button id="{{ $model->ihmIds['authorizeAllButtonId'] }}" class="cookie-consent-btn" type="submit">{{ trans('cookie-consent::global.acceptAll') }}</button>
        </form>

        <button id="{{ $model->ihmIds['savePreferencesButtonId'] }}" class="cookie-consent-btn cookie-consent-popup-hider">{{ trans('cookie-consent::global.savePreferences') }}</button>
    </div>
</div>