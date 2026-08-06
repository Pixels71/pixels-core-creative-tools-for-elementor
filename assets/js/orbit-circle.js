(function ($) {
  "use strict";

  const ORBIT_SIZE_DEFAULT = 400;
  const ORBIT_SIZE_MIN = 200;
  const ORBIT_SIZE_MAX = 1290;
  const ORBIT_SPEED_DEFAULT = 1;
  const ORBIT_SPEED_MIN = 0.01;
  const ORBIT_SPEED_MAX = 20;
  const ORBIT_DURATION_DEFAULT = 20;

  const globalScope = globalThis;

  function parseNumber(value, fallback) {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
  }

  function doubleRaf(callback) {
    globalScope.requestAnimationFrame(function () {
      globalScope.requestAnimationFrame(callback);
    });
  }

  function clampOrbitSize(px) {
    if (!Number.isFinite(px)) {
      return ORBIT_SIZE_DEFAULT;
    }

    return Math.min(ORBIT_SIZE_MAX, Math.max(ORBIT_SIZE_MIN, Math.round(px)));
  }

  function readOrbitSizePx(el) {
    const rect = el.getBoundingClientRect();
    const fromBox = Math.max(rect.width, rect.height);

    if (fromBox >= ORBIT_SIZE_MIN) {
      return clampOrbitSize(fromBox);
    }

    const raw = Number.parseInt(el.dataset.orbitSize || "", 10);

    if (Number.isFinite(raw)) {
      return clampOrbitSize(raw);
    }

    return ORBIT_SIZE_DEFAULT;
  }

  function clampOrbitSpeed(value) {
    if (!Number.isFinite(value)) {
      return ORBIT_SPEED_DEFAULT;
    }

    return Math.min(ORBIT_SPEED_MAX, Math.max(ORBIT_SPEED_MIN, value));
  }

  class OrbitCircle {
    constructor(root) {
      this.root = root;
      this.orbit = root.querySelector("[data-orbit]");
      this.center = root.querySelector("[data-orbit-center]");
      this.speed = clampOrbitSpeed(
        parseNumber(root.dataset.orbitSpeed, ORBIT_SPEED_DEFAULT),
      );
      this.duration = Math.max(
        1,
        parseNumber(root.dataset.orbitDuration, ORBIT_DURATION_DEFAULT),
      );
      this.direction =
        root.dataset.orbitDirection === "counter-clockwise"
          ? "counter-clockwise"
          : "clockwise";
      this.fadeIn = root.dataset.orbitFadeIn !== "no";
      this.fadeDuration = Math.max(
        0.1,
        parseNumber(root.dataset.orbitFadeDuration, 1),
      );
      this.resizeObserver = null;
      this.resizeTimeout = 0;
      this.isDestroyed = false;

      if (!this.orbit || !this.center) {
        return;
      }

      this.scheduleLayout = this.scheduleLayout.bind(this);
      this.runLayout = this.runLayout.bind(this);

      this.orbit.style.visibility = "visible";

      if (this.fadeIn) {
        this.orbit.style.opacity = "0";
        this.orbit.style.transition = "opacity " + this.fadeDuration + "s ease";
        doubleRaf(
          function () {
            this.orbit.style.opacity = "1";
          }.bind(this),
        );
      }

      this.scheduleLayout();
      this.bindResizeObserver();
    }

    clearAnimations() {
      this.center.style.animation = "none";

      const baskets = this.center.querySelectorAll(
        ".orbit-basket, .pixels-core-orbit-circle__basket",
      );

      baskets.forEach(function (basket) {
        basket.style.animation = "none";
      });
    }

    applyOrbitGeometry(sizePx) {
      const sizeStr = sizePx + "px";

      this.orbit.style.setProperty("--orbit-size", sizeStr);

      const hub = this.center.offsetWidth || 20;
      const hubOffset = (sizePx - hub) / 2;

      this.center.style.left = hubOffset + "px";
      this.center.style.top = hubOffset + "px";
      this.center.style.setProperty("--pixels-orbit-base", "0deg");
      this.center.style.transform = "rotate(0deg)";

      const pivotOriginY = sizePx / 2 + 10;
      const pivots = this.center.querySelectorAll(
        ".orbit-pivot-outer, .pixels-core-orbit-circle__pivot",
      );
      const count = pivots.length;

      if (!count) {
        return null;
      }

      const space = 360 / count;

      pivots.forEach(function (pivot, index) {
        const basket = pivot.querySelector(
          ".orbit-basket, .pixels-core-orbit-circle__basket",
        );
        const pivotHalf = pivot.offsetWidth / 2 || 10;
        const baseAngle = index * space;

        pivot.style.transformOrigin = pivotHalf + "px " + pivotOriginY + "px";
        pivot.style.setProperty("--pixels-orbit-base", baseAngle + "deg");
        pivot.style.transform = "rotate(" + baseAngle + "deg)";

        if (basket) {
          basket.style.transformOrigin = "center center";
          basket.style.setProperty("--pixels-orbit-base", -baseAngle + "deg");
          basket.style.transform = "rotate(" + -baseAngle + "deg)";
        }
      });

      return this.center.querySelectorAll(
        ".orbit-basket, .pixels-core-orbit-circle__basket",
      );
    }

    startSpin(basketsInWheel) {
      const effectiveDuration = this.duration / this.speed;
      const spinName =
        this.direction === "counter-clockwise"
          ? "pixels-orbit-spin-ccw"
          : "pixels-orbit-spin-cw";
      const counterName =
        this.direction === "counter-clockwise"
          ? "pixels-orbit-spin-cw"
          : "pixels-orbit-spin-ccw";

      this.center.style.animation =
        spinName + " " + effectiveDuration + "s linear infinite";

      Array.prototype.forEach.call(basketsInWheel, function (basket) {
        basket.style.animation =
          counterName + " " + effectiveDuration + "s linear infinite";
      });
    }

    runLayout() {
      if (this.isDestroyed || !this.orbit || !this.center) {
        return;
      }

      const sizePx = readOrbitSizePx(this.orbit);

      this.clearAnimations();

      const basketsInWheel = this.applyOrbitGeometry(sizePx);

      if (!basketsInWheel || !basketsInWheel.length) {
        return;
      }

      // Force reflow so restarting animation applies cleanly.
      void this.center.offsetWidth;
      this.startSpin(basketsInWheel);
    }

    scheduleLayout() {
      doubleRaf(this.runLayout);
    }

    bindResizeObserver() {
      if (typeof globalScope.ResizeObserver === "undefined") {
        return;
      }

      this.resizeObserver = new globalScope.ResizeObserver(() => {
        globalScope.clearTimeout(this.resizeTimeout);
        this.resizeTimeout = globalScope.setTimeout(this.scheduleLayout, 80);
      });
      this.resizeObserver.observe(this.orbit);
    }

    destroy() {
      this.isDestroyed = true;
      this.clearAnimations();

      if (this.resizeObserver) {
        this.resizeObserver.disconnect();
        this.resizeObserver = null;
      }

      globalScope.clearTimeout(this.resizeTimeout);
    }
  }

  const instances = new WeakMap();

  function initScope($scope) {
    $scope.find(".pixels-core-orbit-circle").each(function () {
      const root = this;

      if (instances.has(root)) {
        instances.get(root).destroy();
      }

      const instance = new OrbitCircle(root);

      instances.set(root, instance);
    });
  }

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/pixels-orbit-circle.default",
      function ($scope) {
        initScope($scope);
      },
    );
  });
})(jQuery);
