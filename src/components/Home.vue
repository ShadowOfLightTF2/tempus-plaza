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
                  :to="{
                    name: 'PlayerPage',
                    params: { playerId: player.id },
                  }"
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
        <hr class="divider" style="width: 100%" />
        <div class="section">
          <div class="container">
            <h2 class="section-title">Most points gained</h2>
            <div class="period-selector">
              <button
                v-for="period in periods"
                :key="period.value"
                @click="selectedPeriod = period.value"
                :class="[
                  'period-btn',
                  { active: selectedPeriod === period.value },
                ]"
              >
                {{ period.label }}
              </button>
            </div>
            <div class="top-players-grid">
              <div class="class-section">
                <h5 class="section-subtitle">Soldier</h5>
                <div class="players-list">
                  <SmartLink
                    v-for="(player, index) in filteredTopSoldiers"
                    :key="player.id"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: player.player_id },
                    }"
                    class="player-card"
                  >
                    <div class="player-rank">#{{ index + 1 }}</div>
                    <img
                      :src="player.steam_avatar"
                      alt="Avatar"
                      class="player-avatar"
                    />
                    <div class="player-info">
                      <div class="player-name">{{ player.player_name }}</div>
                      <div class="country">
                        <img
                          :src="getFlagImageUrl(player.country_code)"
                          alt="flag"
                          class="flag-icon"
                          @error="handleImageError"
                        />
                        {{ player.country }}
                      </div>
                      <div class="points-gained">
                        <span class="points-value"
                          >+{{ player.points_gained }}</span
                        >
                        <span class="points-label">points gained</span>
                      </div>
                    </div>
                  </SmartLink>
                </div>
              </div>
              <div class="class-section">
                <h5 class="section-subtitle">Demoman</h5>
                <div class="players-list">
                  <SmartLink
                    v-for="(player, index) in filteredTopDemomen"
                    :key="player.id"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: player.player_id },
                    }"
                    class="player-card"
                  >
                    <div class="player-rank">#{{ index + 1 }}</div>
                    <img
                      :src="player.steam_avatar"
                      alt="Avatar"
                      class="player-avatar"
                    />
                    <div class="player-info">
                      <div class="player-name">{{ player.player_name }}</div>
                      <div class="country">
                        <img
                          :src="getFlagImageUrl(player.country_code)"
                          alt="flag"
                          class="flag-icon"
                          @error="handleImageError"
                        />
                        {{ player.country }}
                      </div>
                      <div class="points-gained">
                        <span class="points-value"
                          >+{{ player.points_gained }}</span
                        >
                        <span class="points-label">points gained</span>
                      </div>
                    </div>
                  </SmartLink>
                </div>
              </div>
            </div>
          </div>
        </div>
        <hr class="divider" style="width: 100%" />
        <div class="section">
          <div class="container">
            <h2 class="section-title">Latest tier/rating changes</h2>
            <h5 class="section-title-subtitle">(last 10 changes)</h5>
            <div
              class="tier-carousel-container"
              v-if="dataLoaded && tierRatingChanges.length > 0"
            >
              <div
                class="tier-carousel-wrapper"
                @touchstart="handleTouchStart"
                @touchend="handleTouchEnd"
              >
                <div
                  class="tier-carousel-track"
                  :style="{
                    transform: carouselTransform,
                  }"
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
                      (index === currentSlide - 1 ||
                        index === currentSlide + 3) &&
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
                      :to="{
                        name: 'MapPage',
                        params: { mapId: change.map_id },
                      }"
                      :style="{
                        background: `
              linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.6) 50%, rgba(0,0,0,0.6) 100%),
              radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
              url('/map-backgrounds/medium/${change.name}.jpg') center/cover no-repeat
            `,
                        backgroundBlendMode: 'multiply, normal, normal',
                        backgroundSize: 'cover, cover, cover',
                        backgroundPosition: 'center, center, center',
                      }"
                    >
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
                            >
                              T{{ change.old_soldier_tier }}
                            </span>
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
                            >
                              T{{ change.new_soldier_tier }}
                            </span>
                          </div>
                          <div class="rating-change-pills">
                            <span
                              class="rating-pill rating-color"
                              :class="'rating-' + change.old_soldier_rating"
                            >
                              R{{ change.old_soldier_rating }}
                            </span>
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
                            >
                              R{{ change.new_soldier_rating }}
                            </span>
                          </div>
                        </div>
                        <div class="rating-section">
                          <div class="rating-label">Demoman</div>
                          <div class="rating-change-pills">
                            <span
                              class="rating-pill tier-color"
                              :class="'tier-' + change.old_demoman_tier"
                            >
                              T{{ change.old_demoman_tier }}
                            </span>
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
                            >
                              T{{ change.new_demoman_tier }}
                            </span>
                          </div>
                          <div class="rating-change-pills">
                            <span
                              class="rating-pill rating-color"
                              :class="'rating-' + change.old_demoman_rating"
                            >
                              R{{ change.old_demoman_rating }}
                            </span>
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
                            >
                              R{{ change.new_demoman_rating }}
                            </span>
                          </div>
                        </div>
                      </div>
                      <div class="completion-count">
                        <i class="bi bi-clock me-1"></i>
                        {{ formatDate(change.changed_at) }}
                      </div>
                    </SmartLink>
                    <div
                      v-else-if="!isMobileView"
                      class="card"
                      :style="{
                        background: `
                    linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
              radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
              url('/map-backgrounds/medium/${change.name}.jpg') center/cover no-repeat
            `,
                        backgroundBlendMode: 'multiply, normal, normal',
                        backgroundSize: 'cover, cover, cover',
                        backgroundPosition: 'center, center, center',
                      }"
                    >
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
                            >
                              T{{ change.old_soldier_tier }}
                            </span>
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
                            >
                              T{{ change.new_soldier_tier }}
                            </span>
                          </div>
                          <div class="rating-change-pills">
                            <span
                              class="rating-pill rating-color"
                              :class="'rating-' + change.old_soldier_rating"
                            >
                              R{{ change.old_soldier_rating }}
                            </span>
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
                            >
                              R{{ change.new_soldier_rating }}
                            </span>
                          </div>
                        </div>
                        <div class="rating-section">
                          <div class="rating-label">Demoman</div>
                          <div class="rating-change-pills">
                            <span
                              class="rating-pill tier-color"
                              :class="'tier-' + change.old_demoman_tier"
                            >
                              T{{ change.old_demoman_tier }}
                            </span>
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
                            >
                              T{{ change.new_demoman_tier }}
                            </span>
                          </div>
                          <div class="rating-change-pills">
                            <span
                              class="rating-pill rating-color"
                              :class="'rating-' + change.old_demoman_rating"
                            >
                              R{{ change.old_demoman_rating }}
                            </span>
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
                            >
                              R{{ change.new_demoman_rating }}
                            </span>
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
            <div
              v-else-if="!dataLoaded"
              class="loading-container"
              style="padding: 60px 0"
            >
              <div class="loading-spinner"></div>
              <span>Loading tier changes...</span>
            </div>
          </div>
        </div>
        <hr class="divider" style="width: 100%" />
        <div class="section">
          <div class="container">
            <h2 class="section-title">Most popular maps</h2>
            <h5 class="section-title-subtitle">(past 3 days)</h5>
            <div class="grid">
              <SmartLink
                class="card"
                v-for="map in popularSoldierMaps"
                :key="map.id"
                :to="{ name: 'MapPage', params: { mapId: map.map_id } }"
                :style="{
                  background: `
                    linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
                    radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                    url('/map-backgrounds/medium/${map.name}.jpg') center/cover no-repeat
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
                  {{ map.run_count }} runs recently
                </div>
              </SmartLink>
            </div>
            <hr class="divider" style="width: 100%" />
            <div class="grid">
              <SmartLink
                class="card"
                v-for="map in popularDemomanMaps"
                :key="map.id"
                :to="{ name: 'MapPage', params: { mapId: map.map_id } }"
                :style="{
                  background: `
                    linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
                    radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                    url('/map-backgrounds/medium/${map.name}.jpg') center/cover no-repeat
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
                  {{ map.run_count }} runs recently
                </div>
              </SmartLink>
            </div>
          </div>
        </div>
        <hr class="divider" style="width: 100%" />
        <div class="section">
          <div class="container">
            <h2 class="section-title" style="margin-bottom: 50px">
              TF2RJweekly uploads
            </h2>
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
import { ref } from "vue";
import DOMPurify from "dompurify";
import axios from "axios";

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
      countdownInterval: null,
      loadingRecords: false,
      playerData: {},
      searchQuery: "",
      searchResults: null,
      loadingMaps: false,
      loadingPlayers: false,
      debounceTimer: null,
      popularSoldierMaps: [],
      popularDemomanMaps: [],
      tf2rjweeklyVideos: [],
      topSoldiers: [],
      topDemomen: [],
      selectedPeriod: "3day",
      periods: [
        { value: "3day", label: "Past 3 Days" },
        { value: "7day", label: "Past 7 Days" },
        { value: "30day", label: "Past 30 Days" },
      ],
      isMobileView: false,
      tierRatingChanges: [],
      dataLoaded: false,
      currentSlide: 0,
      touchStartX: 0,
      touchEndX: 0,
    };
  },
  computed: {
    filteredTopSoldiers() {
      return this.topSoldiers
        .filter((player) => player.period_type === this.selectedPeriod)
        .sort((a, b) => b.points_gained - a.points_gained)
        .slice(0, 3);
    },
    filteredTopDemomen() {
      return this.topDemomen
        .filter((player) => player.period_type === this.selectedPeriod)
        .sort((a, b) => b.points_gained - a.points_gained)
        .slice(0, 3);
    },
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
      return `translateX(-${this.validCurrentSlide * (100 / 3)}%)`;
    },
    cleanMapName() {
      return (mapName) => {
        return mapName.replace(/^jump_/, "").replace(/_[a-zA-Z0-9_]*$/, "");
      };
    },
    getClassIcon() {
      return (playerClass) => {
        return `/icons/${playerClass}.png`;
      };
    },
    getPlayerAvatar() {
      return (playerId) => {
        return this.playerData[playerId]?.steam_avatar || "";
      };
    },
    getPlayerCountryCode() {
      return (playerId) => {
        const tournamentPlayer = this.asiaTeamPlayers.find(
          (p) => p.id === playerId,
        );
        if (tournamentPlayer && tournamentPlayer.countryCode) {
          return tournamentPlayer.countryCode;
        }
        return this.playerData[playerId]?.country_code || "us";
      };
    },
    getPlayerCountry() {
      return (playerId) => {
        const tournamentPlayer = this.asiaTeamPlayers.find(
          (p) => p.id === playerId,
        );
        if (tournamentPlayer && tournamentPlayer.country) {
          return tournamentPlayer.country;
        }
        return this.playerData[playerId]?.country || "Unknown";
      };
    },
    getPlayerRankForClass() {
      return (playerId, playerClass) => {
        const playerData = this.playerData[playerId];
        if (!playerData) return "N/A";

        return playerClass === "soldier"
          ? playerData.soldier_rank || "N/A"
          : playerData.demoman_rank || "N/A";
      };
    },
    getPlayerSoldierRank() {
      return (playerId) => {
        return this.playerData[playerId]?.soldier_rank || "N/A";
      };
    },
    getPlayerDemoRank() {
      return (playerId) => {
        return this.playerData[playerId]?.demoman_rank || "N/A";
      };
    },
    getFlagImageUrl() {
      return (countryCode) => {
        const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
          ? countryCode.toLowerCase()
          : "unknown";
        return `https://flagcdn.com/24x18/${validCode}.png`;
      };
    },
    sanitize() {
      return (text) => {
        return DOMPurify.sanitize(text);
      };
    },
    formatDate() {
      return (dateString) => {
        const options = { year: "numeric", month: "short", day: "numeric" };
        return new Date(dateString).toLocaleDateString(undefined, options);
      };
    },
  },
  methods: {
    checkMobileView() {
      this.isMobileView = window.innerWidth <= 768;
    },
    handleCarouselClick(index, event, mapId) {
      if (index === this.currentSlide - 1 || index === this.currentSlide + 3) {
        this.goToSlide(index);
      } else if (index >= this.currentSlide && index <= this.currentSlide + 2) {
        this.$router.push({ name: "MapPage", params: { mapId: mapId } });
      }
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
          // Swiped left - go to next
          if (this.currentSlide < this.tierRatingChanges.length - 1) {
            this.currentSlide++;
          }
        } else {
          // Swiped right - go to previous
          if (this.currentSlide > 0) {
            this.currentSlide--;
          }
        }
      }
    },
    formatTime(seconds) {
      const mins = Math.floor(seconds / 60);
      const secs = (seconds % 60).toFixed(3);
      return `${mins}:${secs.padStart(6, "0")}`;
    },
    async fetchPlayerData(playerId) {
      try {
        const response = await axios.get(`${API_BASE_URL}/players/${playerId}`);
        const data = response.data;
        const playerInfo = data[0];

        const ranksResponse = await axios.get(
          `${API_BASE_URL}/players/${playerId}/ranks`,
        );
        const ranksData = ranksResponse.data;
        playerInfo.soldier_rank = ranksData[0].soldier_rank;
        playerInfo.demoman_rank = ranksData[0].demoman_rank;
        this.playerData[playerId] = playerInfo;
        //console.log("Player data:", playerInfo);
        return playerInfo;
      } catch (error) {
        console.error("Error fetching player data:", error);
        return this.playerData[playerId];
      }
    },
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    closeDropdown() {
      this.searchResults = null;
      this.loadingMaps = false;
      this.loadingPlayers = false;
    },
    async fetchTierRatingHistory() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/tier-rating-history`,
        );
        const data = response.data;
        this.tierRatingChanges = data;
        this.dataLoaded = true;
        this.currentSlide = 0;
      } catch (error) {
        console.error("Error fetching tier rating history:", error);
        this.tierRatingChanges = [];
        this.dataLoaded = false;
      }
    },
    async fetchTopPlayers() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/get-top-gainers`,
        );
        const data = response.data;

        this.topSoldiers = data.filter(
          (player) => player.class_type === "soldier",
        );
        this.topDemomen = data.filter(
          (player) => player.class_type === "demoman",
        );
      } catch (error) {
        console.error("Error fetching top players:", error);
      }
    },
    async fetchPopularMaps() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/get-popular-maps`,
        );
        const data = response.data;
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
        const response = await axios.get(`${API_BASE_URL}/news/get-videos`);
        const data = response.data;
        this.tf2rjweeklyVideos = data;
      } catch (error) {
        console.error("Error fetching TF2RJWeekly videos:", error);
      }
    },
    openYoutubeVideo(youtubeId) {
      if (youtubeId) {
        window.open(`https://www.youtube.com/watch?v=${youtubeId}`, "_blank");
      }
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
  mounted() {
    this.fetchTierRatingHistory();
    this.fetchTopPlayers();
    this.fetchPopularMaps();
    this.fetchTF2RJWeeklyVideos();
    this.checkMobileView();
    window.addEventListener("resize", this.checkMobileView);
  },
  beforeDestroy() {
    if (this.updateInterval) {
      clearInterval(this.updateInterval);
      this.updateInterval = null;
    }
    if (this.countdownInterval) {
      clearInterval(this.countdownInterval);
      this.countdownInterval = null;
    }
    if (this.debounceTimer) {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = null;
    }
    window.removeEventListener("resize", this.checkMobileView);
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
  font-size: 4.5rem;
  font-weight: 900;
  margin-bottom: 20px;
  background: linear-gradient(
    135deg,
    #ffffff 0%,
    var(--color-primary) 50%,
    #ffffff 100%
  );
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-shadow: 0 0 80px rgba(102, 126, 234, 0.5);
  letter-spacing: -2px;
}

.search-container {
  margin: 50px 0;
  position: relative;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
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

.section {
  padding: 25px 0;
}

.section-title {
  font-size: 3rem;
  font-weight: 800;
  text-align: center;
  background: linear-gradient(
    135deg,
    #ffffff 0%,
    var(--color-primary) 50%,
    #ffffff 100%
  );
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -1px;
  margin-bottom: 10px;
  text-shadow: 0 0 60px rgba(102, 126, 234, 0.3);
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
.top-players-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  max-width: 1000px;
  margin: 0 auto;
}

.class-section {
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.05) 0%,
    rgba(255, 255, 255, 0.02) 100%
  );
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.section-subtitle {
  font-size: 1.8rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.players-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.player-card {
  display: flex;
  align-items: center;
  padding: 24px;
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.1) 0%,
    rgba(255, 255, 255, 0.05) 100%
  );
  border-radius: 20px;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  text-decoration: none;
  border: 1px solid rgba(255, 255, 255, 0.08);
  position: relative;
  overflow: hidden;
  max-width: 418px;
}

.player-card::after {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.1),
    transparent
  );
  transition: left 0.6s ease;
}

.player-card:hover::after {
  left: 100%;
}

.player-card:hover {
  transform: scale(1.02);
  box-shadow: 0 10px 40px rgba(102, 126, 234, 0.5),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
  border-color: rgba(102, 126, 234, 0.4);
  cursor: pointer;
}

.player-card:nth-child(1) {
  background: linear-gradient(
    135deg,
    rgba(255, 215, 0, 0.15) 0%,
    rgba(255, 215, 0, 0.05) 100%
  );
}

.player-card:nth-child(2) {
  background: linear-gradient(
    135deg,
    rgba(192, 192, 192, 0.15) 0%,
    rgba(192, 192, 192, 0.05) 100%
  );
}

.player-card:nth-child(3) {
  background: linear-gradient(
    135deg,
    rgba(205, 127, 50, 0.15) 0%,
    rgba(205, 127, 50, 0.05) 100%
  );
}

.player-rank {
  font-size: 2rem;
  font-weight: 800;
  color: var(--color-primary);
  margin-right: 15px;
  min-width: 50px;
  text-align: center;
}

.player-card:nth-child(1) .player-rank {
  color: #ffd700;
  text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
}

.player-card:nth-child(2) .player-rank {
  color: #c0c0c0;
  text-shadow: 0 0 10px rgba(192, 192, 192, 0.5);
}

.player-card:nth-child(3) .player-rank {
  color: #cd7f32;
  text-shadow: 0 0 10px rgba(205, 127, 50, 0.5);
}

.player-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  align-items: flex-start;
}

.player-avatar {
  width: 75px;
  height: 75px;
  border-radius: 50%;
  margin-right: 25px;
  border: 3px solid var(--color-primary);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.4);
  transition: all 0.3s ease;
}

.player-card:hover .player-avatar {
  transform: scale(1.1);
  box-shadow: 0 0 30px rgba(102, 126, 234, 0.8);
}

.player-name {
  font-size: 1.3rem;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 5px;
  align-self: flex-start;
  max-width: 210px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.country {
  display: flex;
  align-items: center;
  font-weight: bold;
  color: #d5d5d5;
  margin-bottom: 10px;
  align-self: flex-start;
}

.flag-icon {
  margin-right: 8px;
  width: 20px;
  height: auto;
}

.points-gained {
  display: flex;
  align-items: center;
  gap: 8px;
  align-self: flex-start;
}

.points-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-primary);
}

.points-label {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.6);
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
  text-decoration: none;
  border: 1px solid rgba(255, 255, 255, 0.2);
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
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
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
  .search-container {
    width: 95% !important;
  }
  .search-results-dropdown {
    width: 90vw;
    max-width: 300px;
    left: 50%;
    transform: translateX(-50%);
  }
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

  .top-players-grid {
    grid-template-columns: 1fr;
    gap: 30px;
  }

  .class-section {
    padding: 20px;
  }

  .class-title {
    font-size: 1.5rem;
  }

  .player-card {
    max-width: none;
    padding: 15px;
  }

  .player-rank {
    font-size: 1.5rem;
    margin-right: 15px;
    min-width: 40px;
  }

  .player-name {
    font-size: 1.1rem;
  }

  .points-value {
    font-size: 1rem;
  }
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

.rating-change-pills {
  display: flex;
  gap: 6px;
  align-items: center;
  justify-content: center;
}

.arrow-small {
  color: var(--color-primary);
  font-size: 1.1rem;
  font-weight: bold;
  opacity: 0.8;
}

.rating-pill.changed {
  border: 2px solid white;
  box-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
}

.tier-carousel-container {
  position: relative;
  max-width: 1400px;
  margin: 30px auto;
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
  transform: scale(0.85) rotateY(0deg);
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
  margin-top: 20px;
}

.indicator-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  cursor: pointer;
  transition: all 0.3s ease;
}

.indicator-dot.active {
  background: var(--color-primary);
  width: 24px;
  border-radius: 4px;
}

.indicator-dot:hover {
  background: rgba(255, 255, 255, 0.5);
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
}

.period-selector {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin: 25px 0 35px 0;
}

.period-btn {
  padding: 12px 24px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 25px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.period-btn:hover {
  background: rgba(255, 255, 255, 0.12);
  border-color: rgba(102, 126, 234, 0.5);
  color: #ffffff;
}

.period-btn.active {
  background: linear-gradient(
    135deg,
    var(--color-primary) 0%,
    var(--color-primary-dark) 100%
  );
  border-color: var(--color-primary);
  color: #ffffff;
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
}

@media (max-width: 768px) {
  .period-selector {
    flex-direction: column;
    gap: 10px;
    padding: 0 20px;
  }

  .period-btn {
    width: 100%;
  }
}
</style>
