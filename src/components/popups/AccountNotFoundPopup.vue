<template>
  <div v-if="modelValue" class="login-popup-overlay" @click="close">
    <div class="login-popup" @click.stop>
      <div class="login-popup-content">
        <button class="popup-close" @click="close" aria-label="Close">
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <div class="popup-header">
          <div class="error-icon-wrap">
            <i class="bi bi-steam error-steam-icon"></i>
            <div class="error-badge">
              <i class="bi bi-exclamation-lg"></i>
            </div>
          </div>
          <h3>Account Not Found</h3>
          <p>Your Steam account isn't linked to any Tempus player profile.</p>
        </div>
        <div class="error-info-box">
          <i class="bi bi-info-circle"></i>
          <span
            >Only players who have played on
            <strong>Tempus servers</strong> have a profile. If you've played
            recently, your data may not have synced yet.</span
          >
        </div>
        <div class="popup-actions" style="margin-top: 1.5rem">
          <button class="btn continue-btn" @click="close">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "AccountNotFoundPopup",
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["update:modelValue"],
  methods: {
    close() {
      this.$emit("update:modelValue", false);
    },
  },
};
</script>

<style scoped>
.login-popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  backdrop-filter: blur(4px);
}
.login-popup {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  border-radius: 12px;
  max-width: 450px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  border: 1px solid var(--color-border);
}
.login-popup-content {
  padding: 2rem;
  position: relative;
}
.popup-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  color: var(--color-text);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}
.popup-close:hover {
  background: var(--color-dark);
  color: var(--color-text);
}
.popup-header {
  text-align: center;
  margin-bottom: 1rem;
}
.popup-header h3 {
  color: var(--color-text);
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}
.popup-header p {
  color: var(--color-text);
  margin: 0;
  font-size: 0.95rem;
}
.popup-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}
.continue-btn {
  background: transparent;
  color: var(--color-text) !important;
  border: 1px solid var(--color-border) !important;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.2s ease;
}
.continue-btn:hover {
  background: var(--color-dark) !important;
  border-color: var(--color-border) !important;
}
.error-icon-wrap {
  position: relative;
  display: inline-block;
  margin-bottom: 1rem;
}
.error-steam-icon {
  font-size: 3rem;
  color: var(--color-text);
  opacity: 0.85;
}
.error-badge {
  position: absolute;
  bottom: -4px;
  right: -10px;
  background: linear-gradient(135deg, #e05c5c, #c0392b);
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid var(--color-background-new, #0f1318);
}
.error-badge i {
  font-size: 0.75rem;
  color: #fff;
  font-weight: bold;
}
.error-info-box {
  display: flex;
  align-items: flex-start;
  gap: 0.6rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(74, 111, 165, 0.25);
  border-radius: 10px;
  padding: 0.85rem 1rem;
  color: var(--color-text);
  font-size: 0.88rem;
  line-height: 1.5;
  margin-top: 1rem;
}
.error-info-box i {
  font-size: 1rem;
  color: var(--color-primary, #4a7fc0);
  flex-shrink: 0;
  margin-top: 2px;
}
.error-info-box strong {
  color: #ffffff;
}
</style>
