<template>
  <transition name="fade">
    <div v-if="modelValue" class="unlock-popup-overlay" @click.self="close">
      <div class="unlock-popup">
        <button class="unlock-popup-close" @click="close" aria-label="Close">
          &times;
        </button>
        <div class="unlock-popup-header">
          <h2>New Unlocks!</h2>
          <p class="unlock-popup-subtitle">
            You've unlocked new banner customization options! Open your profile
            dropdown (top right of the navbar) to customize your profile.
          </p>
        </div>
        <div v-if="unlockedColors.length" class="unlock-section">
          <div class="unlock-section-label">Colours</div>
          <div class="unlock-grid">
            <div
              v-for="color in unlockedColors"
              :key="'c-' + color.value"
              class="unlock-item"
            >
              <div
                class="unlock-color-swatch"
                :style="{ background: color.color }"
              ></div>
              <span class="unlock-item-label">{{ color.value }}</span>
            </div>
          </div>
        </div>
        <div v-if="unlockedPatterns.length" class="unlock-section">
          <div class="unlock-section-label">Patterns</div>
          <div class="unlock-grid">
            <div
              v-for="pattern in unlockedPatterns"
              :key="'p-' + pattern.value"
              class="unlock-item"
            >
              <div
                class="unlock-pattern-swatch"
                :style="{
                  backgroundImage: pattern.thumb
                    ? `url(${pattern.thumb})`
                    : 'none',
                }"
              ></div>
              <span class="unlock-item-label">{{ pattern.label }}</span>
            </div>
          </div>
        </div>
        <button class="unlock-popup-ok" @click="close">Close</button>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "UnlockPopup",
  inject: ["navShared"],
  props: {
    modelValue: { type: Boolean, default: false },
  },
  emits: ["update:modelValue"],
  computed: {
    unlockedColors() {
      return this.navShared.colorOptions.filter((c) =>
        this.navShared.newlyUnlockedColors.includes(c.value),
      );
    },
    unlockedPatterns() {
      return this.navShared.patternOptions.filter(
        (p) =>
          p.value && this.navShared.newlyUnlockedPatterns.includes(p.value),
      );
    },
  },
  methods: {
    close() {
      this.$emit("update:modelValue", false);
      this.navShared.acknowledgeUnlocks();
    },
  },
};
</script>

<style scoped>
.unlock-popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.unlock-popup {
  position: relative;
  background: var(--color-dark);
  border: 2px solid var(--color-primary);
  border-radius: 16px;
  padding: 28px 24px 24px;
  width: 340px;
  max-width: 90vw;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.5);
}

.unlock-popup-close {
  position: absolute;
  top: 10px;
  right: 12px;
  background: none;
  border: none;
  color: var(--color-text);
  font-size: 22px;
  line-height: 1;
  cursor: pointer;
  opacity: 0.6;
  transition: opacity 0.15s;
}
.unlock-popup-close:hover {
  opacity: 1;
}

.unlock-popup-header {
  text-align: center;
  margin-bottom: 16px;
}
.unlock-popup-header h2 {
  margin: 0;
  color: var(--color-text);
  font-size: 1.4rem;
}
.unlock-popup-subtitle {
  margin: 4px 0 0;
  color: var(--color-text);
  opacity: 0.7;
  font-size: 0.85rem;
}

.unlock-section {
  margin-bottom: 16px;
}
.unlock-section-label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
  margin-bottom: 8px;
}

.unlock-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(64px, 1fr));
  gap: 10px;
}

.unlock-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.unlock-color-swatch {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.08);
}

.unlock-pattern-swatch {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  background-size: cover;
  background-position: center;
  background-color: rgba(255, 255, 255, 0.05);
}

.unlock-item-label {
  font-size: 10px;
  color: var(--color-text);
  opacity: 0.8;
  text-transform: capitalize;
  text-align: center;
}

.unlock-popup-ok {
  display: block;
  width: 100%;
  margin-top: 6px;
  background: var(--color-row);
  color: white;
  border: none;
  padding: 10px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.unlock-popup-ok:hover {
  background: var(--color-primary);
}
</style>
