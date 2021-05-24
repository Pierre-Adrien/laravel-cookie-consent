window.onload = function() {
    CookieConsentModule.initPopup();
};

let CookieConsentModule = (function() {
    let pAuthorizeAllButton = document.getElementById(window.authorizeAllButtonId).parentNode;
    let pBackButton = document.getElementById(window.backButtonId);
    let pCloseButton = document.getElementById(window.closeButtonId);
    let pCookieConsentPopupContainer = document.getElementById(window.cookieConsentPopupContainerId);
    let pOpenPreferencesLink = document.getElementById(window.openPreferencesLinkId);
    let pPopupNotice = document.getElementById(window.popupNoticeId);
    let pPopupPreferences = document.getElementById(window.popupPreferencesId);
    let pRefuseAllButtonForm = document.getElementById(window.refuseAllButtonId).parentNode;
    let pSavePreferencesButton = document.getElementById((window.savePreferencesButtonId));
    let pUpdatePreferencesButtonIds = window.updatePreferencesButtonIds;
    const pHideClass = "cookie-consent-popup-hider";

    function pInitPopup() {
        // If no consent given
        if (window.hasConsented) {
            if (Array.isArray(pUpdatePreferencesButtonIds)) {
                for (let i = 0; i < pUpdatePreferencesButtonIds.length; i++) {
                    pInitUpdatePreferencesButtons(document.getElementById(pUpdatePreferencesButtonIds[i]));
                }
            } else {
                pInitUpdatePreferencesButtons(document.getElementById(pUpdatePreferencesButtonIds));
            }

            pCloseButton.addEventListener("click", pHidePopup);
        } else {
            // Display first popup
            pDisplayNoticePopup();

            // Initialize event open second popup
            pOpenPreferencesLink.addEventListener("click", () => {
                pDisplayPreferencesPopup();
            });

            pBackButton.addEventListener("click", () => {
                pDisplayNoticePopup()
            });
        }

        pSavePreferencesButton.addEventListener("click", () => {
            pPopupPreferences.children[0].submit();
        });
    }

    function pInitUpdatePreferencesButtons(element) {
        element.addEventListener("click", () => {
            pDisplayPreferencesPopup();
        });
    }

    function pDisplayNoticePopup() {
        pHidePopup();

        let actionItemsToDisplay = [pOpenPreferencesLink, pAuthorizeAllButton, pRefuseAllButtonForm]
        for (let i = 0; i < actionItemsToDisplay.length; i++) {
            pDisplay(actionItemsToDisplay[i]);
        }

        pDisplay(pPopupNotice);
        pDisplay(pCookieConsentPopupContainer);
    }

    function pDisplayPreferencesPopup() {
        pHidePopup();

        let actionItemsToDisplay = [pBackButton, pSavePreferencesButton, pAuthorizeAllButton];
        for (let i = 0; i < actionItemsToDisplay.length; i++) {
            pDisplay(actionItemsToDisplay[i]);
        }

        pDisplay(pPopupPreferences);
        pDisplay(pCookieConsentPopupContainer);
    }

    function pHidePopup() {
        pHide(pCookieConsentPopupContainer);

        let popupTabs = document.getElementsByClassName("cookie-consent-popup-tab");
        for (let i = 0; i < popupTabs.length; i++) {
            pHide(popupTabs[i]);
        }

        let actions = [pOpenPreferencesLink, pAuthorizeAllButton, pRefuseAllButtonForm, pBackButton, pSavePreferencesButton];
        for (let i = 0; i < actions.length; i++) {
            pHide(actions[i]);
        }

        pHide(pOpenPreferencesLink);
    }

    function pDisplay(element) {
        if (element) {
            element.classList.remove(pHideClass);
        }
    }

    function pHide(element) {
        if (element) {
            element.classList.add(pHideClass);
        }
    }

    return {
        initPopup: pInitPopup
    }
})();