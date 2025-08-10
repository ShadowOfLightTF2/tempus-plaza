<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container py-4 d-flex flex-column align-items-center">
      <div class="page-header">
        <h1 class="page-title">
          <span class="title-icon">⚔️</span>
          Compare Players
        </h1>
        <p class="page-subtitle">
          Compare records and stats between two players
        </p>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div class="search-section">
        <div class="search-container" @click.stop>
          <div class="search-input-wrapper">
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
            <input
              type="text"
              v-model="searchQuery"
              @input="onSearch"
              placeholder="Search for players..."
              class="search-input"
            />
          </div>
          <div
            class="search-results-dropdown"
            v-if="
              searchQuery.trim() &&
              (showLoading || (searchResults && searchResults.players.length))
            "
          >
            <div v-if="showLoading" class="loading-container">
              <div class="loading-spinner"></div>
              <span>Searching...</span>
            </div>
            <div v-else>
              <div>
                <h6>Players</h6>
                <ul>
                  <li
                    v-for="player in searchResults.players"
                    :key="player.id"
                    @click="selectPlayer(player)"
                    v-html="sanitize(player.name) || `Player ID: ${player.id}`"
                  ></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="comparison-container">
        <div class="player-selection">
          <div class="player-card" :class="{ selected: playerOne }">
            <div v-if="playerOne" class="selected-player">
              <div class="player-info">
                <div class="player-avatar">
                  <img
                    v-if="playerOneData?.steam_avatar"
                    :src="playerOneData.steam_avatar"
                    :alt="playerOne.name"
                    class="avatar-image"
                  />
                  <div v-else class="avatar-placeholder">
                    <svg
                      width="32"
                      height="32"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.5"
                    >
                      <path
                        d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                      ></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                  </div>
                </div>
                <div class="player-details">
                  <div class="player-name">
                    {{ playerOneData?.name || playerOne.name }}
                  </div>
                  <div v-if="playerOneData?.country" class="player-country">
                    <img
                      :src="getFlagImageUrl(playerOneData.country_code)"
                      :alt="playerOneData.country"
                      class="country-flag"
                    />
                    <span>{{ playerOneData.country }}</span>
                  </div>
                </div>
              </div>
              <button @click="clearPlayer('playerOne')" class="btn btn-clear">
                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Clear
              </button>
            </div>
            <div v-else class="empty-player">
              <div class="player-placeholder">
                <svg
                  width="48"
                  height="48"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <h3>Select Player One</h3>
              <p>Search and click to select</p>
            </div>
          </div>

          <div class="vs-indicator">
            <span>VS</span>
          </div>

          <div class="player-card" :class="{ selected: playerTwo }">
            <div v-if="playerTwo" class="selected-player">
              <div class="player-info">
                <div class="player-avatar">
                  <img
                    v-if="playerTwoData?.steam_avatar"
                    :src="playerTwoData.steam_avatar"
                    :alt="playerTwo.name"
                    class="avatar-image"
                  />
                  <div v-else class="avatar-placeholder">
                    <svg
                      width="32"
                      height="32"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.5"
                    >
                      <path
                        d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                      ></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                  </div>
                </div>
                <div class="player-details">
                  <div class="player-name">
                    {{ playerTwoData?.name || playerTwo.name }}
                  </div>
                  <div v-if="playerTwoData?.country" class="player-country">
                    <img
                      :src="getFlagImageUrl(playerTwoData.country_code)"
                      :alt="playerTwoData.country"
                      class="country-flag"
                    />
                    <span>{{ playerTwoData.country }}</span>
                  </div>
                </div>
              </div>
              <button @click="clearPlayer('playerTwo')" class="btn btn-clear">
                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Clear
              </button>
            </div>
            <div v-else class="empty-player">
              <div class="player-placeholder">
                <svg
                  width="48"
                  height="48"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <h3>Select Player Two</h3>
              <p>Search and click to select</p>
            </div>
          </div>
        </div>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div
        class="loading-indicator"
        v-if="loadingStats.playerTwo || loadingStats.playerOne"
      >
        <div class="small-spinner"></div>
        <span>Loading...</span>
      </div>
      <div
        v-if="
          playerOne &&
          playerTwo &&
          !loadingStats.playerOne &&
          !loadingStats.playerTwo
        "
        class="comparison-results"
      >
        <div class="results-header">
          <h2>📊 Comparison Results</h2>
          <div class="class-selector">
            <button
              v-for="classType in ['combined', 'soldier', 'demoman']"
              :key="classType"
              @click="selectedClass = classType"
              :class="['class-btn', { active: selectedClass === classType }]"
            >
              {{ classType.charAt(0).toUpperCase() + classType.slice(1) }}
            </button>
          </div>
        </div>
        <div class="ranks-cards">
          <div class="rank-card">
            <div class="rank-icon">🌍</div>
            <div class="rank-content">
              <h4>Overall Rank</h4>
              <div class="rank-comparison">
                <span class="rank-value">
                  #{{ playerOneRanks.overall_rank }}
                </span>
                <span class="vs-text">vs</span>
                <span class="rank-value">
                  #{{ playerTwoRanks.overall_rank }}
                </span>
              </div>
              <div class="rank-points">
                <small
                  >{{ playerOneRanks.overall_points?.toLocaleString() }} pts vs
                  {{ playerTwoRanks.overall_points?.toLocaleString() }}
                  pts</small
                >
              </div>
            </div>
          </div>

          <div class="rank-card">
            <div class="rank-icon">
              <img
                src="/icons/soldier.png"
                alt="Soldier"
                class="class-icon"
                loading="lazy"
              />
            </div>
            <div class="rank-content">
              <h4>Soldier Rank</h4>
              <div class="rank-comparison">
                <span class="rank-value">
                  #{{ playerOneRanks.soldier_rank }}
                </span>
                <span class="vs-text">vs</span>
                <span class="rank-value">
                  #{{ playerTwoRanks.soldier_rank }}
                </span>
              </div>
              <div class="rank-points">
                <small
                  >{{ playerOneRanks.soldier_points?.toLocaleString() }} pts vs
                  {{ playerTwoRanks.soldier_points?.toLocaleString() }}
                  pts</small
                >
              </div>
            </div>
          </div>

          <div class="rank-card">
            <div class="rank-icon">
              <img
                src="/icons/demoman.png"
                alt="Demoman"
                class="class-icon"
                loading="lazy"
              />
            </div>
            <div class="rank-content">
              <h4>Demoman Rank</h4>
              <div class="rank-comparison">
                <span class="rank-value">
                  #{{ playerOneRanks.demoman_rank }}
                </span>
                <span class="vs-text">vs</span>
                <span class="rank-value">
                  #{{ playerTwoRanks.demoman_rank }}
                </span>
              </div>
              <div class="rank-points">
                <small
                  >{{ playerOneRanks.demoman_points?.toLocaleString() }} pts vs
                  {{ playerTwoRanks.demoman_points?.toLocaleString() }}
                  pts</small
                >
              </div>
            </div>
          </div>
        </div>
        <div class="summary-cards">
          <div class="summary-card">
            <div class="card-icon">🏆</div>
            <div class="card-content">
              <h4>World Records</h4>
              <div class="comparison-values">
                <span class="value">
                  {{ getStatValue("playerOneStats", "worldRecords", "total") }}
                </span>
                <span class="vs-text">vs</span>
                <span class="value">
                  {{ getStatValue("playerTwoStats", "worldRecords", "total") }}
                </span>
              </div>
            </div>
          </div>

          <div class="summary-card">
            <div class="card-icon">🥇</div>
            <div class="card-content">
              <h4>Top 10 Records</h4>
              <div class="comparison-values">
                <span class="value">
                  {{ getStatValue("playerOneStats", "top10Records", "total") }}
                </span>
                <span class="vs-text">vs</span>
                <span class="value">
                  {{ getStatValue("playerTwoStats", "top10Records", "total") }}
                </span>
              </div>
            </div>
          </div>

          <div class="summary-card">
            <div class="card-icon">✅</div>
            <div class="card-content">
              <h4>Completion Rate</h4>
              <div class="comparison-values">
                <span class="value">
                  {{
                    getStatValue(
                      "playerOneStats",
                      "completion",
                      "total"
                    ).toFixed(2)
                  }}%
                </span>
                <span class="vs-text">vs</span>
                <span class="value">
                  {{
                    getStatValue(
                      "playerTwoStats",
                      "completion",
                      "total"
                    ).toFixed(2)
                  }}%
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="tables-section">
          <div class="table-container">
            <h3>🏆 World Records</h3>
            <table class="comparison-table">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>{{ getPlayerDisplayName("playerOne") }}</th>
                  <th>{{ getPlayerDisplayName("playerTwo") }}</th>
                  <th>Difference</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="type in ['map', 'course', 'bonus', 'total']"
                  :key="type"
                >
                  <td class="type-cell">
                    {{ type.charAt(0).toUpperCase() + type.slice(1) }}
                  </td>
                  <td :class="getWinnerClass('worldRecords', type)">
                    {{ getStatValue("playerOneStats", "worldRecords", type) }}
                  </td>
                  <td :class="getWinnerClass('worldRecords', type, true)">
                    {{ getStatValue("playerTwoStats", "worldRecords", type) }}
                  </td>
                  <td class="difference-cell">
                    {{ getDifference("worldRecords", type) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="table-container">
            <h3>🥇 Top 10 Records</h3>
            <table class="comparison-table">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>{{ getPlayerDisplayName("playerOne") }}</th>
                  <th>{{ getPlayerDisplayName("playerTwo") }}</th>
                  <th>Difference</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="type in ['map', 'course', 'bonus', 'total']"
                  :key="type"
                >
                  <td class="type-cell">
                    {{ type.charAt(0).toUpperCase() + type.slice(1) }}
                  </td>
                  <td :class="getWinnerClass('top10Records', type)">
                    {{ getStatValue("playerOneStats", "top10Records", type) }}
                  </td>
                  <td :class="getWinnerClass('top10Records', type, true)">
                    {{ getStatValue("playerTwoStats", "top10Records", type) }}
                  </td>
                  <td class="difference-cell">
                    {{ getDifference("top10Records", type) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="table-container">
            <h3>⏱️ Group Records</h3>
            <div class="group-tables">
              <div
                v-for="group in [
                  'g1Records',
                  'g2Records',
                  'g3Records',
                  'g4Records',
                ]"
                :key="group"
                class="group-table"
              >
                <h4>{{ group.replace("Records", "").toUpperCase() }}</h4>
                <table class="comparison-table compact">
                  <thead>
                    <tr>
                      <th>Type</th>
                      <th>{{ getPlayerDisplayName("playerOne", true) }}</th>
                      <th>{{ getPlayerDisplayName("playerTwo", true) }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="type in ['map', 'course', 'bonus', 'total']"
                      :key="type"
                    >
                      <td class="type-cell">
                        {{ type.charAt(0).toUpperCase() + type.slice(1) }}
                      </td>
                      <td :class="getWinnerClass(group, type)">
                        {{ getStatValue("playerOneStats", group, type) }}
                      </td>
                      <td :class="getWinnerClass(group, type, true)">
                        {{ getStatValue("playerTwoStats", group, type) }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="table-container">
            <h3>✅ Completion Rates</h3>
            <table class="comparison-table">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>{{ getPlayerDisplayName("playerOne") }}</th>
                  <th>{{ getPlayerDisplayName("playerTwo") }}</th>
                  <th>Difference</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="type in ['map', 'course', 'bonus', 'total']"
                  :key="type"
                >
                  <td class="type-cell">
                    {{ type.charAt(0).toUpperCase() + type.slice(1) }}
                  </td>
                  <td :class="getWinnerClass('completion', type)">
                    {{
                      getStatValue(
                        "playerOneStats",
                        "completion",
                        type
                      ).toFixed(2)
                    }}%
                  </td>
                  <td :class="getWinnerClass('completion', type, true)">
                    {{
                      getStatValue(
                        "playerTwoStats",
                        "completion",
                        type
                      ).toFixed(2)
                    }}%
                  </td>
                  <td class="difference-cell">
                    {{ getCompletionDifference(type) }}%
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <hr
        v-if="
          playerOne &&
          playerTwo &&
          !loadingStats.playerOne &&
          !loadingStats.playerTwo
        "
        class="row-divider"
        style="width: 75%"
        margin-top="50px"
      />
      <div
        v-if="
          playerOne &&
          playerTwo &&
          !loadingStats.playerOne &&
          !loadingStats.playerTwo
        "
        class="map-comparison-section"
      >
        <div class="section-header">
          <h2>🗺️ Map Record Comparison</h2>
          <p class="section-subtitle">
            Compare specific map records between the players
          </p>
        </div>
        <div class="map-search-container">
          <div class="search-input-wrapper">
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
            <input
              type="text"
              v-model="mapSearchQuery"
              @input="onMapSearch"
              placeholder="Search for maps..."
              class="search-input"
            />
          </div>
          <div
            class="search-results-dropdown"
            v-if="
              mapSearchQuery.trim() &&
              (showMapLoading || (mapSearchResults && mapSearchResults.length))
            "
          >
            <div v-if="showMapLoading" class="loading-container">
              <div class="loading-spinner"></div>
              <span>Searching maps...</span>
            </div>
            <div v-else>
              <ul>
                <li
                  v-for="map in mapSearchResults"
                  :key="map.id"
                  @click="selectMap(map)"
                >
                  {{ map.name }}
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div v-if="selectedMap" class="map-selection-info">
          <div
            class="selected-map-card"
            :style="{
              '--map-bg-url': `url('/map-backgrounds/${selectedMap.name}.jpg')`,
            }"
          >
            <h3>{{ selectedMap.name }}</h3>
            <button @click="clearMap" class="btn btn-clear">
              <svg
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
              Clear Map
            </button>
          </div>
          <div class="map-class-selector">
            <button
              v-for="classType in ['soldier', 'demoman']"
              :key="classType"
              @click="selectedMapClass = classType"
              :class="['class-btn', { active: selectedMapClass === classType }]"
            >
              {{ classType.charAt(0).toUpperCase() + classType.slice(1) }}
            </button>
          </div>
        </div>
        <div
          v-if="selectedMap && selectedMapClass"
          class="map-records-comparison"
        >
          <div v-if="loadingMapRecords" class="loading-container">
            <div class="loading-spinner"></div>
            <span>Loading map records...</span>
          </div>
          <div
            v-else-if="playerOneMapRecord || playerTwoMapRecord"
            class="map-records-table"
          >
            <table class="comparison-table">
              <thead>
                <tr>
                  <th>Attribute</th>
                  <th>{{ getPlayerDisplayName("playerOne") }}</th>
                  <th>{{ getPlayerDisplayName("playerTwo") }}</th>
                  <th>Difference</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="type-cell">Rank</td>
                  <td :class="getMapRecordWinnerClass('rank', true)">
                    {{ playerOneMapRecord?.rank || "-" }}
                  </td>
                  <td :class="getMapRecordWinnerClass('rank')">
                    {{ playerTwoMapRecord?.rank || "-" }}
                  </td>
                  <td class="difference-cell">-{{ getRankDifference() }}</td>
                </tr>
                <tr>
                  <td class="type-cell">Time</td>
                  <td>
                    {{
                      playerOneMapRecord?.duration
                        ? formatTime(playerOneMapRecord.duration)
                        : "-"
                    }}
                  </td>
                  <td>
                    {{
                      playerTwoMapRecord?.duration
                        ? formatTime(playerTwoMapRecord.duration)
                        : "-"
                    }}
                  </td>
                  <td class="difference-cell">-{{ getTimeDifference() }}</td>
                </tr>
                <tr v-if="playerOneMapRecord?.date || playerTwoMapRecord?.date">
                  <td class="type-cell">Date</td>
                  <td>
                    {{
                      playerOneMapRecord?.date
                        ? formatDate(playerOneMapRecord.date)
                        : "-"
                    }}
                  </td>
                  <td>
                    {{
                      playerTwoMapRecord?.date
                        ? formatDate(playerTwoMapRecord.date)
                        : "-"
                    }}
                  </td>
                  <td class="difference-cell">-</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="no-records">
            <p>No records found for either player on this map.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DOMPurify from "dompurify";
import { useHead } from "@vueuse/head";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Compare",
  props: {
    playerId1: {
      type: Number,
      default: null,
    },
    playerId2: {
      type: Number,
      default: null,
    },
    mapId: {
      type: Number,
      default: null,
    },
  },
  setup() {
    useHead({
      title: "Tempus Plaza | Head to Head",
    });
  },
  data: () => ({
    searchQuery: "",
    searchResults: null,
    showLoading: false,
    debounceTimer: null,
    playerOne: null,
    playerTwo: null,
    playerOneStats: null,
    playerTwoStats: null,
    playerOneData: null,
    playerTwoData: null,
    selectedClass: "combined",
    playerOneRanks: null,
    playerTwoRanks: null,
    loadingRanks: {
      playerOne: false,
      playerTwo: false,
    },
    loadingStats: {
      playerOne: false,
      playerTwo: false,
    },
    loadingData: {
      playerOne: false,
      playerTwo: false,
    },
    mapSearchQuery: "",
    mapSearchResults: null,
    showMapLoading: false,
    mapDebounceTimer: null,
    selectedMap: null,
    selectedMapClass: "soldier",
    playerOneMapRecord: null,
    playerTwoMapRecord: null,
    loadingMapRecords: false,
    loadingTopPlayers: false,
    updatingUrl: false,
  }),
  async mounted() {
    if (this.playerId1 && this.playerId2) {
      await this.loadPlayersFromIds(this.playerId1, this.playerId2);
    } else {
      await this.loadTopTwoPlayers();
    }
    if (this.mapId) {
      await this.loadMapFromId(this.mapId);
    }
  },
  methods: {
    sanitize(str) {
      return DOMPurify.sanitize(str || "");
    },
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    getPlayerDisplayName(playerKey, short = false) {
      const player = this[playerKey];
      const playerData = this[`${playerKey}Data`];

      if (!player) return "";

      const name = playerData?.name || player.name;
      return short ? name.split(" ")[0] : name;
    },
    async loadPlayersFromIds(playerId1, playerId2) {
      this.loadingStats.playerOne = true;
      this.loadingData.playerOne = true;
      this.loadingRanks.playerOne = true;
      this.loadingStats.playerTwo = true;
      this.loadingData.playerTwo = true;
      this.loadingRanks.playerTwo = true;

      try {
        const [player1Response, player2Response] = await Promise.all([
          fetch(`${API_BASE_URL}/players/${playerId1}`),
          fetch(`${API_BASE_URL}/players/${playerId2}`),
        ]);

        if (player1Response.ok && player2Response.ok) {
          const player1Data = await player1Response.json();
          const player2Data = await player2Response.json();

          const p1 = Array.isArray(player1Data) ? player1Data[0] : player1Data;
          const p2 = Array.isArray(player2Data) ? player2Data[0] : player2Data;

          this.playerOne = {
            id: playerId1,
            name: p1.name || `Player ${playerId1}`,
          };

          this.playerTwo = {
            id: playerId2,
            name: p2.name || `Player ${playerId2}`,
          };

          await Promise.all([
            this.fetchPlayerStats(playerId1, "playerOneStats"),
            this.fetchPlayerData(playerId1, "playerOneData"),
            this.fetchPlayerRanks(playerId1, "playerOneRanks"),
            this.fetchPlayerStats(playerId2, "playerTwoStats"),
            this.fetchPlayerData(playerId2, "playerTwoData"),
            this.fetchPlayerRanks(playerId2, "playerTwoRanks"),
          ]);
        } else {
          console.error("Failed to fetch one or both players");
          await this.loadTopTwoPlayers();
        }
      } catch (error) {
        console.error("Error loading players from IDs:", error);
        await this.loadTopTwoPlayers();
      } finally {
        this.loadingStats.playerOne = false;
        this.loadingData.playerOne = false;
        this.loadingRanks.playerOne = false;
        this.loadingStats.playerTwo = false;
        this.loadingData.playerTwo = false;
        this.loadingRanks.playerTwo = false;
      }
    },
    async loadMapFromId(mapId) {
      try {
        const response = await fetch(`${API_BASE_URL}/maps/${mapId}`);
        if (response.ok) {
          const mapData = await response.json();
          const map = Array.isArray(mapData) ? mapData[0] : mapData;

          this.selectedMap = {
            id: mapId,
            name: map.name || `Map ${mapId}`,
          };

          if (this.playerOne && this.playerTwo) {
            await this.fetchMapRecords();
          }
        } else {
          console.error("Failed to fetch map data");
        }
      } catch (error) {
        console.error("Error loading map from ID:", error);
      }
    },
    async loadTopTwoPlayers() {
      this.loadingTopPlayers = true;
      try {
        const response = await fetch(
          `${API_BASE_URL}/players/top-two-players/overall`
        );
        if (!response.ok) throw new Error("Failed to fetch top players");
        const topPlayers = await response.json();

        if (topPlayers && topPlayers.length >= 2) {
          this.playerOne = {
            id: topPlayers[0].player_id,
            name: topPlayers[0].name,
            rank: topPlayers[0].rank,
            overall_rank: topPlayers[0].rank,
          };

          this.playerTwo = {
            id: topPlayers[1].player_id,
            name: topPlayers[1].name,
            rank: topPlayers[1].rank,
            overall_rank: topPlayers[1].rank,
          };

          this.loadingStats.playerOne = true;
          this.loadingData.playerOne = true;
          this.loadingRanks.playerOne = true;
          this.loadingStats.playerTwo = true;
          this.loadingData.playerTwo = true;
          this.loadingRanks.playerTwo = true;

          await Promise.all([
            this.fetchPlayerStats(this.playerOne.id, "playerOneStats"),
            this.fetchPlayerData(this.playerOne.id, "playerOneData"),
            this.fetchPlayerRanks(this.playerOne.id, "playerOneRanks"),
            this.fetchPlayerStats(this.playerTwo.id, "playerTwoStats"),
            this.fetchPlayerData(this.playerTwo.id, "playerTwoData"),
            this.fetchPlayerRanks(this.playerTwo.id, "playerTwoRanks"),
          ]);

          this.loadingStats.playerOne = false;
          this.loadingData.playerOne = false;
          this.loadingRanks.playerOne = false;
          this.loadingStats.playerTwo = false;
          this.loadingData.playerTwo = false;
          this.loadingRanks.playerTwo = false;

          if (!this.playerId1 && !this.playerId2) {
            this.updateUrl();
          }
        }
      } catch (error) {
        console.error("Error loading top players:", error);
      } finally {
        this.loadingTopPlayers = false;
      }
    },
    async onSearch() {
      if (this.searchQuery.trim()) {
        this.showLoading = true;
      } else {
        this.showLoading = false;
        this.searchResults = null;
        return;
      }

      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(async () => {
        if (this.searchQuery.trim()) {
          try {
            const response = await fetch(`${API_BASE_URL}/search/players`, {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ query: this.searchQuery }),
            });
            if (!response.ok) throw new Error("Failed to search results");
            const data = await response.json();
            if (data.players && data.players.length > 20) {
              data.players = data.players.slice(0, 20);
            }
            this.searchResults = { players: data.players };
          } catch (error) {
            console.error("Error fetching search results:", error);
          } finally {
            this.showLoading = false;
          }
        } else {
          this.searchResults = null;
          this.showLoading = false;
        }
      }, 500);
    },
    updateUrl() {
      const playerId1 = this.playerOne?.id;
      const playerId2 = this.playerTwo?.id;
      const mapId = this.selectedMap?.id;

      let url = "/compare";

      if (playerId1 && playerId2) {
        url += `/${playerId1}/${playerId2}`;
        if (mapId) {
          url += `/${mapId}`;
        }
      } else if (playerId1) {
        url += `/${playerId1}`;
      }

      if (window.location.pathname !== url) {
        window.history.replaceState({}, "", url);
      }
    },
    async selectPlayer(player) {
      if (!this.playerOne) {
        this.playerOne = player;
        this.loadingStats.playerOne = true;
        this.loadingData.playerOne = true;
        this.loadingRanks.playerOne = true;

        await Promise.all([
          this.fetchPlayerStats(player.id, "playerOneStats"),
          this.fetchPlayerData(player.id, "playerOneData"),
          this.fetchPlayerRanks(player.id, "playerOneRanks"),
        ]);

        this.loadingStats.playerOne = false;
        this.loadingData.playerOne = false;
        this.loadingRanks.playerOne = false;

        this.updateUrl();
      } else if (!this.playerTwo) {
        this.playerTwo = player;
        this.loadingStats.playerTwo = true;
        this.loadingData.playerTwo = true;
        this.loadingRanks.playerTwo = true;

        await Promise.all([
          this.fetchPlayerStats(player.id, "playerTwoStats"),
          this.fetchPlayerData(player.id, "playerTwoData"),
          this.fetchPlayerRanks(player.id, "playerTwoRanks"),
        ]);

        this.loadingStats.playerTwo = false;
        this.loadingData.playerTwo = false;
        this.loadingRanks.playerTwo = false;

        this.updateUrl();
      }
      this.searchQuery = "";
      this.searchResults = null;
    },

    clearPlayer(player) {
      if (player === "playerOne") {
        this.playerOne = null;
        this.playerOneStats = null;
        this.playerOneData = null;
        this.loadingStats.playerOne = false;
        this.loadingData.playerOne = false;
      } else {
        this.playerTwo = null;
        this.playerTwoStats = null;
        this.playerTwoData = null;
        this.loadingStats.playerTwo = false;
        this.loadingData.playerTwo = false;
      }

      this.updateUrl();
    },
    async fetchPlayerStats(playerId, statsProperty) {
      try {
        const response = await fetch(
          `${API_BASE_URL}/players/${playerId}/stats`
        );
        if (!response.ok) throw new Error("Failed to fetch player stats");
        const data = await response.json();
        this[statsProperty] = data;
      } catch (error) {
        console.error("Error fetching player stats:", error);
      }
    },
    async fetchPlayerData(playerId, dataProperty) {
      try {
        const response = await fetch(`${API_BASE_URL}/players/${playerId}`);
        if (!response.ok) throw new Error("Failed to fetch player data");
        const data = await response.json();
        this[dataProperty] = Array.isArray(data) ? data[0] : data;
      } catch (error) {
        console.error("Error fetching player data:", error);
      }
    },
    async fetchPlayerRanks(playerId, ranksProperty) {
      try {
        const response = await fetch(
          `${API_BASE_URL}/players/${playerId}/ranks`
        );
        if (!response.ok) throw new Error("Failed to fetch player ranks");
        const data = await response.json();
        this[ranksProperty] = Array.isArray(data) ? data[0] : data;
      } catch (error) {
        console.error("Error fetching player ranks:", error);
      }
    },
    getStatValue(playerStats, category, type) {
      if (
        !this[playerStats] ||
        !this[playerStats][this.selectedClass] ||
        !this[playerStats][this.selectedClass][category]
      ) {
        return 0;
      }
      return this[playerStats][this.selectedClass][category][type] || 0;
    },
    getWinnerClass(category, type, isPlayerTwo = false) {
      const player1Value = this.getStatValue("playerOneStats", category, type);
      const player2Value = this.getStatValue("playerTwoStats", category, type);

      if (player1Value === player2Value) return "tie";

      const player1Wins = player1Value > player2Value;

      if (isPlayerTwo) {
        return player1Wins ? "loser" : "winner";
      } else {
        return player1Wins ? "winner" : "loser";
      }
    },
    getDifference(category, type) {
      const player1Value = this.getStatValue("playerOneStats", category, type);
      const player2Value = this.getStatValue("playerTwoStats", category, type);
      const diff = Math.abs(player1Value - player2Value);
      const winner =
        player1Value > player2Value
          ? this.getPlayerDisplayName("playerOne", true)
          : this.getPlayerDisplayName("playerTwo", true);
      return diff === 0 ? "Tied" : `+${diff} (${winner})`;
    },
    getCompletionDifference(type) {
      const player1Value = this.getStatValue(
        "playerOneStats",
        "completion",
        type
      );
      const player2Value = this.getStatValue(
        "playerTwoStats",
        "completion",
        type
      );
      const diff = Math.abs(player1Value - player2Value);
      return diff.toFixed(2);
    },
    async onMapSearch() {
      if (this.mapSearchQuery.trim()) {
        this.showMapLoading = true;
      } else {
        this.showMapLoading = false;
        this.mapSearchResults = null;
        return;
      }

      clearTimeout(this.mapDebounceTimer);
      this.mapDebounceTimer = setTimeout(async () => {
        if (this.mapSearchQuery.trim()) {
          try {
            const response = await fetch(`${API_BASE_URL}/search/maps`, {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ query: this.mapSearchQuery }),
            });
            if (!response.ok) throw new Error("Failed to search maps");
            const data = await response.json();
            if (data.maps && data.maps.length > 20) {
              data.maps = data.maps.slice(0, 20);
            }
            this.mapSearchResults = data.maps || [];
          } catch (error) {
            console.error("Error fetching map search results:", error);
          } finally {
            this.showMapLoading = false;
          }
        } else {
          this.mapSearchResults = null;
          this.showMapLoading = false;
        }
      }, 500);
    },
    async selectMap(map) {
      this.selectedMap = map;
      this.mapSearchQuery = "";
      this.mapSearchResults = null;
      this.updateUrl();
      await this.fetchMapRecords();
    },
    clearMap() {
      this.selectedMap = null;
      this.playerOneMapRecord = null;
      this.playerTwoMapRecord = null;
      this.updateUrl();
    },
    async fetchMapRecords() {
      if (
        !this.selectedMap ||
        !this.selectedMapClass ||
        !this.playerOne ||
        !this.playerTwo
      )
        return;

      this.loadingMapRecords = true;
      try {
        const [player1Response, player2Response] = await Promise.all([
          fetch(
            `${API_BASE_URL}/maps/${this.selectedMap.id}/${this.playerOne.id}/records/${this.selectedMapClass}/0/1`
          ),
          fetch(
            `${API_BASE_URL}/maps/${this.selectedMap.id}/${this.playerTwo.id}/records/${this.selectedMapClass}/0/1`
          ),
        ]);

        const player1Data = player1Response.ok
          ? await player1Response.json()
          : null;
        const player2Data = player2Response.ok
          ? await player2Response.json()
          : null;

        this.playerOneMapRecord = player1Data[0] || null;
        this.playerTwoMapRecord = player2Data[0] || null;

        if (
          this.selectedMap &&
          (!this.mapId || this.mapId !== this.selectedMap.id)
        ) {
          this.updateUrl();
        }
      } catch (error) {
        console.error("Error fetching map records:", error);
        this.playerOneMapRecord = null;
        this.playerTwoMapRecord = null;
      } finally {
        this.loadingMapRecords = false;
      }
    },
    formatTime(timeInSeconds) {
      const totalSeconds = timeInSeconds;
      const hours = Math.floor(totalSeconds / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const seconds = Math.floor(totalSeconds % 60);
      const milliseconds = Math.floor((totalSeconds % 1) * 1000);

      if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, "0")}:${seconds
          .toString()
          .padStart(2, "0")}.${milliseconds.toString().padStart(3, "0")}`;
      } else {
        return `${minutes.toString().padStart(2, "0")}:${seconds
          .toString()
          .padStart(2, "0")}.${milliseconds.toString().padStart(3, "0")}`;
      }
    },
    formatDate(dateString) {
      if (!dateString) return null;
      const date = new Date(dateString * 1000);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}/${month}/${day}`;
    },
    getMapRecordWinnerClass(attribute, reverse = false) {
      const record1 = this.playerOneMapRecord;
      const record2 = this.playerTwoMapRecord;
      if (!record1 || !record2) return "";
      if (record1.rank === record2.rank) return "tied";
      let player1Better = record1.rank > record2.rank;
      if (reverse) player1Better = !player1Better;
      return player1Better ? "winner" : "loser";
    },
    getTimeDifference() {
      const record1 = this.playerOneMapRecord;
      const record2 = this.playerTwoMapRecord;

      if (!record1?.duration || !record2?.duration) return "N/A";

      const diff = Math.abs(record1.duration - record2.duration);
      const winner =
        record1.duration < record2.duration
          ? this.getPlayerDisplayName("playerOne", true)
          : this.getPlayerDisplayName("playerTwo", true);

      let formattedDiff;

      if (diff < 60) {
        formattedDiff = `${diff.toFixed(3)}s`;
      } else if (diff < 3600) {
        const minutes = Math.floor(diff / 60);
        const seconds = Math.floor(diff % 60);
        formattedDiff = `${minutes}m ${seconds}s`;
      } else {
        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        formattedDiff = `${hours}h ${minutes}m`;
      }

      return `${formattedDiff} (${winner})`;
    },
    getRankDifference() {
      const record1 = this.playerOneMapRecord;
      const record2 = this.playerTwoMapRecord;

      if (!record1?.rank || !record2?.rank) return "N/A";

      const diff = Math.abs(record1.rank - record2.rank);
      const winner =
        record1.rank < record2.rank
          ? this.getPlayerDisplayName("playerOne", true)
          : this.getPlayerDisplayName("playerTwo", true);
      return diff === 0 ? "Tied" : `${diff} (${winner})`;
    },
  },
  watch: {
    playerId1(newId, oldId) {
      if (newId !== oldId && newId && this.playerId2) {
        this.loadPlayersFromIds(newId, this.playerId2);
      }
    },

    playerId2(newId, oldId) {
      if (newId !== oldId && newId && this.playerId1) {
        this.loadPlayersFromIds(this.playerId1, newId);
      }
    },

    mapId(newId, oldId) {
      if (newId !== oldId && newId) {
        this.loadMapFromId(newId);
      } else if (!newId && oldId) {
        this.selectedMap = null;
        this.playerOneMapRecord = null;
        this.playerTwoMapRecord = null;
      }
    },

    selectedMapClass() {
      if (this.selectedMap) {
        this.fetchMapRecords();
      }
    },

    playerOne() {
      if (this.selectedMap) {
        this.fetchMapRecords();
      }
    },

    playerTwo() {
      if (this.selectedMap) {
        this.fetchMapRecords();
      }
    },
  },
};
</script>

<style scoped>
.row-divider {
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
.avatar-image {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--color-primary);
}

.avatar-placeholder {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, 0.6);
}

.avatar-placeholder svg {
  width: 40px;
  height: 40px;
}

.player-details h2 {
  font-size: 1.1rem;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.loading-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.7);
}

.small-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-top: 2px solid #007bff;
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
.comparison-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 20px 0;
  width: 100%;
}

.player-selection {
  display: flex;
  gap: 30px;
  margin-bottom: 30px;
  align-items: center;
  flex-wrap: wrap;
  justify-content: center;
}

.vs-indicator {
  background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
  border-radius: 50%;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 18px;
  color: white;
  box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
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

.player-card {
  display: flex;
  align-items: center;
  padding: 15px 40px;
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
  max-width: 400px;
  box-shadow: 0 0px 10px rgba(0, 0, 0, 0.5);
}

.player-card.selected {
  background: #25375218;
  border-color: #253752a4;
  box-shadow: 0 0px 10px rgba(0, 0, 0, 0.5);
}

.player-placeholder {
  opacity: 0.4;
  margin-bottom: 10px;
  color: var(--color-text-soft);
}

.selected-player {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.player-info h2 {
  margin: 0;
  font-size: 1.4rem;
  color: var(--color-text);
}
.player-info {
  display: flex;
  align-items: center;
  width: 100%;
}

.player-avatar {
  flex-shrink: 0;
  margin-right: 15px;
}

.player-details {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 0;
  width: 100%;
}

.player-name {
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--color-text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
}

.player-country {
  display: flex;
  align-items: center;
  font-weight: bold;
  color: #d5d5d5;
  margin-bottom: 10px;
  align-self: flex-start;
}

.country-flag {
  margin-right: 8px;
  width: 20px;
  height: auto;
}

.player-placeholder {
  opacity: 0.4;
  margin-bottom: 15px;
  color: var(--color-text-soft);
}

.player-placeholder svg {
  width: 64px;
  height: 64px;
}

.empty-player h3 {
  color: var(--color-text);
  margin: 0;
  font-size: 1.4rem;
}

.empty-player p {
  color: var(--color-text-soft);
  margin: 0;
  font-size: 1rem;
}

.loading-indicator {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.7);
}

.small-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-top: 2px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.btn-clear {
  background: rgba(244, 67, 54, 0.1);
  border: 1px solid rgba(244, 67, 54, 0.3);
  color: #f44336;
  padding: 12px 20px;
  border-radius: 25px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  align-self: center;
  gap: 8px;
  width: 200px;
  height: 40px;
  justify-content: center;
  flex-direction: row-reverse;
}

.map-comparison-section {
  margin-top: 0px !important;
  margin-bottom: 80px;
  padding-top: 30px;
  padding-bottom: 50px;
  border: none !important;
}

.btn-clear:hover {
  color: #f44336;
  border-color: rgba(244, 67, 54, 0.5);
}

.comparison-results {
  width: 100%;
  max-width: 1200px;
}

.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 20px;
}

.results-header h2 {
  margin: 0;
  font-size: 2rem;
  color: var(--color-text);
}

.class-selector {
  display: flex;
  gap: 10px;
  background: rgba(255, 255, 255, 0.05);
  padding: 5px;
  border-radius: 25px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 10px rgba(0, 0, 0, 0.5);
}

.class-btn {
  background: transparent;
  border: 2px solid transparent;
  color: var(--color-text-soft);
  padding: 10px 20px;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.class-btn.active {
  background: rgba(255, 255, 255, 0.1);
  border-color: var(--color-primary);
  color: white;
  box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

.class-btn:hover:not(.active) {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.1);
  color: var(--color-text);
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.summary-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 25px;
  display: flex;
  align-items: center;
  gap: 20px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  transition: transform 0.3s ease;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.summary-card:hover {
  transform: translateY(-2px);
}

.card-icon {
  font-size: 2.5rem;
  width: 60px;
  text-align: center;
}

.card-content h4 {
  margin: 0 0 10px 0;
  font-size: 1.1rem;
  color: var(--color-text);
}

.comparison-values {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1.5rem;
  font-weight: bold;
  color: var(--color-text);
}
.ranks-section {
  width: 100%;
  max-width: 1200px;
  margin: 30px 0;
  padding: 25px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.ranks-header h2 {
  margin: 0;
  font-size: 1.8rem;
  color: var(--color-text);
}

.ranks-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.rank-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  transition: transform 0.3s ease;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.rank-card:hover {
  transform: translateY(-2px);
}

.rank-icon {
  font-size: 2rem;
  width: 50px;
  text-align: center;
}

.class-icon {
  width: 40px;
  height: 40px;
}

.rank-content {
  flex: 1;
}

.rank-content h4 {
  margin: 0 0 10px 0;
  font-size: 1.1rem;
  color: var(--color-text);
}

.rank-comparison {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 5px;
}

.rank-value {
  color: var(--color-text);
  font-size: 1.3rem;
  font-weight: bold;
  padding: 4px 8px;
  border-radius: 6px;
}

.rank-points {
  color: var(--color-text-soft);
  font-size: 0.85rem;
}

.vs-text {
  font-size: 1rem;
  color: var(--color-text-soft);
  font-weight: normal;
}

.tables-section {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.table-container {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 16px;
  padding: 25px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.table-container h3 {
  margin: 0 0 20px 0;
  font-size: 1.5rem;
  border-bottom: 2px solid rgba(255, 255, 255, 0.1);
  padding-bottom: 10px;
  color: var(--color-text);
}

.comparison-table {
  width: 100%;
  border-collapse: collapse;
  background: transparent;
  color: var(--color-text);
}

.comparison-table th,
.comparison-table td {
  padding: 12px 15px;
  text-align: center;
  vertical-align: middle;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  max-width: 200px !important;
  width: 200px !important;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.comparison-table th {
  background: rgba(255, 255, 255, 0.05);
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.9rem;
  letter-spacing: 0.5px;
  color: var(--color-text);
}

.type-cell {
  font-weight: 500;
  text-transform: capitalize;
  color: var(--color-text);
}

.winner {
  background: rgba(76, 175, 80, 0.2);
  color: #81c784;
  font-weight: bold;
}

.loser {
  background: rgba(244, 67, 54, 0.1);
  color: var(--color-text-soft);
}

.tied {
  background: rgba(129, 129, 129, 0.1);
  color: var(--color-text-soft);
}
.difference-cell {
  font-weight: 500;
  font-style: italic;
  color: var(--color-text-soft);
}

.group-tables {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.group-table h4 {
  margin: 0 0 15px 0;
  font-size: 1.1rem;
  color: var(--color-text);
  text-align: center;
  padding: 8px;
  background: #25375244;
  border-radius: 8px;
}

.comparison-table.compact th,
.comparison-table.compact td {
  padding: 8px 12px;
  font-size: 0.9rem;
  text-align: center;
  vertical-align: middle;
}

.map-comparison-section {
  margin-top: 50px;
  padding-top: 30px;
  border-top: 2px solid rgba(255, 255, 255, 0.1);
}

.section-header {
  text-align: center;
  margin-bottom: 30px;
}

.section-header h2 {
  margin: 0 0 10px 0;
  font-size: 2rem;
  color: var(--color-text);
}

.section-subtitle {
  color: var(--color-text-soft);
  margin: 0;
  font-size: 1.1rem;
}

.map-search-container {
  max-width: 500px;
  margin: 0 auto 30px auto;
  position: relative;
}

.map-selection-info {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 30px;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.selected-map-card {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  border-radius: 20px;
  padding: 35px;
  display: flex;
  align-items: center;
  gap: 15px;
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
  min-height: 120px;
  width: auto;
  position: relative;
  overflow: hidden;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.selected-map-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
      135deg,
      rgba(0, 0, 0, 0.7) 0%,
      rgba(0, 0, 0, 0.4) 50%,
      rgba(0, 0, 0, 0.8) 100%
    ),
    radial-gradient(
      circle at 30% 20%,
      rgba(255, 255, 255, 0.1) 0%,
      transparent 50%
    );
  background-blend-mode: multiply, normal;
  z-index: -2;
}

.selected-map-card {
  background-image: var(--map-bg-url);
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.map-class-selector {
  display: flex;
  gap: 10px;
  background: rgba(255, 255, 255, 0.05);
  padding: 5px;
  border-radius: 25px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 10px rgba(0, 0, 0, 0.5);
}

.map-records-comparison {
  max-width: 800px;
  margin: 0 auto;
}

.map-records-table {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 16px;
  padding: 25px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.no-records {
  text-align: center;
  padding: 40px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.no-records p {
  color: var(--color-text-soft);
  font-size: 1.1rem;
  margin: 0;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.search-section {
  width: 100%;
  max-width: 400px;
  margin-top: 20px;
  margin-bottom: 20px;
}

.search-container {
  position: relative;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
  box-shadow: 0 0px 20px rgb(0, 0, 0, 0.25);
}

.search-icon {
  position: absolute;
  left: 16px;
  color: #888;
  z-index: 2;
}

.search-input {
  width: 100%;
  padding: 16px 16px 16px 50px;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(68, 68, 68, 0.3);
  border-radius: 12px;
  color: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  background: rgba(74, 111, 165, 0.8);
  box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.1);
}

.search-input::placeholder {
  color: #888;
}
.search-results-dropdown {
  position: relative;
  background: var(--color-box);
  border: 1px solid rgba(68, 68, 68, 0.3);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  width: 400px;
  max-height: 400px;
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
  font-weight: bold;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.search-results-dropdown li:hover {
  background: rgba(74, 111, 165, 0.8);
  transform: translateX(4px);
}

.search-results-dropdown li:last-child {
  margin-bottom: 0;
}

.search-results-dropdown h6 {
  margin: 12px 16px 8px;
  font-size: 12px;
  font-weight: 700;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 1px;
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

@media (max-width: 768px) {
  .ranks-cards {
    grid-template-columns: 1fr;
  }

  .rank-comparison {
    font-size: 1.1rem;
  }
  .player-info {
    flex-direction: column;
    text-align: center;
    gap: 8px;
  }

  .player-details h2 {
    font-size: 1rem;
  }

  .avatar-image,
  .avatar-placeholder {
    width: 40px;
    height: 40px;
  }
  .player-selection {
    flex-direction: column;
    gap: 20px;
  }

  .vs-indicator {
    order: 2;
  }

  .results-header {
    flex-direction: column;
    text-align: center;
  }

  .group-tables {
    grid-template-columns: 1fr;
  }

  .summary-cards {
    grid-template-columns: 1fr;
  }

  .comparison-values {
    font-size: 1.2rem;
  }
  .selected-map-card {
    width: 100%;
    min-width: 280px;
    padding: 25px;
    min-height: 100px;
  }

  .map-selection-info {
    flex-direction: column;
    gap: 20px;
  }

  .map-records-table {
    padding: 15px;
    margin: 0 -15px;
    border-radius: 0;
  }

  .map-records-comparison {
    margin: 0 -20px;
  }

  .comparison-table th,
  .comparison-table td {
    padding: 8px 6px;
    font-size: 0.85rem;
    max-width: none !important;
    width: auto !important;
  }

  .comparison-table th:first-child,
  .comparison-table td:first-child {
    min-width: 80px;
  }
}
</style>
