/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/resources/assets/es6/app.js":
/*!*****************************************!*\
  !*** ./src/resources/assets/es6/app.js ***!
  \*****************************************/
/***/ (() => {

window.onload = function () {
  CookieConsentModule.initPopup();
};

var CookieConsentModule = function () {
  var pAuthorizeAllButton = document.getElementById(window.authorizeAllButtonId).parentNode;
  var pBackButton = document.getElementById(window.backButtonId);
  var pCloseButton = document.getElementById(window.closeButtonId);
  var pCookieConsentPopupContainer = document.getElementById(window.cookieConsentPopupContainerId);
  var pOpenPreferencesLink = document.getElementById(window.openPreferencesLinkId);
  var pPopupNotice = document.getElementById(window.popupNoticeId);
  var pPopupPreferences = document.getElementById(window.popupPreferencesId);
  var pRefuseAllButtonForm = document.getElementById(window.refuseAllButtonId).parentNode;
  var pSavePreferencesButton = document.getElementById(window.savePreferencesButtonId);
  var pUpdatePreferencesButtonIds = window.updatePreferencesButtonIds;
  var pHideClass = "cookie-consent-popup-hider";

  function pInitPopup() {
    // If no consent given
    if (window.hasConsented) {
      if (Array.isArray(pUpdatePreferencesButtonIds)) {
        for (var i = 0; i < pUpdatePreferencesButtonIds.length; i++) {
          pInitUpdatePreferencesButtons(document.getElementById(pUpdatePreferencesButtonIds[i]));
        }
      } else {
        pInitUpdatePreferencesButtons(document.getElementById(pUpdatePreferencesButtonIds));
      }

      pCloseButton.addEventListener("click", pHidePopup);
    } else {
      // Display first popup
      pDisplayNoticePopup(); // Initialize event open second popup

      pOpenPreferencesLink.addEventListener("click", function () {
        pDisplayPreferencesPopup();
      });
      pBackButton.addEventListener("click", function () {
        pDisplayNoticePopup();
      });
    }

    pSavePreferencesButton.addEventListener("click", function () {
      pPopupPreferences.children[0].submit();
    });
  }

  function pInitUpdatePreferencesButtons(element) {
    element.addEventListener("click", function () {
      pDisplayPreferencesPopup();
    });
  }

  function pDisplayNoticePopup() {
    pHidePopup();
    var actionItemsToDisplay = [pOpenPreferencesLink, pAuthorizeAllButton, pRefuseAllButtonForm];

    for (var i = 0; i < actionItemsToDisplay.length; i++) {
      pDisplay(actionItemsToDisplay[i]);
    }

    pDisplay(pPopupNotice);
    pDisplay(pCookieConsentPopupContainer);
  }

  function pDisplayPreferencesPopup() {
    pHidePopup();
    var actionItemsToDisplay = [pBackButton, pSavePreferencesButton, pAuthorizeAllButton];

    for (var i = 0; i < actionItemsToDisplay.length; i++) {
      pDisplay(actionItemsToDisplay[i]);
    }

    pDisplay(pPopupPreferences);
    pDisplay(pCookieConsentPopupContainer);
  }

  function pHidePopup() {
    pHide(pCookieConsentPopupContainer);
    var popupTabs = document.getElementsByClassName("cookie-consent-popup-tab");

    for (var i = 0; i < popupTabs.length; i++) {
      pHide(popupTabs[i]);
    }

    var actions = [pOpenPreferencesLink, pAuthorizeAllButton, pRefuseAllButtonForm, pBackButton, pSavePreferencesButton];

    for (var _i = 0; _i < actions.length; _i++) {
      pHide(actions[_i]);
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
  };
}();

/***/ }),

/***/ "./src/resources/assets/scss/app.scss":
/*!********************************************!*\
  !*** ./src/resources/assets/scss/app.scss ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					result = fn();
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/src/resources/assets/js/app": 0,
/******/ 			"src/resources/assets/css/app": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			for(moduleId in moreModules) {
/******/ 				if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 					__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 				}
/******/ 			}
/******/ 			if(runtime) runtime(__webpack_require__);
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkIds[i]] = 0;
/******/ 			}
/******/ 			__webpack_require__.O();
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkpam_laravel_cookie_consent"] = self["webpackChunkpam_laravel_cookie_consent"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["src/resources/assets/css/app"], () => (__webpack_require__("./src/resources/assets/es6/app.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["src/resources/assets/css/app"], () => (__webpack_require__("./src/resources/assets/scss/app.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;