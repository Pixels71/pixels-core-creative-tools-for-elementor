(function () {
  "use strict";

  function getRoot(scope) {
    if (scope && scope.querySelectorAll) {
      return scope;
    }

    return document;
  }

  function initButtonV4(root) {
    var wrappers = getRoot(root).querySelectorAll(
      "[data-pixeccte-button-v4]:not(.pixeccte-button__link--v9)",
    );

    Array.prototype.forEach.call(wrappers, function (buttonWrapper) {
      if (buttonWrapper.dataset.pixelsV4Bound === "true") {
        return;
      }

      buttonWrapper.dataset.pixelsV4Bound = "true";

      var iconWrapper = buttonWrapper.querySelector("[data-pixeccte-button-v4-icon]");
      var buttonText = buttonWrapper.querySelector("[data-pixeccte-button-v4-text]");

      if (!iconWrapper || !buttonText) {
        return;
      }

      var calculateDistance = function () {
        var flexContainer = iconWrapper.parentElement;

        if (!flexContainer) {
          return { iconTranslateXDistance: 0, textTranslateXDistance: 0 };
        }

        var flexContainerWidth = Math.ceil(flexContainer.clientWidth);
        var iconWidth = iconWrapper.clientWidth;

        return {
          iconTranslateXDistance: flexContainerWidth - iconWidth,
          textTranslateXDistance: iconWidth,
        };
      };

      buttonWrapper.addEventListener("mouseenter", function () {
        var distances = calculateDistance();
        iconWrapper.style.transform =
          "translateX(" + distances.iconTranslateXDistance + "px)";
        buttonText.style.transform =
          "translateX(-" + distances.textTranslateXDistance + "px)";
      });

      buttonWrapper.addEventListener("mouseleave", function () {
        iconWrapper.style.transform = "translateX(0)";
        buttonText.style.transform = "translateX(0)";
      });
    });
  }

  /**
   * Split an element's text into character spans for hover animation.
   *
   * @param {HTMLElement} el
   * @return {HTMLElement[]}
   */
  function splitChars(el) {
    var text = String(el.textContent || "");
    el.textContent = "";
    el.setAttribute("aria-label", text.trim());

    var chars = [];
    var i;

    for (i = 0; i < text.length; i++) {
      var span = document.createElement("span");
      span.className = "pixeccte-button__char";
      span.setAttribute("aria-hidden", "true");
      span.textContent = text.charAt(i) === " " ? "\u00a0" : text.charAt(i);
      span.style.display = "inline-block";
      span.style.transition =
        "transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1)";
      span.style.transitionDelay = i * 0.00625 + "s";
      el.appendChild(span);
      chars.push(span);
    }

    return chars;
  }

  /**
   * @return {boolean}
   */
  function createSplitHover(buttonWrapper, upperSelector, lowerSelector) {
    var upperText = buttonWrapper.querySelector(upperSelector);
    var lowerText = buttonWrapper.querySelector(lowerSelector);

    if (!upperText || !lowerText) {
      return false;
    }

    if (!String(upperText.textContent || "").trim()) {
      return false;
    }

    var upperChars = splitChars(upperText);
    var lowerChars = splitChars(lowerText);

    if (!upperChars.length || !lowerChars.length) {
      return false;
    }

    upperChars.forEach(function (char) {
      char.style.transform = "translateY(0%)";
      char.style.opacity = "1";
    });

    lowerChars.forEach(function (char) {
      char.style.transform = "translateY(100%)";
      char.style.opacity = "0";
    });

    buttonWrapper.addEventListener("mouseenter", function () {
      upperChars.forEach(function (char) {
        char.style.transform = "translateY(-100%)";
        char.style.opacity = "0";
      });
      lowerChars.forEach(function (char) {
        char.style.transform = "translateY(0%)";
        char.style.opacity = "1";
      });
    });

    buttonWrapper.addEventListener("mouseleave", function () {
      upperChars.forEach(function (char) {
        char.style.transform = "translateY(0%)";
        char.style.opacity = "1";
      });
      lowerChars.forEach(function (char) {
        char.style.transform = "translateY(100%)";
        char.style.opacity = "0";
      });
    });

    return true;
  }

  function initButtonV6(root) {
    var wrappers = getRoot(root).querySelectorAll(".pixeccte-button__inner--v6");

    Array.prototype.forEach.call(wrappers, function (buttonWrapper) {
      if (buttonWrapper.dataset.pixelsV6Bound === "true") {
        return;
      }

      if (
        createSplitHover(
          buttonWrapper,
          ".pixeccte-button__v6-text--upper",
          ".pixeccte-button__v6-text--lower",
        )
      ) {
        buttonWrapper.dataset.pixelsV6Bound = "true";
      }
    });
  }

  function initButtonV7(root) {
    var wrappers = getRoot(root).querySelectorAll(".pixeccte-button__link--v7");

    Array.prototype.forEach.call(wrappers, function (buttonWrapper) {
      if (buttonWrapper.dataset.pixelsV7Bound === "true") {
        return;
      }

      if (
        createSplitHover(
          buttonWrapper,
          ".pixeccte-button__v7-text--upper",
          ".pixeccte-button__v7-text--lower",
        )
      ) {
        buttonWrapper.dataset.pixelsV7Bound = "true";
      }
    });
  }

  function initAll(scope) {
    initButtonV4(scope);
    initButtonV6(scope);
    initButtonV7(scope);
  }

  function runReadyTriggers() {
    if (!window.jQuery || !window.elementorFrontend || !elementorFrontend.elementsHandler) {
      initAll(document);
      return;
    }

    window.jQuery(".elementor-widget-pixeccte-button").each(function () {
      try {
        elementorFrontend.elementsHandler.runReadyTrigger(this);
      } catch (error) {
        initAll(this);
      }
    });
  }

  function registerElementorHooks() {
    if (!window.elementorFrontend || !elementorFrontend.hooks) {
      return false;
    }

    if (registerElementorHooks.initialized) {
      return true;
    }

    registerElementorHooks.initialized = true;

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/pixeccte-button.default",
      function ($scope) {
        initAll($scope[0]);
      },
    );

    if (typeof requestAnimationFrame === "function") {
      requestAnimationFrame(runReadyTriggers);
    } else {
      runReadyTriggers();
    }

    return true;
  }

  function frontendFallback() {
    if (registerElementorHooks.initialized) {
      return;
    }

    initAll(document);
  }

  window.PixeccteButton = {
    init: initAll,
  };

  if (window.jQuery) {
    window.jQuery(window).on("elementor/frontend/init", registerElementorHooks);

    if (window.elementorFrontend && elementorFrontend.hooks) {
      registerElementorHooks();
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      window.setTimeout(frontendFallback, 0);
    });
  } else {
    window.setTimeout(frontendFallback, 0);
  }
})();
