<template>
  <div
    ref="cardElement"
    class="card-image"
    :class="{
      loaded: imageLoaded,
      loading: isLoading,
      'image-failed': imageFailed,
    }"
    :style="backgroundImageStyle"
  >
    <div v-if="!imageLoaded && !imageFailed" class="loading-placeholder">
      <div class="spinner"></div>
    </div>
    <div class="card-overlay">
      <div
        class="card-header"
        :style="currentView !== 'maps' ? 'margin-top: 70px;' : ''"
      >
        <div class="class-icon-container">
          <img
            v-if="item.intended_class === 3 || item.intended_class === 4"
            :src="`/icons/${item.class}.png`"
            :alt="item.class"
            class="author-class-icon"
          />
          <template v-if="item.intended_class === 5">
            <img
              :src="`/icons/soldier.png`"
              :alt="item.class"
              class="author-class-icon dual-icon"
            />
            <img
              :src="`/icons/demoman.png`"
              :alt="item.class"
              class="author-class-icon dual-icon"
            />
          </template>
        </div>
        <h3 class="card-title">
          {{
            currentView === "maps"
              ? item.name
              : `${item.map_name} (${currentView.slice(0, 1).toUpperCase()}${
                  item.index
                })`
          }}
        </h3>
      </div>

      <div class="card-body">
        <div class="class-tiers">
          <div class="tier-section">
            <span class="class-label">Soldier</span>
            <div class="tier-group">
              <span
                class="tier-badge tier-badge-card"
                :class="`tier-${item.soldier_tier}`"
                >T{{ item.soldier_tier }}</span
              >
              <span
                class="rating-badge rating-badge-card"
                :class="`rating-${item.soldier_rating}`"
                >R{{ item.soldier_rating }}</span
              >
            </div>
            <div class="completion-item">
              <i class="bi bi-check-circle me-1"></i>
              {{ item.soldier_completion_count?.toLocaleString() || 0 }}
            </div>
          </div>

          <div class="tier-section">
            <span class="class-label">Demoman</span>
            <div class="tier-group">
              <span
                class="tier-badge tier-badge-card"
                :class="`tier-${item.demoman_tier}`"
                >T{{ item.demoman_tier }}</span
              >
              <span
                class="rating-badge rating-badge-card"
                :class="`rating-${item.demoman_rating}`"
                >R{{ item.demoman_rating }}</span
              >
            </div>
            <div class="completion-item">
              <i class="bi bi-check-circle me-1"></i>
              {{ item.demoman_completion_count?.toLocaleString() || 0 }}
            </div>
          </div>
        </div>

        <div class="card-stats">
          <div v-if="currentView === 'maps'" class="stat-row">
            <div class="stat-item">
              <span class="stat-label">Courses</span>
              <span class="stat-value">{{ item.course_count || 0 }}</span>
            </div>
            <div class="stat-item">
              <span class="stat-label">Bonuses</span>
              <span class="stat-value">{{ item.bonus_count || 0 }}</span>
            </div>
          </div>

          <div class="map-tags" v-if="item.tags && item.tags.length > 0">
            <div
              v-for="tag in item.tags"
              :key="tag.id"
              class="map-tag"
              :style="{
                backgroundColor: tag.color + '20',
                borderColor: tag.color,
              }"
            >
              <i class="bi bi-tag-fill me-1" :style="{ color: tag.color }"></i>
              {{ tag.name }}
            </div>
          </div>
        </div>

        <div v-if="currentView === 'maps'" class="map-date-added">
          Date Added:
          {{ formatDate(item.date_added * 1000) }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "MapCard",
  props: {
    item: {
      type: Object,
      required: true,
    },
    currentView: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      imageLoaded: false,
      imageFailed: false,
      isLoading: false,
      observer: null,
    };
  },
  computed: {
    formatDate() {
      return (dateString) => {
        const options = { year: "numeric", month: "short", day: "numeric" };
        return new Date(dateString).toLocaleDateString(undefined, options);
      };
    },
    backgroundImageStyle() {
      if (this.imageFailed) {
        return "background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%)";
      }
      return this.imageLoaded
        ? `background-image: url('${this.imagePath}')`
        : "";
    },
    imagePath() {
      return `/map-backgrounds/medium/${
        this.currentView === "maps" ? this.item.name : this.item.map_name
      }.jpg`;
    },
    backgroundImageStyle() {
      return this.imageLoaded
        ? `background-image: url('${this.imagePath}')`
        : "";
    },
  },
  mounted() {
    this.setupLazyLoading();
  },
  beforeUnmount() {
    if (this.observer) {
      this.observer.disconnect();
    }
  },
  methods: {
    setupLazyLoading() {
      this.$nextTick(() => {
        if (!this.$refs.cardElement) {
          console.warn("cardElement ref not found");
          return;
        }

        this.observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                this.loadImage();
                this.observer.unobserve(entry.target);
              }
            });
          },
          {
            rootMargin: "50px",
          }
        );

        this.observer.observe(this.$refs.cardElement);
      });
    },

    loadImage() {
      this.isLoading = true;
      const img = new Image();
      img.onload = () => {
        this.imageLoaded = true;
        this.imageFailed = false;
        this.isLoading = false;
      };
      img.onerror = () => {
        console.error("Failed to load image:", this.imagePath);
        this.imageFailed = true;
        this.imageLoaded = false;
        this.isLoading = false;
      };
      img.src = this.imagePath;
    },
  },
};
</script>

<style scoped>
.card-image {
  position: relative;
  transition: opacity 0.3s ease;
  opacity: 0;
  background-size: cover;
  background-position: center;
  min-height: 200px;
  width: 100%;
  height: 100%;
  background-repeat: no-repeat;
}

.card-image.loaded {
  opacity: 1;
}

.card-image.loading {
}
.card-image.image-failed {
  opacity: 1;
}

.loading-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8f9fa;
  z-index: 1;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #e3e3e3;
  border-top: 3px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.card-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.3) 0%,
    rgba(0, 0, 0, 0.1) 30%,
    rgba(0, 0, 0, 0.7) 100%
  );
  padding: 15px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.card-header {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
  margin-top: 10px;
}

.card-title {
  color: white;
  font-size: 16px;
  font-weight: bold;
  margin: 0;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
  line-height: 1.2;
}

.card-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}

.class-tiers {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}

.tier-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  flex: 1;
}

.tier-group {
  display: flex;
  gap: 6px;
}

.tier-badge-card,
.rating-badge-card {
  padding: 4px 8px;
  border-radius: 6px;
  font-weight: bold;
  font-size: 11px;
  text-align: center;
  min-width: 24px;
}

.class-label {
  color: rgba(255, 255, 255, 0.8);
  font-size: 10px;
  font-weight: bold;
  text-transform: uppercase;
}

.card-stats {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 10px;
}

.map-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  margin-bottom: 0.5rem;
  justify-content: center;
}

.map-tag {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 20px;
  border: 1px solid;
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--color-text) !important;
}

.stat-row {
  display: flex;
  justify-content: space-between;
  background: #00000080;
  border-radius: 8px;
  padding: 4px 6px;
  margin-bottom: 10px;
  width: fit-content;
  gap: 15px;
  align-self: center;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.stat-label {
  color: rgba(255, 255, 255, 0.7);
  font-size: 9px;
  text-transform: uppercase;
  font-weight: bold;
}

.stat-value {
  color: white;
  font-size: 10px;
  font-weight: bold;
}

.completion-stats {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.completion-item {
  font-size: 0.65rem;
  color: var(--color-text);
  font-weight: 500;
  margin-top: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.completion-item i {
  font-size: 0.6rem;
  color: var(--color-primary);
}

.class-icon-container {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 2;
  display: flex;
  gap: 8px;
}

.author-class-icon {
  width: 45px;
  height: 45px;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 50%;
  padding: 8px;
  border: 2px solid rgba(74, 111, 165, 0.3);
}

.author-class-icon.dual-icon {
  width: 35px;
  height: 35px;
  padding: 6px;
}

.map-date-added {
  text-align: center;
  font-size: 0.7rem;
  color: var(--color-text-soft);
  font-style: italic;
  margin-top: auto;
}

.card-eliminated {
  opacity: 0.3;
  transform: scale(0.95);
  filter: grayscale(100%);
}

.card-eliminating {
  animation: eliminateCard 0.8s ease-in-out;
}

@keyframes eliminateCard {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(255, 71, 87, 0.8);
    filter: hue-rotate(0deg);
  }
  100% {
    transform: scale(0.95);
    filter: hue-rotate(180deg);
  }
}

.card-winner {
  box-shadow: 0 0 30px rgba(255, 215, 0, 0.8);
  border: 2px solid #ffd700;
}

.card-winner .card-overlay {
  background: linear-gradient(
    to bottom,
    rgba(255, 215, 0, 0.3) 0%,
    rgba(255, 215, 0, 0.1) 30%,
    rgba(255, 215, 0, 0.4) 100%
  );
}

@media (max-width: 1400px) {
  .maps-grid {
    grid-template-columns: repeat(3, minmax(280px, 1fr));
  }
}

@media (max-width: 1200px) {
  .maps-grid {
    grid-template-columns: repeat(2, minmax(260px, 1fr));
  }
}

@media (max-width: 767.98px) {
  .button-group {
    flex-direction: row;
    justify-content: center;
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    white-space: nowrap;
    flex-wrap: wrap;
    gap: 5px;
  }

  .toggle-btn {
    justify-content: center;
    flex: 1;
    min-width: 100px;
  }

  .table-header-content {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }

  .table-container {
    width: 95vw;
    overflow-x: auto;
    border-radius: 0;
  }

  .table-responsive {
    width: 100%;
    overflow-x: auto;
  }

  .table {
    width: max-content;
    min-width: 100%;
  }

  .table-dark td {
    white-space: nowrap;
  }

  .avatar {
    width: 20px;
    height: 20px;
  }

  .table-header-icon {
    font-size: 1.5rem;
  }

  .filter-section {
    width: 100%;
    padding: 15px;
  }

  .filter-columns {
    flex-direction: column;
    gap: 20px;
    width: 100%;
  }

  .filter-column {
    width: 100%;
  }

  .tier-filters,
  .rating-filters {
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
  }

  .filter-actions {
    flex-direction: column;
    gap: 10px;
    align-items: center;
  }

  .picker-controls {
    flex-direction: column;
    align-items: center;
  }

  .maps-grid {
    grid-template-columns: repeat(2, minmax(200px, 1fr));
    gap: 15px;
  }

  .responsive-ratings-row {
    flex-direction: column !important;
    gap: 20px !important;
    align-items: center !important;
  }

  .responsive-ratings-row .filter-column {
    width: 100%;
    max-width: 300px;
  }

  .intended-class-buttons {
    gap: 10px;
  }

  .intended-class-btn {
    width: 28px;
    height: 28px;
  }

  .intended-class-btn img {
    width: 24px;
    height: 24px;
  }

  .layout-divider {
    display: none;
  }

  .grid-container {
    padding: 15px;
  }

  .map-card {
    height: 280px;
  }

  .card-title {
    font-size: 14px;
  }
}

@media (max-width: 480px) {
  .maps-grid {
    grid-template-columns: repeat(3, minmax(120px, 1fr));
    gap: 10px;
  }

  .map-card {
    height: 200px;
  }

  .card-title {
    font-size: 12px;
  }

  .tier-badge-card,
  .rating-badge-card {
    font-size: 9px;
    padding: 2px 4px;
  }
}
</style>
