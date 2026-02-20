<template>
  <div>
    <div class="divider-container">
      <div class="divider-line"></div>
      <div class="divider-icon">✚</div>
      <div class="divider-line"></div>
    </div>
    <div class="section">
      <div class="container">
        <h2 class="section-title">
          <span class="title-accent">LATEST</span> TIER/RATING
          <span class="title-accent">CHANGES</span>
        </h2>
        <h5 class="section-title-subtitle">(last 10 changes)</h5>
        <div class="tier-carousel-container" v-if="!dataLoaded">
          <div class="tier-carousel-wrapper">
            <div class="tier-carousel-track">
              <div
                class="tier-carousel-item carousel-active"
                v-for="n in 3"
                :key="'skel-' + n"
              >
                <div class="card skeleton-card">
                  <div class="skeleton-inner">
                    <div class="skeleton-title"></div>
                    <div class="skeleton-zone"></div>
                    <div class="skeleton-ratings">
                      <div
                        class="skeleton-rating-block"
                        v-for="cls in 2"
                        :key="cls"
                      >
                        <div class="skeleton-label"></div>
                        <div class="skeleton-pill-row">
                          <div class="skeleton-pill"></div>
                          <div class="skeleton-arrow"></div>
                          <div class="skeleton-pill"></div>
                        </div>
                        <div class="skeleton-pill-row">
                          <div class="skeleton-pill"></div>
                          <div class="skeleton-arrow"></div>
                          <div class="skeleton-pill"></div>
                        </div>
                      </div>
                    </div>
                    <div class="skeleton-date"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="tier-carousel-container"
          v-if="dataLoaded && tierRatingChanges.length > 0"
        >
          <button
            v-if="!isMobileView && currentSlide > 0"
            class="carousel-arrow carousel-arrow-left"
            @click="currentSlide = Math.max(0, currentSlide - 1)"
          >
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
          <button
            v-if="!isMobileView && currentSlide < tierRatingChanges.length - 3"
            class="carousel-arrow carousel-arrow-right"
            @click="
              currentSlide = Math.min(
                tierRatingChanges.length - 3,
                currentSlide + 1,
              )
            "
          >
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>

          <div
            class="tier-carousel-wrapper"
            @touchstart="handleTouchStart"
            @touchend="handleTouchEnd"
          >
            <div
              class="tier-carousel-track"
              :style="{ transform: carouselTransform }"
            >
              <div
                class="tier-carousel-item"
                v-for="(change, index) in tierRatingChanges"
                :key="change.id"
                :class="{
                  'carousel-prev':
                    index === validCurrentSlide - 1 && !isMobileView,
                  'carousel-active': isMobileView
                    ? index === validCurrentSlide
                    : index === validCurrentSlide ||
                      index === validCurrentSlide + 1 ||
                      index === validCurrentSlide + 2,
                  'carousel-next':
                    index === validCurrentSlide + 3 && !isMobileView,
                }"
                @click="
                  (index === currentSlide - 1 || index === currentSlide + 3) &&
                  !isMobileView
                    ? goToSlide(index)
                    : null
                "
              >
                <SmartLink
                  v-if="
                    isMobileView ||
                    (index >= currentSlide && index <= currentSlide + 2)
                  "
                  class="card"
                  :to="{ name: 'MapPage', params: { mapId: change.map_id } }"
                >
                  <div
                    class="card-bg"
                    :style="{
                      backgroundImage: `url('/map-backgrounds/medium/${change.name}.jpg')`,
                    }"
                  ></div>
                  <div class="card-inner">
                    <div class="change-header-inline">
                      <h3>{{ change.name }}</h3>
                    </div>
                    <div class="zone-info-center">
                      <span class="zone-info">
                        {{
                          change.zone_type === "map"
                            ? "Map"
                            : change.zone_type === "course"
                            ? "Course"
                            : "Bonus"
                        }}
                        {{
                          change.zone_type !== "map" ? change.zone_index : ""
                        }}
                      </span>
                    </div>
                    <div class="compact-ratings-grid">
                      <div class="rating-section">
                        <div class="rating-label">Soldier</div>
                        <div class="rating-change-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="'tier-' + change.old_soldier_tier"
                            >T{{ change.old_soldier_tier }}</span
                          >
                          <span class="arrow-small">→</span>
                          <span
                            class="rating-pill tier-color"
                            :class="[
                              'tier-' + change.new_soldier_tier,
                              {
                                changed:
                                  change.old_soldier_tier !==
                                  change.new_soldier_tier,
                              },
                            ]"
                            >T{{ change.new_soldier_tier }}</span
                          >
                        </div>
                        <div class="rating-change-pills">
                          <span
                            class="rating-pill rating-color"
                            :class="'rating-' + change.old_soldier_rating"
                            >R{{ change.old_soldier_rating }}</span
                          >
                          <span class="arrow-small">→</span>
                          <span
                            class="rating-pill rating-color"
                            :class="[
                              'rating-' + change.new_soldier_rating,
                              {
                                changed:
                                  change.old_soldier_rating !==
                                  change.new_soldier_rating,
                              },
                            ]"
                            >R{{ change.new_soldier_rating }}</span
                          >
                        </div>
                      </div>
                      <div class="rating-section">
                        <div class="rating-label">Demoman</div>
                        <div class="rating-change-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="'tier-' + change.old_demoman_tier"
                            >T{{ change.old_demoman_tier }}</span
                          >
                          <span class="arrow-small">→</span>
                          <span
                            class="rating-pill tier-color"
                            :class="[
                              'tier-' + change.new_demoman_tier,
                              {
                                changed:
                                  change.old_demoman_tier !==
                                  change.new_demoman_tier,
                              },
                            ]"
                            >T{{ change.new_demoman_tier }}</span
                          >
                        </div>
                        <div class="rating-change-pills">
                          <span
                            class="rating-pill rating-color"
                            :class="'rating-' + change.old_demoman_rating"
                            >R{{ change.old_demoman_rating }}</span
                          >
                          <span class="arrow-small">→</span>
                          <span
                            class="rating-pill rating-color"
                            :class="[
                              'rating-' + change.new_demoman_rating,
                              {
                                changed:
                                  change.old_demoman_rating !==
                                  change.new_demoman_rating,
                              },
                            ]"
                            >R{{ change.new_demoman_rating }}</span
                          >
                        </div>
                      </div>
                    </div>
                    <div class="completion-count">
                      <i class="bi bi-clock me-1"></i>
                      {{ formatDate(change.changed_at) }}
                    </div>
                  </div>
                </SmartLink>

                <div v-else-if="!isMobileView" class="card">
                  <div
                    class="card-bg"
                    :style="{
                      backgroundImage: `url('/map-backgrounds/medium/${change.name}.jpg')`,
                    }"
                  ></div>
                  <div class="card-inner">
                    <div class="change-header-inline">
                      <h3>{{ change.name }}</h3>
                    </div>
                    <div class="zone-info-center">
                      <span class="zone-info">
                        {{
                          change.zone_type === "map"
                            ? "Map"
                            : change.zone_type === "course"
                            ? "Course"
                            : "Bonus"
                        }}
                        {{
                          change.zone_type !== "map" ? change.zone_index : ""
                        }}
                      </span>
                    </div>
                    <div class="compact-ratings-grid">
                      <div class="rating-section">
                        <div class="rating-label">Soldier</div>
                        <div class="rating-change-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="'tier-' + change.old_soldier_tier"
                            >T{{ change.old_soldier_tier }}</span
                          >
                          <span class="arrow-small">→</span>
                          <span
                            class="rating-pill tier-color"
                            :class="[
                              'tier-' + change.new_soldier_tier,
                              {
                                changed:
                                  change.old_soldier_tier !==
                                  change.new_soldier_tier,
                              },
                            ]"
                            >T{{ change.new_soldier_tier }}</span
                          >
                        </div>
                        <div class="rating-change-pills">
                          <span
                            class="rating-pill rating-color"
                            :class="'rating-' + change.old_soldier_rating"
                            >R{{ change.old_soldier_rating }}</span
                          >
                          <span class="arrow-small">→</span>
                          <span
                            class="rating-pill rating-color"
                            :class="[
                              'rating-' + change.new_soldier_rating,
                              {
                                changed:
                                  change.old_soldier_rating !==
                                  change.new_soldier_rating,
                              },
                            ]"
                            >R{{ change.new_soldier_rating }}</span
                          >
                        </div>
                      </div>
                      <div class="rating-section">
                        <div class="rating-label">Demoman</div>
                        <div class="rating-change-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="'tier-' + change.old_demoman_tier"
                            >T{{ change.old_demoman_tier }}</span
                          >
                          <span class="arrow-small">→</span>
                          <span
                            class="rating-pill tier-color"
                            :class="[
                              'tier-' + change.new_demoman_tier,
                              {
                                changed:
                                  change.old_demoman_tier !==
                                  change.new_demoman_tier,
                              },
                            ]"
                            >T{{ change.new_demoman_tier }}</span
                          >
                        </div>
                        <div class="rating-change-pills">
                          <span
                            class="rating-pill rating-color"
                            :class="'rating-' + change.old_demoman_rating"
                            >R{{ change.old_demoman_rating }}</span
                          >
                          <span class="arrow-small">→</span>
                          <span
                            class="rating-pill rating-color"
                            :class="[
                              'rating-' + change.new_demoman_rating,
                              {
                                changed:
                                  change.old_demoman_rating !==
                                  change.new_demoman_rating,
                              },
                            ]"
                            >R{{ change.new_demoman_rating }}</span
                          >
                        </div>
                      </div>
                    </div>
                    <div class="completion-count">
                      <i class="bi bi-clock me-1"></i>
                      {{ formatDate(change.changed_at) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="carousel-indicators"
            v-if="!isMobileView && tierRatingChanges.length > 3"
          >
            <span
              v-for="index in tierRatingChanges.length - 2"
              :key="'indicator-' + (index - 1)"
              class="indicator-dot"
              :class="{ active: index - 1 === validCurrentSlide }"
              @click="currentSlide = index - 1"
            ></span>
          </div>
          <div
            class="carousel-indicators"
            v-if="isMobileView && tierRatingChanges.length > 1"
          >
            <span
              v-for="(change, index) in tierRatingChanges"
              :key="'indicator-' + index"
              class="indicator-dot"
              :class="{ active: index === validCurrentSlide }"
              @click="currentSlide = index"
            ></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "TierRatingChangesSection",
  emits: ["data-loaded"],
  data() {
    return {
      tierRatingChanges: [],
      dataLoaded: false,
      currentSlide: 0,
      isMobileView: false,
      touchStartX: 0,
      touchEndX: 0,
    };
  },
  computed: {
    validCurrentSlide() {
      if (!this.tierRatingChanges.length) return 0;
      return Math.min(
        this.currentSlide,
        Math.max(
          0,
          this.tierRatingChanges.length - (this.isMobileView ? 1 : 3),
        ),
      );
    },
    carouselTransform() {
      if (this.isMobileView) {
        return `translateX(-${this.validCurrentSlide * 100}%)`;
      }
      return `translateX(calc(-${this.validCurrentSlide} * (100% / 3 + 10px)))`;
    },
    latestChange() {
      return this.tierRatingChanges[0] || null;
    },
  },
  methods: {
    checkMobileView() {
      this.isMobileView = window.innerWidth <= 768;
    },
    goToSlide(index) {
      if (index === this.currentSlide - 1) {
        this.currentSlide = Math.max(0, this.currentSlide - 1);
      } else if (index === this.currentSlide + 3) {
        this.currentSlide = Math.min(
          this.tierRatingChanges.length - 3,
          this.currentSlide + 1,
        );
      }
    },
    handleTouchStart(e) {
      this.touchStartX = e.changedTouches[0].screenX;
    },
    handleTouchEnd(e) {
      this.touchEndX = e.changedTouches[0].screenX;
      this.handleSwipe();
    },
    handleSwipe() {
      const swipeThreshold = 50;
      const diff = this.touchStartX - this.touchEndX;
      if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
          if (this.currentSlide < this.tierRatingChanges.length - 1)
            this.currentSlide++;
        } else {
          if (this.currentSlide > 0) this.currentSlide--;
        }
      }
    },
    formatDate(dateString) {
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    async fetchTierRatingHistory() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/tier-rating-history`,
        );
        this.tierRatingChanges = response.data;
        this.dataLoaded = true;
        this.currentSlide = 0;
        this.$emit("data-loaded", this.tierRatingChanges[0]);
      } catch (error) {
        console.error("Error fetching tier rating history:", error);
        this.tierRatingChanges = [];
        this.dataLoaded = false;
      }
    },
  },
  mounted() {
    this.fetchTierRatingHistory();
    this.checkMobileView();
    window.addEventListener("resize", this.checkMobileView);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkMobileView);
  },
};
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
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

.card {
  box-shadow: 0 0 20px rgba(0, 0, 0);
  background: linear-gradient(
      to bottom,
      rgba(255, 255, 255, 0.15) 0%,
      rgba(255, 255, 255, 0.288) 20%,
      rgba(255, 255, 255, 0.15) 40%,
      rgba(255, 255, 255, 0) 100%
    ),
    rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  padding: 10px;
  padding-bottom: 25px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  text-decoration: none;
  border: 1px solid rgba(255, 255, 255, 0.2);
  transform: translateZ(0);
  backface-visibility: hidden;
  -webkit-font-smoothing: antialiased;
  will-change: transform;
}

.card-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  transition: transform 0.4s ease;
  z-index: 0;
}

.card-bg::before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(0, 0, 0, 0.6) 0%,
    rgba(0, 0, 0, 0.6) 50%,
    rgba(0, 0, 0, 0.6) 100%
  );
  z-index: 1;
}

.card-bg::after {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(
    circle at 30% 20%,
    rgba(255, 255, 255, 0.1) 0%,
    transparent 50%
  );
  z-index: 2;
}

.card:hover .card-bg {
  transform: scale(1.04);
}

.card-inner {
  position: relative;
  z-index: 3;
  display: flex;
  flex-direction: column;
  flex: 1;
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

.rating-label {
  font-size: 0.75rem;
  color: var(--color-text);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.8;
}

.rating-change-pills {
  display: flex;
  gap: 6px;
  align-items: center;
  justify-content: center;
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

.rating-pill.changed {
  border: 2px solid rgba(255, 255, 255, 0.9);
  box-shadow: 0 0 15px rgba(102, 126, 234, 0.6), 0 4px 12px rgba(0, 0, 0, 0.3);
}

.arrow-small {
  color: var(--color-primary);
  font-size: 1.1rem;
  font-weight: bold;
  opacity: 0.8;
}

.completion-count {
  color: var(--color-text);
  margin-top: 20px;
}

.change-header-inline {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 5px;
}

.change-header-inline h3 {
  margin-bottom: 0;
}

.zone-info-center {
  display: flex;
  justify-content: center;
  margin: 10px 0px;
}

.zone-info {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.8);
  background: rgba(255, 255, 255, 0.2);
  padding: 4px 10px;
  border-radius: 12px;
  text-transform: capitalize;
  font-weight: 600;
}

.loading-container {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 16px;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 10px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
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

.tier-carousel-container {
  position: relative;
  max-width: 1400px;
  padding: 0 100px;
}

.tier-carousel-wrapper {
  overflow: visible;
  position: relative;
}

.tier-carousel-track {
  display: flex;
  gap: 30px;
  transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.tier-carousel-item {
  flex: 0 0 calc((100% - 60px) / 3);
  transition: all 0.5s ease;
  transform-origin: center;
  position: relative;
}

.tier-carousel-item.carousel-active {
  z-index: 2;
  opacity: 1;
  transform: scale(1);
  pointer-events: auto;
}

.tier-carousel-item.carousel-prev,
.tier-carousel-item.carousel-next {
  opacity: 0.2;
  cursor: pointer;
  pointer-events: auto;
  z-index: 1;
  position: relative;
}

.tier-carousel-item.carousel-prev {
  transform: scale(0.85) rotateY(15deg);
}

.tier-carousel-item.carousel-next {
  transform: scale(0.85) rotateY(-15deg);
}

.tier-carousel-item.carousel-prev:hover,
.tier-carousel-item.carousel-next:hover {
  opacity: 0.7;
}

.tier-carousel-item:not(.carousel-active):not(.carousel-prev):not(
    .carousel-next
  ) {
  opacity: 0;
  pointer-events: none;
}

.tier-carousel-item .card {
  width: 100%;
  height: 100%;
}

.carousel-indicators {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin: 30px 0px 0px 0px;
  position: relative;
  z-index: 1;
}

.indicator-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.indicator-dot.active {
  background: var(--color-primary);
  width: 28px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(102, 126, 234, 0.6);
}

.indicator-dot:hover {
  background: rgba(255, 255, 255, 0.6);
  border-color: rgba(255, 255, 255, 0.3);
}

.carousel-arrow {
  position: absolute;
  top: 183px;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 10;
  color: #ffffff;
  backdrop-filter: blur(10px);
}

.carousel-arrow:hover {
  background: rgba(102, 126, 234, 0.3);
  border-color: var(--color-primary);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
  transform: translateY(-50%) scale(1.1);
}

.carousel-arrow-left {
  left: 20px;
}
.carousel-arrow-right {
  right: 20px;
}
.carousel-arrow svg {
  width: 24px;
  height: 24px;
}

@media (max-width: 768px) {
  .tier-carousel-container {
    padding: 0 20px;
  }

  .tier-carousel-track {
    gap: 0;
  }

  .tier-carousel-item {
    flex: 0 0 100%;
  }

  .tier-carousel-item .card {
    padding-bottom: 40px;
  }

  .tier-carousel-item.carousel-active {
    opacity: 1;
    transform: none;
    display: block;
  }

  .tier-carousel-item:not(.carousel-active) {
    opacity: 0;
    transform: none;
  }

  .tier-carousel-item.carousel-prev,
  .tier-carousel-item.carousel-next {
    opacity: 0;
    transform: none;
  }

  .section-title {
    font-size: 2rem;
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
  min-height: 320px;
  cursor: default;
  pointer-events: none;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.skeleton-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  padding: 20px 16px 24px;
  width: 100%;
}

.skeleton-title,
.skeleton-zone,
.skeleton-label,
.skeleton-pill,
.skeleton-arrow,
.skeleton-date {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 25%,
    rgba(255, 255, 255, 0.12) 50%,
    rgba(255, 255, 255, 0.05) 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.6s infinite linear;
  border-radius: 6px;
}

.skeleton-title {
  width: 65%;
  height: 28px;
}
.skeleton-zone {
  width: 80px;
  height: 22px;
  border-radius: 12px;
}
.skeleton-ratings {
  display: flex;
  flex-direction: column;
  gap: 18px;
  width: 100%;
  align-items: center;
}
.skeleton-rating-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}
.skeleton-label {
  width: 60px;
  height: 11px;
}
.skeleton-pill-row {
  display: flex;
  align-items: center;
  gap: 6px;
}
.skeleton-pill {
  width: 52px;
  height: 32px;
  border-radius: 20px;
}
.skeleton-arrow {
  width: 16px;
  height: 16px;
  border-radius: 50%;
}
.skeleton-date {
  width: 100px;
  height: 14px;
  margin-top: 8px;
}
</style>
