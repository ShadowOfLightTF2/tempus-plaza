<template>
  <div id="app" @click="closeDropdown">
    <div
      class="position-relative min-vh-100 w-100 overflow-hidden background-container"
    >
      <div class="container hero">
        <h1>Tempus Plaza</h1>
        <div class="search-container" @click.stop>
          <div class="search-box">
            <div class="search-icon-container">
              <svg
                class="search-icon"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
            </div>
            <input
              type="text"
              v-model="searchQuery"
              @input="onSearch"
              @focus="onSearch"
              placeholder="Search for players or maps..."
              class="search-input"
            />
            <button class="search-btn" @click="performSearch">Search</button>
          </div>
          <div
            class="search-results-dropdown"
            v-if="
              searchResults &&
              (searchResults.maps.length || searchResults.players.length)
            "
          >
            <div v-if="searchResults.maps.length">
              <h6>Maps</h6>
              <ul>
                <li
                  v-for="map in searchResults.maps"
                  :key="map.id"
                  @click="goToMap(map.id)"
                  v-html="sanitize(map.name || `Map ID: ${map.id}`)"
                ></li>
              </ul>
            </div>
            <div v-if="searchResults.players.length">
              <h6>Players</h6>
              <ul>
                <li
                  v-for="player in searchResults.players"
                  :key="player.id"
                  @click="goToPlayer(player.id)"
                  v-html="sanitize(player.name || `Player ID: ${player.id}`)"
                ></li>
              </ul>
            </div>
          </div>
        </div>
        <hr class="divider" style="width: 100%; margin-top: 100px" />
        <div class="section">
          <div class="container">
            <h2 class="section-title">Most popular maps</h2>
            <div class="grid">
              <a
                class="card"
                v-for="map in popularSoldierMaps"
                :key="map.id"
                :href="`/maps/${map.map_id}`"
                @click.prevent="goToMap(map.map_id)"
                :style="{
                  background: `
                    linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
                    radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                    url('/map-backgrounds/${map.name}.jpg') center/cover no-repeat
                  `,
                  backgroundBlendMode: 'multiply, normal, normal',
                  backgroundSize: 'cover, cover, cover',
                  backgroundPosition: 'center, center, center',
                }"
              >
                <h5 class="section-subtitle">Soldier</h5>
                <h3>{{ map.name }}</h3>
                <div class="compact-ratings-grid">
                  <div class="rating-section">
                    <div class="rating-label">Soldier</div>
                    <div class="rating-pills">
                      <span
                        class="rating-pill tier-color"
                        :class="'tier-' + map.soldier_tier"
                      >
                        T{{ map.soldier_tier }}
                      </span>
                      <span
                        class="rating-pill rating-color"
                        :class="'rating-' + map.soldier_rating"
                      >
                        R{{ map.soldier_rating }}
                      </span>
                    </div>
                  </div>
                  <div class="rating-section">
                    <div class="rating-label">Demoman</div>
                    <div class="rating-pills">
                      <span
                        class="rating-pill tier-color"
                        :class="'tier-' + map.demoman_tier"
                      >
                        T{{ map.demoman_tier }}
                      </span>
                      <span
                        class="rating-pill rating-color"
                        :class="'rating-' + map.demoman_rating"
                      >
                        R{{ map.demoman_rating }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="completion-count">
                  <i class="bi bi-check-circle me-1"></i>
                  {{ map.run_count }}
                  runs recently
                </div>
              </a>
            </div>
            <hr class="divider" style="width: 100%" />
            <div class="grid">
              <a
                class="card"
                v-for="map in popularDemomanMaps"
                :key="map.id"
                :href="`/maps/${map.map_id}`"
                @click.prevent="goToMap(map.map_id)"
                :style="{
                  background: `
                    linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
                    radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                    url('/map-backgrounds/${map.name}.jpg') center/cover no-repeat
                  `,
                  backgroundBlendMode: 'multiply, normal, normal',
                  backgroundSize: 'cover, cover, cover',
                  backgroundPosition: 'center, center, center',
                }"
              >
                <h5 class="section-subtitle">Demoman</h5>
                <h3>{{ map.name }}</h3>
                <div class="compact-ratings-grid">
                  <div class="rating-section">
                    <div class="rating-label">Demoman</div>
                    <div class="rating-pills">
                      <span
                        class="rating-pill tier-color"
                        :class="'tier-' + map.demoman_tier"
                      >
                        T{{ map.demoman_tier }}
                      </span>
                      <span
                        class="rating-pill rating-color"
                        :class="'rating-' + map.demoman_rating"
                      >
                        R{{ map.demoman_rating }}
                      </span>
                    </div>
                  </div>
                  <div class="rating-section">
                    <div class="rating-label">Soldier</div>
                    <div class="rating-pills">
                      <span
                        class="rating-pill tier-color"
                        :class="'tier-' + map.soldier_tier"
                      >
                        T{{ map.soldier_tier }}
                      </span>
                      <span
                        class="rating-pill rating-color"
                        :class="'rating-' + map.soldier_rating"
                      >
                        R{{ map.soldier_rating }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="completion-count">
                  <i class="bi bi-check-circle me-1"></i>
                  {{ map.run_count }}
                  runs recently
                </div>
              </a>
            </div>
          </div>
        </div>
        <hr class="divider" style="width: 100%" />
        <div class="section">
          <div class="container">
            <h2 class="section-title">TF2RJweekly uploads</h2>
            <div class="grid">
              <div
                class="video-card youtube-card"
                v-for="video in tf2rjweeklyVideos"
                :key="video.id"
                @click="openYoutubeVideo(video.youtube_id)"
              >
                <div class="youtube-thumbnail">
                  <img :src="video.thumbnail_url" alt="Video Thumbnail" />
                </div>
                <div class="video-title">{{ video.title }}</div>
                <div class="video-meta">
                  {{ formatDate(video.published_on) }}
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
import { useHead } from "@vueuse/head";
import debounce from "debounce";
import DOMPurify from "dompurify";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Home",
  setup() {
    useHead({
      title: "Tempus Plaza | Home",
    });
  },
  data() {
    return {
      searchQuery: "",
      searchResults: null,
      popularSoldierMaps: [],
      popularDemomanMaps: [],
      tf2rjweeklyVideos: [],
    };
  },
  methods: {
    closeDropdown() {
      this.searchResults = null;
    },
    goToMap(mapId) {
      this.$router.push({ name: "MapPage", params: { mapId } });
      this.searchResults = null;
    },
    goToPlayer(playerId) {
      this.$router.push({ name: "PlayerPage", params: { playerId } });
      this.searchResults = null;
    },
    async fetchSearchResults() {
      if (this.searchQuery.trim()) {
        try {
          const response = await fetch(`${API_BASE_URL}/search`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ query: this.searchQuery }),
          });

          if (!response.ok) throw new Error("Failed to fetch search results");
          const data = await response.json();

          if (data.players && data.players.length > 20)
            data.players = data.players.slice(0, 20);
          if (data.maps && data.maps.length > 5)
            data.maps = data.maps.slice(0, 5);

          this.searchResults = data;
        } catch (error) {
          console.error("Error fetching search results:", error);
        }
      } else {
        this.searchResults = null;
      }
    },
    async fetchPopularMaps() {
      try {
        const response = await fetch(`${API_BASE_URL}/maps/get-popular-maps`);
        if (!response.ok) throw new Error("Failed to fetch popular maps");
        const data = await response.json();
        this.popularSoldierMaps = data
          .filter((map) => map.class_type === "soldier")
          .sort((a, b) => b.run_count - a.run_count);

        this.popularDemomanMaps = data
          .filter((map) => map.class_type === "demoman")
          .sort((a, b) => b.run_count - a.run_count);
      } catch (error) {
        console.error("Error fetching popular maps:", error);
      }
    },
    async fetchTF2RJWeeklyVideos() {
      try {
        const response = await fetch(`${API_BASE_URL}/news/get-videos`);
        if (!response.ok) throw new Error("Failed to fetch TF2RJWeekly videos");
        const data = await response.json();
        this.tf2rjweeklyVideos = data;
      } catch (error) {
        console.error("Error fetching TF2RJWeekly videos:", error);
      }
    },
    onSearch() {
      this.debouncedSearch();
    },
    sanitize(html) {
      return DOMPurify.sanitize(html);
    },
    formatDate(dateString) {
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    openYoutubeVideo(youtubeId) {
      if (youtubeId) {
        window.open(`https://www.youtube.com/watch?v=${youtubeId}`, "_blank");
      }
    },
  },
  created() {
    this.debouncedSearch = debounce(this.fetchSearchResults, 500);
    this.fetchPopularMaps();
    this.fetchTF2RJWeeklyVideos();
  },
  watch: {
    searchQuery() {
      this.debouncedSearch();
    },
  },
};
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.hero {
  padding: 80px 0;
  text-align: center;
  position: relative;
}

.hero h1 {
  font-size: 4rem;
  font-weight: 800;
  margin-bottom: 20px;
  background: linear-gradient(135deg, #ffffff 0%, var(--color-primary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero p {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 40px;
}

.search-container {
  margin: 40px 0;
  position: relative;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.search-box {
  display: flex;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 25px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.search-box:hover,
.search-box:focus-within {
  border-color: var(--color-primary);
  box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
  transform: translateY(-2px);
}
.search-icon-container {
  display: flex;
  align-items: center;
  justify-content: center;
  padding-left: 15px;
}

.search-icon {
  width: 20px;
  height: 20px;
  color: rgba(255, 255, 255, 0.5);
}

.search-input {
  flex: 1;
  padding: 18px 10px;
  background: transparent;
  border: none;
  color: #ffffff;
  font-size: 16px;
  outline: none;
}

.search-input::placeholder {
  color: rgba(255, 255, 255, 0.5);
}
.search-results-dropdown {
  position: absolute;
  background: var(--color-box);
  border: 1px solid rgba(68, 68, 68, 0.3);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  min-width: 500px;
  max-height: 500px;
  overflow-y: auto;
  z-index: 1000;
  margin-top: 8px;
}

.search-results-dropdown ul {
  list-style: none;
  padding: 8px;
  margin: 0;
}

.search-results-dropdown li {
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 4px;
  background: var(--color-box);
  color: #ffffff;
  cursor: pointer;
  font-size: 18px;
  font-weight: bold;
  transition: all 0.2s ease;
  display: flex;
  align-items: left;
}

.search-results-dropdown li:hover {
  background: rgba(74, 111, 165, 0.8);
  transform: translateX(4px);
}

.search-results-dropdown li:last-child {
  margin-bottom: 0;
}

.search-results-dropdown h6 {
  display: flex;
  align-items: left;
  margin: 12px 16px 8px;
  font-size: 20px;
  font-weight: bold;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.search-btn {
  padding: 18px 25px;
  background: linear-gradient(
    135deg,
    var(--color-primary) 0%,
    var(--color-primary-dark) 100%
  );
  border: none;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
}

.divider {
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

.section {
  padding: 50px 0;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 50px;
  background: linear-gradient(135deg, #ffffff 0%, var(--color-primary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.section-subtitle {
  font-size: 2rem;
  font-weight: 700;
  text-align: center;
  color: #ffffff;
  margin-bottom: 50px;
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

.card,
.video-card {
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
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: scale(1.05);
  box-shadow: 0 0 40px rgba(102, 126, 234, 0.6);
  cursor: pointer;
}

.video-card:hover {
  transform: translateY(-10px) translateX(-10px) rotate(2deg);
  box-shadow: 0 0 40px rgba(102, 126, 234, 0.6);
  cursor: pointer;
}

.card h3 {
  font-size: 1.5rem;
  margin-bottom: 15px;
  color: #ffffff;
}

.card p {
  color: var(--color-text);
  line-height: 1.6;
  margin-bottom: 20px;
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
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.8rem;
  color: var(--color-dark);
  text-shadow: none;
  min-width: 40px;
  text-align: center;
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
  background: rgba(110, 208, 246, 0.5); /* Cold Blue */
  color: var(--color-text);
}
.tier-color.tier-2 {
  background: rgba(86, 179, 233, 0.5); /* Sky Blue */
  color: var(--color-text);
}
.tier-color.tier-3 {
  background: rgba(69, 184, 173, 0.5); /* Teal */
  color: var(--color-text);
}
.tier-color.tier-4 {
  background: rgba(101, 193, 139, 0.5); /* Mint */
  color: var(--color-text);
}
.tier-color.tier-5 {
  background: rgba(163, 217, 119, 0.5); /* Lime Green */
  color: var(--color-text);
}
.tier-color.tier-6 {
  background: rgba(243, 230, 131, 0.5); /* Yellow */
  color: var(--color-text);
}
.tier-color.tier-7 {
  background: rgba(246, 194, 103, 0.5); /* Orange-Yellow */
  color: var(--color-text);
}
.tier-color.tier-8 {
  background: rgba(240, 141, 91, 0.5); /* Orange */
  color: var(--color-text);
}
.tier-color.tier-9 {
  background: rgba(230, 105, 94, 0.5); /* Coral */
  color: var(--color-text);
}
.tier-color.tier-10 {
  background: rgba(214, 69, 69, 0.5); /* Red */
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

.youtube-card {
  display: flex;
  flex-direction: column;
}

.youtube-thumbnail {
  width: 100%;
  height: 180px;
  border-radius: 12px;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2rem;
  position: relative;
  overflow: hidden;
}
.youtube-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
}
.video-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 8px;
  color: #ffffff;
}

.video-meta {
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
}

@media (max-width: 768px) {
  .hero h1 {
    font-size: 2.5rem;
  }

  .nav {
    display: none;
  }

  .grid {
    grid-template-columns: 1fr;
  }

  .section-title {
    font-size: 2rem;
  }
  .search-results-dropdown {
    min-width: 150px;
    max-width: 300px;
  }

  .card {
    padding: 8px;
  }

  .card-image {
    height: 80px;
  }

  .card h3 {
    font-size: 1rem;
  }

  .completion-count {
    font-size: 0.7rem;
  }
}
</style>
