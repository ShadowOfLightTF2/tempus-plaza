<template>
  <div>
    <div v-if="view === 'topplayers'" class="top-players-cards">
      <div class="table-header-content topplayers-header">
        <div class="skeleton skeleton-icon"></div>
        <div class="table-header-text">
          <div class="skeleton skeleton-title"></div>
          <div class="skeleton skeleton-subtitle"></div>
        </div>
        <div class="skeleton skeleton-checkbox min-mode-skel"></div>
      </div>
      <div class="players-cards-container" :class="{ 'min-mode': isMinMode }">
        <div
          v-for="n in 20"
          :key="n"
          class="player-card"
          :class="{ 'min-mode': isMinMode }"
        >
          <div class="ranks-section">
            <div class="player-ranks">
              <template v-if="!isMinMode">
                <div class="skeleton skeleton-rank"></div>
                <div class="skeleton skeleton-rank"></div>
              </template>
              <template v-else>
                <div class="skeleton skeleton-rank"></div>
              </template>
            </div>
          </div>
          <div class="player-info-section">
            <div class="player-link-skel">
              <div
                v-if="!isMinMode"
                class="skeleton skeleton-avatar-large"
              ></div>
              <div class="player-details">
                <div class="name-row-skel">
                  <div
                    v-if="isMinMode"
                    class="skeleton skeleton-avatar-small"
                  ></div>
                  <div class="skeleton skeleton-player-name"></div>
                </div>
                <div
                  v-if="!isMinMode"
                  class="skeleton skeleton-player-country"
                ></div>
              </div>
            </div>
          </div>
          <div class="map-info-section">
            <div class="map-details-skel">
              <div
                class="skeleton skeleton-class-icon"
                :class="{ small: isMinMode }"
              ></div>
              <div class="skeleton skeleton-map-name"></div>
            </div>
          </div>
          <div class="server-info-section">
            <div class="skeleton skeleton-server-name"></div>
            <div class="skeleton skeleton-btn"></div>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="table-wrapper">
      <div class="table-header-content">
        <div class="table-header-top">
          <div class="skeleton skeleton-icon"></div>
          <div class="table-header-title-section">
            <div class="skeleton skeleton-title"></div>
            <div class="skeleton skeleton-subtitle"></div>
          </div>
        </div>
        <div class="table-header-filters">
          <div class="region-buttons">
            <div
              v-for="n in 9"
              :key="n"
              class="skeleton skeleton-region-btn"
            ></div>
          </div>
          <div class="server-type-buttons">
            <div
              v-for="n in 4"
              :key="n"
              class="skeleton skeleton-type-btn"
            ></div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-dark">
          <thead>
            <tr>
              <th class="col-location">
                <div class="skeleton skeleton-th"></div>
              </th>
              <th class="col-server">
                <div class="skeleton skeleton-th"></div>
              </th>
              <th class="col-map"><div class="skeleton skeleton-th"></div></th>
              <th class="col-players">
                <div class="skeleton skeleton-th"></div>
              </th>
              <th class="col-connect">
                <div class="skeleton skeleton-th"></div>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="n in 30" :key="n">
              <td class="align-middle col-location">
                <div class="skeleton-row-inner">
                  <div class="skeleton skeleton-flag"></div>
                  <div class="skeleton skeleton-td-text"></div>
                </div>
              </td>
              <td class="align-middle col-server">
                <div class="skeleton-row-inner">
                  <div class="skeleton skeleton-arrow"></div>
                  <div class="skeleton skeleton-td-text-long"></div>
                </div>
              </td>
              <td class="align-middle col-map">
                <div class="skeleton skeleton-td-text-med"></div>
              </td>
              <td class="align-middle col-players">
                <div class="skeleton-row-inner">
                  <div class="skeleton skeleton-td-text-short"></div>
                  <div class="skeleton skeleton-status-dot"></div>
                </div>
              </td>
              <td class="align-middle col-connect">
                <div class="skeleton skeleton-btn-sm"></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ServersSkeleton",
  props: {
    view: {
      type: String,
      default: "topplayers",
    },
  },
  data() {
    return {
      windowWidth: window.innerWidth,
    };
  },
  computed: {
    isMinMode() {
      return this.windowWidth <= 1200;
    },
  },
  mounted() {
    window.addEventListener("resize", this.onResize);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.onResize);
  },
  methods: {
    onResize() {
      this.windowWidth = window.innerWidth;
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
  border-radius: 4px;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.06) 25%,
    rgba(255, 255, 255, 0.13) 50%,
    rgba(255, 255, 255, 0.06) 75%
  );
  background-size: 600px 100%;
  animation: shimmer 1.6s ease-in-out infinite;
  flex-shrink: 0;
}

.skeleton-icon {
  width: 2.2rem;
  height: 2.2rem;
  border-radius: 6px;
}
.skeleton-title {
  width: 180px;
  height: 1.3rem;
  margin-bottom: 0.4rem;
}
.skeleton-subtitle {
  width: 120px;
  height: 0.85rem;
}
.skeleton-checkbox {
  width: 110px;
  height: 22px;
}
.skeleton-rank {
  width: 52px;
  height: 28px;
  border-radius: 6px;
}
.skeleton-avatar-large {
  width: 54px;
  height: 54px;
  border-radius: 4px;
}
.skeleton-avatar-small {
  width: 24px;
  height: 24px;
  border-radius: 2px;
  flex-shrink: 0;
}
.skeleton-player-name {
  width: 130px;
  height: 1.1rem;
}
.skeleton-player-country {
  width: 90px;
  height: 0.85rem;
}
.skeleton-class-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
}
.skeleton-class-icon.small {
  width: 24px;
  height: 24px;
}
.skeleton-map-name {
  width: 120px;
  height: 1rem;
}
.skeleton-server-name {
  width: 80%;
  height: 0.9rem;
  margin-bottom: 0.5rem;
}
.skeleton-btn {
  width: 72px;
  height: 30px;
  border-radius: 5px;
}
.skeleton-btn-sm {
  width: 72px;
  height: 28px;
  border-radius: 5px;
}
.skeleton-region-btn {
  width: 90px;
  height: 32px;
  border-radius: 5px;
}
.skeleton-type-btn {
  width: 80px;
  height: 32px;
  border-radius: 5px;
}
.skeleton-th {
  width: 60%;
  height: 0.85rem;
}
.skeleton-arrow {
  width: 10px;
  height: 0.85rem;
  border-radius: 2px;
  flex-shrink: 0;
}
.skeleton-flag {
  width: 24px;
  height: 18px;
  border-radius: 2px;
}
.skeleton-td-text {
  width: 80px;
  height: 0.85rem;
}
.skeleton-td-text-long {
  width: 180px;
  height: 0.85rem;
}
.skeleton-td-text-med {
  width: 110px;
  height: 0.85rem;
}
.skeleton-td-text-short {
  width: 44px;
  height: 0.85rem;
}
.skeleton-status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.skeleton-row-inner {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.top-players-cards {
  width: 100%;
  border-radius: 0 0 8px 8px;
  overflow: hidden;
}

.table-header-content {
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
  border-bottom: 1px solid var(--color-border-soft);
}

.topplayers-header {
  display: flex !important;
  flex-direction: row !important;
  align-items: center !important;
  gap: 1rem;
}

.table-header-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.min-mode-skel {
  margin-left: auto;
  flex-shrink: 0;
}

.players-cards-container {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.02);
}

.players-cards-container.min-mode {
  gap: 0.3rem;
}

.player-card {
  display: grid;
  grid-template-columns: 0.6fr 2fr 2fr minmax(400px, 1.5fr);
  gap: 0.5rem;
  padding: 0.3rem 0.6rem;
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  pointer-events: none;
  align-items: center;
}

.player-card:nth-child(odd) {
  background: rgba(255, 255, 255, 0.05);
}
.player-card:nth-child(even) {
  background: rgba(255, 255, 255, 0.08);
}

.player-card.min-mode {
  padding: 0.2rem 0.4rem;
  gap: 1rem;
}

.ranks-section {
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.player-ranks {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.player-info-section {
  display: flex;
  align-items: center;
  border-left: 1px solid var(--color-border-soft);
  border-right: 1px solid var(--color-border-soft);
  padding-left: 1.5rem;
  min-height: 60px;
}

.player-card.min-mode .player-info-section {
  min-height: unset;
}

.player-link-skel {
  display: flex;
  align-items: center;
  gap: 1rem;
  width: 100%;
}

.player-card.min-mode .player-link-skel {
  gap: 0.5rem;
}

.player-details {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.name-row-skel {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.map-info-section {
  display: flex;
  align-items: center;
  padding-left: 1.5rem;
}

.player-card.min-mode .map-info-section {
  padding-left: 1rem;
}

.map-details-skel {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.server-info-section {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  border-left: 1px solid var(--color-border-soft);
  padding: 0.5rem;
}

.player-card.min-mode .server-info-section {
  flex-direction: row;
  padding-left: 1rem;
}

.table-wrapper {
  width: 100%;
  border-radius: 0 0 8px 8px;
  overflow: hidden;
}

.table-header-top {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
  gap: 1rem;
}

.table-header-title-section {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.table-header-filters {
  width: 100%;
}

.region-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 1rem;
}

.server-type-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.table-responsive {
  overflow: hidden;
  margin-bottom: 0;
}

.table-dark {
  margin: 0;
  width: 100%;
  border-collapse: collapse;
}

.table-dark thead {
  border-top: 1px solid var(--color-border-soft);
}

.table-dark th {
  background: rgba(74, 111, 165, 0.3) !important;
  padding: 8px 6px;
  font-weight: bold;
}

.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  padding: 6px 6px;
  border: 2px solid var(--color-border-soft);
}

.table-dark td:first-child {
  border-left: none !important;
}
.table-dark td:last-child {
  border-right: none !important;
}
.table-dark tr:last-child td {
  border-bottom: none !important;
}
.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}

.col-location {
  width: 14%;
}
.col-server {
  width: 38%;
}
.col-map {
  width: 24%;
}
.col-players {
  width: 12%;
}
.col-connect {
  width: 12%;
}

@media (max-width: 1200px) {
  .min-mode-skel {
    display: none;
  }
}

@media (max-width: 1024px) {
  .player-card:not(.min-mode) {
    grid-template-columns: 1fr;
    gap: 0.8rem;
  }

  .player-card:not(.min-mode) .ranks-section {
    border-bottom: 1px solid var(--color-border-soft);
    justify-content: center;
    padding-bottom: 0.5rem;
  }

  .player-card:not(.min-mode) .player-info-section {
    border-left: none;
    border-right: none;
    border-bottom: 1px solid var(--color-border-soft);
    padding-left: 0;
    padding-bottom: 0.8rem;
    min-height: unset;
  }

  .player-card:not(.min-mode) .map-info-section {
    border-bottom: 1px solid var(--color-border-soft);
    padding-left: 0;
    padding-bottom: 0.8rem;
  }

  .player-card:not(.min-mode) .server-info-section {
    border-left: none;
    flex-direction: row;
    justify-content: space-between;
    padding-top: 0.5rem;
  }

  .player-card.min-mode {
    grid-template-columns: 65px minmax(100px, 200px) minmax(110px, auto) auto;
    gap: 0.5rem;
  }

  .player-card.min-mode .ranks-section {
    border-right: 1px solid var(--color-border-soft);
    padding: 0 0.5rem;
  }

  .player-card.min-mode .player-info-section {
    border-left: none;
    border-right: 1px solid var(--color-border-soft);
    padding: 0 0.5rem 0 0;
    min-height: unset;
  }

  .player-card.min-mode .map-info-section {
    border-left: none;
    padding: 0 0.5rem 0 0;
  }

  .player-card.min-mode .server-info-section {
    display: none;
  }
}

@media (max-width: 767.98px) {
  .topplayers-header {
    flex-direction: column !important;
    align-items: center !important;
    gap: 0.75rem;
    text-align: center;
  }

  .region-buttons,
  .server-type-buttons {
    justify-content: center;
  }

  .table-responsive {
    display: block;
    overflow-x: auto;
  }

  .table-dark {
    min-width: 560px;
  }
}
</style>
