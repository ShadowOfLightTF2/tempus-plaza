<template>
  <div
    class="hover-preview-wrapper"
    @mouseenter="showPreview"
    @mouseleave="hidePreview"
    @mousemove="updatePosition"
  >
    <slot></slot>
  </div>
  <Teleport to="body">
    <div
      v-if="isVisible"
      class="hover-preview-tooltip"
      :style="{ top: tooltipY + 'px', left: tooltipX + 'px' }"
    >
      <div class="preview-image-container" :style="imageContainerStyle">
        <img
          v-show="!imageError"
          :src="`/map-backgrounds/medium/${mapName}.jpg`"
          :alt="mapName"
          class="preview-image"
          @error="handleImageError"
          @load="handleImageLoad"
        />
      </div>
      <div class="preview-map-name">{{ mapName }}</div>
    </div>
  </Teleport>
</template>

<script>
export default {
  name: "HoverPreview",
  props: {
    mapName: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      isVisible: false,
      tooltipX: 0,
      tooltipY: 0,
      imageError: false,
    };
  },
  computed: {
    imageContainerStyle() {
      if (this.imageError) {
        return {
          background: `
            linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
            radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%)
          `,
        };
      }
      return {};
    },
  },
  methods: {
    showPreview() {
      this.isVisible = true;
      this.imageError = false;
    },
    hidePreview() {
      this.isVisible = false;
    },
    updatePosition(event) {
      const offset = 20;
      const tooltipWidth = 300; // Width of preview
      const tooltipHeight = 250; // Approximate height (200px image + 50px name)

      let x = event.clientX + offset;
      let y = event.clientY + offset;

      // Check if tooltip goes off the right edge
      if (x + tooltipWidth > window.innerWidth) {
        x = event.clientX - tooltipWidth - offset;
      }

      // Check if tooltip goes off the bottom edge
      if (y + tooltipHeight > window.innerHeight) {
        y = event.clientY - tooltipHeight - offset;
      }

      // Check if tooltip goes off the left edge
      if (x < 0) {
        x = offset;
      }

      // Check if tooltip goes off the top edge
      if (y < 0) {
        y = offset;
      }

      this.tooltipX = x;
      this.tooltipY = y;
    },
    handleImageError() {
      this.imageError = true;
    },
    handleImageLoad() {
      this.imageError = false;
    },
  },
};
</script>

<style scoped>
.hover-preview-wrapper {
  display: inline-block;
  position: relative;
}

.hover-preview-tooltip {
  position: fixed;
  z-index: 99999;
  pointer-events: none;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
  border: 2px solid rgba(74, 111, 165, 0.5);
  background: rgba(0, 0, 0, 0.9);
  animation: fadeIn 0.2s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.preview-image-container {
  width: 300px;
  height: 200px;
  position: relative;
  overflow: hidden;
}

.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.preview-map-name {
  padding: 8px 12px;
  background: rgba(0, 0, 0, 0.8);
  color: #ffffff;
  font-weight: 600;
  font-size: 0.9rem;
  text-align: center;
  border-top: 1px solid rgba(74, 111, 165, 0.3);
}
</style>
