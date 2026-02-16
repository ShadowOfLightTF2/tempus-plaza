<template>
  <div class="skeleton-wrapper">
    <div class="table-container">
      <div class="table-header-section">
        <div class="button-group">
          <button class="toggle-btn btn btn-dark update-button active">
            Maps
          </button>
          <button class="toggle-btn btn btn-dark update-button">Courses</button>
          <button class="toggle-btn btn btn-dark update-button">Bonuses</button>
          <div class="layout-divider"></div>
          <button class="toggle-btn btn btn-dark update-button layout-toggle">
            ⊟
          </button>
          <button
            class="toggle-btn btn btn-dark update-button layout-toggle active"
          >
            ⊞
          </button>
        </div>
      </div>
      <div class="grid-container">
        <div class="maps-grid">
          <div
            v-for="i in 24"
            :key="'card-' + i"
            class="map-card skeleton-card"
          >
            <div class="shimmer"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "MapsSkeleton",
};
</script>

<style scoped>
@keyframes shimmer {
  0% {
    background-position: -800px 0;
  }
  100% {
    background-position: 800px 0;
  }
}

.skeleton-wrapper {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: -20px;
}

.shimmer {
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.04) 25%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.04) 75%
  );
  background-size: 800px 100%;
  animation: shimmer 1.6s infinite linear;
}

.skeleton-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border-soft);
  overflow: hidden;
  pointer-events: none;
  user-select: none;
}

.page-header {
  text-align: center;
  margin-bottom: 2rem;
  padding: 1.5rem 0;
}

.row-divider {
  border: none;
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent,
    var(--color-primary),
    transparent
  );
  margin: 30px 0;
  opacity: 0.6;
}

.filter-section {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  padding: 20px;
  margin: 20px;
  display: flex;
  justify-content: center;
  width: fit-content;
  max-width: 100%;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  opacity: 0.5;
  pointer-events: none;
}

.filter-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}

.filter-column {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.filter-title {
  color: #ffffff;
  font-weight: bold;
  margin-bottom: 10px;
  text-align: center;
  text-transform: uppercase;
}

.tier-filter-container,
.rating-filter-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.tier-filters,
.rating-filters {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.tier-placeholder,
.rating-placeholder {
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 12px;
  border: 2px solid transparent;
  min-width: 28px;
  text-align: center;
  background: rgba(255, 255, 255, 0.07);
  color: transparent;
}

.column-toggles-section {
  display: flex;
  justify-content: center;
  margin: 30px 0;
}

.column-toggles {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.filter-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  justify-content: center;
}

.filter-count {
  color: white;
  font-weight: bold;
  opacity: 0.4;
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.164);
  border: 1px solid var(--color-border-soft);
  font-weight: bold;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 16px;
  color: #888;
  z-index: 2;
}

.search-input {
  width: 200px;
  padding: 8px 8px 8px 40px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border-soft);
  border-radius: 12px;
  color: #ffffff;
  font-size: 16px;
}

.intended-class-buttons {
  display: flex;
  gap: 15px;
  justify-content: center;
}

.intended-class-btn {
  width: 32px;
  height: 32px;
  background: rgba(255, 255, 255, 0.07);
  border-radius: 4px;
}

.table-container {
  border-radius: 8px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  margin-top: 20px;
  width: 100%;
}

.table-header-section {
  border-radius: 8px 8px 0 0;
  overflow: hidden;
  opacity: 0.5;
  pointer-events: none;
}

.button-group {
  display: flex;
  border: none;
  border-radius: 0;
}

.toggle-btn,
.toggle-btn:active,
.toggle-btn:hover,
.toggle-btn:first-child,
.toggle-btn:last-child {
  border-radius: 0 !important;
}

.layout-divider {
  width: 1px;
  background: var(--color-border-soft);
  margin: 0 10px;
}

.layout-toggle {
  font-size: 18px !important;
  min-width: 45px;
  padding: 8px 12px !important;
}

.grid-container {
  width: 100%;
  border-left: 1px solid var(--color-border-soft);
  border-right: 1px solid var(--color-border-soft);
  border-bottom: 1px solid var(--color-border-soft);
  border-radius: 0 0 8px 8px;
  background: rgba(255, 255, 255, 0.02);
  padding: 20px;
  box-sizing: border-box;
}

.maps-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(300px, 1fr));
  gap: 20px;
  width: 100%;
}

.map-card {
  border-radius: 12px;
  height: 275px;
  width: 100%;
}

@media (max-width: 1400px) {
  .maps-grid {
    grid-template-columns: repeat(3, minmax(300px, 1fr));
  }
}
@media (max-width: 1024px) {
  .maps-grid {
    grid-template-columns: repeat(2, minmax(300px, 1fr));
  }
}
@media (max-width: 708px) {
  .maps-grid {
    grid-template-columns: repeat(1, minmax(300px, 1fr));
  }

  .filter-section {
    width: 100%;
    padding: 15px;
  }

  .responsive-ratings-row {
    flex-direction: column !important;
    gap: 20px !important;
    align-items: center !important;
  }
}
</style>
