<template>
  <div id="app" @click="closeDropdown">
    <div
      class="position-relative min-vh-100 w-100 overflow-hidden background-container"
    >
      <div class="container hero">
        <h1>Tempus Plaza</h1>
        <div class="search-container" @click.stop>
          <div class="search-box" :class="{ 'is-focused': isInputFocused }">
            <div class="search-icon-container">
              <svg
                class="search-icon"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
              >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
            </div>
            <input
              type="text"
              v-model="searchQuery"
              @input="onSearch"
              @focus="
                isInputFocused = true;
                onSearch();
              "
              @blur="isInputFocused = false"
              placeholder="Search players or maps..."
              class="search-input"
            />
            <button
              v-if="searchQuery"
              class="clear-btn"
              @mousedown.prevent="clearSearch"
              aria-label="Clear search"
            >
              <svg
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
              >
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
          </div>
          <Transition name="dropdown">
            <div
              class="search-results-dropdown"
              v-if="
                searchQuery.trim() &&
                (loadingMaps || loadingPlayers || searchResults)
              "
            >
              <div class="search-section">
                <div class="section-label">
                  <svg
                    width="12"
                    height="12"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <polygon
                      points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"
                    ></polygon>
                  </svg>
                  Maps
                </div>
                <div v-if="loadingMaps" class="loading-row">
                  <span class="skeleton skeleton-icon"></span>
                  <span class="skeleton skeleton-text"></span>
                </div>
                <ul v-else-if="searchResults && searchResults.maps.length">
                  <HoverPreview
                    v-for="map in searchResults.maps"
                    :key="map.id"
                    :mapName="map.name"
                    style="display: block"
                  >
                    <SmartLink
                      :to="{ name: 'MapPage', params: { mapId: map.id } }"
                      tag="li"
                      class="result-item map-item"
                    >
                      <span class="item-name">{{
                        map.name || `Map ID: ${map.id}`
                      }}</span>
                    </SmartLink>
                  </HoverPreview>
                </ul>
                <div
                  v-else-if="!loadingMaps && searchResults"
                  class="empty-state"
                >
                  No maps found
                </div>
              </div>
              <div class="section-divider"></div>
              <div class="search-section">
                <div class="section-label">
                  <svg
                    width="12"
                    height="12"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  Players
                </div>
                <div v-if="loadingPlayers" class="loading-rows">
                  <div v-for="n in 3" :key="n" class="loading-row">
                    <span class="skeleton skeleton-avatar"></span>
                    <span class="skeleton skeleton-text"></span>
                  </div>
                </div>
                <ul v-else-if="searchResults && searchResults.players.length">
                  <SmartLink
                    v-for="player in searchResults.players"
                    :key="player.id"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: player.id },
                    }"
                    tag="li"
                    class="result-item player-item"
                  >
                    <div class="player-avatar-wrapper">
                      <img
                        v-if="player.steam_avatar"
                        :src="player.steam_avatar"
                        :alt="player.name"
                        class="player-avatar"
                        @error="handleAvatarError"
                      />
                      <div v-else class="player-avatar-fallback">
                        {{ (player.name || "?")[0].toUpperCase() }}
                      </div>
                    </div>
                    <span class="item-name">{{
                      player.name || `Player ID: ${player.id}`
                    }}</span>
                  </SmartLink>
                </ul>
                <div
                  v-else-if="!loadingPlayers && searchResults"
                  class="empty-state"
                >
                  No players found
                </div>
              </div>
            </div>
          </Transition>
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
import axios from "axios";
import HoverPreview from "./HoverPreview.vue";
import TopPlayersSection from "./TopPlayersSection.vue";
import TierRatingChangesSection from "./TierRatingChangesSection.vue";
import PopularMapsSection from "./PopularMapsSection.vue";
import YoutubeSection from "./YoutubeSection.vue";
import NewestSection from "./NewestSection.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Home",
  components: {
    HoverPreview,
    NewestSection,
    TopPlayersSection,
    TierRatingChangesSection,
    PopularMapsSection,
    YoutubeSection,
  },
  setup() {
    useHead({ title: "Tempus Plaza" });
  },
  data() {
    return {
      searchQuery: "",
      searchResults: null,
      loadingMaps: false,
      loadingPlayers: false,
      debounceTimer: null,
      isInputFocused: false,
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
    clearSearch() {
      this.searchQuery = "";
      this.searchResults = null;
      this.loadingMaps = false;
      this.loadingPlayers = false;
    },
    handleAvatarError(e) {
      e.target.style.display = "none";
      e.target.nextElementSibling &&
        (e.target.nextElementSibling.style.display = "flex");
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
        .catch(() => {
          this.searchResults.maps = [];
        })
        .finally(() => {
          this.loadingMaps = false;
        });

      this.fetchPlayers(query)
        .then((results) => {
          this.searchResults.players = (results.players || []).slice(0, 20);
        })
        .catch(() => {
          this.searchResults.players = [];
        })
        .finally(() => {
          this.loadingPlayers = false;
        });
    },
    onSearch() {
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
      }, 400);
    },
  },
  beforeUnmount() {
    if (this.debounceTimer) {
      clearTimeout(this.debounceTimer);
    }
  },
  watch: {
    searchQuery() {
      this.onSearch();
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
  padding: 50px 0 100px;
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
  max-width: 560px;
}

.search-box {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 14px;
  transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
}
.search-box.is-focused,
.search-box:hover {
  border-color: rgba(102, 126, 234, 0.6);
  background: rgba(255, 255, 255, 0.09);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15),
    0 4px 24px rgba(0, 0, 0, 0.35);
}

.search-icon-container {
  padding: 0 12px 0 16px;
  display: flex;
  align-items: center;
  flex-shrink: 0;
}
.search-icon {
  color: rgba(255, 255, 255, 0.35);
}

.search-input {
  flex: 1;
  padding: 16px 0;
  background: transparent;
  border: none;
  color: #fff;
  font-size: 15px;
  outline: none;
  min-width: 0;
}
.search-input::placeholder {
  color: rgba(255, 255, 255, 0.35);
}

.clear-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0 14px;
  color: rgba(255, 255, 255, 0.35);
  display: flex;
  align-items: center;
  transition: color 0.15s;
  flex-shrink: 0;
}
.clear-btn:hover {
  color: rgba(255, 255, 255, 0.75);
}

.search-results-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  min-width: 420px;
  background: rgba(18, 20, 30, 0.95);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6),
    0 0 0 1px rgba(255, 255, 255, 0.04);
  overflow: hidden;
  z-index: 1000;
}

.dropdown-enter-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.dropdown-leave-active {
  transition: opacity 0.1s ease, transform 0.1s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(-6px);
}

.search-section {
  padding: 8px 0;
}

.section-label {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px 6px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.3);
}

.section-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.06);
  margin: 0 14px;
}

ul {
  list-style: none;
  margin: 0;
  padding: 4px 8px 8px;
}

.result-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.15s;
  text-decoration: none;
  color: #fff;
}
.result-item:hover {
  background: rgba(102, 126, 234, 0.15);
}
.result-item:active {
  background: rgba(102, 126, 234, 0.25);
}

.map-icon {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: rgba(255, 255, 255, 0.45);
}

.player-avatar-wrapper {
  position: relative;
  flex-shrink: 0;
}
.player-avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  object-fit: cover;
  display: block;
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.player-avatar-fallback {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: linear-gradient(
    135deg,
    rgba(102, 126, 234, 0.5),
    rgba(154, 176, 255, 0.3)
  );
  border: 1px solid rgba(102, 126, 234, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.8);
}

.item-name {
  font-size: 16px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.88);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.empty-state {
  padding: 10px 14px 14px;
  font-size: 13px;
  color: rgba(255, 255, 255, 0.25);
  font-style: italic;
}

.loading-rows {
  padding: 4px 8px 8px;
}
.loading-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
}

.skeleton {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 0%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 100%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 6px;
  display: block;
}
.skeleton-avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  flex-shrink: 0;
}
.skeleton-icon {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  flex-shrink: 0;
}
.skeleton-text {
  flex: 1;
  height: 14px;
  max-width: 160px;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

.search-results-dropdown :deep(.hover-preview-wrapper) {
  display: block;
  width: 100%;
}

@media (max-width: 768px) {
  .hero h1 {
    font-size: 2.5rem;
  }
  .search-container {
    width: 95%;
  }
  .search-results-dropdown {
    min-width: unset;
    width: 90vw;
    left: 50%;
    transform: translateX(-50%);
  }
  .dropdown-enter-from,
  .dropdown-leave-to {
    transform: translateX(-50%) translateY(-6px);
  }
}
</style>
