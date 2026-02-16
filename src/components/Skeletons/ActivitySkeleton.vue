<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div
      class="container mx-auto py-4 d-flex flex-column align-items-center position-relative"
      style="z-index: 1"
    >
      <div class="content-container">
        <div class="table-container-wrapper">
          <div class="activity-cards">
            <div class="table-header-content">
              <div class="skeleton skeleton-icon"></div>
              <div class="skeleton skeleton-header-title"></div>
              <div class="filter-container ms-auto">
                <div class="filter-group">
                  <div class="skeleton skeleton-filter-label"></div>
                  <div class="d-flex gap-2">
                    <div class="skeleton skeleton-filter-btn"></div>
                    <div class="skeleton skeleton-filter-btn"></div>
                  </div>
                </div>
                <div class="filter-group">
                  <div class="skeleton skeleton-filter-label"></div>
                  <div class="d-flex gap-2">
                    <div class="skeleton skeleton-filter-btn"></div>
                    <div class="skeleton skeleton-filter-btn"></div>
                    <div class="skeleton skeleton-filter-btn"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="activity-cards-container">
              <div v-for="i in 20" :key="i" class="activity-card">
                <div class="activity-player-section">
                  <div class="d-flex align-items-center gap-3 w-100">
                    <div class="skeleton skeleton-avatar"></div>
                    <div class="d-flex flex-column gap-2">
                      <div class="skeleton skeleton-player-name"></div>
                      <div class="skeleton skeleton-player-country"></div>
                    </div>
                  </div>
                </div>
                <div class="activity-map-section">
                  <div class="skeleton skeleton-map-thumb"></div>
                  <div class="d-flex flex-column gap-2 flex-grow-1">
                    <div class="skeleton skeleton-map-name"></div>
                    <div class="skeleton skeleton-map-type"></div>
                  </div>
                  <div class="skeleton skeleton-class-icon"></div>
                </div>
                <div class="activity-time-section">
                  <div class="skeleton skeleton-time"></div>
                  <div class="skeleton skeleton-time-ago"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ActivitySkeleton",
};
</script>

<style scoped>
@keyframes shimmer {
  0% {
    background-position: -600px 0;
  }
  100% {
    background-position: 600px 0;
  }
}

.skeleton {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 25%,
    rgba(255, 255, 255, 0.12) 50%,
    rgba(255, 255, 255, 0.05) 75%
  );
  background-size: 600px 100%;
  animation: shimmer 1.6s infinite linear;
  border-radius: 6px;
  flex-shrink: 0;
}

.skeleton-icon {
  width: 36px;
  height: 36px;
  border-radius: 6px;
}

.skeleton-header-title {
  width: 200px;
  height: 24px;
  border-radius: 6px;
}

.skeleton-filter-label {
  width: 50px;
  height: 14px;
  border-radius: 4px;
  margin-bottom: 6px;
}

.skeleton-filter-btn {
  width: 80px;
  height: 32px;
  border-radius: 6px;
}

.skeleton-avatar {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  flex-shrink: 0;
}

.skeleton-player-name {
  width: 140px;
  height: 16px;
  border-radius: 4px;
}

.skeleton-player-country {
  width: 80px;
  height: 13px;
  border-radius: 4px;
}

.skeleton-map-thumb {
  width: 40px;
  height: 40px;
  border-radius: 4px;
  flex-shrink: 0;
}

.skeleton-map-name {
  width: 160px;
  height: 16px;
  border-radius: 4px;
}

.skeleton-map-type {
  width: 80px;
  height: 13px;
  border-radius: 4px;
}

.skeleton-class-icon {
  width: 35px;
  height: 35px;
  border-radius: 50%;
}

.skeleton-time {
  width: 100px;
  height: 16px;
  border-radius: 4px;
}

.skeleton-time-ago {
  width: 60px;
  height: 13px;
  border-radius: 4px;
}

.filter-container {
  display: flex;
  gap: 20px;
  align-items: center;
}

.filter-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.content-container {
  width: 100%;
  max-width: 1200px;
}

.table-container-wrapper {
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.table-header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
  border-bottom: 1px solid var(--color-border-soft);
}

.activity-cards {
  width: 100%;
  border-radius: 0 0 8px 8px;
  overflow: hidden;
}

.activity-cards-container {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.02);
}

.activity-card {
  display: grid;
  grid-template-columns: minmax(250px, 1.5fr) minmax(300px, 2fr) minmax(
      275px,
      1.2fr
    );
  gap: 1.5rem;
  padding: 0.3rem 0.6rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  min-height: 60px;
}

.activity-player-section {
  display: flex;
  align-items: center;
}

.activity-map-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 1.5rem;
}

.activity-time-section {
  display: flex;
  justify-content: center;
  flex-direction: column;
  gap: 0.5rem;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 1.5rem;
}

@media (max-width: 1024px) {
  .activity-card {
    grid-template-columns: minmax(50px, 0.8fr) minmax(100px, 1.5fr) 0.7fr;
    padding: 0.5rem;
    gap: 0.75rem;
  }
  .filter-container {
    flex-direction: column;
    gap: 12px;
  }
}
</style>
