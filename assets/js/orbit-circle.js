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
      this.timeline = null;
      this.resizeObserver = null;
      this.resizeTimeout = 0;
      this.isDestroyed = false;

      if (!this.orbit || !this.center || globalScope.gsap === undefined) {
        return;
      }

      this.scheduleLayout = this.scheduleLayout.bind(this);
      this.runLayout = this.runLayout.bind(this);

      if (this.fadeIn) {
        globalScope.gsap.from(this.orbit, {
          autoAlpha: 0,
          duration: this.fadeDuration,
        });
      }

      this.scheduleLayout();
      this.bindResizeObserver();
    }

    killTimeline() {
      if (this.timeline) {
        this.timeline.kill();
        this.timeline = null;
      }
    }

    buildTimeline(center, basketsInWheel) {
      const tl = globalScope.gsap.timeline({ repeat: -1 });
      const isCounterClockwise = this.direction === "counter-clockwise";
      const centerRotation = isCounterClockwise ? -360 : 360;
      const basketRotation = isCounterClockwise ? "+=360" : "-=360";

      tl.to(center, {
        rotation: centerRotation,
        duration: this.duration,
        ease: "none",
      });
      tl.to(
        basketsInWheel,
        {
          rotation: basketRotation,
          duration: this.duration,
          ease: "none",
        },
        0,
      );
      tl.timeScale(this.speed);
      tl.play();

      return tl;
    }

    applyOrbitGeometry(sizePx) {
      const sizeStr = sizePx + "px";

      this.orbit.style.setProperty("--orbit-size", sizeStr);

      const hub = this.center.offsetWidth || 20;
      const hubOffset = (sizePx - hub) / 2;

      globalScope.gsap.set(this.center, {
        x: hubOffset,
        y: hubOffset,
        rotation: 0,
      });

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

        globalScope.gsap.set(pivot, {
          rotation: index * space,
          transformOrigin: pivotHalf + "px " + pivotOriginY + "px",
        });

        if (basket) {
          globalScope.gsap.set(basket, {
            rotation: -index * space,
            transformOrigin: "center center",
          });
        }
      });

      return this.center.querySelectorAll(
        ".orbit-basket, .pixels-core-orbit-circle__basket",
      );
    }

    runLayout() {
      if (this.isDestroyed || !this.orbit || !this.center) {
        return;
      }

      const sizePx = readOrbitSizePx(this.orbit);

      this.killTimeline();

      const basketsInWheel = this.applyOrbitGeometry(sizePx);

      if (!basketsInWheel || !basketsInWheel.length) {
        return;
      }

      this.timeline = this.buildTimeline(this.center, basketsInWheel);
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
      this.killTimeline();

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
