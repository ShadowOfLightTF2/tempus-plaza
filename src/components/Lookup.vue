<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container py-4 d-flex flex-column align-items-center">
      <div class="page-header">
        <h1 class="page-title">
          <span class="title-icon">🔍</span>
          Lookup Players and Maps
        </h1>
        <p class="page-subtitle">
          Search and filter through player or map records
        </p>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <SmartLink
        v-if="playerId"
        tag="div"
        :to="{ name: 'PlayerPage', params: { playerId: playerId } }"
        class="player-name-display"
      >
        <h2
          class="fancy-hover"
          v-html="sanitize(selectedPlayerName) || 'Selected Player'"
        ></h2>
      </SmartLink>
      <SmartLink
        v-else-if="mapId"
        tag="div"
        :to="{ name: 'MapPage', params: { mapId: mapId } }"
        class="map-name-display"
      >
        <h2
          class="fancy-hover"
          v-html="sanitize(selectedMapName) || 'Selected Map'"
        ></h2>
      </SmartLink>
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
              placeholder="Search for players or maps..."
              class="search-input"
            />
          </div>
          <div
            class="search-results-dropdown"
            v-if="
              searchQuery.trim() &&
              (showLoading ||
                (searchResults &&
                  (searchResults.players.length || searchResults.maps.length)))
            "
          >
            <div v-if="showLoading" class="loading-container">
              <div class="loading-spinner"></div>
              <span>Searching...</span>
            </div>
            <div v-else>
              <div v-if="searchResults.maps && searchResults.maps.length">
                <h6>Maps</h6>
                <ul>
                  <li
                    v-for="map in searchResults.maps"
                    :key="'map-' + map.id"
                    @click="selectMap(map.id, map.name)"
                    v-html="sanitize(map.name) || `Map ID: ${map.id}`"
                  ></li>
                </ul>
              </div>
              <div v-if="searchResults.players && searchResults.players.length">
                <h6>Players</h6>
                <ul>
                  <li
                    v-for="player in searchResults.players"
                    :key="'player-' + player.id"
                    @click="selectPlayer(player.id)"
                    v-html="sanitize(player.name) || `Player ID: ${player.id}`"
                  ></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div
          v-if="searchResults && searchResults.players.length"
          class="dropdown-overlay"
          @click="searchResults = null"
          style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 999;
            background: transparent;
          "
        ></div>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div class="filter-section">
        <div class="filter-content">
          <div class="filter-columns" style="flex-direction: column; gap: 20px">
            <div
              class="filter-row"
              style="display: flex; gap: 40px; justify-content: center"
            >
              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Soldier tiers</h6>
                  <div class="tier-filter-container">
                    <div class="tier-filters">
                      <label
                        v-for="tier in availableTiers"
                        :key="'soldier-tier-' + tier"
                        class="tier-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="tier"
                          v-model="selectedSoldierTiers"
                          @change="onFilterChange"
                        />
                        <span :class="`tier-badge tier-${tier}`">{{
                          tier
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Demoman tiers</h6>
                  <div class="tier-filter-container">
                    <div class="tier-filters">
                      <label
                        v-for="tier in availableTiers"
                        :key="'demo-tier-' + tier"
                        class="tier-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="tier"
                          v-model="selectedDemomanTiers"
                          @change="onFilterChange"
                        />
                        <span :class="`tier-badge tier-${tier}`">{{
                          tier
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              class="filter-row responsive-ratings-row"
              style="
                display: flex;
                gap: 40px;
                justify-content: center;
                align-items: flex-start;
                width: 100%;
              "
            >
              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Soldier ratings</h6>
                  <div class="rating-filter-container">
                    <div class="rating-filters">
                      <label
                        v-for="rating in availableRatings"
                        :key="'soldier-rating-' + rating"
                        class="rating-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="rating"
                          v-model="selectedSoldierRatings"
                          @change="onFilterChange"
                        />
                        <span :class="`rating-badge rating-${rating}`">{{
                          rating
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="filter-column intended-class-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Intended Class</h6>
                  <div class="intended-class-buttons">
                    <button
                      v-for="cls in availableIntendedClasses"
                      :key="cls.id"
                      @click="toggleIntendedClass(cls.id)"
                      :class="{
                        active: selectedIntendedClasses.includes(cls.id),
                      }"
                      class="intended-class-btn"
                    >
                      <img :src="cls.icon" :alt="cls.label" />
                    </button>
                  </div>
                </div>
              </div>
              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Demoman ratings</h6>
                  <div class="rating-filter-container">
                    <div class="rating-filters">
                      <label
                        v-for="rating in availableRatings"
                        :key="'demo-rating-' + rating"
                        class="rating-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="rating"
                          v-model="selectedDemomanRatings"
                          @change="onFilterChange"
                        />
                        <span :class="`rating-badge rating-${rating}`">{{
                          rating
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="filter-group">
            <h6 class="filter-title mb-2">Placement</h6>
            <div class="group-filter-container">
              <div class="group-filters">
                <label class="group-checkbox">
                  <input
                    type="checkbox"
                    value="WR"
                    v-model="selectedGroups"
                    @change="onFilterChange"
                  />
                  <span class="group-badge group-wr">WR</span>
                </label>
                <label class="group-checkbox">
                  <input
                    type="checkbox"
                    value="TT"
                    v-model="selectedGroups"
                    @change="onFilterChange"
                  />
                  <span class="group-badge group-tt">TT</span>
                </label>
                <label
                  v-for="group in availableGroups"
                  :key="'group-' + group"
                  class="group-checkbox"
                >
                  <input
                    type="checkbox"
                    :value="group"
                    v-model="selectedGroups"
                    @change="onFilterChange"
                  />
                  <span :class="`group-badge group-${group}`"
                    >G{{ group }}</span
                  >
                </label>
                <label class="group-checkbox">
                  <input
                    type="checkbox"
                    value="BT"
                    v-model="selectedGroups"
                    @change="onFilterChange"
                  />
                  <span class="group-badge group-bt">BT</span>
                </label>
              </div>
            </div>
          </div>
          <div class="filter-columns">
            <div class="filter-group">
              <h6 class="filter-title mb-2">Class</h6>
              <div class="class-filter-container">
                <div
                  v-for="classOption in ['soldier', 'demoman']"
                  :key="classOption"
                  class="class-checkbox"
                  :class="{ selected: selectedClasses.includes(classOption) }"
                  @click="toggleClass(classOption)"
                >
                  <span>{{ classOption }}</span>
                </div>
              </div>
            </div>
            <div class="filter-group">
              <h6 class="filter-title mb-2">Type</h6>
              <div class="type-filter-container">
                <div
                  v-for="typeOption in ['map', 'course', 'bonus']"
                  :key="typeOption"
                  class="type-checkbox"
                  :class="{ selected: selectedTypes.includes(typeOption) }"
                  @click="toggleType(typeOption)"
                >
                  <span>{{ typeOption }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="filter-actions">
            <div class="filter-group">
              <h6 class="filter-title mb-2">Status</h6>
              <div class="status-filter-container">
                <div
                  v-for="statusOption in ['completed', 'incomplete']"
                  :key="statusOption"
                  class="status-checkbox"
                  :class="{ selected: selectedStatus.includes(statusOption) }"
                  @click="toggleStatus(statusOption)"
                >
                  <span>{{ statusOption }}</span>
                </div>
              </div>
            </div>
            <div class="filter-group clear-filter">
              <button
                type="button"
                @click="clearAllFilters"
                class="btn btn-secondary clear-filters-btn"
              >
                Clear filters
              </button>
              <span class="filter-count"
                >Displaying {{ filteredSortedItems.length }} of
                {{ totalRecordsLength() }} records</span
              >
            </div>
          </div>
        </div>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div v-if="loading" class="text-center">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading records...</span>
        </div>
      </div>
      <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
      <div v-else>
        <div
          v-if="playerId != null || mapId != null"
          class="search-records-container"
        >
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
              <path d="M21 21l-4.35-4.35"></path>
            </svg>
            <input
              type="text"
              v-model="recordSearchQuery"
              :placeholder="playerId ? 'Search map...' : 'Search player...'"
              class="search-records-input"
            />
          </div>
        </div>
        <div v-if="playerId != null || mapId != null" class="table-container">
          <div class="table-responsive">
            <table class="table table-dark">
              <thead>
                <tr>
                  <th @click="setSortColumn('map')" class="sortable-header">
                    {{ playerId ? "Map" : "Player" }}
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'map'"
                    >
                      {{ sortDirection === "desc" ? "↓" : "↑" }}
                    </span>
                  </th>
                  <th>Type</th>
                  <th>Class</th>
                  <th @click="setSortColumn('tier')" class="sortable-header">
                    T
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'tier'"
                    >
                      {{ sortDirection === "desc" ? "↓" : "↑" }}
                    </span>
                  </th>
                  <th @click="setSortColumn('rating')" class="sortable-header">
                    R
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'rating'"
                    >
                      {{ sortDirection === "desc" ? "↓" : "↑" }}
                    </span>
                  </th>
                  <th
                    @click="setSortColumn('duration')"
                    class="sortable-header"
                  >
                    Time
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'duration'"
                    >
                      {{ sortDirection === "desc" ? "↓" : "↑" }}
                    </span>
                  </th>
                  <th @click="setSortColumn('rank')" class="sortable-header">
                    Rank
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'rank'"
                    >
                      {{ sortDirection === "desc" ? "↓" : "↑" }}
                    </span>
                  </th>
                  <th
                    @click="setSortColumn('completion')"
                    class="sortable-header"
                  >
                    Completion
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'completion'"
                    >
                      {{ sortDirection === "desc" ? "↓" : "↑" }}
                    </span>
                  </th>
                  <th
                    @click="setSortColumn('percentage')"
                    class="sortable-header"
                  >
                    Percentile
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'percentage'"
                    >
                      {{ sortDirection === "desc" ? "↓" : "↑" }}
                    </span>
                  </th>
                  <th @click="setSortColumn('points')" class="sortable-header">
                    Points
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'points'"
                    >
                      {{ sortDirection === "desc" ? "↓" : "↑" }}
                    </span>
                  </th>
                  <th @click="setSortColumn('time')" class="sortable-header">
                    Date
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'time'"
                    >
                      {{ sortDirection === "desc" ? "↓" : "↑" }}
                    </span>
                  </th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="record in filteredRecords"
                  :key="record.id"
                  class="fade-in"
                >
                  <SmartLink
                    v-if="playerId"
                    tag="td"
                    :to="{
                      name: 'MapPage',
                      params: { mapId: record.map_id },
                    }"
                    class="fancy-hover"
                  >
                    {{ record.map_name }}
                  </SmartLink>
                  <SmartLink
                    v-else-if="mapId"
                    tag="td"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: record.player_id },
                    }"
                    class="fancy-hover"
                  >
                    {{ record.name || "Unknown Player" }}
                  </SmartLink>
                  <td>
                    {{ getRecordType(record.type) }}{{ formatIndex(record) }}
                  </td>
                  <td>
                    <img
                      :src="`/icons/${record.class}.png`"
                      :alt="`${record.class}`"
                      class="class-icon"
                    />
                  </td>
                  <td>T{{ record.tier }}</td>
                  <td>R{{ record.rating }}</td>
                  <td>
                    {{
                      record.duration !== null
                        ? formatDuration(record.duration)
                        : ""
                    }}
                  </td>
                  <td :class="getRankColorClass(record.placement)">
                    {{ record.rank !== null ? record.rank : "" }}
                    {{
                      record.placement !== null
                        ? formatPlacement(record.placement)
                        : ""
                    }}
                  </td>
                  <td>{{ record.completion_count }}</td>
                  <td>
                    {{
                      record.rank && record.completion_count
                        ? (
                            (record.rank / record.completion_count) *
                            100
                          ).toFixed(1) + "%"
                        : ""
                    }}
                  </td>
                  <td>{{ record.points !== null ? record.points : "" }}</td>
                  <td class="text-small">
                    {{
                      record.date !== null
                        ? formatDate(new Date(record.date * 1000))
                        : ""
                    }}
                  </td>
                  <td class="text-center">
                    {{ record.duration !== null ? "✓" : "X" }}
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="maps-footer">
              <button
                v-if="displayCount < filteredSortedItems.length"
                @click="showMore"
                class="btn btn-dark update-button show-more-btn"
              >
                Show more
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DOMPurify from "dompurify";
import { formatDuration } from "@/utils/calculations.js";
import { useHead } from "@vueuse/head";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "PlayerRecords",
  props: {
    playerId: {
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
      title: "Tempus Plaza | Lookup",
    });
  },
  data: () => ({
    playerId: null,
    playerName: null,
    selectedPlayerName: null,
    records: [],
    loading: false,
    error: null,
    selectedClasses: [],
    selectedTypes: [],
    selectedStatus: ["completed"],
    selectedSoldierTiers: [],
    selectedSoldierRatings: [],
    selectedDemomanTiers: [],
    selectedDemomanRatings: [],
    availableTiers: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 0],
    availableRatings: [1, 2, 3, 4],
    selectedIntendedClasses: [],
    availableIntendedClasses: [
      { id: 3, label: "Soldier", icon: "/icons/soldier.png" },
      { id: 4, label: "Demoman", icon: "/icons/demoman.png" },
    ],
    availableGroups: [1, 2, 3, 4, 5],
    selectedGroups: [],
    sortByCategory: "time",
    sortDirection: "asc",
    recordSearchQuery: "",
    searchQuery: "",
    searchResults: null,
    mapId: null,
    mapInfo: null,
    selectedMapName: null,
    searchMode: "both",
    cachedMapRecords: {
      soldier_map: [],
      demoman_map: [],
      soldier_course: [],
      demoman_course: [],
      soldier_bonus: [],
      demoman_bonus: [],
    },
    showLoading: false,
    debounceTimer: null,
    cachedRecords: {
      records: [],
      courseRecords: [],
      bonusRecords: [],
    },
    validPlacements: [],
    displayCount: 300,
  }),
  computed: {
    filteredSortedItems() {
      let recordsToFilter = [];

      if (this.selectedTypes.length === 0) {
        recordsToFilter = [
          ...this.cachedRecords.records,
          ...this.cachedRecords.courseRecords,
          ...this.cachedRecords.bonusRecords,
        ];
      } else {
        if (this.selectedTypes.includes("map")) {
          recordsToFilter.push(...this.cachedRecords.records);
        }
        if (this.selectedTypes.includes("course")) {
          recordsToFilter.push(...this.cachedRecords.courseRecords);
        }
        if (this.selectedTypes.includes("bonus")) {
          recordsToFilter.push(...this.cachedRecords.bonusRecords);
        }
      }

      const filtered = recordsToFilter.filter((record) => {
        if (
          this.selectedClasses.length > 0 &&
          !this.selectedClasses.includes(record.class)
        ) {
          return false;
        }

        if (this.selectedStatus.length > 0) {
          const isComplete = record.duration !== null;
          const shouldShowComplete = this.selectedStatus.includes("completed");
          const shouldShowIncomplete =
            this.selectedStatus.includes("incomplete");

          if (isComplete && !shouldShowComplete) {
            return false;
          }
          if (!isComplete && !shouldShowIncomplete) {
            return false;
          }
        }

        if (record.class === "soldier") {
          if (
            this.selectedSoldierTiers.length > 0 &&
            !this.selectedSoldierTiers.includes(record.tier)
          ) {
            return false;
          }
          if (
            this.selectedSoldierRatings.length > 0 &&
            !this.selectedSoldierRatings.includes(record.rating)
          ) {
            return false;
          }
        } else if (record.class === "demoman") {
          if (
            this.selectedDemomanTiers.length > 0 &&
            !this.selectedDemomanTiers.includes(record.tier)
          ) {
            return false;
          }
          if (
            this.selectedDemomanRatings.length > 0 &&
            !this.selectedDemomanRatings.includes(record.rating)
          ) {
            return false;
          }
        }

        if (this.selectedGroups.length > 0) {
          let shouldInclude = false;
          if (
            this.selectedGroups.includes("BT") &&
            record.rank === record.completion_count
          ) {
            shouldInclude = true;
          }
          if (
            this.validPlacements.length > 0 &&
            this.validPlacements.includes(record.placement)
          ) {
            shouldInclude = true;
          }

          if (!shouldInclude) {
            return false;
          }
        }

        if (this.selectedIntendedClasses.length > 0) {
          if (
            this.selectedIntendedClasses.includes(3) &&
            this.selectedIntendedClasses.includes(4)
          ) {
            if (record.intended_class !== 5) {
              return false;
            }
          } else {
            if (
              !this.selectedIntendedClasses.includes(record.intended_class) &&
              record.intended_class !== 5
            ) {
              return false;
            }
          }
        }

        if (this.recordSearchQuery) {
          const query = this.recordSearchQuery.toLowerCase();
          if (this.playerId) {
            if (!record.map_name.toLowerCase().includes(query)) {
              return false;
            }
          } else if (this.mapId) {
            if (!record.name || !record.name.toLowerCase().includes(query)) {
              return false;
            }
          }
        }
        return true;
      });

      return filtered.sort((a, b) => {
        let comparison = 0;

        switch (this.sortByCategory) {
          case "time":
            comparison = b.date - a.date;
            break;
          case "rank":
            comparison = a.rank - b.rank;
            break;
          case "points":
            comparison = b.points - a.points;
            break;
          case "percentage":
            comparison =
              a.rank / a.completion_count - b.rank / b.completion_count;
            break;
          case "duration":
            comparison = a.duration - b.duration;
            break;
          case "tier":
            comparison = a.tier - b.tier;
            break;
          case "rating":
            comparison = a.rating - b.rating;
            break;
          case "completion":
            comparison = a.completion_count - b.completion_count;
            break;
          case "map":
            comparison = a.map_name.localeCompare(b.map_name);
            break;
          default:
            comparison = b.date - a.date;
        }

        return this.sortDirection === "desc" ? -comparison : comparison;
      });
    },
    filteredRecords() {
      return this.filteredSortedItems.slice(0, this.displayCount);
    },
  },
  watch: {
    playerId(newPlayerId) {
      if (newPlayerId) {
        this.fetchRecords();
      }
    },
    mapId(newMapId) {
      if (newMapId) {
        this.fetchMapRecords();
      }
    },
    selectedGroups: {
      handler() {
        const placements = [];
        this.selectedGroups.forEach((group) => {
          if (group === "WR") {
            placements.push(1);
          } else if (group === "TT") {
            placements.push(...[2, 3, 4, 5, 6, 7, 8, 9, 10]);
          } else {
            placements.push(10 + Number(group));
          }
        });
        this.validPlacements = placements;
      },
      immediate: true,
    },
    $route(to) {
      if (to.params.playerId) {
        this.playerId = to.params.playerId;
      }
    },
  },
  async mounted() {
    try {
      const response = await fetch(`${API_BASE_URL}/api/get-user`, {
        credentials: "include",
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      });

      if (response.ok) {
        const user = await response.json();
        this.playerId = user.data?.playerid || null;
        this.playerName = user.data?.name || null;
      } else {
        console.log(
          "User not logged in or auth failed:",
          response.status,
          response.statusText
        );
        this.playerId = null;
        this.playerName = null;
      }
    } catch (error) {
      console.error("Error fetching user data:", error);
      this.playerId = null;
      this.playerName = null;
    }
    if (this.$route.params.playerId) {
      this.playerId = this.$route.params.playerId;
      this.mapId = null;
      this.selectedMapName = null;
      await this.findPlayerName(this.playerId);
      await this.fetchRecords();
    } else if (this.$route.params.mapId) {
      this.mapId = this.$route.params.mapId;
      this.playerId = null;
      this.selectedPlayerName = null;
      await this.findMapName(this.mapId);
      await this.fetchMapRecords();
    } else if (
      this.playerId &&
      !this.$route.params.playerId &&
      !this.$route.params.mapId
    ) {
      this.selectedPlayerName = this.playerName;
      this.$router.push({
        name: "LookupPlayer",
        params: { playerId: this.playerId },
      });
    }

    if (this.playerId && !this.$route.params.mapId) {
      await this.fetchRecords();
    }
  },
  methods: {
    async fetchMapRecords() {
      this.loading = true;
      this.error = null;

      const mapId = this.mapId;
      if (!mapId) {
        this.error = "Please select a map first.";
        this.loading = false;
        return;
      }

      try {
        // Fetch both records and map info
        const [recordsResponse, infoResponse] = await Promise.all([
          fetch(`${API_BASE_URL}/maps/${mapId}/all-records`),
          fetch(`${API_BASE_URL}/maps/${mapId}/all-info`),
        ]);

        if (!recordsResponse.ok) {
          if (recordsResponse.status === 404) {
            throw new Error("Map not found");
          } else if (recordsResponse.status === 403) {
            throw new Error("Access denied");
          } else {
            throw new Error(
              `Failed to fetch records (${recordsResponse.status})`
            );
          }
        }

        if (!infoResponse.ok) {
          throw new Error(`Failed to fetch map info (${infoResponse.status})`);
        }

        const [mapRecords, mapInfoData] = await Promise.all([
          recordsResponse.json(),
          infoResponse.json(),
        ]);

        this.cachedMapRecords = mapRecords;
        this.mapInfo = mapInfoData;

        // Convert to consistent format with proper map info
        this.cachedRecords = {
          records: [
            ...(mapRecords.soldier_map || []).map((r) =>
              this.convertMapRecord(r, "soldier", "map")
            ),
            ...(mapRecords.demoman_map || []).map((r) =>
              this.convertMapRecord(r, "demoman", "map")
            ),
          ],
          courseRecords: [
            ...(mapRecords.soldier_course || []).map((r) =>
              this.convertMapRecord(r, "soldier", "course")
            ),
            ...(mapRecords.demoman_course || []).map((r) =>
              this.convertMapRecord(r, "demoman", "course")
            ),
          ],
          bonusRecords: [
            ...(mapRecords.soldier_bonus || []).map((r) =>
              this.convertMapRecord(r, "soldier", "bonus")
            ),
            ...(mapRecords.demoman_bonus || []).map((r) =>
              this.convertMapRecord(r, "demoman", "bonus")
            ),
          ],
        };
      } catch (error) {
        this.error = error.message || "Error fetching map records.";
        console.error("Error fetching map records:", error);

        this.cachedMapRecords = {
          soldier_map: [],
          demoman_map: [],
          soldier_course: [],
          demoman_course: [],
          soldier_bonus: [],
          demoman_bonus: [],
        };
        this.cachedRecords = {
          records: [],
          courseRecords: [],
          bonusRecords: [],
        };
        this.mapInfo = null;
      } finally {
        this.loading = false;
      }
    },
    convertMapRecord(record, className, type) {
      let tierInfo, ratingInfo, completionCount, intendedClass;

      if (this.mapInfo) {
        if (type === "map") {
          tierInfo =
            className === "soldier"
              ? this.mapInfo.map.soldier_tier
              : this.mapInfo.map.demoman_tier;
          ratingInfo =
            className === "soldier"
              ? this.mapInfo.map.soldier_rating
              : this.mapInfo.map.demoman_rating;
          completionCount =
            className === "soldier"
              ? this.mapInfo.map.soldier_completion_count
              : this.mapInfo.map.demoman_completion_count;
          intendedClass = this.mapInfo.map.intended_class;
        } else if (type === "course") {
          const course = this.mapInfo.courses?.find(
            (c) => c.index === record.index
          );
          if (course) {
            tierInfo =
              className === "soldier"
                ? course.soldier_tier
                : course.demoman_tier;
            ratingInfo =
              className === "soldier"
                ? course.soldier_rating
                : course.demoman_rating;
            completionCount =
              className === "soldier"
                ? course.soldier_completion_count
                : course.demoman_completion_count;
          }
        } else if (type === "bonus") {
          const bonus = this.mapInfo.bonuses?.find(
            (b) => b.index === record.index
          );
          if (bonus) {
            tierInfo =
              className === "soldier" ? bonus.soldier_tier : bonus.demoman_tier;
            ratingInfo =
              className === "soldier"
                ? bonus.soldier_rating
                : bonus.demoman_rating;
            completionCount =
              className === "soldier"
                ? bonus.soldier_completion_count
                : bonus.demoman_completion_count;
          }
        }
      }

      return {
        ...record,
        class: className,
        type: type,
        map_name: this.selectedMapName,
        map_id: this.mapId,
        tier: tierInfo || null,
        rating: ratingInfo || null,
        completion_count: completionCount || null,
        intended_class: intendedClass || null,
        name: record.name || null,
      };
    },
    async findMapName(mapId) {
      try {
        const response = await fetch(`${API_BASE_URL}/maps/${mapId}/all-info`);
        const data = await response.json();
        this.selectedMapName = data.map?.name || "Unknown Map";
        this.mapInfo = data;
      } catch (error) {
        console.error("Error fetching map name:", error);
        this.selectedMapName = "Unknown Map";
        this.mapInfo = null;
      }
    },
    setSortColumn(column) {
      if (this.sortByCategory === column) {
        this.sortDirection = this.sortDirection === "desc" ? "asc" : "desc";
      } else {
        this.sortByCategory = column;
        this.sortDirection = "asc";
      }
    },
    onFilterChange() {
      // This method can be used to trigger any additional filter change logic
    },
    toggleClass(classOption) {
      if (this.selectedClasses.includes(classOption)) {
        this.selectedClasses = this.selectedClasses.filter(
          (c) => c !== classOption
        );
      } else {
        this.selectedClasses.push(classOption);
      }
      this.onFilterChange();
    },
    toggleType(typeOption) {
      if (this.selectedTypes.includes(typeOption)) {
        this.selectedTypes = this.selectedTypes.filter((t) => t !== typeOption);
      } else {
        this.selectedTypes.push(typeOption);
      }
      this.onFilterChange();
    },
    toggleStatus(statusOption) {
      if (this.selectedStatus.includes(statusOption)) {
        this.selectedStatus = this.selectedStatus.filter(
          (s) => s !== statusOption
        );
      } else {
        this.selectedStatus.push(statusOption);
      }
      this.onFilterChange();
    },
    toggleIntendedClass(clsId) {
      if (this.selectedIntendedClasses.includes(clsId)) {
        this.selectedIntendedClasses = this.selectedIntendedClasses.filter(
          (id) => id !== clsId
        );
      } else {
        this.selectedIntendedClasses.push(clsId);
      }
      this.onFilterChange();
    },
    formatPlacement(placement) {
      if (placement <= 10) {
        return "";
      } else if (placement <= 15) {
        return "(G" + (placement - 10) + ")";
      }
      return placement;
    },
    async findPlayerName(playerId) {
      const response = await fetch(`${API_BASE_URL}/players/${playerId}`);
      const data = await response.json();
      this.selectedPlayerName = data[0].name;
    },
    formatIndex(record) {
      const type = record.type;
      if (type === "map") return "";
      return `${type.charAt(0).toUpperCase()}${record.index}`;
    },
    getRecordType(type) {
      const firstLetter = type.slice(0, 1).toUpperCase();
      switch (firstLetter) {
        case "M":
          return "🌍";
        case "C":
          return "🚩";
        case "B":
          return "⭐";
        default:
          return "";
      }
    },
    sanitize(str) {
      return DOMPurify.sanitize(str || "");
    },
    showMore() {
      this.displayCount += 300;
    },
    getRankColorClass(placement) {
      if (placement === 1) return "placement-rank-gold";
      else if (placement === 2) return "placement-rank-silver";
      else if (placement === 3) return "placement-rank-bronze";
      else if (placement >= 4 && placement <= 10) return "placement-rank-tt";
      else if (placement === 11) return "placement-group-1";
      else if (placement === 12) return "placement-group-2";
      else if (placement === 13) return "placement-group-3";
      else if (placement === 14) return "placement-group-4";
      else if (placement === 15) return "placement-group-5";
      return "";
    },
    totalRecordsLength() {
      if (this.playerId || this.mapId) {
        return (
          this.cachedRecords.records.length +
          this.cachedRecords.courseRecords.length +
          this.cachedRecords.bonusRecords.length
        );
      }
      return 0;
    },
    formatDate(date) {
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const year = date.getFullYear();

      return `${year}/${month}/${day}`;
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
            const [playersResponse, mapsResponse] = await Promise.all([
              fetch(`${API_BASE_URL}/search/players`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ query: this.searchQuery }),
              }),
              fetch(`${API_BASE_URL}/search/maps`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ query: this.searchQuery }),
              }),
            ]);

            if (!playersResponse.ok || !mapsResponse.ok) {
              throw new Error("Failed to search results");
            }

            const [playersData, mapsData] = await Promise.all([
              playersResponse.json(),
              mapsResponse.json(),
            ]);

            const players =
              playersData.players && playersData.players.length > 10
                ? playersData.players.slice(0, 10)
                : playersData.players || [];
            const maps =
              mapsData.maps && mapsData.maps.length > 3
                ? mapsData.maps.slice(0, 3)
                : mapsData.maps || [];

            this.searchResults = { players, maps };
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
    selectPlayer(playerId) {
      this.playerId = playerId;
      this.mapId = null;
      this.selectedMapName = null;
      this.selectedPlayerName = this.searchResults.players.find(
        (player) => player.id === playerId
      ).name;
      this.searchQuery = "";
      this.searchResults = null;
      this.$router.push({
        name: "LookupPlayer",
        params: { playerId: playerId },
      });
    },
    selectMap(mapId, mapName) {
      this.mapId = mapId;
      this.playerId = null;
      this.selectedPlayerName = null;
      this.selectedMapName = mapName;
      this.searchQuery = "";
      this.searchResults = null;
      this.$router.push({
        name: "LookupMap",
        params: { mapId: mapId },
      });
    },
    selectSortOption(value) {
      this.sortByCategory = value;
    },
    toggleSortDirection() {
      this.sortDirection = this.sortDirection === "desc" ? "asc" : "desc";
    },
    async fetchRecords() {
      this.loading = true;
      this.error = null;

      const playerId = this.playerId;
      if (!playerId) {
        this.error = "Please select a player first.";
        this.loading = false;
        return;
      }

      try {
        const response = await fetch(
          `${API_BASE_URL}/players/${playerId}/all-records`
        );

        if (!response.ok) {
          if (response.status === 404) {
            throw new Error("Player not found");
          } else if (response.status === 403) {
            throw new Error("Access denied");
          } else {
            throw new Error(`Failed to fetch records (${response.status})`);
          }
        }

        const allRecords = await response.json();

        if (!Array.isArray(allRecords)) {
          console.warn("Expected array but got:", typeof allRecords);
          throw new Error("Invalid data format received");
        }

        this.cachedRecords = {
          records: allRecords.filter((record) => record.type === "map"),
          courseRecords: allRecords.filter(
            (record) => record.type === "course"
          ),
          bonusRecords: allRecords.filter((record) => record.type === "bonus"),
          allRecords: allRecords,
        };

        this.records = allRecords;
      } catch (error) {
        this.error = error.message || "Error fetching records.";
        console.error("Error fetching records:", error);

        this.cachedRecords = {
          records: [],
          courseRecords: [],
          bonusRecords: [],
          allRecords: [],
        };
        this.records = [];
      } finally {
        this.loading = false;
      }
    },
    clearAllFilters() {
      this.selectedClasses = [];
      this.selectedTypes = [];
      this.selectedStatus = ["completed"];
      this.selectedSoldierTiers = [];
      this.selectedSoldierRatings = [];
      this.selectedDemomanTiers = [];
      this.selectedDemomanRatings = [];
      this.selectedIntendedClasses = [];
      this.selectedGroups = [];
      this.sortByCategory = "time";
      this.sortDirection = "desc";
      this.recordSearchQuery = "";
    },
    formatDuration(unixTimestamp) {
      return formatDuration(unixTimestamp);
    },
  },
  beforeDestroy() {
    clearTimeout(this.debounceTimer);
  },
};
</script>

<style scoped>
.sortable-header {
  cursor: pointer;
  user-select: none;
  transition: background-color 0.2s ease;
  position: relative;
}

.sortable-header:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.sort-indicator {
  margin-left: 5px;
  font-size: 12px;
  opacity: 0.8;
}

.sortable-header:active {
  background-color: rgba(255, 255, 255, 0.2);
}
.clickable {
  cursor: pointer;
  color: var(--color-text-clickable) !important;
}

.text-small {
  font-size: 0.8rem;
}

.search-section {
  width: 100%;
  max-width: 400px;
  margin-top: 20px;
}

.search-container {
  position: relative;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
  box-shadow: 0 0px 20px rgb(0, 0, 0, 0.5);
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

.filter-section {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  width: fit-content;
  max-width: 100%;
}

.filter-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
}

.filter-columns {
  display: flex;
  gap: 48px;
  align-items: flex-start;
}

.filter-group {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.clear-filter {
  padding-left: 50px;
}

.filter-count {
  color: white;
  font-weight: bold;
  margin-top: 5px;
}

.filter-title {
  color: #ffffff;
  font-weight: bold;
  margin-bottom: 10px;
  text-align: center;
  text-transform: uppercase;
}

.class-filter-container,
.type-filter-container,
.status-filter-container {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
}

.class-checkbox,
.type-checkbox,
.status-checkbox {
  display: inline-block;
  margin: 4px;

  border: 2px solid var(--color-border-soft);
  color: white;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 6px;
  padding: 8px 16px;
  cursor: pointer;
  font-weight: bold;
  font-size: 13px;

  text-transform: capitalize;
  transition: all 0.3s ease;
  min-width: 80px;
  text-align: center;
}

.class-checkbox.selected,
.type-checkbox.selected,
.status-checkbox.selected {
  background: rgba(74, 111, 165, 0.5);
  border-color: var(--color-primary);
  color: white;
}

.class-checkbox:hover,
.type-checkbox:hover,
.status-checkbox:hover {
  background: rgba(74, 111, 165, 0.3);
  border-color: var(--color-primary);
}

.clear-filters-btn {
  margin-right: 10px;
}

.sort-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.sort-toggle-btn {
  padding: 6px 10px;
  min-width: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: bold;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.sort-toggle-btn:hover {
  background-color: rgba(74, 111, 165, 0.8);
  transform: scale(1.05);
}

.sort-toggle-btn span {
  line-height: 1;
}

.search-results-dropdown {
  position: absolute;
  background: var(--color-box);
  border: 1px solid rgba(68, 68, 68, 0.3);
  border-radius: 12px;
  box-shadow: 0 0 0 1px var(--color-box, #444);
  width: 100%;
  max-height: 400px;
  overflow-y: auto;
  z-index: 1000;
  margin-top: 8px;
}

.search-results-dropdown {
  position: absolute;
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

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.form-select {
  padding: 12px 16px;
  background: rgba(119, 119, 119, 0.05);
  border: 2px solid rgba(68, 68, 68, 0.3);
  border-radius: 8px;
  color: #ffffff;
  font-weight: bold;
  transition: all 0.3s ease;
}

.form-select:focus {
  background: rgba(74, 111, 165, 0.8);
}

.form-option:hover {
  background: red !important;
}

.filter-actions {
  display: flex;
  align-items: center;
  gap: 20px;
}

.btn-secondary {
  border: 1px solid var(--color-dark);
  font-weight: bold;
}

.btn-secondary:hover {
  border: 1px solid var(--color-dark);
  background: rgba(74, 111, 165, 0.8);
}

.tier-filter-container,
.rating-filter-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
}

.tier-filters,
.rating-filters {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.tier-checkbox,
.rating-checkbox {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin: 0;
}

.tier-checkbox:hover,
.rating-checkbox:hover,
.group-checkbox:hover {
  border-radius: 4px;
  border-color: var(--color-border, #444);
  box-shadow: 0 0 0 1px var(--color-border, #444);
}

.tier-checkbox input,
.rating-checkbox input {
  display: none;
}

.tier-checkbox input:checked + .tier-badge,
.rating-checkbox input:checked + .rating-badge {
  border-color: var(--color-border, #444);
  box-shadow: 0 0 0 1px var(--color-border, #444);
}

.tier-badge,
.rating-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 12px;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  min-width: 28px;
  text-align: center;
}

.tier-badge.tier-0,
.tier-color.tier-0 {
  background: var(--color-dark);
  color: var(--color-text);
}

.tier-badge.tier-1,
.tier-color.tier-1 {
  background: #6ed0f681;
  color: var(--color-text);
}

.tier-badge.tier-2,
.tier-color.tier-2 {
  background: #56b3e977;
  color: var(--color-text);
}

.tier-badge.tier-3,
.tier-color.tier-3 {
  background: #45b8ad6e;
  color: var(--color-text);
}

.tier-badge.tier-4,
.tier-color.tier-4 {
  background: #65c18b75;
  color: var(--color-text);
}

.tier-badge.tier-5,
.tier-color.tier-5 {
  background: #a3d97775;
  color: var(--color-text);
}

.tier-badge.tier-6,
.tier-color.tier-6 {
  background: #f3e68370;
  color: var(--color-text);
}

.tier-badge.tier-7,
.tier-color.tier-7 {
  background: #f6c26767;
  color: var(--color-text);
}

.tier-badge.tier-8,
.tier-color.tier-8 {
  background: #f08d5b65;
  color: var(--color-text);
}

.tier-badge.tier-9,
.tier-color.tier-9 {
  background: #e6695e6b;
  color: var(--color-text);
}

.tier-badge.tier-10,
.tier-color.tier-10 {
  background: #d6454569;
  color: var(--color-text);
}

.rating-badge.rating-1,
.rating-color.rating-1 {
  background: #94c47d69;
  color: var(--color-text);
}

.rating-badge.rating-2,
.rating-color.rating-2 {
  background: #abd0996b;
  color: var(--color-text);
}

.rating-badge.rating-3,
.rating-color.rating-3 {
  background: #c3b29365;
  color: var(--color-text);
}

.rating-badge.rating-4,
.rating-color.rating-4 {
  background: #e0666669;
  color: var(--color-text);
}

.group-badge.group-wr {
  background: #ffd700 !important;
  color: var(--color-dark) !important;
}

.group-badge.group-tt {
  background: #30869b !important;
  color: var(--color-dark) !important;
}

.group-badge.group-bt {
  background: #383838 !important;
  color: var(--color-text) !important;
}

.group-badge.group-1 {
  background: #ff9797af !important;
  color: var(--color-dark) !important;
}

.group-badge.group-2 {
  background: #f7cf84 !important;
  color: var(--color-dark) !important;
}

.group-badge.group-3 {
  background: #d27d2dbb !important;
  color: var(--color-dark) !important;
}

.group-badge.group-4 {
  background: #b3b3b3ce !important;
  color: var(--color-dark) !important;
}

.group-badge.group-5 {
  background: #e4e4e4ce !important;
  color: var(--color-dark) !important;
}

.placement-rank-gold {
  color: #ffd700 !important;
}

.placement-rank-silver {
  color: #c0c0c0 !important;
}

.placement-rank-bronze {
  color: #cd7f32 !important;
}

.placement-rank-tt {
  color: #30869b !important;
}

.placement-group-1 {
  color: #ff9797af !important;
}

.placement-group-2 {
  color: #f7cf84 !important;
}

.placement-group-3 {
  color: #d27d2dbb !important;
}

.placement-group-4 {
  color: #b3b3b3ce !important;
}

.placement-group-5 {
  color: #e4e4e4ce !important;
}

.group-filter-container,
.rating-filter-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
}

.group-filters,
.rating-filters {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.group-checkbox,
.rating-checkbox {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin: 0;
}

.group-checkbox input,
.rating-checkbox input {
  display: none;
}

.group-checkbox input:checked + .group-badge,
.rating-checkbox input:checked + .rating-badge {
  border-color: var(--color-border, #444);
  box-shadow: 0 0 0 1px var(--color-border, #444);
}

.group-badge,
.rating-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 12px;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  min-width: 28px;
  text-align: center;
}

.group-badge.group-1,
.group-color.group-1 {
  background: #6ed0f681;
  color: var(--color-text);
}

.group-badge.group-2,
.group-color.group-2 {
  background: #56b3e977;
  color: var(--color-text);
}

.group-badge.group-3,
.group-color.group-3 {
  background: #45b8ad6e;
  color: var(--color-text);
}

.group-badge.group-4,
.group-color.group-4 {
  background: #65c18b75;
  color: var(--color-text);
}

.group-badge.group-5,
.group-color.group-5 {
  background: #a3d97775;
  color: var(--color-text);
}

.dropdown-menu {
  background-color: var(--color-row);
  border: 1px solid var(--color-border);
}

.dropdown-item {
  font-weight: bold;
  color: var(--color-text);
  background-color: var(--color-row);
}

.dropdown-item:hover {
  color: var(--color-text);
  background: rgba(74, 111, 165, 0.8);
}

.class-icon {
  width: 25px;
  height: 25px;
  margin: 8px;
}

.search-records-container {
  width: 100%;
  max-width: 500px;
  margin: 0 auto 25px auto;
  display: flex;
  justify-content: center;
}

.search-records-input {
  width: 100%;
  padding: 12px 12px 12px 50px;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(68, 68, 68, 0.3);
  border-radius: 8px;
  color: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
}

.search-records-input:focus {
  outline: none;
  background: rgba(74, 111, 165, 0.8);
  box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.1);
}

.search-records-input::placeholder {
  color: #888;
}

.table-responsive {
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  border: 1px solid var(--color-border-soft);
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 0px;
  width: 100%;
}

.table-dark {
  margin: 0;
}

.table-dark thead {
  border-bottom: 1px solid var(--color-border-soft) !important;
}

.table-dark th {
  color: var(--color-text);
  background: rgba(74, 111, 165, 0.3) !important;
  text-align: center;
  font-weight: bold;
  padding: 16px 12px;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  vertical-align: middle;
}

.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  font-weight: 500;
  padding: 6px 12px;
  text-align: center;
  vertical-align: middle;
}

.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}

.spinner-border {
  color: white;
  font-weight: bold;
  width: 3rem;
  height: 3rem;
}

.alert-danger {
  background: rgba(220, 53, 69, 0.1);
  border: 1px solid rgba(220, 53, 69, 0.3);
  border-radius: 8px;
  color: #ff6b6b;
}

.player-name-display,
.map-name-display {
  text-align: center;
}

.player-name-display h2,
.map-name-display h2 {
  color: var(--color-text);
  font-size: 2.5rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  margin: 0 auto;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.show-more-btn {
  background: rgba(74, 111, 165, 0.8) !important;
  font-weight: bold;
  width: 100%;
  border-radius: 0 0 10px 10px;
}

.show-more-btn:hover {
  background-color: var(--color-row) !important;
}

@media (max-width: 767.98px) {
  .clear-filter {
    padding: none;
    margin: none;
    margin-top: 10px;
    padding-left: 0px;
  }

  .container {
    padding-left: 15px;
    padding-right: 15px;
    max-width: 100%;
    overflow-x: hidden;
  }

  .filter-section {
    padding: 15px;
    width: 100%;
    margin: 0;
  }

  .filter-columns {
    flex-direction: column;
    gap: 16px;
    width: 100%;
  }

  .filter-group {
    width: 100%;
  }

  .tier-filters,
  .rating-filters,
  .group-filters {
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
  }

  /* Fix both search sections to center properly */
  .search-section {
    width: 100%;
    max-width: 100%;
    margin: 20px 0;
    padding: 0;
    display: flex;
    justify-content: center;
  }

  .search-container {
    width: 100%;
    max-width: 400px;
  }

  .search-input-wrapper {
    width: 100%;
  }

  .search-input {
    width: 100%;
    min-width: 100%;
    padding: 16px 16px 16px 50px;
    box-sizing: border-box;
  }

  .search-records-container {
    width: 100%;
    max-width: 100%;
    margin: 10px 0;
    padding: 0;
    display: flex;
    justify-content: center;
  }

  .search-records-container .search-input-wrapper {
    width: 100%;
    max-width: 500px;
  }

  .search-records-input {
    width: 100%;
    padding: 12px 12px 12px 50px;
    box-sizing: border-box;
  }

  .table-responsive {
    width: 100%;
    overflow-x: auto;
  }

  .table-dark td {
    white-space: nowrap;
  }

  .maps-footer {
    width: 100%;
    min-width: 800px;
    padding: 0;
  }

  .filter-actions {
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  .btn-secondary {
    width: 100%;
  }

  .show-more-btn {
    background: rgba(74, 111, 165, 0.8) !important;
    font-weight: bold;
    width: 100%;
    border-radius: 0 0 10px 10px;
  }

  .player-name-display h2 {
    font-size: 1.5rem;
  }

  .responsive-ratings-row {
    flex-direction: column !important;
    gap: 20px !important;
    align-items: center !important;
  }

  .responsive-ratings-row .filter-column {
    width: 100%;
    max-width: 300px;
  }

  .intended-class-buttons {
    gap: 10px;
  }

  .intended-class-btn {
    width: 28px;
    height: 28px;
  }

  .intended-class-btn img {
    width: 24px;
    height: 24px;
  }

  .button-group {
    flex-direction: row;
    justify-content: center;
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    white-space: nowrap;
  }

  .toggle-btn {
    justify-content: center;
    flex: 1;
    min-width: 100px;
  }

  .table-header-content {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }

  .table-container {
    width: 95vw;
    overflow-x: auto;
    border-radius: 0;
  }

  .table {
    width: max-content;
    min-width: 100%;
  }

  .avatar {
    width: 20px;
    height: 20px;
  }

  .table-header-icon {
    font-size: 1.5rem;
  }

  .filter-column {
    width: 100%;
  }

  .picker-controls {
    flex-direction: column;
    align-items: center;
  }
}
.intended-class-buttons {
  display: flex;
  gap: 15px;
  justify-content: center;
}

.intended-class-btn {
  background: none;
  border: 1px solid transparent;
  border-radius: 4px;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
}

.intended-class-btn img {
  width: 28px;
  height: 28px;
  display: block;
}

.intended-class-btn.active {
  background: rgba(165, 165, 165, 0.5);
  border-color: var(--color-border);
  color: white;
}

.intended-class-btn:hover {
  background: rgba(165, 165, 165, 0.3);
  border-color: var(--color-border);
}

@media (max-width: 767.98px) {
  .responsive-ratings-row {
    flex-direction: column !important;
    gap: 20px !important;
    align-items: center !important;
  }

  .responsive-ratings-row .filter-column {
    width: 100%;
    max-width: 300px;
  }

  .intended-class-buttons {
    gap: 10px;
  }

  .intended-class-btn {
    width: 28px;
    height: 28px;
  }

  .intended-class-btn img {
    width: 24px;
    height: 24px;
  }
  .search-results-dropdown {
    margin-top: 65px !important;
  }
}
</style>
