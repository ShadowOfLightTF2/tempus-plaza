<template>
  <div v-if="authoredMaps.length > 0" id="authored-maps" class="map-section">
    <div class="map-container">
      <h4 class="map-section-title">
        Authored maps
        <span class="rotw-count">({{ authoredMaps.length }})</span>
      </h4>
      <div class="author-map-grid">
        <SmartLink
          v-for="(map, index) in visibleAuthoredMaps"
          :key="index"
          :to="{
            name: 'MapPage',
            params: { mapId: map.map_id },
          }"
          tag="div"
          class="map-card author-card"
          :style="{
            background: `
              linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
              radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
              url('/map-backgrounds/medium/${map.map_name}.jpg') center/cover no-repeat
            `,
          }"
        >
          <div class="class-icon-container">
            <img
              v-for="icon in map.classIcons"
              :key="icon.alt"
              :src="icon.src"
              :class="icon.class"
              :alt="icon.alt"
            />
          </div>
          <div class="map-header">
            <h3 class="map-name">{{ map.map_name }}</h3>
          </div>
          <div class="map-compact-ratings-grid">
            <div
              v-if="map.intended_class === 4"
              class="map-rating-section intended-class-section"
            >
              <div class="map-rating-label">Demoman</div>
              <div class="map-rating-pills">
                <span
                  class="map-rating-pill map-tier-color"
                  :class="'tier-' + map.demoman_tier"
                >
                  T{{ map.demoman_tier }}
                </span>
                <span
                  class="map-rating-pill map-rating-color"
                  :class="'rating-' + map.demoman_rating"
                >
                  R{{ map.demoman_rating }}
                </span>
              </div>
            </div>
            <div v-else class="map-rating-section intended-class-section">
              <div class="map-rating-label">Soldier</div>
              <div class="map-rating-pills">
                <span
                  class="map-rating-pill map-tier-color"
                  :class="'tier-' + map.soldier_tier"
                >
                  T{{ map.soldier_tier }}
                </span>
                <span
                  class="map-rating-pill map-rating-color"
                  :class="'rating-' + map.soldier_rating"
                >
                  R{{ map.soldier_rating }}
                </span>
              </div>
            </div>
            <div v-if="map.intended_class === 4" class="map-rating-section">
              <div class="map-rating-label">Soldier</div>
              <div class="map-rating-pills">
                <span
                  class="map-rating-pill map-tier-color"
                  :class="'tier-' + map.soldier_tier"
                >
                  T{{ map.soldier_tier }}
                </span>
                <span
                  class="map-rating-pill map-rating-color"
                  :class="'rating-' + map.soldier_rating"
                >
                  R{{ map.soldier_rating }}
                </span>
              </div>
            </div>
            <div v-else class="map-rating-section">
              <div class="map-rating-label">Demoman</div>
              <div class="map-rating-pills">
                <span
                  class="map-rating-pill map-tier-color"
                  :class="'tier-' + map.demoman_tier"
                >
                  T{{ map.demoman_tier }}
                </span>
                <span
                  class="map-rating-pill map-rating-color"
                  :class="'rating-' + map.demoman_rating"
                >
                  R{{ map.demoman_rating }}
                </span>
              </div>
            </div>
          </div>
          <div class="map-date-added">
            Date Added:
            {{ map.date_added }}
          </div>
        </SmartLink>
      </div>
      <div class="load-more-container">
        <button
          v-if="visibleAuthoredMaps.length < authoredMaps.length"
          @click="loadMoreAuthoredMaps"
          class="global-btn"
        >
          Load More
        </button>
        <button
          v-if="visibleAuthoredMaps.length < authoredMaps.length"
          @click="showAllAuthoredMaps"
          class="global-btn"
        >
          Show All
        </button>
        <button
          v-if="visibleAuthoredMaps.length > authoredMapsPerPage"
          @click="closeAllAuthoredMaps"
          class="global-btn"
        >
          Close All
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "AuthoredMaps",
  props: {
    playerId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      authoredMaps: [],
      visibleAuthoredMaps: [],
      authoredMapsPage: 1,
      authoredMapsPerPage: 3,
    };
  },
  mounted() {
    this.fetchAuthoredMaps(this.playerId);
  },
  watch: {
    playerId: {
      immediate: false,
      handler(newId) {
        this.fetchAuthoredMaps(newId);
      },
    },
    authoredMaps(newMaps) {
      if (newMaps.length > 0 && this.$route.hash === "#authored-maps") {
        this.scrollToAuthoredMaps();
      }
    },
  },
  methods: {
    scrollToAuthoredMaps() {
      const attemptScroll = () => {
        const element = document.getElementById("authored-maps");
        if (element) {
          element.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      };
      this.$nextTick(attemptScroll);
      setTimeout(attemptScroll, 500);
    },
    getClassIcons(intendedClass) {
      if (intendedClass === 3) {
        return [
          {
            src: "/icons/soldier.png",
            alt: "Soldier class icon",
            class: "author-class-icon",
          },
        ];
      }
      if (intendedClass === 4) {
        return [
          {
            src: "/icons/demoman.png",
            alt: "Demoman class icon",
            class: "author-class-icon",
          },
        ];
      }
      if (intendedClass === 5) {
        return [
          {
            src: "/icons/soldier.png",
            alt: "Soldier class icon",
            class: "author-class-icon dual-icon",
          },
          {
            src: "/icons/demoman.png",
            alt: "Demoman class icon",
            class: "author-class-icon dual-icon",
          },
        ];
      }
      return [];
    },
    async fetchAuthoredMaps(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/maps-from-author`,
        );
        this.authoredMaps = response.data
          .map((map) => ({
            map_id: map.map_id,
            map_name: map.name,
            soldier_tier: map.soldier_tier,
            soldier_rating: map.soldier_rating,
            demoman_tier: map.demoman_tier,
            demoman_rating: map.demoman_rating,
            intended_class: map.intended_class,
            classIcons: this.getClassIcons(map.intended_class),
            date_added: this.formatDate2(map.date_added),
          }))
          .sort((a, b) => b.map_id - a.map_id);

        this.visibleAuthoredMaps = this.authoredMaps.slice(
          0,
          this.authoredMapsPerPage,
        );
      } catch (error) {
        console.error("Error fetching authored maps:", error);
      }
    },
    formatDate2(unixTimestamp) {
      const date = new Date(unixTimestamp * 1000);
      const day = String(date.getDate()).padStart(2, "0");
      const monthNames = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];
      const monthName = monthNames[date.getMonth()];
      const year = date.getFullYear();
      return `${day} ${monthName} ${year}`;
    },
    loadMoreAuthoredMaps() {
      const start = this.authoredMapsPage * this.authoredMapsPerPage;
      const end = start + this.authoredMapsPerPage;
      this.visibleAuthoredMaps = [
        ...this.visibleAuthoredMaps,
        ...this.authoredMaps.slice(start, end),
      ];
      this.authoredMapsPage++;
    },
    showAllAuthoredMaps() {
      this.visibleAuthoredMaps = [...this.authoredMaps];
      this.authoredMapsPage = Math.ceil(
        this.authoredMaps.length / this.authoredMapsPerPage,
      );
    },
    closeAllAuthoredMaps() {
      this.visibleAuthoredMaps = this.authoredMaps.slice(
        0,
        this.authoredMapsPerPage,
      );
      this.authoredMapsPage = 1;
    },
  },
};
</script>

<style scoped>
.map-section {
  padding: 15px 0;
  display: flex;
  justify-content: center;
}
.map-container {
  width: fit-content;
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}
.map-section-title {
  font-size: 1.75rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 30px;
  color: var(--color-text);
}
.rotw-count {
  font-size: 1.5rem;
  color: var(--color-text-soft);
  font-weight: normal;
}
.author-map-grid {
  display: grid;
  gap: 30px;
  margin: 20px auto;
  justify-content: center;
  max-width: 1200px;
}
.author-map-grid:has(.map-card:nth-child(1):last-child) {
  grid-template-columns: 1fr;
  width: 500px;
}
.author-map-grid:has(.map-card:nth-child(2):last-child) {
  grid-template-columns: repeat(2, 1fr);
  width: 800px;
}
.author-map-grid:has(.map-card:nth-child(3)) {
  grid-template-columns: repeat(3, 1fr);
  width: 1200px;
}
.author-card {
  justify-content: center;
  position: relative;
  overflow: hidden;
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
  width: 50px;
  height: 50px;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 50%;
  padding: 8px;
  border: 2px solid rgba(74, 111, 165, 0.3);
}
.author-class-icon.dual-icon {
  width: 40px;
  height: 40px;
  padding: 6px;
}
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
.map-card:hover {
  border-radius: 15px;
  box-shadow: 0 0 40px rgba(102, 126, 234, 0.6);
  cursor: pointer;
}
.map-header {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
  margin-top: 50px;
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
.map-date-added {
  text-align: center;
  font-size: 0.85rem;
  margin-top: 0.75rem;
  color: var(--color-text-soft);
  font-style: italic;
  margin-top: auto;
}
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
.load-more-container {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 20px;
}
.global-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  border-radius: 8px;
  max-width: 150px;
}

@media (max-width: 1400px) {
  .author-map-grid {
    grid-template-columns: 1fr !important;
    width: 100% !important;
    max-width: none !important;
    overflow-x: hidden;
    gap: 15px;
  }
  .map-card {
    transform: none !important;
    width: 100%;
    max-width: 100%;
  }
  .map-section {
    padding: 15px 10px;
  }
  .map-container {
    padding: 10px;
    width: 100%;
    box-sizing: border-box;
  }
  .map-section-title {
    font-size: 1.5rem;
    margin-bottom: 20px;
  }
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
}
</style>
