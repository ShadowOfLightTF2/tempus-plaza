<template>
  <component
    :is="tag"
    :href="resolvedHref"
    :rel="relAttr"
    @click="handleClick"
    @auxclick="handleAuxClick"
    @mousedown="handleMouseDown"
    v-on="
      needsCustomLongPress
        ? {
            touchstart: handleTouchStart,
            touchmove: handleTouchMove,
            touchend: handleTouchEnd,
            touchcancel: handleTouchCancel,
          }
        : {}
    "
    v-bind="$attrs"
  >
    <slot></slot>
  </component>
</template>
<script>
const LONG_PRESS_DURATION = 500; // ms
const MOVE_CANCEL_THRESHOLD = 10; // px

export default {
  name: "SmartLink",
  props: {
    href: String,
    to: [String, Object],
    tag: { type: String, default: "a" },
    nofollow: { type: Boolean, default: false },
  },
  data() {
    return {
      longPressTimer: null,
      longPressTriggered: false,
      touchStartX: 0,
      touchStartY: 0,
    };
  },
  computed: {
    // Real <a href> elements already get native long-press-to-open-in-new-tab from the browser itself
    // Our own long-press handling is only needed when we're NOT rendering a real anchor with an href e.g. tag="button" or a custom component where no such native gesture exists
    needsCustomLongPress() {
      return this.tag !== "a" || !this.resolvedHref;
    },
    resolvedHref() {
      if (this.href) return this.href;
      if (this.to) {
        const route = this.$router.resolve(this.to);
        return route.href;
      }
      return null;
    },
    relAttr() {
      if (this.nofollow) return "nofollow";
      return null;
    },
  },
  beforeUnmount() {
    this.clearLongPressTimer();
  },
  methods: {
    handleClick(event) {
      // Suppress the click that follows a long-press-triggered new-tab open.
      if (this.longPressTriggered) {
        event.preventDefault();
        event.stopPropagation();
        this.longPressTriggered = false;
        return;
      }
      if (event.ctrlKey || event.metaKey) {
        event.preventDefault();
        this.openInNewTab();
        return;
      }
      if (this.to) {
        event.preventDefault();
        this.$router.push(this.to);
        return;
      }
      this.$emit("click", event);
    },
    handleMouseDown(event) {
      if (event.button === 1) {
        event.preventDefault();
      }
    },
    handleAuxClick(event) {
      if (event.button === 1) {
        event.preventDefault();
        event.stopPropagation();
        this.openInNewTab();
      }
    },
    handleTouchStart(event) {
      if (event.touches.length !== 1) {
        this.clearLongPressTimer();
        return;
      }
      this.longPressTriggered = false;
      const touch = event.touches[0];
      this.touchStartX = touch.clientX;
      this.touchStartY = touch.clientY;

      this.longPressTimer = setTimeout(() => {
        this.longPressTriggered = true;
        this.longPressTimer = null;
        if (navigator.vibrate) {
          navigator.vibrate(10);
        }
        // Note: we deliberately do NOT call openInNewTab() here.
        // window.open() must run synchronously inside a trusted user-gesture
        // event (like touchend) or mobile browsers will silently block it
        // as a popup, since a setTimeout callback isn't considered
        // gesture-triggered.
      }, LONG_PRESS_DURATION);
    },
    handleTouchMove(event) {
      if (!this.longPressTimer) return;
      const touch = event.touches[0];
      const dx = Math.abs(touch.clientX - this.touchStartX);
      const dy = Math.abs(touch.clientY - this.touchStartY);
      if (dx > MOVE_CANCEL_THRESHOLD || dy > MOVE_CANCEL_THRESHOLD) {
        this.clearLongPressTimer();
      }
    },
    handleTouchEnd(event) {
      this.clearLongPressTimer();
      if (this.longPressTriggered) {
        // Prevent the synthetic click (and any default navigation/zoom) that mobile browsers fire after touchend.
        event.preventDefault();
        // Call this synchronously within touchend (a trusted user-gesture event) so the browser doesn't block it as a popup.
        this.openInNewTab();
      }
    },
    handleTouchCancel() {
      this.clearLongPressTimer();
      this.longPressTriggered = false;
    },
    clearLongPressTimer() {
      if (this.longPressTimer) {
        clearTimeout(this.longPressTimer);
        this.longPressTimer = null;
      }
    },
    openInNewTab() {
      if (this.href) {
        window.open(this.href, "_blank");
      } else if (this.to) {
        const route = this.$router.resolve(this.to);
        window.open(route.href, "_blank");
      }
    },
  },
};
</script>
