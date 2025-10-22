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
        <div class="tempus-cup-wrapper">
          <div class="tempus-cup-container">
            <div class="tempus-cup-header">
              <h3 class="cup-title">TEMPUS CUP 2</h3>
              <p class="cup-subtitle">Phase {{ currentPhase }} of 3</p>
              <p class="cup-dates">
                {{
                  new Date(
                    tempusCupPhases[currentPhase - 1].startDate
                  ).toLocaleDateString()
                }}
                -
                {{
                  new Date(
                    tempusCupPhases[currentPhase - 1].endDate
                  ).toLocaleDateString()
                }}
              </p>
            </div>
            <div class="phase-content">
              <!-- Soldier Section (Left) -->
              <div class="phase-section soldier-section">
                <div class="records-list" v-if="!loadingRecords">
                  <div class="records-header">
                    <img
                      src="/icons/soldier.png"
                      alt="Soldier"
                      class="class-icon-medium"
                    />
                    <span>Top 10 - Phase {{ selectedSoldierPhase }}</span>
                  </div>
                  <SmartLink
                    v-for="(record, index) in phaseRecords.soldier"
                    :key="record.id"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: record.player_id },
                    }"
                    class="record-item"
                    :class="{
                      'record-gold': index === 0,
                      'record-silver': index === 1,
                      'record-bronze': index === 2,
                    }"
                  >
                    <div class="record-rank">{{ index + 1 }}</div>
                    <img
                      :src="record.steam_avatar"
                      alt="Avatar"
                      class="record-avatar"
                    />
                    <div class="record-info">
                      <div class="record-player-name">
                        {{ record.name }}
                      </div>
                      <div class="record-time">
                        {{ formatTime(record.duration) }}
                      </div>
                    </div>
                  </SmartLink>
                </div>
                <div v-else class="loading-container">
                  <div class="loading-spinner"></div>
                  <span>Loading records...</span>
                </div>
              </div>

              <!-- Maps Section (Center) -->
              <div class="maps-center">
                <!-- Phase 1 Soldier Map -->
                <div
                  @click="isPhaseActive(1) && selectMap(1, 'soldier')"
                  class="cup-map-card soldier-map"
                  :class="{
                    'map-locked': !isPhaseActive(1),
                    'map-selected': selectedSoldierPhase === 1,
                  }"
                  :style="
                    isPhaseActive(1)
                      ? {
                          background: `
      linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
      radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
      url('/map-backgrounds/medium/${tempusCupPhases[0].soldierMap.name}.jpg') center/cover no-repeat
    `,
                          backgroundBlendMode: 'multiply, normal, normal',
                          backgroundSize: 'cover, cover, cover',
                          backgroundPosition: 'center, center, center',
                        }
                      : {}
                  "
                >
                  <div v-if="!isPhaseActive(1)" class="locked-overlay">
                    <div class="lock-icon">?</div>
                    <div class="lock-text">Phase 1</div>
                  </div>
                  <template v-else>
                    <h5 class="section-subtitle">Soldier</h5>
                    <h3>{{ tempusCupPhases[0].soldierMap.name }}</h3>
                    <div class="compact-ratings-grid">
                      <div class="rating-section">
                        <div class="rating-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="
                              'tier-' +
                              tempusCupPhases[0].soldierMap.soldier_tier
                            "
                          >
                            T{{ tempusCupPhases[0].soldierMap.soldier_tier }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>

                <!-- Phase 1 Demoman Map -->
                <div
                  @click="isPhaseActive(1) && selectMap(1, 'demoman')"
                  class="cup-map-card demoman-map"
                  :class="{
                    'map-locked': !isPhaseActive(1),
                    'map-selected': selectedDemomanPhase === 1,
                  }"
                  :style="
                    isPhaseActive(1)
                      ? {
                          background: `
      linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
      radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
      url('/map-backgrounds/medium/${tempusCupPhases[0].demomanMap.name}.jpg') center/cover no-repeat
    `,
                          backgroundBlendMode: 'multiply, normal, normal',
                          backgroundSize: 'cover, cover, cover',
                          backgroundPosition: 'center, center, center',
                        }
                      : {}
                  "
                >
                  <div v-if="!isPhaseActive(1)" class="locked-overlay">
                    <div class="lock-icon">?</div>
                    <div class="lock-text">Phase 1</div>
                  </div>
                  <template v-else>
                    <h5 class="section-subtitle">Demoman</h5>
                    <h3>{{ tempusCupPhases[0].demomanMap.name }}</h3>
                    <div class="compact-ratings-grid">
                      <div class="rating-section">
                        <div class="rating-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="
                              'tier-' +
                              tempusCupPhases[0].demomanMap.demoman_tier
                            "
                          >
                            T{{ tempusCupPhases[0].demomanMap.demoman_tier }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>

                <!-- Phase 2 Soldier Map -->
                <div
                  @click="isPhaseActive(2) && selectMap(2, 'soldier')"
                  class="cup-map-card soldier-map"
                  :class="{
                    'map-locked': !isPhaseActive(2),
                    'map-selected': selectedSoldierPhase === 2,
                  }"
                  :style="
                    isPhaseActive(2)
                      ? {
                          background: `
      linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
      radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
      url('/map-backgrounds/medium/${tempusCupPhases[1].soldierMap.name}.jpg') center/cover no-repeat
    `,
                          backgroundBlendMode: 'multiply, normal, normal',
                          backgroundSize: 'cover, cover, cover',
                          backgroundPosition: 'center, center, center',
                        }
                      : {}
                  "
                >
                  <div v-if="!isPhaseActive(2)" class="locked-overlay">
                    <div class="lock-icon">?</div>
                    <div class="lock-text">Phase 2</div>
                  </div>
                  <template v-else>
                    <h5 class="section-subtitle">Soldier</h5>
                    <h3>{{ tempusCupPhases[1].soldierMap.name }}</h3>
                    <div class="compact-ratings-grid">
                      <div class="rating-section">
                        <div class="rating-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="
                              'tier-' +
                              tempusCupPhases[1].soldierMap.soldier_tier
                            "
                          >
                            T{{ tempusCupPhases[1].soldierMap.soldier_tier }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>

                <!-- Phase 2 Demoman Map -->
                <div
                  @click="isPhaseActive(2) && selectMap(2, 'demoman')"
                  class="cup-map-card demoman-map"
                  :class="{
                    'map-locked': !isPhaseActive(2),
                    'map-selected': selectedDemomanPhase === 2,
                  }"
                  :style="
                    isPhaseActive(2)
                      ? {
                          background: `
      linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
      radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
      url('/map-backgrounds/medium/${tempusCupPhases[1].demomanMap.name}.jpg') center/cover no-repeat
    `,
                          backgroundBlendMode: 'multiply, normal, normal',
                          backgroundSize: 'cover, cover, cover',
                          backgroundPosition: 'center, center, center',
                        }
                      : {}
                  "
                >
                  <div v-if="!isPhaseActive(2)" class="locked-overlay">
                    <div class="lock-icon">?</div>
                    <div class="lock-text">Phase 2</div>
                  </div>
                  <template v-else>
                    <h5 class="section-subtitle">Demoman</h5>
                    <h3>{{ tempusCupPhases[1].demomanMap.name }}</h3>
                    <div class="compact-ratings-grid">
                      <div class="rating-section">
                        <div class="rating-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="
                              'tier-' +
                              tempusCupPhases[1].demomanMap.demoman_tier
                            "
                          >
                            T{{ tempusCupPhases[1].demomanMap.demoman_tier }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>

                <!-- Phase 3 Soldier Map -->
                <div
                  @click="isPhaseActive(3) && selectMap(3, 'soldier')"
                  class="cup-map-card soldier-map"
                  :class="{
                    'map-locked': !isPhaseActive(3),
                    'map-selected': selectedSoldierPhase === 3,
                  }"
                  :style="
                    isPhaseActive(3)
                      ? {
                          background: `
      linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
      radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
      url('/map-backgrounds/medium/${tempusCupPhases[2].soldierMap.name}.jpg') center/cover no-repeat
    `,
                          backgroundBlendMode: 'multiply, normal, normal',
                          backgroundSize: 'cover, cover, cover',
                          backgroundPosition: 'center, center, center',
                        }
                      : {}
                  "
                >
                  <div v-if="!isPhaseActive(3)" class="locked-overlay">
                    <div class="lock-icon">?</div>
                    <div class="lock-text">Phase 3</div>
                  </div>
                  <template v-else>
                    <h5 class="section-subtitle">Soldier</h5>
                    <h3>{{ tempusCupPhases[2].soldierMap.name }}</h3>
                    <div class="compact-ratings-grid">
                      <div class="rating-section">
                        <div class="rating-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="
                              'tier-' +
                              tempusCupPhases[2].soldierMap.soldier_tier
                            "
                          >
                            T{{ tempusCupPhases[2].soldierMap.soldier_tier }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>

                <!-- Phase 3 Demoman Map -->
                <div
                  @click="isPhaseActive(3) && selectMap(3, 'demoman')"
                  class="cup-map-card demoman-map"
                  :class="{
                    'map-locked': !isPhaseActive(3),
                    'map-selected': selectedDemomanPhase === 3,
                  }"
                  :style="
                    isPhaseActive(3)
                      ? {
                          background: `
      linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
      radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
      url('/map-backgrounds/medium/${tempusCupPhases[2].demomanMap.name}.jpg') center/cover no-repeat
    `,
                          backgroundBlendMode: 'multiply, normal, normal',
                          backgroundSize: 'cover, cover, cover',
                          backgroundPosition: 'center, center, center',
                        }
                      : {}
                  "
                >
                  <div v-if="!isPhaseActive(3)" class="locked-overlay">
                    <div class="lock-icon">?</div>
                    <div class="lock-text">Phase 3</div>
                  </div>
                  <template v-else>
                    <h5 class="section-subtitle">Demoman</h5>
                    <h3>{{ tempusCupPhases[2].demomanMap.name }}</h3>
                    <div class="compact-ratings-grid">
                      <div class="rating-section">
                        <div class="rating-pills">
                          <span
                            class="rating-pill tier-color"
                            :class="
                              'tier-' +
                              tempusCupPhases[2].demomanMap.demoman_tier
                            "
                          >
                            T{{ tempusCupPhases[2].demomanMap.demoman_tier }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
              </div>

              <!-- Demoman Section (Right) -->
              <div class="phase-section demoman-section">
                <div class="records-list" v-if="!loadingRecords">
                  <div class="records-header">
                    <img
                      src="/icons/demoman.png"
                      alt="Demoman"
                      class="class-icon-medium"
                    />
                    <span>Top 10 - Phase {{ selectedDemomanPhase }}</span>
                  </div>
                  <SmartLink
                    v-for="(record, index) in phaseRecords.demoman"
                    :key="record.id"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: record.player_id },
                    }"
                    class="record-item"
                    :class="{
                      'record-gold': index === 0,
                      'record-silver': index === 1,
                      'record-bronze': index === 2,
                    }"
                  >
                    <div class="record-rank">{{ index + 1 }}</div>
                    <img
                      :src="record.steam_avatar"
                      alt="Avatar"
                      class="record-avatar"
                    />
                    <div class="record-info">
                      <div class="record-player-name">
                        {{ record.name }}
                      </div>
                      <div class="record-time">
                        {{ formatTime(record.duration) }}
                      </div>
                    </div>
                  </SmartLink>
                </div>
                <div v-else class="loading-container">
                  <div class="loading-spinner"></div>
                  <span>Loading records...</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <hr class="divider" style="width: 100%" />
        <div class="section">
          <div class="container">
            <h2 class="section-title">Most points gained</h2>
            <h5 class="section-title-subtitle">(past 3 days)</h5>
            <div class="top-players-grid">
              <div class="class-section">
                <h5 class="section-subtitle">Soldier</h5>
                <div class="players-list">
                  <SmartLink
                    v-for="(player, index) in topSoldiers"
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
                    v-for="(player, index) in topDemomen"
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
                    url('/map-backgrounds/thumbnails/${map.name}.jpg') center/cover no-repeat
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
                    url('/map-backgrounds/thumbnails/${map.name}.jpg') center/cover no-repeat
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
      tempusCupPhases: [
        {
          phase: 1,
          startDate: "2025-10-10",
          endDate: "2025-10-14",
          soldierMap: {
            id: 708,
            name: "jump_relicta_rc2",
            soldier_tier: 2,
          },
          demomanMap: {
            id: 709,
            name: "jump_beryllium_v5",
            demoman_tier: 2,
          },
        },
        {
          phase: 2,
          startDate: "2025-10-14",
          endDate: "2025-10-25",
          soldierMap: {
            id: 710,
            name: "jump_vespertine_rc3",
            soldier_tier: 4,
          },
          demomanMap: {
            id: 711,
            name: "jump_causatham_rc5",
            demoman_tier: 4,
          },
        },
        {
          phase: 3,
          startDate: "2025-11-17",
          endDate: "2025-11-24",
          soldierMap: {
            id: 712,
            name: "jump_ember_rc6",
            soldier_tier: 5,
          },
          demomanMap: {
            id: 713,
            name: "jump_and",
            demoman_tier: 5,
          },
        },
      ],
      currentPhase: 1,
      selectedSoldierPhase: 1,
      selectedDemomanPhase: 1,
      phaseRecords: {
        soldier: [
          {
            id: 1,
            player_id: 101,
            name: "speedrunner123",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 45.123,
          },
          {
            id: 2,
            player_id: 102,
            name: "jump_master",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 46.789,
          },
          {
            id: 3,
            player_id: 103,
            name: "rocket_wizard",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 47.456,
          },
          {
            id: 4,
            player_id: 104,
            name: "pogo_king",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 48.234,
          },
          {
            id: 5,
            player_id: 105,
            name: "airshot_pro",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 49.012,
          },
          {
            id: 6,
            player_id: 106,
            name: "sync_legend",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 50.567,
          },
          {
            id: 7,
            player_id: 107,
            name: "tempus_hero",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 51.89,
          },
          {
            id: 8,
            player_id: 108,
            name: "jump_veteran",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 52.345,
          },
          {
            id: 9,
            player_id: 109,
            name: "marathon_man",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 53.678,
          },
          {
            id: 10,
            player_id: 110,
            name: "tf2_jumper",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 54.901,
          },
        ],
        demoman: [
          {
            id: 11,
            player_id: 201,
            name: "sticky_expert",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 42.567,
          },
          {
            id: 12,
            player_id: 202,
            name: "demo_warrior",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 43.89,
          },
          {
            id: 13,
            player_id: 203,
            name: "pipe_dream",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 44.234,
          },
          {
            id: 14,
            player_id: 204,
            name: "bomb_squad",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 45.678,
          },
          {
            id: 15,
            player_id: 205,
            name: "highland_hero",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 46.123,
          },
          {
            id: 16,
            player_id: 206,
            name: "kaboom_king",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 47.456,
          },
          {
            id: 17,
            player_id: 207,
            name: "demo_god",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 48.789,
          },
          {
            id: 18,
            player_id: 208,
            name: "sticky_master",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 49.012,
          },
          {
            id: 19,
            player_id: 209,
            name: "explosion_ace",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 50.345,
          },
          {
            id: 20,
            player_id: 210,
            name: "charge_champion",
            steam_avatar:
              "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
            duration: 51.678,
          },
        ],
      },
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
    };
  },
  computed: {
    currentPhase() {
      const today = new Date();
      for (let i = 0; i < this.tempusCupPhases.length; i++) {
        const phase = this.tempusCupPhases[i];
        const start = new Date(phase.startDate);
        const end = new Date(phase.endDate);
        if (today >= start && today <= end) {
          return i + 1;
        }
      }
      // If no active phase, return the last phase
      return this.tempusCupPhases.length;
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
          (p) => p.id === playerId
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
          (p) => p.id === playerId
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
    selectMap(phase, classType) {
      if (classType === "soldier") {
        this.selectedSoldierPhase = phase;
      } else {
        this.selectedDemomanPhase = phase;
      }
      this.loadPhaseRecords(phase, classType);
    },
    async loadPhaseRecords(phase, classType) {
      //this.loadingRecords = true;
      const phaseData = this.tempusCupPhases[phase - 1];
      const mapId =
        classType === "soldier"
          ? phaseData.soldierMap.id
          : phaseData.demomanMap.id;

      try {
        const records = await this.fetchPhaseRecords(mapId, classType);
        this.phaseRecords[classType] =
          records && records.length > 0 ? records : [];
      } catch (error) {
        console.error(
          `Error loading ${classType} records for phase ${phase}:`,
          error
        );
        this.phaseRecords[classType] = [];
      } finally {
        //this.loadingRecords = false;
      }
    },

    isPhaseActive(phase) {
      const today = new Date();
      const phaseData = this.tempusCupPhases[phase - 1];
      const start = new Date(phaseData.startDate);
      return today >= start;
    },

    async fetchPhaseRecords(mapId, classType) {
      try {
        const url = `${API_BASE_URL}/maps/${mapId}/null/records/${classType}/0/10`;
        const response = await axios.get(url);
        const records = response.data;

        // Fetch player data for each record
        const recordsWithAvatars = await Promise.all(
          records.map(async (record) => {
            try {
              const playerResponse = await axios.get(
                `${API_BASE_URL}/players/${record.id}`
              );
              const playerData = playerResponse.data[0];
              return {
                ...record,
                steam_avatar: playerData.steam_avatar,
                player_name: playerData.name,
              };
            } catch (error) {
              console.error(`Error fetching player ${record.id}:`, error);
              return {
                ...record,
                steam_avatar:
                  "https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb_full.jpg",
                player_name: record.player_name || `Player ${record.id}`,
              };
            }
          })
        );

        return recordsWithAvatars;
      } catch (error) {
        console.error(`Error fetching ${classType} records:`, error);
        return [];
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
          `${API_BASE_URL}/players/${playerId}/ranks`
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
    async fetchTopPlayers() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/get-top-gainers`
        );
        const data = response.data;

        this.topSoldiers = data
          .filter((player) => player.class_type === "soldier")
          .sort((a, b) => b.points_gained - a.points_gained)
          .slice(0, 3);

        this.topDemomen = data
          .filter((player) => player.class_type === "demoman")
          .sort((a, b) => b.points_gained - a.points_gained)
          .slice(0, 3);
      } catch (error) {
        console.error("Error fetching top players:", error);
      }
    },
    async fetchPopularMaps() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/get-popular-maps`
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
    if (!this.updateInterval) {
      this.updateInterval = setInterval(this.checkUpdateStatus, 30000);
    }
    this.fetchTopPlayers();
    this.fetchPopularMaps();
    this.fetchTF2RJWeeklyVideos();
    this.loadPhaseRecords(1, "soldier");
    this.loadPhaseRecords(1, "demoman");
  },
  beforeDestroy() {
    if (this.updateInterval) {
      clearInterval(this.updateInterval);
      this.updateInterval = null;
    }
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
  font-size: 4rem;
  font-weight: 800;
  margin-bottom: 20px;
  background: linear-gradient(135deg, #ffffff 0%, var(--color-primary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
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
  padding: 25px 0;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  text-align: center;
  background: linear-gradient(135deg, #ffffff 0%, var(--color-primary) 100%);
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
  padding: 20px;
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.08) 0%,
    rgba(255, 255, 255, 0.03) 100%
  );
  border-radius: 15px;
  transition: all 0.3s ease;
  text-decoration: none;
  border: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
  overflow: hidden;
}

.player-card:hover {
  transform: scale(1.03);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.6);
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
  border: 2px solid var(--color-primary);
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
.tempus-cup-wrapper {
  background: linear-gradient(
    135deg,
    rgba(102, 126, 234, 0.15) 0%,
    rgba(102, 126, 234, 0.08) 50%,
    rgba(74, 111, 165, 0.08) 100%
  );
  border-radius: 25px;
  padding: 40px 30px;
  margin: 40px auto;
  max-width: 1600px;
  box-shadow: 0 0 60px rgba(102, 126, 234, 0.3);
  border: 2px solid rgba(102, 126, 234, 0.3);
  position: relative;
  overflow: hidden;
}

.tempus-cup-container {
}

.phase-content {
  display: grid;
  grid-template-columns: auto auto auto;
  gap: 50px;
  align-items: start;
  justify-content: center;
  width: 100%;
  max-width: 1500px;
  margin: 0 auto;
}

.phase-section {
  width: 350px;
  margin: 0 auto;
}

.tempus-cup-header {
  text-align: center;
}

.cup-title {
  font-size: 3.5rem;
  font-weight: 900;
  background: linear-gradient(135deg, #667eea 0%, #4a6fa5 50%, #667eea 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 10px;
  letter-spacing: 3px;
  animation: glow-pulse 2s ease-in-out infinite alternate;
}

@keyframes glow-pulse {
  0% {
    filter: drop-shadow(0 0 10px rgba(102, 126, 234, 0.5));
  }
  100% {
    filter: drop-shadow(0 0 30px rgba(102, 126, 234, 0.9));
  }
}

.cup-subtitle {
  font-size: 1.5rem;
  color: rgba(255, 255, 255, 0.9);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin: 0px;
}

.cup-dates {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 400;
  margin: 3px;
}

.maps-center {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 25px;
  width: 100%;
  margin: 20px auto 0;
}

.cup-map-card {
  display: block;
  border-radius: 20px;
  padding: 10px;
  padding-bottom: 25px;
  min-height: 200px;
  width: 100%;
  min-width: 300px;
  position: relative;
  overflow: hidden;
  text-decoration: none;
  transition: all 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  flex-direction: column;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.cup-map-card h3 {
  margin: 15px 0;
  color: #ffffff;
  padding: 0 5px;
}

.cup-map-card .section-subtitle {
  font-size: 1.5rem;
  text-align: left;
  margin-bottom: 0;
}

.cup-map-card .compact-ratings-grid {
  margin-top: auto;
}

.cup-map-card .rating-pill {
  padding: 5px 10px;
  font-size: 1.5rem;
  border-radius: 50%;
}

.cup-map-card:hover {
  transform: scale(1.05);
  box-shadow: 0 0 30px rgba(102, 126, 234, 0.5);
  border-color: rgba(102, 126, 234, 0.5);
  cursor: pointer;
}

.map-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 1rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.8);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.map-class-icon {
  width: 24px;
  height: 24px;
}

.map-name {
  font-size: 1.8rem;
  font-weight: 700;
  color: #ffffff;
  text-align: center;
  margin: 0;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
}

.records-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.records-header {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 1.2rem;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.9);
  text-align: center;
  margin-bottom: 12px;
  padding-bottom: 8px;
  border-bottom: 2px solid rgba(102, 126, 234, 0.3);
}

.class-icon-medium {
  width: 28px;
  height: 28px;
}

.record-item {
  display: flex;
  align-items: center;
  padding: 10px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  transition: all 0.2s ease;
  text-decoration: none;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.record-item:hover {
  background: rgba(102, 126, 234, 0.2);
  transform: translateX(5px);
  cursor: pointer;
}

.soldier-section .record-item:hover {
  transform: translateX(-5px);
}

.record-gold {
  background: linear-gradient(
    135deg,
    rgba(255, 215, 0, 0.15),
    rgba(255, 215, 0, 0.05)
  );
  border-color: rgba(255, 215, 0, 0.3);
}

.record-silver {
  background: linear-gradient(
    135deg,
    rgba(192, 192, 192, 0.2),
    rgba(192, 192, 192, 0.1)
  );
  border-color: rgba(192, 192, 192, 0.3);
}

.record-bronze {
  background: linear-gradient(
    135deg,
    rgba(205, 127, 50, 0.15),
    rgba(205, 127, 50, 0.05)
  );
  border-color: rgba(205, 127, 50, 0.3);
}

.record-rank {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-primary);
  min-width: 30px;
  text-align: center;
  margin-right: 8px;
}

.record-gold .record-rank {
  color: #ffd700;
}

.record-silver .record-rank {
  color: #c0c0c0;
}

.record-bronze .record-rank {
  color: #cd7f32;
}

.record-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  margin-right: 10px;
  border: 2px solid rgba(102, 126, 234, 0.5);
}

.record-info {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
}

.record-player-name {
  font-size: 0.95rem;
  font-weight: 600;
  color: #ffffff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
  text-align: left;
  max-width: 175px;
}

.record-time {
  font-size: 0.9rem;
  font-weight: 700;
  color: #ffffff;
  font-family: monospace;
  white-space: nowrap;
}

@media (max-width: 1400px) {
  .phase-content {
    grid-template-columns: 1fr;
    gap: 30px;
  }

  .phase-section {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
  }

  .maps-center {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
  }

  .soldier-section .record-item:hover {
    transform: translateX(5px);
  }
}

@media (max-width: 768px) {
  .tempus-cup-wrapper {
    padding: 30px 15px;
  }

  .cup-title {
    font-size: 2.5rem;
  }

  .cup-subtitle {
    font-size: 1.2rem;
  }

  .tempus-cup-wrapper {
    max-width: 100%;
  }
}
@media (max-width: 560px) {
  .tempus-cup-wrapper {
    padding: 20px 10px;
    max-width: 100%;
    width: 100%;
  }

  .cup-title {
    font-size: 1.8rem;
  }

  .cup-subtitle {
    font-size: 1rem;
  }

  .cup-dates {
    font-size: 0.9rem;
  }

  .phase-content {
    grid-template-columns: 1fr;
    gap: 20px;
    width: 100%;
  }

  .phase-section {
    width: 100%;
  }

  .maps-center {
    grid-template-columns: 1fr;
    min-width: unset;
    width: 100%;
  }

  .cup-map-card {
    min-width: unset;
    width: 100%;
  }

  .records-list {
    width: 100%;
  }

  .record-item {
    padding: 8px;
  }

  .record-player-name {
    font-size: 0.85rem;
  }

  .record-time {
    font-size: 0.8rem;
  }
}
.map-locked {
  filter: blur(8px);
  cursor: not-allowed !important;
  position: relative;
}

.map-locked:hover {
  transform: none !important;
  box-shadow: 0 0px 20px rgb(0, 0, 0) !important;
}

.map-selected {
  border: 3px solid var(--color-primary);
  box-shadow: 0 0 30px rgba(102, 126, 234, 0.8);
}

.locked-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.7);
  z-index: 10;
}

.lock-icon {
  font-size: 4rem;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 10px;
  font-weight: bold;
}

.lock-text {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.8);
  font-weight: 600;
}

.cup-map-card {
  cursor: pointer;
}
</style>
