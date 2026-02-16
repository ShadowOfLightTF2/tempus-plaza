<template>
  <div>
    <div class="divider-container">
      <div class="divider-line"></div>
      <div class="divider-icon">★</div>
      <div class="divider-line"></div>
    </div>
    <div class="section">
      <div class="container">
        <h2 class="section-title">
          <span class="title-accent">MOST </span> POPULAR MAPS
        </h2>
        <h5 class="section-title-subtitle">(past 3 days)</h5>

        <!-- Soldier grid -->
        <div class="grid">
          <template v-if="loading">
            <div class="card skeleton-card" v-for="n in 3" :key="'sol-' + n">
              <div class="skeleton-inner">
                <div class="skeleton-badge"></div>
                <div class="skeleton-title"></div>
                <div class="skeleton-ratings">
                  <div class="skeleton-rating-row" v-for="r in 2" :key="r">
                    <div class="skeleton-label"></div>
                    <div class="skeleton-pills">
                      <div class="skeleton-pill"></div>
                      <div class="skeleton-pill"></div>
                    </div>
                  </div>
                </div>
                <div class="skeleton-count"></div>
              </div>
            </div>
          </template>
          <template v-else>
            <SmartLink
              class="card"
              v-for="map in popularSoldierMaps"
              :key="map.id"
              :to="{ name: 'MapPage', params: { mapId: map.map_id } }"
            >
              <div
                class="card-bg"
                :style="{
                  backgroundImage: `url('/map-backgrounds/medium/${map.name}.jpg')`,
                }"
              ></div>
              <div class="card-inner">
                <h5 class="section-subtitle">Soldier</h5>
                <h3>{{ map.name }}</h3>
                <div class="compact-ratings-grid">
                  <div class="rating-section">
                    <div class="rating-label">Soldier</div>
                    <div class="rating-pills">
                      <span
                        class="rating-pill tier-color"
                        :class="'tier-' + map.soldier_tier"
                        >T{{ map.soldier_tier }}</span
                      >
                      <span
                        class="rating-pill rating-color"
                        :class="'rating-' + map.soldier_rating"
                        >R{{ map.soldier_rating }}</span
                      >
                    </div>
                  </div>
                  <div class="rating-section">
                    <div class="rating-label">Demoman</div>
                    <div class="rating-pills">
                      <span
                        class="rating-pill tier-color"
                        :class="'tier-' + map.demoman_tier"
                        >T{{ map.demoman_tier }}</span
                      >
                      <span
                        class="rating-pill rating-color"
                        :class="'rating-' + map.demoman_rating"
                        >R{{ map.demoman_rating }}</span
                      >
                    </div>
                  </div>
                </div>
                <div class="completion-count">
                  <i class="bi bi-check-circle me-1"></i
                  >{{ map.run_count }} runs recently
                </div>
              </div>
            </SmartLink>
          </template>
        </div>

        <hr class="divider" style="width: 100%" />

        <!-- Demoman grid -->
        <div class="grid">
          <template v-if="loading">
            <div class="card skeleton-card" v-for="n in 3" :key="'dem-' + n">
              <div class="skeleton-inner">
                <div class="skeleton-badge"></div>
                <div class="skeleton-title"></div>
                <div class="skeleton-ratings">
                  <div class="skeleton-rating-row" v-for="r in 2" :key="r">
                    <div class="skeleton-label"></div>
                    <div class="skeleton-pills">
                      <div class="skeleton-pill"></div>
                      <div class="skeleton-pill"></div>
                    </div>
                  </div>
                </div>
                <div class="skeleton-count"></div>
              </div>
            </div>
          </template>
          <template v-else>
            <SmartLink
              class="card"
              v-for="map in popularDemomanMaps"
              :key="map.id"
              :to="{ name: 'MapPage', params: { mapId: map.map_id } }"
            >
              <div
                class="card-bg"
                :style="{
                  backgroundImage: `url('/map-backgrounds/medium/${map.name}.jpg')`,
                }"
              ></div>
              <div class="card-inner">
                <h5 class="section-subtitle">Demoman</h5>
                <h3>{{ map.name }}</h3>
                <div class="compact-ratings-grid">
                  <div class="rating-section">
                    <div class="rating-label">Demoman</div>
                    <div class="rating-pills">
                      <span
                        class="rating-pill tier-color"
                        :class="'tier-' + map.demoman_tier"
                        >T{{ map.demoman_tier }}</span
                      >
                      <span
                        class="rating-pill rating-color"
                        :class="'rating-' + map.demoman_rating"
                        >R{{ map.demoman_rating }}</span
                      >
                    </div>
                  </div>
                  <div class="rating-section">
                    <div class="rating-label">Soldier</div>
                    <div class="rating-pills">
                      <span
                        class="rating-pill tier-color"
                        :class="'tier-' + map.soldier_tier"
                        >T{{ map.soldier_tier }}</span
                      >
                      <span
                        class="rating-pill rating-color"
                        :class="'rating-' + map.soldier_rating"
                        >R{{ map.soldier_rating }}</span
                      >
                    </div>
                  </div>
                </div>
                <div class="completion-count">
                  <i class="bi bi-check-circle me-1"></i
                  >{{ map.run_count }} runs recently
                </div>
              </div>
            </SmartLink>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;
export default {
  name: "PopularMapsSection",
  data() {
    return { popularSoldierMaps: [], popularDemomanMaps: [], loading: true };
  },
  methods: {
    async fetchPopularMaps() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/get-popular-maps`,
        );
        const data = response.data;
        this.popularSoldierMaps = data
          .filter((m) => m.class_type === "soldier")
          .sort((a, b) => b.run_count - a.run_count);
        this.popularDemomanMaps = data
          .filter((m) => m.class_type === "demoman")
          .sort((a, b) => b.run_count - a.run_count);
      } catch (error) {
        console.error("Error fetching popular maps:", error);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.fetchPopularMaps();
  },
};
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.divider {
  border: none;
  height: 1px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(102, 126, 234, 0.6),
    transparent
  );
  margin: 40px 0;
  opacity: 0.8;
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
}

.divider-container {
  display: flex;
  align-items: center;
  gap: 16px;
  margin: 48px 0 20px;
  padding: 0 20px;
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
}

.divider-line {
  flex: 1;
  height: 1px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(102, 126, 234, 0.5),
    transparent
  );
}

.divider-icon {
  font-size: 0.65rem;
  color: rgba(102, 126, 234, 0.7);
  letter-spacing: 0.1em;
  border: 1px solid rgba(102, 126, 234, 0.3);
  padding: 5px 9px;
  border-radius: 4px;
  background: rgba(102, 126, 234, 0.08);
  backdrop-filter: blur(4px);
}

.section-title {
  font-family: "Barlow Condensed", sans-serif;
  font-size: 3rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  line-height: 1;
  text-transform: uppercase;
  color: #ffffff;
  margin: 0 0 10px;
}

.title-accent {
  background: linear-gradient(135deg, #4a6fa5, #253752);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.section-title-subtitle {
  font-size: 15px;
  font-weight: 500;
  text-align: center;
  color: #ffffff;
  margin-bottom: 35px;
  background: linear-gradient(135deg, #ffffff 0%, var(--color-primary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
  z-index: 10;
}

.section-subtitle {
  font-size: 1.5rem;
  font-weight: 700;
  text-align: center;
  color: #ffffff;
  background: linear-gradient(135deg, #ffffff 0%, var(--color-primary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
  z-index: 10;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 30px;
  margin-top: 20px;
  margin-bottom: 20px;
}

.card {
  border-radius: 20px;
  padding: 10px;
  padding-bottom: 25px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  text-decoration: none;
  transform: translateZ(0);
  backface-visibility: hidden;
  -webkit-font-smoothing: antialiased;
  will-change: transform;

  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.2);
  cursor: pointer;
  transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1),
    box-shadow 0.25s ease, border-color 0.25s ease;
  animation: fadeSlideUp 0.4s ease both;
  animation-delay: var(--delay, 0ms);
}

@keyframes fadeSlideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.card-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  transition: transform 0.4s ease;
  z-index: 0;
  background-image: linear-gradient(
    135deg,
    rgba(0, 0, 0, 0.7) 0%,
    rgba(0, 0, 0, 0.4) 50%,
    rgba(0, 0, 0, 0.8) 100%
  );
}

.card:hover .card-bg {
  transform: scale(1.04);
}

.card-bg::after {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
}

.card-inner {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.card:hover {
  box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2),
    0 0 0 1px rgba(102, 126, 234, 0.3);
  border-color: rgba(102, 126, 234, 0.35);
  cursor: pointer;
}

.card h3 {
  font-size: 1.5rem;
  margin-bottom: 15px;
  color: #ffffff;
}

.compact-ratings-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.rating-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.rating-pills {
  display: flex;
  gap: 8px;
}

.rating-pill {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.85rem;
  color: var(--color-dark);
  text-shadow: none;
  min-width: 45px;
  text-align: center;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.rating-label {
  font-size: 0.75rem;
  color: var(--color-text);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.8;
}

.completion-count {
  color: var(--color-text);
  margin-top: 20px;
}

.tier-color.tier-0 {
  background: rgba(51, 51, 51, 0.5);
  color: var(--color-text);
}
.tier-color.tier-1 {
  background: rgba(110, 208, 246, 0.5);
  color: var(--color-text);
}
.tier-color.tier-2 {
  background: rgba(86, 179, 233, 0.5);
  color: var(--color-text);
}
.tier-color.tier-3 {
  background: rgba(69, 184, 173, 0.5);
  color: var(--color-text);
}
.tier-color.tier-4 {
  background: rgba(101, 193, 139, 0.5);
  color: var(--color-text);
}
.tier-color.tier-5 {
  background: rgba(163, 217, 119, 0.5);
  color: var(--color-text);
}
.tier-color.tier-6 {
  background: rgba(243, 230, 131, 0.5);
  color: var(--color-text);
}
.tier-color.tier-7 {
  background: rgba(246, 194, 103, 0.5);
  color: var(--color-text);
}
.tier-color.tier-8 {
  background: rgba(240, 141, 91, 0.5);
  color: var(--color-text);
}
.tier-color.tier-9 {
  background: rgba(230, 105, 94, 0.5);
  color: var(--color-text);
}
.tier-color.tier-10 {
  background: rgba(214, 69, 69, 0.5);
  color: var(--color-text);
}

.rating-color.rating-1 {
  background: rgba(148, 196, 125, 0.5);
  color: var(--color-text);
}
.rating-color.rating-2 {
  background: rgba(171, 208, 153, 0.5);
  color: var(--color-text);
}
.rating-color.rating-3 {
  background: rgba(195, 178, 147, 0.5);
  color: var(--color-text);
}
.rating-color.rating-4 {
  background: rgba(224, 102, 102, 0.5);
  color: var(--color-text);
}

@media (max-width: 768px) {
  .grid {
    grid-template-columns: 1fr;
  }

  .section-title {
    font-size: 2rem;
  }

  .card {
    padding: 8px;
  }

  .card h3 {
    font-size: 1rem;
  }

  .completion-count {
    font-size: 0.7rem;
  }
}
@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

.skeleton-card {
  min-height: 220px;
  cursor: default;
  pointer-events: none;
}

.skeleton-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  padding: 10px 16px 20px;
  width: 100%;
}

.skeleton-badge,
.skeleton-title,
.skeleton-label,
.skeleton-pill,
.skeleton-count {
  border-radius: 8px;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 25%,
    rgba(255, 255, 255, 0.12) 50%,
    rgba(255, 255, 255, 0.05) 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.6s infinite linear;
}

.skeleton-badge {
  width: 80px;
  height: 20px;
  border-radius: 20px;
}
.skeleton-title {
  width: 68%;
  height: 24px;
  border-radius: 6px;
}
.skeleton-ratings {
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: 100%;
  align-items: center;
}
.skeleton-rating-row {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}
.skeleton-label {
  width: 58px;
  height: 11px;
  border-radius: 4px;
}
.skeleton-pills {
  display: flex;
  gap: 8px;
}
.skeleton-pill {
  width: 52px;
  height: 32px;
  border-radius: 20px;
}
.skeleton-count {
  width: 110px;
  height: 14px;
  border-radius: 4px;
  margin-top: 6px;
}
</style>
