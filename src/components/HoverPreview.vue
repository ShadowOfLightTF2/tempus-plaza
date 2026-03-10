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
        <div v-if="imageLoading" class="image-loading-overlay">
          <div class="loading-spinner-medium"></div>
        </div>
        <img
          v-show="!imageError && !imageLoading"
          :src="`/map-backgrounds/medium/${mapName}.webp`"
          :alt="mapName"
          class="preview-image"
          @error="handleImageError"
          @load="handleImageLoad"
        />
      </div>
      <div class="preview-map-name">{{ mapName }}</div>
      <div v-if="metadata" class="preview-metadata">
        <div class="tier-info">
          <div class="tier-group">
            <div
              class="icon-wrapper"
              :class="{
                'class-icon-circle':
                  metadata.intended_class === 3 ||
                  metadata.intended_class === 5,
              }"
            >
              <img src="/icons/soldier.png" alt="Soldier" class="tier-icon" />
            </div>
            <span
              class="tier-badge"
              :class="`tier-color-tier-${metadata.soldier_tier}`"
              >T{{ metadata.soldier_tier }}</span
            >
            <span
              class="rating-badge"
              :class="`rating-color-rating-${metadata.soldier_rating}`"
              >R{{ metadata.soldier_rating }}</span
            >
          </div>
          <div class="tier-group">
            <div
              class="icon-wrapper"
              :class="{
                'class-icon-circle':
                  metadata.intended_class === 4 ||
                  metadata.intended_class === 5,
              }"
            >
              <img src="/icons/demoman.png" alt="Demoman" class="tier-icon" />
            </div>
            <span
              class="tier-badge"
              :class="`tier-color-tier-${metadata.demoman_tier}`"
              >T{{ metadata.demoman_tier }}</span
            >
            <span
              class="rating-badge"
              :class="`rating-color-rating-${metadata.demoman_rating}`"
              >R{{ metadata.demoman_rating }}</span
            >
          </div>
        </div>
      </div>
      <div v-else-if="!metadataError" class="preview-metadata-loading">
        <div class="loading-spinner-small"></div>
        <span>Loading info...</span>
      </div>
      <div v-if="metadataError" class="preview-metadata-error">
        <i class="bi bi-exclamation-circle"></i>
        <span>Info unavailable</span>
      </div>
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
      imageLoading: true,
      metadata: null,
      metadataError: false,
      metadataFetchAttempted: false,
    };
  },
  computed: {
    imageContainerStyle() {
      if (this.imageError) {
        return {
          background: `
            linear-gradient(135deg, rgba(10,15,25,0.9) 0%, rgba(10,15,25,0.7) 50%, rgba(10,15,25,0.95) 100%),
            radial-gradient(circle at 30% 20%, rgba(74,111,165,0.1) 0%, transparent 50%)
          `,
        };
      }
      return {};
    },
  },
  methods: {
    async showPreview() {
      this.isVisible = true;
      this.imageError = false;
      this.imageLoading = true;
      if (
        !this.metadata &&
        !this.metadataError &&
        !this.metadataFetchAttempted
      ) {
        this.metadataFetchAttempted = true;
        await this.fetchMetadata();
      }
    },
    hidePreview() {
      this.isVisible = false;
    },
    async fetchMetadata() {
      try {
        const response = await fetch(
          `/map-backgrounds/${this.mapName}.meta.json`,
        );
        if (!response.ok) {
          throw new Error("Metadata not found");
        }
        this.metadata = await response.json();
      } catch (error) {
        console.warn(`No metadata found for ${this.mapName}:`, error);
        this.metadataError = true;
      }
    },
    updatePosition(event) {
      const offset = 20;
      const tooltipWidth = 300;
      const tooltipHeight = 280;
      let x = event.clientX + offset;
      let y = event.clientY + offset;
      if (x + tooltipWidth > window.innerWidth) {
        x = event.clientX - tooltipWidth - offset;
      }
      if (y + tooltipHeight > window.innerHeight) {
        y = event.clientY - tooltipHeight - offset;
      }
      if (x < 0) {
        x = offset;
      }
      if (y < 0) {
        y = offset;
      }
      this.tooltipX = x;
      this.tooltipY = y;
    },
    handleImageError() {
      this.imageError = true;
      this.imageLoading = false;
    },
    handleImageLoad() {
      this.imageLoading = false;
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
  background: rgba(10, 15, 25, 0.95);
  animation: fadeIn 0.2s ease-in-out;
  min-width: 300px;
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
  background: rgba(10, 15, 25, 0.9);
}
.preview-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.image-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(10, 15, 25, 0.8);
}
.loading-spinner-medium {
  width: 32px;
  height: 32px;
  border: 3px solid rgba(74, 111, 165, 0.3);
  border-top-color: #4a6fa5;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
.tier-icon {
  width: 18px;
  height: 18px;
  object-fit: contain;
}
.class-icon-circle {
  width: 26px;
  height: 26px;
  border: 2px solid rgba(74, 111, 165, 0.7);
  border-radius: 50%;
  background: rgba(10, 15, 25, 0.8);
  padding: 2px;
}
.preview-map-name {
  padding: 10px 12px 8px;
  background: rgba(10, 15, 25, 0.95);
  color: #ffffff;
  font-weight: 600;
  font-size: 0.95rem;
  text-align: center;
  border-top: 1px solid rgba(74, 111, 165, 0.3);
}
.preview-metadata {
  padding: 10px 12px;
  background: rgba(10, 15, 25, 0.9);
  border-top: 1px solid rgba(74, 111, 165, 0.2);
}
.tier-info {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 14px;
}
.tier-group {
  display: flex;
  align-items: center;
  gap: 4px;
}
.icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
}
.tier-badge {
  font-weight: 700;
  font-size: 0.75rem;
  padding: 2px 6px;
  border-radius: 4px;
  min-width: 28px;
  text-align: center;
  display: inline-block;
}
.rating-badge {
  font-weight: 700;
  font-size: 0.75rem;
  padding: 2px 6px;
  border-radius: 4px;
  min-width: 28px;
  text-align: center;
  display: inline-block;
}
.preview-metadata-loading {
  padding: 10px 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: rgba(10, 15, 25, 0.9);
  border-top: 1px solid rgba(74, 111, 165, 0.2);
  color: #8b9dc3;
  font-size: 0.8rem;
}
.loading-spinner-small {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(74, 111, 165, 0.3);
  border-top-color: #4a6fa5;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.preview-metadata-error {
  padding: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  background: rgba(10, 15, 25, 0.9);
  border-top: 1px solid rgba(74, 111, 165, 0.2);
  color: #8b9dc3;
  font-size: 0.85rem;
}
.preview-metadata-error i {
  font-size: 1.2rem;
  color: rgba(74, 111, 165, 0.6);
}
</style>
