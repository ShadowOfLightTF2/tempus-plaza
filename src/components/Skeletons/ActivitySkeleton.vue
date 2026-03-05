<template>
  <div class="activity-cards">
    <div class="table-header-content">
      <div class="skeleton skeleton-icon"></div>
      <div class="header-text">
        <div class="skeleton skeleton-header-title"></div>
      </div>
      <div class="header-right">
        <div class="filter-container">
          <div class="filter-group">
            <div class="skeleton skeleton-filter-label"></div>
            <div class="class-filter-container">
              <div class="skeleton skeleton-filter-btn"></div>
              <div class="skeleton skeleton-filter-btn"></div>
            </div>
          </div>
          <div class="filter-group">
            <div class="skeleton skeleton-filter-label"></div>
            <div class="type-filter-container">
              <div class="skeleton skeleton-filter-btn"></div>
              <div class="skeleton skeleton-filter-btn"></div>
              <div class="skeleton skeleton-filter-btn"></div>
            </div>
          </div>
          <div class="filter-group">
            <div class="skeleton skeleton-filter-label"></div>
            <div class="class-filter-container">
              <div class="skeleton skeleton-filter-btn"></div>
              <div class="skeleton skeleton-filter-btn"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="activity-cards-container">
      <div v-for="i in 20" :key="i" class="activity-card">
        <!-- Player section -->
        <div class="activity-player-section">
          <div class="player-link-skel">
            <div class="skeleton skeleton-avatar"></div>
            <div class="skeleton skeleton-player-name"></div>
          </div>
          <div class="skeleton skeleton-class-icon"></div>
        </div>

        <!-- Map section -->
        <div class="activity-map-section">
          <div class="map-details-skel">
            <div class="map-name-row-skel">
              <div class="skeleton skeleton-map-name"></div>
              <div class="skeleton skeleton-map-badge"></div>
            </div>
          </div>
        </div>

        <!-- Time section -->
        <div class="activity-time-section">
          <div class="rank-time-skel">
            <div class="skeleton skeleton-time"></div>
            <div class="skeleton skeleton-rank"></div>
          </div>
        </div>

        <!-- Time-ago section -->
        <div class="activity-timeago-section">
          <div class="skeleton skeleton-time-ago"></div>
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

/* ── Header ── */
.table-header-content {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
  border-bottom: 1px solid var(--color-border-soft);
  gap: 0.5rem;
}

.skeleton-icon {
  width: 36px;
  height: 36px;
  border-radius: 6px;
  flex-shrink: 0;
}

.header-text {
  flex-shrink: 0;
  margin-left: 1rem;
}

.skeleton-header-title {
  width: 200px;
  height: 24px;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-left: auto;
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

.skeleton-filter-label {
  width: 70px;
  height: 14px;
  border-radius: 4px;
}

.class-filter-container,
.type-filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.skeleton-filter-btn {
  width: 80px;
  height: 32px;
  border-radius: 6px;
}

/* ── Cards ── */
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

/* Match the real card's grid exactly */
.activity-card {
  display: grid;
  grid-template-columns: minmax(0, 250px) 1fr 250px 100px;
  padding: 0.5rem;
  gap: 0.75rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  min-height: 44px;
  align-items: center;
}

/* Player */
.activity-player-section {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  overflow: hidden;
}

.player-link-skel {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  overflow: hidden;
  flex: 1;
  min-width: 0;
}

.skeleton-avatar {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  flex-shrink: 0;
}

.skeleton-player-name {
  height: 14px;
  border-radius: 4px;
  width: 100px;
}

.skeleton-class-icon {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-left: auto;
}

/* Map */
.activity-map-section {
  display: flex;
  align-items: center;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 0.75rem;
  overflow: hidden;
}

.map-details-skel {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  width: 100%;
  overflow: hidden;
}

.map-name-row-skel {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.skeleton-map-name {
  height: 14px;
  border-radius: 4px;
  width: 130px;
}

.skeleton-map-badge {
  width: 32px;
  height: 14px;
  border-radius: 4px;
  flex-shrink: 0;
}

/* Time */
.activity-time-section {
  display: flex;
  justify-content: center;
  flex-direction: column;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 0.75rem;
  overflow: hidden;
}

.rank-time-skel {
  display: flex;
  align-items: center;
  gap: 12px;
}

.skeleton-time {
  width: 80px;
  height: 14px;
  border-radius: 4px;
}

.skeleton-rank {
  width: 36px;
  height: 14px;
  border-radius: 4px;
}

/* Time-ago */
.activity-timeago-section {
  display: flex;
  align-items: center;
  justify-content: center;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 0.75rem;
}

.skeleton-time-ago {
  width: 52px;
  height: 13px;
  border-radius: 4px;
}

/* ── 1200px: header wraps ── */
@media (max-width: 1200px) {
  .table-header-content {
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: center;
  }
  .header-right {
    flex-direction: column;
    align-items: center;
    width: 100%;
    margin-left: 0;
    gap: 0.75rem;
    margin-top: 5px;
  }
  .filter-container {
    flex-wrap: wrap;
    gap: 12px;
    width: 100%;
    justify-content: center;
  }
  .filter-group {
    align-items: center;
  }
}

/* ── 992px: compact grid, shorter time-ago ── */
@media (max-width: 992px) {
  .activity-timeago-section {
    /* hide long; only short visible — mirror real component */
    display: none;
  }
  .activity-card {
    grid-template-columns: minmax(0, 150px) minmax(140px, 1fr) 210px;
  }
  .skeleton-player-name {
    width: 75px;
  }
  .skeleton-map-name {
    width: 100px;
  }
  .skeleton-time {
    width: 65px;
  }
  .skeleton-rank {
    width: 28px;
  }
}

/* ── 768px mobile: 3-col grid, no time-ago col ── */
@media (max-width: 767.98px) {
  .activity-card {
    grid-template-columns: minmax(0, 130px) 1fr auto;
    grid-template-rows: auto;
    gap: 0.5rem;
  }

  .activity-player-section {
    grid-column: 1;
    grid-row: 1;
  }
  .activity-map-section {
    grid-column: 2;
    grid-row: 1;
    border-top: none;
    padding-top: 0;
  }
  .activity-time-section {
    grid-column: 3;
    grid-row: 1;
    border-top: none;
    padding-top: 0;
  }
  .activity-timeago-section {
    display: none;
  }

  .rank-time-skel {
    flex-direction: column;
    gap: 4px;
    align-items: flex-start;
  }

  .skeleton-player-name {
    width: 70px;
  }
  .skeleton-map-name {
    width: 90px;
  }
  .skeleton-time {
    width: 55px;
  }
  .skeleton-rank {
    width: 24px;
  }
  .skeleton-map-badge {
    width: 26px;
  }

  .filter-group {
    width: 100%;
  }
  .class-filter-container,
  .type-filter-container {
    justify-content: center;
    width: 100%;
  }
  .skeleton-filter-btn {
    flex: 1;
    min-width: 70px;
  }
}
</style>
