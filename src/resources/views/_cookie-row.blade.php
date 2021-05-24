<?php
    use Pam\CookieConsent\ViewModels\CookieViewModel;
    /** @var CookieViewModel $cookieModel */
    /** @var bool $isGroupDisplayMode */
    /** @var bool $isGroupValidationMode */
?>

<div class="cookie-consent-cookie-item {{ $cookieModel->isGroup ? 'cookie-consent-cookie-group' : '' }}">
    <div class="cookie-consent-cookie-item-title">{{ trans('cookie-consent::cookies.' . $cookieModel->titleKey) }}</div>
    <div class="cookie-consent-cookie-item-detail">
        <div class="cookie-consent-cookie-item-description">{{ trans('cookie-consent::cookies.' . $cookieModel->descriptionKey) }}</div>
        @if(!$isGroupDisplayMode || ($isGroupValidationMode && $cookieModel->isGroup) || (!$isGroupValidationMode && !$cookieModel->isGroup))
            <input id="{{ $cookieModel->key }}" name="{{ $cookieModel->key }}" type="checkbox" class="cookie-consent-checkbox" @if($cookieModel->isLocked || $cookieModel->isAllowed)checked="checked"@endif @if($cookieModel->isLocked)disabled="disabled"@endif/>
            <label class="cookie-consent-switchbox" for="{{ $cookieModel->key }}"></label>
        @endif
    </div>
    @if(!$cookieModel->cookies->isEmpty())
        @foreach($cookieModel->cookies as $child)
            @include('cookie-consent::_cookie-row', [
                    'cookieModel' => $child,
                    'isGroupDisplayMode' => $isGroupDisplayMode,
                    'isGroupValidationMode' => $isGroupValidationMode
                ])
        @endforeach
    @endif
</div>