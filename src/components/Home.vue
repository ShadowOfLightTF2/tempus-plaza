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
              searchQuery.trim() &&
              (loadingMaps || loadingPlayers || searchResults)
            "
          >
            <div class="search-section">
              <h6>Maps</h6>
              <div v-if="loadingMaps" class="loading-container">
                <div class="loading-spinner"></div>
                <span>Loading maps...</span>
              </div>
              <ul v-else-if="searchResults && searchResults.maps.length">
                <SmartLink
                  v-for="map in searchResults.maps"
                  :key="map.id"
                  :to="{ name: 'MapPage', params: { mapId: map.id } }"
                  tag="li"
                  class="search-result-item"
                >
                  {{ map.name || `Map ID: ${map.id}` }}
                </SmartLink>
              </ul>
              <div v-else-if="!loadingMaps && searchResults" class="no-results">
                No maps found
              </div>
            </div>
            <div class="search-section">
              <h6>Players</h6>
              <div v-if="loadingPlayers" class="loading-container">
                <div class="loading-spinner"></div>
                <span>Loading players...</span>
              </div>
              <ul v-else-if="searchResults && searchResults.players.length">
                <SmartLink
                  v-for="player in searchResults.players"
                  :key="player.id"
                  :to="{ name: 'PlayerPage', params: { playerId: player.id } }"
                  tag="li"
                  class="search-result-item"
                >
                  {{ player.name || `Player ID: ${player.id}` }}
                </SmartLink>
              </ul>
              <div
                v-else-if="!loadingPlayers && searchResults"
                class="no-results"
              >
                No players found
              </div>
            </div>
          </div>
        </div>
        <NewestSection />
        <div>
          <TopPlayersSection />
          <PopularMapsSection />
          <TierRatingChangesSection @data-loaded="onTierDataLoaded" />
          <YoutubeSection @data-loaded="onYoutubeDataLoaded" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useHead } from "@vueuse/head";
import DOMPurify from "dompurify";
import axios from "axios";
import TopPlayersSection from "./TopPlayersSection.vue";
import TierRatingChangesSection from "./TierRatingChangesSection.vue";
import PopularMapsSection from "./PopularMapsSection.vue";
import YoutubeSection from "./YoutubeSection.vue";
import NewestSection from "./NewestSection.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Home",
  components: {
    NewestSection,
    TopPlayersSection,
    TierRatingChangesSection,
    PopularMapsSection,
    YoutubeSection,
  },
  setup() {
    useHead({ title: "Tempus Plaza | Home" });
  },
  data() {
    return {
      searchQuery: "",
      searchResults: null,
      loadingMaps: false,
      loadingPlayers: false,
      debounceTimer: null,
      latestTierChange: null,
      latestYoutubeVideo: null,
    };
  },
  methods: {
    onTierDataLoaded(latestChange) {
      this.latestTierChange = latestChange;
    },
    onYoutubeDataLoaded(latestVideo) {
      this.latestYoutubeVideo = latestVideo;
    },
    closeDropdown() {
      this.searchResults = null;
      this.loadingMaps = false;
      this.loadingPlayers = false;
    },
    async fetchMaps(query) {
      const response = await axios.post(`${API_BASE_URL}/search/maps`, {
        query,
      });
      return response.data;
    },
    async fetchPlayers(query) {
      const response = await axios.post(`${API_BASE_URL}/search/players`, {
        query,
      });
      return response.data;
    },
    async fetchSearchResults() {
      const query = this.searchQuery.trim();
      this.searchResults = { maps: [], players: [] };
      this.loadingMaps = true;
      this.loadingPlayers = true;

      this.fetchMaps(query)
        .then((results) => {
          this.searchResults.maps = (results.maps || []).slice(0, 5);
        })
        .catch((error) => {
          console.error("Error fetching maps:", error);
          this.searchResults.maps = [];
        })
        .finally(() => {
          this.loadingMaps = false;
        });

      this.fetchPlayers(query)
        .then((results) => {
          this.searchResults.players = (results.players || []).slice(0, 20);
        })
        .catch((error) => {
          console.error("Error fetching players:", error);
          this.searchResults.players = [];
        })
        .finally(() => {
          this.loadingPlayers = false;
        });
    },
    debouncedSearch() {
      clearTimeout(this.debounceTimer);
      if (!this.searchQuery.trim()) {
        this.searchResults = null;
        this.loadingMaps = false;
        this.loadingPlayers = false;
        return;
      }
      this.loadingMaps = true;
      this.loadingPlayers = true;
      this.debounceTimer = setTimeout(() => {
        this.fetchSearchResults();
      }, 500);
    },
  },
  beforeUnmount() {
    if (this.debounceTimer) {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = null;
    }
  },
  watch: {
    searchQuery() {
      this.debouncedSearch();
    },
  },
};
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.container.hero {
  max-width: 1600px;
}

.hero {
  padding: 50px 0 100px 0;
  text-align: center;
  position: relative;
}

.hero h1 {
  font-size: 5rem;
  font-weight: 900;
  margin-bottom: 50px;
  background: linear-gradient(
    160deg,
    #e8eeff 0%,
    #c5cfff 20%,
    var(--color-primary) 50%,
    #9ab0ff 75%,
    #ffffff 100%
  );
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-shadow: 0 0 80px rgba(102, 126, 234, 0.5);
  letter-spacing: -2px;
}

.search-container {
  margin: 20px auto;
  position: relative;
  max-width: 600px;
}

.search-box {
  display: flex;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 30px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.search-box:hover,
.search-box:focus-within {
  border-color: var(--color-primary);
  box-shadow: 0 15px 45px rgba(102, 126, 234, 0.4),
    0 0 60px rgba(102, 126, 234, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.15);
  transform: translateY(-3px);
  background: rgba(255, 255, 255, 0.12);
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
  padding: 20px 15px;
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
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
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
  padding: 20px 30px;
  background: linear-gradient(
    135deg,
    var(--color-primary) 0%,
    var(--color-primary-dark) 100%
  );
  border: none;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 700;
  font-size: 15px;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.search-btn:hover {
  background: linear-gradient(
    135deg,
    var(--color-primary-dark) 0%,
    var(--color-primary) 100%
  );
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.6);
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

.no-results {
  padding: 8px 12px;
  color: #999;
  font-size: 0.9rem;
  font-style: italic;
}

@media (max-width: 768px) {
  .search-container {
    width: 95% !important;
  }

  .search-results-dropdown {
    width: 90vw;
    max-width: 300px;
    left: 50%;
    transform: translateX(-50%);
    min-width: 150px;
  }

  .hero h1 {
    font-size: 2.5rem;
  }
}
</style>
