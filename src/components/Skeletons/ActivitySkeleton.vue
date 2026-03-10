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
          <div class="filter-group mode-toggle-group desktop-only">
            <div class="skeleton skeleton-filter-label"></div>
            <div class="skeleton skeleton-checkbox"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Day group -->
    <div class="day-group">
      <div class="skeleton skeleton-day-label"></div>
      <div class="activity-cards-container">
        <div
          v-for="i in 20"
          :key="i"
          class="activity-card-base"
          :class="isMinMode ? 'is-min-mode' : 'is-normal-mode'"
        >
          <!-- CLASS SECTION (normal mode only) -->
          <div class="card-class-section">
            <div class="skeleton skeleton-class-icon-normal"></div>
          </div>

          <!-- PLAYER SECTION -->
          <div class="card-player-section">
            <!-- min mode: class icon inline -->
            <div
              v-if="isMinMode"
              class="skeleton skeleton-class-icon-row"
            ></div>
            <div class="player-link-skel">
              <div
                class="skeleton"
                :class="
                  isMinMode ? 'skeleton-avatar-small' : 'skeleton-avatar-large'
                "
              ></div>
              <div class="player-details-skel">
                <div
                  class="skeleton skeleton-player-name"
                  :class="
                    isMinMode
                      ? 'skeleton-player-name--min'
                      : 'skeleton-player-name--normal'
                  "
                ></div>
                <div v-if="!isMinMode" class="skeleton skeleton-country"></div>
              </div>
            </div>
          </div>

          <!-- MAP SECTION -->
          <div class="card-map-section">
            <div class="map-details-skel">
              <div class="map-name-row-skel">
                <div
                  class="skeleton skeleton-map-name"
                  :class="
                    isMinMode
                      ? 'skeleton-map-name--min'
                      : 'skeleton-map-name--normal'
                  "
                ></div>
                <div class="skeleton skeleton-map-badge"></div>
              </div>
            </div>
            <!-- min mode: intended class + tier/rating on right -->
            <div v-if="isMinMode" class="min-mode-meta-skel">
              <div class="skeleton skeleton-intended-class-icon"></div>
              <div class="skeleton skeleton-tier-badge"></div>
            </div>
            <!-- normal mode: intended class + tier/rating top-right -->
            <div v-if="!isMinMode" class="normal-map-top-right-skel">
              <div class="skeleton skeleton-intended-class-icon-normal"></div>
              <div class="skeleton skeleton-tier-badge"></div>
            </div>
          </div>

          <!-- TIME SECTION -->
          <div class="card-time-section">
            <div
              class="time-rank-row-skel"
              :class="
                isMinMode
                  ? 'time-rank-row-skel--min'
                  : 'time-rank-row-skel--normal'
              "
            >
              <div
                class="skeleton skeleton-time"
                :class="
                  isMinMode ? 'skeleton-time--min' : 'skeleton-time--normal'
                "
              ></div>
              <div class="skeleton skeleton-rank"></div>
            </div>
            <div
              v-if="!isMinMode"
              class="skeleton skeleton-timeago-normal"
            ></div>
          </div>

          <!-- TIMEAGO SECTION (min mode only) -->
          <div v-if="isMinMode" class="card-timeago-section">
            <div class="skeleton skeleton-time-ago"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ActivitySkeleton",
  data() {
    return {
      isMinMode:
        window.innerWidth <= 992
          ? true
          : localStorage.getItem("activityMinMode") === "true",
    };
  },
  mounted() {
    window.addEventListener("resize", this.handleResize);
    window.addEventListener("storage", this.handleStorageChange);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.handleResize);
    window.removeEventListener("storage", this.handleStorageChange);
  },
  methods: {
    handleResize() {
      const mobile = window.innerWidth <= 992;
      this.isMinMode = mobile
        ? true
        : localStorage.getItem("activityMinMode") === "true";
    },
    handleStorageChange(event) {
      if (event.key === "activityMinMode" && window.innerWidth > 992) {
        this.isMinMode = event.newValue === "true";
      }
    },
  },
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
  margin-left: 0.5rem;
}

.skeleton-header-title {
  width: 200px;
  height: 28px;
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

.skeleton-checkbox {
  width: 110px;
  height: 22px;
  border-radius: 4px;
}

/* ── Day group ── */
.activity-cards {
  width: 100%;
  border-radius: 0 0 8px 8px;
  overflow: hidden;
}

.day-group {
  width: 100%;
}

.skeleton-day-label {
  width: 60px;
  height: 20px;
  border-radius: 4px;
  margin: 0.75rem 1rem 0.35rem;
}

.activity-cards-container {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.02);
}

/* ── Card base ── */
.activity-card-base {
  display: grid;
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  overflow: hidden;
}

.activity-card-base.is-min-mode {
  grid-template-columns: minmax(0, 250px) 1fr 250px 100px;
  gap: 0.75rem;
  padding: 0.5rem;
  background: rgba(255, 255, 255, 0.05);
  min-height: 0;
  align-items: center;
}

.activity-card-base.is-normal-mode {
  grid-template-columns: 0.2fr 1.2fr 2fr 1.5fr;
  gap: 0.5rem;
  padding: 0.3rem 0.6rem;
  min-height: 75px;
  align-items: stretch;
  background: rgba(255, 255, 255, 0.05);
}

/* ── Class section ── */
.card-class-section {
  display: flex;
  align-items: center;
  justify-content: center;
}

.is-min-mode .card-class-section {
  display: none;
}

.skeleton-class-icon-normal {
  width: 36px;
  height: 36px;
  border-radius: 50%;
}

/* ── Player section ── */
.card-player-section {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  overflow: hidden;
  border-right: 1px solid var(--color-border-soft);
}

.is-normal-mode .card-player-section {
  padding-left: 0.5rem;
}

.skeleton-class-icon-row {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  flex-shrink: 0;
}

.player-link-skel {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  overflow: hidden;
  flex: 1;
  min-width: 0;
}

.is-normal-mode .player-link-skel {
  gap: 1rem;
}

.skeleton-avatar-small {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  flex-shrink: 0;
}

.skeleton-avatar-large {
  width: 54px;
  height: 54px;
  border-radius: 4px;
  flex-shrink: 0;
}

.player-details-skel {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  overflow: hidden;
  flex: 1;
}

.skeleton-player-name {
  height: 14px;
  border-radius: 4px;
}

.skeleton-player-name--min {
  width: 100px;
}
.skeleton-player-name--normal {
  width: 130px;
}

.skeleton-country {
  width: 80px;
  height: 12px;
  border-radius: 4px;
}

/* ── Map section ── */
.card-map-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
}

.is-min-mode .card-map-section {
  padding-left: 0.75rem;
}

.is-normal-mode .card-map-section {
  padding: 0 0.75rem 0 1.5rem;
  align-self: stretch;
}

.map-details-skel {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  overflow: hidden;
  flex: 1;
  min-width: 0;
}

.map-name-row-skel {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.skeleton-map-name {
  height: 14px;
  border-radius: 4px;
  flex-shrink: 1;
}

.skeleton-map-name--min {
  width: 120px;
}
.skeleton-map-name--normal {
  width: 160px;
}

.skeleton-map-badge {
  width: 32px;
  height: 18px;
  border-radius: 4px;
  flex-shrink: 0;
}

/* min mode meta (right side of map section) */
.min-mode-meta-skel {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
  margin-left: auto;
  padding-right: 0.5rem;
}

.skeleton-intended-class-icon {
  width: 22px;
  height: 22px;
  border-radius: 50%;
}

.skeleton-tier-badge {
  width: 28px;
  height: 18px;
  border-radius: 4px;
}

/* normal mode top-right */
.normal-map-top-right-skel {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  margin-left: auto;
  flex-shrink: 0;
}

.skeleton-intended-class-icon-normal {
  width: 26px;
  height: 26px;
  border-radius: 50%;
}

/* ── Time section ── */
.card-time-section {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.3rem;
  border-left: 1px solid var(--color-border-soft);
}

.is-min-mode .card-time-section {
  align-items: flex-start;
  padding-left: 0.75rem;
  white-space: nowrap;
  overflow: hidden;
}

.is-normal-mode .card-time-section {
  align-items: center;
  padding: 0.5rem 1rem;
  text-align: center;
}

.time-rank-row-skel {
  display: flex;
  align-items: center;
}

.time-rank-row-skel--min {
  flex-direction: row;
  gap: 12px;
}
.time-rank-row-skel--normal {
  flex-direction: row;
  gap: 0.75rem;
}

.skeleton-time {
  height: 14px;
  border-radius: 4px;
}
.skeleton-time--min {
  width: 80px;
}
.skeleton-time--normal {
  width: 90px;
}

.skeleton-rank {
  width: 36px;
  height: 14px;
  border-radius: 4px;
}

.skeleton-timeago-normal {
  width: 70px;
  height: 11px;
  border-radius: 4px;
  opacity: 0.65;
}

/* ── Timeago section (min mode) ── */
.card-timeago-section {
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

/* ── 1200px ── */
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

  .activity-card-base.is-normal-mode {
    min-height: 62px;
    gap: 0.4rem;
    padding: 0.25rem 0.5rem;
  }
  .skeleton-avatar-large {
    width: 40px;
    height: 40px;
  }
  .skeleton-player-name--normal {
    width: 100px;
  }
  .skeleton-map-name--normal {
    width: 120px;
  }
  .skeleton-class-icon-normal {
    width: 28px;
    height: 28px;
  }
  .skeleton-intended-class-icon-normal {
    width: 22px;
    height: 22px;
  }

  .activity-card-base.is-min-mode {
    grid-template-columns: minmax(0, 200px) 1fr 250px 100px;
  }
}

/* ── 992px ── */
@media (max-width: 992px) {
  .desktop-only {
    display: none;
  }

  .activity-card-base.is-min-mode {
    grid-template-columns: minmax(0, 150px) minmax(140px, 1fr) 210px 55px;
  }

  .skeleton-player-name--min {
    width: 75px;
  }
  .skeleton-map-name--min {
    width: 90px;
  }
  .skeleton-time--min {
    width: 60px;
  }
  .skeleton-rank {
    width: 28px;
  }

  .min-mode-meta-skel {
    display: none;
  }
}

/* ── 768px ── */
@media (max-width: 767.98px) {
  .activity-card-base.is-min-mode {
    grid-template-columns: minmax(0, 130px) 1fr auto;
  }

  /* hide the timeago column on mobile */
  .card-timeago-section {
    display: none;
  }

  .skeleton-player-name--min {
    width: 65px;
  }
  .skeleton-map-name--min {
    width: 80px;
  }
  .skeleton-time--min {
    width: 50px;
  }
  .skeleton-rank {
    width: 22px;
  }
  .skeleton-map-badge {
    width: 26px;
  }

  /* stack time + rank vertically on mobile */
  .time-rank-row-skel--min {
    flex-direction: column;
    gap: 4px;
    align-items: flex-start;
  }

  .is-min-mode .card-time-section {
    border-left: 1px solid var(--color-border-soft);
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
