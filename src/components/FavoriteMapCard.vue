<template>
  <div
    class="map-card"
    :class="{ 'map-empty-map': !map.name }"
    @click="handleClick"
  >
    <div class="card-bg" :style="backgroundStyle"></div>
    <div class="card-inner">
      <h5 v-if="map.name" class="map-card-subtitle">
        {{ map.class_type }}
      </h5>
      <div class="map-header-nonmargin">
        <h3 class="map-name">
          {{ map.name || "Click to add a map" }}
        </h3>
      </div>
      <div v-if="map.name" class="map-compact-ratings-grid">
        <div
          v-if="map.class_type === 'soldier'"
          class="map-rating-section intended-class-section"
        >
          <div class="map-rating-label">Soldier</div>
          <div class="map-rating-pills">
            <span
              class="map-rating-pill map-tier-color"
              :class="'tier-' + map.map_soldier_tier"
            >
              T{{ map.map_soldier_tier }}
            </span>
            <span
              class="map-rating-pill map-rating-color"
              :class="'rating-' + map.map_soldier_rating"
            >
              R{{ map.map_soldier_rating }}
            </span>
          </div>
        </div>
        <div v-else class="map-rating-section intended-class-section">
          <div class="map-rating-label">Demoman</div>
          <div class="map-rating-pills">
            <span
              class="map-rating-pill map-tier-color"
              :class="'tier-' + map.map_demoman_tier"
            >
              T{{ map.map_demoman_tier }}
            </span>
            <span
              class="map-rating-pill map-rating-color"
              :class="'rating-' + map.map_demoman_rating"
            >
              R{{ map.map_demoman_rating }}
            </span>
          </div>
        </div>
        <div v-if="map.class_type === 'soldier'" class="map-rating-section">
          <div class="map-rating-label">Demoman</div>
          <div class="map-rating-pills">
            <span
              class="map-rating-pill map-tier-color"
              :class="'tier-' + map.map_demoman_tier"
            >
              T{{ map.map_demoman_tier }}
            </span>
            <span
              class="map-rating-pill map-rating-color"
              :class="'rating-' + map.map_demoman_rating"
            >
              R{{ map.map_demoman_rating }}
            </span>
          </div>
        </div>
        <div v-else class="map-rating-section">
          <div class="map-rating-label">Soldier</div>
          <div class="map-rating-pills">
            <span
              class="map-rating-pill map-tier-color"
              :class="'tier-' + map.map_soldier_tier"
            >
              T{{ map.map_soldier_tier }}
            </span>
            <span
              class="map-rating-pill map-rating-color"
              :class="'rating-' + map.map_soldier_rating"
            >
              R{{ map.map_soldier_rating }}
            </span>
          </div>
        </div>
        <div class="record-row">
          <img
            :src="`/icons/${map.class_type}.png`"
            :alt="map.class_type"
            class="map-class-icon"
            loading="lazy"
          />
          {{ formatDuration(map.player_record_duration) }} |
          <span :class="getPlacementClass(map.record_placement)">
            #{{ map.record_rank }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDuration } from "@/utils/calculations.js";

export default {
  name: "FavoriteMapCard",
  props: {
    map: { type: Object, required: true },
    index: { type: Number, required: true },
    isCurrentUser: { type: Boolean, default: false },
  },
  emits: ["open-search", "navigate-to-map"],
  computed: {
    backgroundStyle() {
      if (!this.map.name) return {};
      return {
        backgroundImage: `url('/map-backgrounds/medium/${this.map.name}.jpg')`,
      };
    },
  },
  methods: {
    handleClick() {
      if (this.isCurrentUser) {
        this.$emit("open-search", this.index);
      } else if (this.map.id) {
        this.$emit("navigate-to-map", this.map.id);
      }
    },
    formatDuration(duration) {
      return formatDuration(duration);
    },
    getPlacementClass(placement) {
      const placementClasses = {
        1: "placement-gold",
        2: "placement-silver",
        3: "placement-bronze",
        11: "placement-g1",
        12: "placement-g2",
        13: "placement-g3",
        14: "placement-g4",
        15: "placement-g5",
      };
      if (placement >= 4 && placement <= 10) return "placement-blue";
      return placementClasses[placement] || "";
    },
  },
};
</script>

<style scoped>
.map-card {
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
  min-height: 300px;
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
    rgba(0, 0, 0, 0.7) 0%,
    rgba(0, 0, 0, 0.4) 50%,
    rgba(0, 0, 0, 0.8) 100%
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

.map-card:hover .card-bg {
  transform: scale(1.04);
}

.card-inner {
  position: relative;
  z-index: 3;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.map-card:hover {
  border-radius: 15px;
  box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2),
    0 0 0 1px rgba(102, 126, 234, 0.3);
  cursor: pointer;
}

.map-card-subtitle {
  font-size: 1.75rem;
  font-weight: 700;
  text-align: center;
  color: #ffffff;
  margin-bottom: 35px;
  background: linear-gradient(135deg, #ffffff 0%, var(--color-primary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
  z-index: 10;
  text-transform: capitalize;
}

.map-header-nonmargin {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
}

.map-name {
  color: white;
  font-size: 16px;
  font-weight: bold;
  margin: 0;
  text-shadow: 2px 3px 1px rgba(0, 0, 0, 0.4);
  line-height: 1.2;
}

.map-compact-ratings-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.map-rating-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.map-rating-pills {
  display: flex;
  gap: 8px;
}

.map-rating-pill {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.8rem;
  color: var(--color-dark);
  text-shadow: none;
  min-width: 40px;
  text-align: center;
}

.map-rating-label {
  font-size: 0.75rem;
  color: var(--color-text);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.8;
}

.record-row {
  margin-top: 20px;
  text-align: center;
  font-size: 1rem;
  color: var(--color-text);
  font-weight: 600;
  letter-spacing: 0.5px;
}

.map-class-icon {
  height: 24px;
  width: 24px;
}

.map-empty-map {
  justify-content: center;
  align-items: center;
  background: rgba(255, 255, 255, 0.05) !important;
  border: 2px dashed rgba(255, 255, 255, 0.3);
}

.map-empty-map h3 {
  color: rgba(255, 255, 255, 0.6);
  text-align: center;
}

/* Tier colors */
.map-tier-color.tier-0 {
  background: rgba(51, 51, 51, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-1 {
  background: rgba(110, 208, 246, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-2 {
  background: rgba(86, 179, 233, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-3 {
  background: rgba(69, 184, 173, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-4 {
  background: rgba(101, 193, 139, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-5 {
  background: rgba(163, 217, 119, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-6 {
  background: rgba(243, 230, 131, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-7 {
  background: rgba(246, 194, 103, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-8 {
  background: rgba(240, 141, 91, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-9 {
  background: rgba(230, 105, 94, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-10 {
  background: rgba(214, 69, 69, 0.5);
  color: var(--color-text);
}

/* Rating colors */
.map-rating-color.rating-1 {
  background: rgba(148, 196, 125, 0.5);
  color: var(--color-text);
}
.map-rating-color.rating-2 {
  background: rgba(171, 208, 153, 0.5);
  color: var(--color-text);
}
.map-rating-color.rating-3 {
  background: rgba(195, 178, 147, 0.5);
  color: var(--color-text);
}
.map-rating-color.rating-4 {
  background: rgba(224, 102, 102, 0.5);
  color: var(--color-text);
}

/* Placement colors */
.placement-gold {
  color: #ffd700;
  font-weight: bold;
}
.placement-silver {
  color: #c0c0c0;
  font-weight: bold;
}
.placement-bronze {
  color: #cd7f32;
  font-weight: bold;
}
.placement-blue {
  color: #30869b;
  font-weight: bold;
}
.placement-g1 {
  color: #ff9797af !important;
  font-weight: bold;
}
.placement-g2 {
  color: #f7cf84 !important;
  font-weight: bold;
}
.placement-g3 {
  color: #d27d2dbb !important;
  font-weight: bold;
}
.placement-g4 {
  color: #b3b3b3ce !important;
  font-weight: bold;
}
.placement-g5 {
  color: var(--color-text) !important;
  font-weight: bold;
}

@media (max-width: 1400px) {
  .map-card {
    border-radius: 15px;
    padding: 15px;
    padding-bottom: 20px;
    min-height: 280px;
  }

  .map-card:hover {
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(102, 126, 234, 0.6);
  }

  .map-card-subtitle {
    font-size: 1.3rem;
    margin-bottom: 20px;
  }

  .map-card h3 {
    font-size: 1.1rem;
  }

  .map-rating-pill {
    padding: 3px 8px;
    border-radius: 15px;
    font-size: 0.7rem;
    min-width: 30px;
  }

  .map-rating-label {
    font-size: 0.65rem;
  }

  .record-row {
    margin-top: 10px;
    font-size: 0.8rem;
  }

  .map-class-icon {
    height: 18px;
    width: 18px;
  }
}
</style>
