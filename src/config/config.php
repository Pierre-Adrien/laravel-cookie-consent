<?php

return [
    /**
     * Lifetime of the cookie (in minutes).
     */
    'cookie_lifetime' => 60 * 24 * 365,

    /**
     * Name of the cookie where the user preferences are stored.
     */
    'cookie_name' => 'cookie-consent',

    /**
     * Cookies description.
     *
     * key : cookie's identifier (required).
     * title : cookie's title translation key (required).
     * description : cookie's description translation key
     * group : cookie's corresponding group identifier (required if preferences-display-mode = 'group').
     * locked : if set to true, this cookie is display but is not part of the validation process.
     */
    'cookies' => [[
        'key' => 'required',
        'title' => 'requiredCookiesTitle',
        'description' => 'requiredCookiesDescription',
        'group' => 'requiredGroup',
        'locked' => true
    ], [
        'key' => 'googleAnalytics',
        'title' => 'googleAnalyticsTitle',
        'description' => 'googleAnalyticsDescription',
        'group' => 'analyticsGroup'
    ],[
        'key' => 'googleAds',
        'title' => 'googleAdsTitle',
        'description' => 'googleAdsDescription',
        'group' => 'adsGroup'
    ]],

    /**
     * Groups description.
     *
     * key : group's identifier (required).
     * title : group's title translation key (required).
     * description : group's description translation key
     * locked : if set to true, this group is display but is not part of the validation process
     */
    'cookies-group' => [[
        'key' => 'requiredGroup',
        'title' => 'requiredGroupTitle',
        'description' => 'requiredGroupDescription',
        'locked' => true
    ], [
        'key' => 'analyticsGroup',
        'title' => 'analyticsGroupTitle',
        'description' => 'analyticsGroupDescription'
    ], [
        'key' => 'adsGroup',
        'title' => 'adsGroupTitle',
        'description' => 'adsGroupDescription'
    ]],

    /**
     * A css class (or many 'class1 class2'). It's add at the top level of the popup, with the 'cookie-consent-popup-container' class.
     * Default design can be override with custom classes.
     */
    'custom-class' => '',

    /**
     * Ids in the views.
     *
     * Package's ids can be changed if some of the default ones already exist in the website.
     */
    'ihmIds' => [
        // Id of the button to authorize all cookies
        'authorizeAllButtonId' => 'cookie-consent-authorize-all',
        // Id of the button to return to the cookie notice popup
        'backButtonId' => 'cookie-consent-back-button',
        // Id of the popup container
        'cookieConsentPopupContainerId' => 'cookie-consent-popup-container',
        // Id of the close button
        'closeButtonId' => 'cookie-consent-close-button',
        // Id of the link to open the cookie preferences popup
        'openPreferencesLinkId' => 'cookie-consent-open-preferences',
        // Id of the notice popup
        'popupNoticeId' => 'cookie-consent-popup-notice',
        // Id of the preferences popup
        'popupPreferencesId' => 'cookie-consent-popup-preferences',
        // Id of the button to refuse all cookies
        'refuseAllButtonId' => 'cookie-consent-refuse-all',
        // Id of the button to save the cookies preferences
        'savePreferencesButtonId' => 'cookie-consent-save-preferences'
    ],

    /**
     * Position of the cookie consent popup.
     *
     * Can be 'top' or 'bottom'.
     */
    'position' => 'bottom',

    /**
     * Display mode of the preferences, can be 'list' or 'group'.
     *
     * list : a list of all the cookies with a check for each one.
     * group : display by group of cookies ; the checks position depends on the 'preferences-validation-mode' configuration.
     */
    'preferences-display-mode' => 'list',

    /**
     * Validation mode of the preferences, can be 'item' or 'group'.
     *
     * item : a check for each cookie.
     * group : a check for each group.
     *
     * The configuration is used only with the 'group' display ('preferences-display-mode')
     */
    'preferences-validation-mode' => 'group',

    /**
     * Routes used to save the user preferences.
     */
    'routes' => [
        'acceptAll' => 'acceptAllCookies',
        'refuseAll' => 'refuseAllCookies',
        'savePreferences' => 'savePreferences'
    ],

    /**
     * Link's ids to open the cookie preferences popup when already validated (can be a string or an array of strings).
     */
    'updatePreferencesButtonIds' => 'update-preferences'
];