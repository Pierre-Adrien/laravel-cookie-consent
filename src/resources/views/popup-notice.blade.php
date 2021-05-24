<?php
    use Pam\CookieConsent\ViewModels\CookieConsentViewModel;
    /** @var CookieConsentViewModel $model */
?>

<div id="{{ $model->ihmIds['popupNoticeId'] }}" class="popup-noticepopup-notice cookie-consent-popup-tab cookie-consent-popup-hider">
    <span class="description">{!! trans('cookie-consent::global.description') !!}</span>
</div>