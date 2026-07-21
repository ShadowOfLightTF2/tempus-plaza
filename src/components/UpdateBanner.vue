<template>
  <div class="update-banner">
    <div class="update-banner-content">
      <div class="update-icon">
        <svg
          class="spinning"
          width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="M21 12a9 9 0 11-6.219-8.56" />
        </svg>
      </div>
      <span class="update-text">
        <span class="d-none d-md-inline"
          >Database is updating - Performance may be temporarily affected and
          some data may be missing</span
        >
        <span class="d-md-none">Database updating...</span>
      </span>
      <button
        class="update-close"
        @click="$emit('dismiss')"
        aria-label="Dismiss"
      >
        <svg
          width="14"
          height="14"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "UpdateBanner",
  emits: ["dismiss"],
};
</script>

<style scoped>
.update-banner {
  background: linear-gradient(135deg, #ff6b35, #f7931e);
  color: white;
  position: relative;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  animation: slideDown 0.3s ease-out;
}
.update-banner-content {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px 20px;
  gap: 12px;
  max-width: 1200px;
  margin: 0 auto;
}
.update-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
}
.spinning {
  animation: spin 2s linear infinite;
}
.update-text {
  font-weight: 600;
  font-size: 16px;
  flex: 1;
  text-align: center;
}
.update-close {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.update-close:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

@keyframes slideDown {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
