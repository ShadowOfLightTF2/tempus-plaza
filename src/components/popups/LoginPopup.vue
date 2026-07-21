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
          <h3>Welcome to Tempus Plaza!</h3>
          <p>Login for enhanced features and personalized experience</p>
          <small class="popup-disclaimer"
            >This popup will not appear again</small
          >
        </div>
        <div class="popup-features">
          <div class="feature-item">
            <i class="bi bi-person-circle"></i
            ><span>Easy navigation to your profile</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-person-fill-gear"></i
            ><span>Personalized player profiles</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-graph-up"></i
            ><span>See your own run on map leaderboards</span>
          </div>
          <div class="feature-item">
            <i class="bi bi-trophy"></i
            ><span>Automatically loads your player lookup</span>
          </div>
        </div>
        <div class="popup-actions">
          <button class="btn login-popup-btn" @click="login">
            <i class="bi bi-steam"></i> Login with Steam
          </button>
          <button class="btn continue-btn" @click="close">
            Continue without login
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "LoginPopup",
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["update:modelValue", "login-with-steam"],
  methods: {
    close() {
      this.$emit("update:modelValue", false);
      localStorage.setItem("tempus_popup_shown", "true");
    },
    login() {
      this.$emit("login-with-steam");
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
.popup-disclaimer {
  color: var(--color-text-soft);
  font-size: 0.85rem;
  opacity: 0.8;
  display: block;
  margin: 0;
}
.popup-features {
  margin-bottom: 2rem;
}
.feature-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 0;
  color: var(--color-text);
  font-size: 0.9rem;
}
.feature-item i {
  margin-right: 0.75rem;
  color: var(--color-text);
  font-size: 1.1rem;
  width: 20px;
  text-align: center;
}
.popup-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}
.login-popup-btn {
  background: #1b2838;
  color: var(--color-text) !important;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}
.login-popup-btn:hover {
  background: #2a3f5f;
  transform: translateY(-1px);
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
.bi-steam {
  color: var(--color-text);
}
</style>
