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
        "[data-pixels-button-v4]:not(.pixels-core-button__link--v9)",
      );
  
      Array.prototype.forEach.call(wrappers, function (buttonWrapper) {
        if (buttonWrapper.dataset.pixelsV4Bound === "true") {
          return;
        }
  
        buttonWrapper.dataset.pixelsV4Bound = "true";
  
        var iconWrapper = buttonWrapper.querySelector("[data-pixels-button-v4-icon]");
        var buttonText = buttonWrapper.querySelector("[data-pixels-button-v4-text]");
  
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
     * @return {boolean}
     */
    function createSplitHover(buttonWrapper, upperSelector, lowerSelector) {
      if (typeof gsap === "undefined" || typeof SplitText === "undefined") {
        return false;
      }
  
      var upperText = buttonWrapper.querySelector(upperSelector);
      var lowerText = buttonWrapper.querySelector(lowerSelector);
  
      if (!upperText || !lowerText) {
        return false;
      }
  
      if (!String(upperText.textContent || "").trim()) {
        return false;
      }
  
      try {
        gsap.registerPlugin(SplitText);
      } catch (e) {
        // Already registered.
      }
  
      var upperSplit;
      var lowerSplit;
  
      try {
        if (typeof SplitText.create !== "undefined") {
          upperSplit = SplitText.create(upperText, { type: "chars", tag: "span" });
          lowerSplit = SplitText.create(lowerText, { type: "chars", tag: "span" });
        } else {
          upperSplit = new SplitText(upperText, { type: "chars", tag: "span" });
          lowerSplit = new SplitText(lowerText, { type: "chars", tag: "span" });
        }
      } catch (error) {
        return false;
      }
  
      if (!upperSplit.chars || !lowerSplit.chars || !upperSplit.chars.length) {
        return false;
      }
  
      var duration = 0.4;
      var stagger = 0.00625;
  
      // Upper starts in view; lower starts one line below (clipped by overflow).
      gsap.set(upperSplit.chars, {
        yPercent: 0,
        opacity: 1,
        display: "inline-block",
      });
  
      gsap.set(lowerSplit.chars, {
        yPercent: 100,
        opacity: 0,
        display: "inline-block",
      });
  
      var hoverInTl = gsap.timeline({ paused: true });
  
      hoverInTl
        .to(upperSplit.chars, {
          yPercent: -100,
          duration: duration,
          opacity: 0,
          ease: "power2.inOut",
          stagger: stagger,
        })
        .to(
          lowerSplit.chars,
          {
            yPercent: 0,
            duration: duration,
            opacity: 1,
            stagger: stagger,
            ease: "power2.inOut",
          },
          "<",
        );
  
      buttonWrapper.addEventListener("mouseenter", function () {
        hoverInTl.play();
      });
  
      buttonWrapper.addEventListener("mouseleave", function () {
        hoverInTl.reverse();
      });
  
      return true;
    }
  
    function initButtonV6(root) {
      var wrappers = getRoot(root).querySelectorAll(".pixels-core-button__inner--v6");
  
      Array.prototype.forEach.call(wrappers, function (buttonWrapper) {
        if (buttonWrapper.dataset.pixelsV6Bound === "true") {
          return;
        }
  
        if (
          createSplitHover(
            buttonWrapper,
            ".pixels-core-button__v6-text--upper",
            ".pixels-core-button__v6-text--lower",
          )
        ) {
          buttonWrapper.dataset.pixelsV6Bound = "true";
        }
      });
    }
  
    function initButtonV7(root) {
      var wrappers = getRoot(root).querySelectorAll(".pixels-core-button__link--v7");
  
      Array.prototype.forEach.call(wrappers, function (buttonWrapper) {
        if (buttonWrapper.dataset.pixelsV7Bound === "true") {
          return;
        }
  
        if (
          createSplitHover(
            buttonWrapper,
            ".pixels-core-button__v7-text--upper",
            ".pixels-core-button__v7-text--lower",
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
  
      window.jQuery(".elementor-widget-pixels-button").each(function () {
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
        "frontend/element_ready/pixels-button.default",
        function ($scope) {
          initAll($scope[0]);
        },
      );
  
      // Preview iframe often loads this file after element_ready already ran.
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
  
    window.PixelsCoreButton = {
      init: initAll,
    };
  
    if (window.jQuery) {
      window.jQuery(window).on("elementor/frontend/init", registerElementorHooks);
  
      // Editor/preview: Elementor may already be initialized when this script loads.
      if (window.elementorFrontend && elementorFrontend.hooks) {
        registerElementorHooks();
      }
    }
  
    // Fallback for pages where Elementor hooks never fire.
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", function () {
        window.setTimeout(frontendFallback, 0);
      });
    } else {
      window.setTimeout(frontendFallback, 0);
    }
})();