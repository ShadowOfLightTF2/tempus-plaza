<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container py-4 d-flex" style="margin-bottom: 10rem !important">
      <div
        v-if="playerNotFound"
        class="alert alert-warning player-not-found text-center"
      >
        <p class="mb-3">No player found</p>
        <button @click="returnToAllPlayers" class="btn return-button">
          Return to all players
        </button>
      </div>
      <div v-else>
        <ProfileBanner
          :player="player"
          :player-rank-info="playerRankInfo"
          :banner-colors="bannerColors"
          :loading="loading.ranks"
        />
        <div class="row">
          <div class="col-md-4">
            <PointsChart
              title="Soldier Points History"
              chart-type="soldier"
              :points-data="pointsHistory"
              :loading="loading.points"
            />
          </div>
          <div class="col-md-4">
            <PointsChart
              title="Overall Points History"
              chart-type="overall"
              :points-data="pointsHistory"
              :loading="loading.points"
            />
          </div>
          <div class="col-md-4">
            <PointsChart
              title="Demoman Points History"
              chart-type="demoman"
              :points-data="pointsHistory"
              :loading="loading.points"
            />
          </div>
        </div>
        <div class="row main-content-wrapper">
          <div class="col-md-3 stats-boxes">
            <div class="stats-container">
              <ClassStatsCard
                class-type="soldier"
                :current-stat-type="currentStatType.soldier"
                :stats="stats[currentStatType.soldier]"
                :loading="loading.stats"
                @prev-stat="prevStatType('soldier')"
                @next-stat="nextStatType('soldier')"
              />
              <SharedTimesCard
                :shared-times="sharedTimesSoldier"
                :shared-type="player.shared_soldier_type"
                :loading="loading.shared"
                :g1-count="stats.map.placement.soldier.G1"
                :top-times-count="stats.map.topTimesAmount.soldier"
              />
            </div>
          </div>
          <div class="col-md-6 tabs-container">
            <RecordsSection
              :player-id="playerId"
              :recent-records="records.recentRecords"
              :changed-placements="changedPlacements"
              :loading="loading"
              :next-update-countdown="nextUpdateCountdown"
            />
          </div>
          <div class="col-md-3 stats-boxes">
            <div class="stats-container">
              <ClassStatsCard
                class-type="demoman"
                :current-stat-type="currentStatType.demoman"
                :stats="stats[currentStatType.demoman]"
                :loading="loading.stats"
                @prev-stat="prevStatType('demoman')"
                @next-stat="nextStatType('demoman')"
              />
              <SharedTimesCard
                :shared-times="sharedTimesDemoman"
                :shared-type="player.shared_demoman_type"
                :loading="loading.shared"
                :g1-count="stats.map.placement.demoman.G1"
                :top-times-count="stats.map.topTimesAmount.demoman"
              />
            </div>
          </div>
          <div v-if="isCurrentUser || hasFavoriteMaps" class="map-section">
            <div class="map-container">
              <h4 class="map-section-title">Favourite Maps</h4>
              <div class="map-grid">
                <FavoriteMapCard
                  v-for="(map, index) in displayedMaps"
                  :key="map.id || index"
                  :map="map"
                  :index="index"
                  :is-current-user="isCurrentUser"
                  @open-search="openMapSearch"
                  @navigate-to-map="goToRecords"
                />
              </div>
            </div>
          </div>
          <RotwVideos :player-id="playerId" />
          <AuthoredMaps :player-id="playerId" />
          <MapSearchModal
            :show="showMapSearch"
            :search-results="mapSearchResults"
            :has-map-at-current-index="hasMapAtCurrentIndex"
            @close="cancelMapSearch"
            @search="searchMap"
            @select-map="selectMap"
            @remove-map="removeMap"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ProfileBanner from "./ProfileBanner.vue";
import ClassStatsCard from "./ClassStatsCard.vue";
import SharedTimesCard from "./SharedTimesCard.vue";
import RecordsSection from "./RecordsSection.vue";
import FavoriteMapCard from "./FavoriteMapCard.vue";
import MapSearchModal from "./MapSearchModal.vue";
import RotwVideos from "./RotwVideos.vue";
import AuthoredMaps from "./AuthoredMaps.vue";
import axios from "axios";
import PointsChart from "./PointsChart.vue";
import { ref } from "vue";
import { useHead } from "@vueuse/head";
import { formatDuration } from "@/utils/calculations.js";
import { formatDate } from "@/utils/calculations.js";
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  inject: ["profileUpdateTracker"],
  name: "PlayerPage",
  setup() {
    const pageTitle = ref("Tempus Plaza | Player");
    useHead({
      title: pageTitle,
    });
    return {
      updateTitle: (playerName) => {
        pageTitle.value = `Tempus Plaza | ${playerName}`;
      },
    };
  },
  props: {
    playerId: {
      type: Number,
      required: true,
    },
  },
  components: {
    ProfileBanner,
    PointsChart,
    ClassStatsCard,
    SharedTimesCard,
    RecordsSection,
    FavoriteMapCard,
    MapSearchModal,
    RotwVideos,
    AuthoredMaps,
  },
  data: () => ({
    currentTime: new Date(),
    updateTimer: null,
    debounceTimer: null,
    currentUser: null,
    selectedClass: null,
    _cachedRankInfo: null,
    changedPlacements: [],
    showClassWarning: false,
    classWarningTimeout: null,
    favoriteMaps: [
      {
        id: null,
        name: "",
        index: 0,
        class_type: null,
        map_soldier_tier: null,
        map_soldier_rating: null,
        map_demoman_tier: null,
        map_demoman_rating: null,
        player_record_duration: null,
        record_rank: null,
        record_placement: null,
      },
      {
        id: null,
        name: "",
        index: 1,
        class_type: null,
        map_soldier_tier: null,
        map_soldier_rating: null,
        map_demoman_tier: null,
        map_demoman_rating: null,
        player_record_duration: null,
        record_rank: null,
        record_placement: null,
      },
      {
        id: null,
        name: "",
        index: 2,
        class_type: null,
        map_soldier_tier: null,
        map_soldier_rating: null,
        map_demoman_tier: null,
        map_demoman_rating: null,
        player_record_duration: null,
        record_rank: null,
        record_placement: null,
      },
    ],
    showMapSearch: false,
    currentMapIndex: null,
    mapSearchResults: [],
    player: {
      id: null,
      steamid: null,
      steamidconverted: null,
      avatar: null,
      name: null,
      country: "unknown",
      country_code: "unknown",
      overall_rank: null,
      soldier_rank: null,
      demoman_rank: null,
      overall_points: null,
      soldier_points: null,
      demoman_points: null,
      gender: "male",
      launcher_pref: 1,
      rank_pref: "",
      donator: false,
      shared_soldier_type: "",
      shared_demoman_type: "",
    },
    launcherOptions: [
      { value: 1, icon: "/icons/rocketlauncher.png" },
      { value: 2, icon: "/icons/cowmangler.png" },
      { value: 3, icon: "/icons/original.png" },
    ],
    rankOptions: [
      { value: 1, label: "Overall Rank" },
      { value: 2, label: "Soldier Rank" },
      { value: 3, label: "Demoman Rank" },
    ],
    showRankMenu: false,
    records: {
      recentRecords: [],
    },
    loading: {
      "Latest runs": true,
      shared: true,
      ranks: true,
      stats: true,
      points: true,
      "Lost placements": false,
    },
    currentPage: 1,
    pageSize: 8,
    playerNotFound: false,
    stats: {
      total: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
      map: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
      course: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
      bonus: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
    },
    currentStatType: {
      soldier: "total",
      demoman: "total",
    },
    sharedTimesSoldier: [],
    sharedTimesDemoman: [],
    pointsHistory: [],
  }),
  computed: {
    hasMapAtCurrentIndex() {
      const currentMap = this.favoriteMaps.find(
        (map) => map.index === this.currentMapIndex,
      );
      return currentMap && currentMap.id !== null;
    },
    nextUpdateCountdown() {
      const now = this.currentTime;
      const nextUpdate = new Date(now);

      const nextHour = Math.ceil((now.getHours() + 1) / 2) * 2;
      nextUpdate.setHours(nextHour, 0, 0, 0);

      const diff = nextUpdate - now;

      const hours = Math.floor(diff / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((diff % (1000 * 60)) / 1000);

      if (hours > 0) {
        return `${hours}h ${minutes}m ${seconds}s`;
      } else if (minutes > 0) {
        return `${minutes}m ${seconds}s`;
      }
      return `${seconds}s`;
    },
    currentTimeString() {
      return this.currentTime.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      });
    },
    highestRank() {
      if (this.player.rank_pref === "overall") return this.player.overall_rank;
      else if (this.player.rank_pref === "soldier")
        return this.player.soldier_rank;
      else if (this.player.rank_pref === "demoman")
        return this.player.demoman_rank;
      return Math.min(
        this.player.overall_rank,
        this.player.soldier_rank,
        this.player.demoman_rank,
      );
    },
    playerRankInfo() {
      return this.getRankName(this.highestRank, this.player.gender);
    },
    hasFavoriteMaps() {
      return this.favoriteMaps.some(
        (map) => map.name && map.name.trim() !== "",
      );
    },
    filteredFavoriteMaps() {
      return this.favoriteMaps.filter(
        (map) => map.name && map.name.trim() !== "",
      );
    },
    displayedMaps() {
      const displayArray = [
        { name: null, id: null, invisible: true },
        { name: null, id: null, invisible: true },
        { name: null, id: null, invisible: true },
      ];

      this.favoriteMaps.forEach((map, index) => {
        if (index < 3) {
          if (map.name && map.name.trim() !== "") {
            displayArray[index] = { ...map, invisible: false };
          } else if (this.isCurrentUser) {
            displayArray[index] = { name: null, id: null, invisible: false };
          }
        }
      });

      if (!this.isCurrentUser) {
        return displayArray.filter((map) => !map.invisible);
      }

      return displayArray;
    },
    isCurrentUser() {
      return this.currentUser && this.currentUser.playerid === this.playerId;
    },
    bannerColors() {
      const color = this.player.color || "blue";
      return {
        color1: `var(--color-banner-${color}-1)`,
        color2: `var(--color-banner-${color}-2)`,
      };
    },
  },
  async mounted() {
    await this.loadPlayerPageData(this.playerId);
    this.startUpdateTimer();
  },
  beforeUnmount() {
    if (this.updateTimer) {
      clearInterval(this.updateTimer);
    }
  },
  watch: {
    authoredMaps(newMaps) {
      if (newMaps.length > 0 && this.$route.hash === "#authored-maps") {
        this.scrollToAuthoredMaps();
      }
    },
    profileUpdateTracker: {
      handler(newTracker) {
        this.fetchUserData(this.playerId);
      },
      deep: true,
    },
    playerId: {
      immediate: false,
      async handler(newId, oldId) {
        if (newId !== oldId) {
          this.playerNotFound = false;
          this.loading = {
            ...this.loading,
            ranks: true,
            stats: true,
            shared: true,
            "Latest runs": true,
          };
          this.currentPage = 1;
          this.currentStatType.soldier = "total";
          this.currentStatType.demoman = "total";
          this.favoriteMaps = [
            {
              id: null,
              name: "",
              index: 0,
              class_type: null,
              map_soldier_tier: null,
              map_soldier_rating: null,
              map_demoman_tier: null,
              map_demoman_rating: null,
              player_record_duration: null,
              record_rank: null,
              record_placement: null,
            },
            {
              id: null,
              name: "",
              index: 1,
              class_type: null,
              map_soldier_tier: null,
              map_soldier_rating: null,
              map_demoman_tier: null,
              map_demoman_rating: null,
              player_record_duration: null,
              record_rank: null,
              record_placement: null,
            },
            {
              id: null,
              name: "",
              index: 2,
              class_type: null,
              map_soldier_tier: null,
              map_soldier_rating: null,
              map_demoman_tier: null,
              map_demoman_rating: null,
              player_record_duration: null,
              record_rank: null,
              record_placement: null,
            },
          ];
          await this.loadPlayerPageData(newId);
        }
      },
    },
  },
  methods: {
    handleClickOutside(e) {
      if (!e.target.closest(".rotw-card")) {
        this.activeRotwVideo = null;
      }
    },
    startUpdateTimer() {
      this.updateTimer = setInterval(() => {
        this.currentTime = new Date();
      }, 1000);
    },
    async loadPlayerPageData(playerId) {
      try {
        fetch(
          `https://api.tempusplaza.com/players/${playerId}/update-player-last-seen`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
          },
        );
        this.currentUser = await this.fetchUser();
        await Promise.all([
          this.fetchPlayerData(playerId),
          this.fetchUserData(playerId),
          this.fetchPlayerRanks(playerId),
          this.fetchRecentRecords(playerId),
          this.fetchPlayerPoints(playerId),
          this.fetchFavoriteMaps(playerId),
          this.fetchChangedPlacements(playerId),
        ]);
        await this.fetchPlayerStats(playerId);
        await this.fetchSharedTimes(playerId);
      } catch (error) {
        console.error("Error loading player page data:", error);
      }
    },
    async fetchChangedPlacements(playerId) {
      this.loading["Lost placements"] = true;
      try {
        const response = await fetch(
          `${API_BASE_URL}/players/${playerId}/lost-records`,
        );
        const data = await response.json();

        this.changedPlacements = data.map((p) => {
          const old_rank = p.old_placement;
          const new_rank = p.new_placement;
          const points_change = p.points_change;
          const date = p.change_date;

          return {
            ...p,
            class: p.record_type.split("_")[0], // 'soldier' | 'demoman'
            type: p.record_type.split("_")[1], // 'map' | 'course' | 'bonus'
            old_rank,
            new_rank,
            rank_change: p.placement_change,
            date,
            points_change,

            oldRankDisplay: `${this.getMedal(old_rank)}${this.formatRankDisplay(
              old_rank,
            )}`,
            newRankDisplay: `${this.getMedal(new_rank)}${this.formatRankDisplay(
              new_rank,
            )}`,
            oldRankClass: this.getPlacementClass(old_rank),
            newRankClass: this.getPlacementClass(new_rank),

            pointsChangeClass: this.getPointsChangeClass(points_change),
            pointsChangeText: this.formatPointsChange(points_change),

            formattedDate: this.formatDate(date),
          };
        });
      } catch (error) {
        console.error("Error fetching lost placements:", error);
      } finally {
        this.loading["Lost placements"] = false;
      }
    },
    convertSteamId(steamId) {
      if (!steamId) {
        return "#";
      }

      const parts = steamId.split(":");
      if (parts.length === 3 && parts[0] === "STEAM_0") {
        const y = parseInt(parts[1]);
        const z = parseInt(parts[2]);
        return (
          BigInt(z) * BigInt(2) +
          BigInt(y) +
          BigInt("76561197960265728")
        ).toString();
      }
      return "#";
    },
    openMapSearch(index) {
      this.currentMapIndex = index;
      this.showMapSearch = true;
      this.mapSearchResults = [];
    },
    searchMap(query) {
      if (this.debounceTimer) clearTimeout(this.debounceTimer);

      if (!query.trim()) {
        this.mapSearchResults = [];
        return;
      }

      this.debounceTimer = setTimeout(async () => {
        try {
          const response = await fetch(`${API_BASE_URL}/search/maps`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ query }),
          });

          const data = await response.json();
          this.mapSearchResults = (data.maps || data || []).slice(0, 5);
        } catch (error) {
          console.error("Error searching for maps:", error);
          this.mapSearchResults = [];
        }
      }, 500);
    },
    selectClass(className) {
      this.selectedClass = className;
      this.hideClassWarning();
    },
    handleMapClick(map) {
      if (!this.selectedClass) {
        this.showClassWarningPopup();
        return;
      }
      this.selectMap(map, this.selectedClass);
    },
    async selectMap(mapData, classType) {
      this.cancelMapSearch();
      await this.updateMap(mapData.id, classType);
      await this.fetchFavoriteMaps(this.playerId);
    },
    showClassWarningPopup() {
      this.showClassWarning = true;
      if (this.classWarningTimeout) {
        clearTimeout(this.classWarningTimeout);
      }
      this.classWarningTimeout = setTimeout(() => {
        this.hideClassWarning();
      }, 2000);
    },
    hideClassWarning() {
      this.showClassWarning = false;
      if (this.classWarningTimeout) {
        clearTimeout(this.classWarningTimeout);
        this.classWarningTimeout = null;
      }
    },
    async fetchUser() {
      try {
        const response = await fetch(`${API_BASE_URL}/api/get-user`, {
          credentials: "include",
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) {
          console.log("Response not ok:", response.status, response.statusText);
          return null;
        }

        const result = await response.json();
        return result.data;
      } catch (error) {
        console.error("Error fetching user data:", error);
        return null;
      }
    },
    async updateMap(mapId, classType) {
      const currentUser = await this.fetchUser();
      const index = this.currentMapIndex;
      try {
        const response = await fetch(
          `${API_BASE_URL}/users/${currentUser.playerid}/update-favorite-maps/${mapId}/${index}/${classType}`,
          {
            method: "POST",
          },
        );

        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);
      } catch (error) {
        console.error("Failed to update favorite map:", error);
      }
    },
    async removeMap() {
      const currentUser = await this.fetchUser();
      const index = this.currentMapIndex;
      try {
        const response = await fetch(
          `${API_BASE_URL}/users/${currentUser.playerid}/remove-favorite-map/${index}`,
          {
            method: "DELETE",
          },
        );

        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);

        await this.fetchFavoriteMaps(this.playerId);
        this.cancelMapSearch();
      } catch (error) {
        console.error("Failed to remove favorite map:", error);
      }
    },
    cancelMapSearch() {
      this.showMapSearch = false;
      this.mapSearchResults = [];
    },
    prevStatType(classType) {
      const statTypes = ["total", "map", "course", "bonus"];
      const currentIndex = statTypes.indexOf(this.currentStatType[classType]);
      const prevIndex =
        (currentIndex - 1 + statTypes.length) % statTypes.length;
      this.currentStatType[classType] = statTypes[prevIndex];
    },
    nextStatType(classType) {
      const statTypes = ["total", "map", "course", "bonus"];
      const currentIndex = statTypes.indexOf(this.currentStatType[classType]);
      const nextIndex = (currentIndex + 1) % statTypes.length;
      this.currentStatType[classType] = statTypes[nextIndex];
    },
    toggleClassFilter(classType) {
      const index = this.filterOptions.selectedClasses.indexOf(classType);
      if (index === -1) {
        this.filterOptions.selectedClasses.push(classType);
      } else {
        this.filterOptions.selectedClasses.splice(index, 1);
      }
    },
    toggleTypeFilter(type) {
      const index = this.filterOptions.selectedTypes.indexOf(type);
      if (index === -1) {
        this.filterOptions.selectedTypes.push(type);
      } else {
        this.filterOptions.selectedTypes.splice(index, 1);
      }
    },
    togglePlacementFilter(placement) {
      const index = this.filterOptions.selectedPlacements.indexOf(placement);
      if (index === -1) {
        this.filterOptions.selectedPlacements.push(placement);
      } else {
        this.filterOptions.selectedPlacements.splice(index, 1);
      }
    },
    toggleGainLossFilter(type) {
      const index = this.filterOptions.selectedGainLoss.indexOf(type);
      if (index === -1) {
        this.filterOptions.selectedGainLoss.push(type);
      } else {
        this.filterOptions.selectedGainLoss.splice(index, 1);
      }
    },
    async fetchPlayerPoints(playerId) {
      try {
        this.loading.points = true;
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/points`,
        );
        this.pointsHistory = response.data;
        this.loading.points = false;
      } catch (error) {
        console.error("Error fetching player points:", error);
        this.loading.points = false;
      }
    },
    goToRecords(mapId) {
      this.$router.push({
        name: "MapPage",
        params: { mapId: mapId },
      });
    },
    goToPlayer(playerId) {
      this.$router.push({
        name: "PlayerPage",
        params: { playerId: playerId },
      });
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
    getMedal(rank) {
      return ["🥇", "🥈", "🥉"][rank - 1] || "";
    },
    formatRankDisplay(rank) {
      if (rank >= 11 && rank <= 15) {
        return `G${rank - 10}`;
      }
      return `#${rank}`;
    },
    getPointsChangeClass(pointsChange) {
      if (pointsChange > 0) return "points-gain";
      if (pointsChange < 0) return "points-loss";
      return "points-tied";
    },
    formatPointsChange(pointsChange) {
      const roundedPoints = Math.round(pointsChange * 100) / 100;

      if (roundedPoints > 0) return `+${roundedPoints.toFixed(2)}pts`;
      if (roundedPoints < 0) return `${roundedPoints.toFixed(2)}pts`;
      return "0.00pts";
    },
    getRankName(rank, gender) {
      const titles = [
        { range: [1, 1], male: "Emperor", female: "Empress" },
        { range: [2, 2], male: "King", female: "Queen" },
        { range: [3, 3], male: "Archduke", female: "Archduchess" },
        { range: [4, 4], male: "Lord", female: "Lady" },
        { range: [5, 5], male: "Duke", female: "Duchess" },
        { range: [6, 6], male: "Prince V", female: "Princess V" },
        { range: [7, 7], male: "Prince IV", female: "Princess IV" },
        { range: [8, 8], male: "Prince III", female: "Princess III" },
        { range: [9, 9], male: "Prince II", female: "Princess II" },
        { range: [10, 10], male: "Prince I", female: "Princess I" },
        { range: [11, 11], male: "Earl V", female: "Gearl V" },
        { range: [12, 12], male: "Earl IV", female: "Gearl IV" },
        { range: [13, 13], male: "Earl III", female: "Gearl III" },
        { range: [14, 14], male: "Earl II", female: "Gearl II" },
        { range: [15, 15], male: "Earl I", female: "Gearl I" },
        { range: [16, 16], male: "Sir V", female: "Madam V" },
        { range: [17, 17], male: "Sir IV", female: "Madam IV" },
        { range: [18, 18], male: "Sir III", female: "Madam III" },
        { range: [19, 19], male: "Sir II", female: "Madam II" },
        { range: [20, 20], male: "Sir I", female: "Madam I" },
        { range: [21, 21], male: "Count V", female: "Countess" },
        { range: [22, 22], male: "Count IV", female: "Countess" },
        { range: [23, 23], male: "Count III", female: "Countess" },
        { range: [24, 24], male: "Count II", female: "Countess" },
        { range: [25, 25], male: "Count I", female: "Countess" },
        { range: [26, 30], male: "Baron V", female: "Baroness V" },
        { range: [31, 35], male: "Baron IV", female: "Baroness IV" },
        { range: [36, 40], male: "Baron III", female: "Baroness III" },
        { range: [41, 45], male: "Baron II", female: "Baroness II" },
        { range: [46, 50], male: "Baron I", female: "Baroness I" },
        { range: [51, 60], male: "Knight V", female: "Dame V" },
        { range: [61, 70], male: "Knight IV", female: "Dame IV" },
        { range: [71, 80], male: "Knight III", female: "Dame III" },
        { range: [81, 90], male: "Knight II", female: "Dame II" },
        { range: [91, 100], male: "Knight I", female: "Dame I" },
        { range: [101, 120], male: "Noble V", female: "Noblewoman V" },
        { range: [121, 140], male: "Noble IV", female: "Noblewoman IV" },
        { range: [141, 160], male: "Noble III", female: "Noblewoman III" },
        { range: [161, 180], male: "Noble II", female: "Noblewoman II" },
        { range: [181, 200], male: "Noble I", female: "Noblewoman I" },
        { range: [201, 260], male: "Esquire V", female: "Esquire V" },
        { range: [261, 320], male: "Esquire IV", female: "Esquire IV" },
        { range: [321, 380], male: "Esquire III", female: "Esquire III" },
        { range: [381, 440], male: "Esquire II", female: "Esquire II" },
        { range: [441, 500], male: "Esquire I", female: "Esquire I" },
        { range: [501, 600], male: "Jester V", female: "Jester V" },
        { range: [601, 700], male: "Jester IV", female: "Jester IV" },
        { range: [701, 800], male: "Jester III", female: "Jester III" },
        { range: [801, 900], male: "Jester II", female: "Jester II" },
        { range: [901, 1000], male: "Jester I", female: "Jester I" },
        { range: [1001, 1300], male: "Plebeian V", female: "Plebeian V" },
        { range: [1301, 1600], male: "Plebeian IV", female: "Plebeian IV" },
        { range: [1601, 1900], male: "Plebeian III", female: "Plebeian III" },
        { range: [1901, 2200], male: "Plebeian II", female: "Plebeian II" },
        { range: [2201, 2500], male: "Plebeian I", female: "Plebeian I" },
        { range: [2501, 2700], male: "Peasant V", female: "Peasant V" },
        { range: [2701, 2900], male: "Peasant IV", female: "Peasant IV" },
        { range: [2901, 3100], male: "Peasant III", female: "Peasant III" },
        { range: [3101, 3300], male: "Peasant II", female: "Peasant II" },
        { range: [3301, 3500], male: "Peasant I", female: "Peasant I" },
        { range: [3501, 4000], male: "Peasant I", female: "Peasant I" },
        { range: [4001, 5000], male: "Peasant I", female: "Peasant I" },
        { range: [5001, 999999], male: "Peon", female: "Peon" },
      ];
      for (const title of titles) {
        if (rank >= title.range[0] && rank <= title.range[1]) {
          const selectedTitle = gender === "male" ? title.male : title.female;
          let cleanedTitle = title.male.replace(/\s[IVX]+$/, "").toLowerCase();
          if (selectedTitle === "Queen" || selectedTitle === "Empress")
            cleanedTitle = title.female.replace(/\s[IVX]+$/, "").toLowerCase();
          return {
            title: selectedTitle,
            color: `rank-color-${cleanedTitle}`,
          };
        }
      }
      return { title: "Unranked", color: "--color-peon" };
    },
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    handleImageError(event) {
      event.target.src = "/icons/default-flag.jpg";
    },
    returnToAllPlayers() {
      this.$router.push({ name: "Players" });
    },
    async fetchUserData(playerId) {
      try {
        const response = await axios.get(`${API_BASE_URL}/users/${playerId}`);
        const data = response.data;
        if (!data || data.length === 0) {
          this.player = {
            ...this.player,
            gender: "male",
            donator: false,
            color: "blue",
          };
          return;
        }
        this.player = {
          ...this.player,
          gender: data.gender,
          rank_pref: data.rank_pref,
          donator: Boolean(data.donator),
          color: data.color,
        };
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    },
    async fetchFavoriteMaps(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/users/${playerId}/get-favorite-maps`,
        );
        this.populateFavoriteMaps(response.data);
      } catch (error) {
        console.error("Error fetching favorite maps:", error);
      }
    },
    populateFavoriteMaps(favoriteMapData) {
      this.favoriteMaps.forEach((map, index) => {
        this.favoriteMaps[index] = {
          id: null,
          name: "",
          index: index,
          class_type: null,
          map_soldier_tier: null,
          map_soldier_rating: null,
          map_demoman_tier: null,
          map_demoman_rating: null,
          player_record_duration: null,
          record_rank: null,
          record_placement: null,
        };
      });
      if (!favoriteMapData) {
        return;
      }

      let parsedMaps = [];
      if (typeof favoriteMapData === "string") {
        try {
          parsedMaps = JSON.parse(favoriteMapData);
        } catch (e) {
          console.error("Error parsing favorite_maps JSON:", e);
          return;
        }
      } else if (Array.isArray(favoriteMapData)) {
        parsedMaps = favoriteMapData;
      }

      if (Array.isArray(parsedMaps)) {
        parsedMaps.forEach((mapData) => {
          if (mapData.favorite_index >= 0 && mapData.favorite_index <= 2) {
            this.favoriteMaps[mapData.favorite_index] = {
              id: mapData.map_id || null,
              name: mapData.map_name || "",
              index: mapData.favorite_index,
              class_type: mapData.class_type || null,
              map_soldier_tier: mapData.map_soldier_tier || null,
              map_soldier_rating: mapData.map_soldier_rating || null,
              map_demoman_tier: mapData.map_demoman_tier || null,
              map_demoman_rating: mapData.map_demoman_rating || null,
              player_record_duration: mapData.player_record_duration || null,
              record_rank: mapData.record_rank || null,
              record_placement: mapData.record_placement || null,
            };
          }
        });
      }
    },
    async fetchPlayerData(playerId) {
      try {
        const response = await axios.get(`${API_BASE_URL}/players/${playerId}`);
        if (response.data.length === 0) {
          this.playerNotFound = true;
          return;
        }
        const playerData = response.data[0];
        this.player = {
          ...this.player,
          ...playerData,
          avatar: playerData.steam_avatar || "golly.jpg",
          country: playerData.country || "unknown",
          country_code: playerData.country_code || "unknown",
          steamidconverted: this.convertSteamId(playerData.steamid),
        };
        this.updateTitle(this.player.name);
      } catch (error) {
        this.error = "Failed to fetch player data. Please try again later.";
        console.error("Error fetching player data:", error);
        this.playerNotFound = true;
      } finally {
        this.loading.ranks = false;
      }
    },
    async fetchPlayerRanks(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/ranks`,
        );
        const playerRanks = response.data[0];
        this.player.overall_rank = playerRanks.overall_rank;
        this.player.soldier_rank = playerRanks.soldier_rank;
        this.player.demoman_rank = playerRanks.demoman_rank;
        this.player.overall_points =
          playerRanks.soldier_points + playerRanks.demoman_points;
        this.player.soldier_points = playerRanks.soldier_points;
        this.player.demoman_points = playerRanks.demoman_points;
      } catch (error) {
        this.error = "Failed to fetch player ranks. Please try again later.";
        console.error("Error fetching player ranks:", error);
      } finally {
        this.loading.ranks = false;
      }
    },
    async fetchRecentRecords(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/recent-records`,
        );

        this.records.recentRecords = response.data.map((r) => ({
          ...r,
          durationFormatted: this.formatDuration(r.duration),
          rankClass:
            r.rank >= 1 && r.rank <= 3 ? this.getPlacementClass(r.rank) : "",
          placementClass: this.getPlacementClass(r.placement),
          medal: this.getMedal(r.rank),
          formattedDate: this.formatDate(r.date),
        }));
      } catch (error) {
        this.error =
          "Failed to fetch player recent records. Please try again later.";
        console.error("Error fetching recent records:", error);
      } finally {
        this.loading["Latest runs"] = false;
      }
    },
    async fetchPlayerStats(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/stats`,
        );
        const stats = response.data;
        this.stats = {
          total: {
            completion: {
              soldier:
                Math.round(
                  (stats.soldier.completion.total + Number.EPSILON) * 100,
                ) / 100,
              demoman:
                Math.round(
                  (stats.demoman.completion.total + Number.EPSILON) * 100,
                ) / 100,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.total,
              demoman: stats.demoman.worldRecords.total,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.total,
              demoman: stats.demoman.top10Records.total,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.total,
                G2: stats.soldier.g2Records.total,
                G3: stats.soldier.g3Records.total,
                G4: stats.soldier.g4Records.total,
              },
              demoman: {
                G1: stats.demoman.g1Records.total,
                G2: stats.demoman.g2Records.total,
                G3: stats.demoman.g3Records.total,
                G4: stats.demoman.g4Records.total,
              },
            },
          },
          map: {
            completion: {
              soldier:
                Math.round(
                  (stats.soldier.completion.map + Number.EPSILON) * 100,
                ) / 100,
              demoman:
                Math.round(
                  (stats.demoman.completion.map + Number.EPSILON) * 100,
                ) / 100,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.map,
              demoman: stats.demoman.worldRecords.map,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.map,
              demoman: stats.demoman.top10Records.map,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.map,
                G2: stats.soldier.g2Records.map,
                G3: stats.soldier.g3Records.map,
                G4: stats.soldier.g4Records.map,
              },
              demoman: {
                G1: stats.demoman.g1Records.map,
                G2: stats.demoman.g2Records.map,
                G3: stats.demoman.g3Records.map,
                G4: stats.demoman.g4Records.map,
              },
            },
          },
          course: {
            completion: {
              soldier:
                Math.round(
                  (stats.soldier.completion.course + Number.EPSILON) * 100,
                ) / 100,
              demoman:
                Math.round(
                  (stats.demoman.completion.course + Number.EPSILON) * 100,
                ) / 100,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.course,
              demoman: stats.demoman.worldRecords.course,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.course,
              demoman: stats.demoman.top10Records.course,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.course,
                G2: stats.soldier.g2Records.course,
                G3: stats.soldier.g3Records.course,
                G4: stats.soldier.g4Records.course,
              },
              demoman: {
                G1: stats.demoman.g1Records.course,
                G2: stats.demoman.g2Records.course,
                G3: stats.demoman.g3Records.course,
                G4: stats.demoman.g4Records.course,
              },
            },
          },
          bonus: {
            completion: {
              soldier:
                Math.round(
                  (stats.soldier.completion.bonus + Number.EPSILON) * 100,
                ) / 100,
              demoman:
                Math.round(
                  (stats.demoman.completion.bonus + Number.EPSILON) * 100,
                ) / 100,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.bonus,
              demoman: stats.demoman.worldRecords.bonus,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.bonus,
              demoman: stats.demoman.top10Records.bonus,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.bonus,
                G2: stats.soldier.g2Records.bonus,
                G3: stats.soldier.g3Records.bonus,
                G4: stats.soldier.g4Records.bonus,
              },
              demoman: {
                G1: stats.demoman.g1Records.bonus,
                G2: stats.demoman.g2Records.bonus,
                G3: stats.demoman.g3Records.bonus,
                G4: stats.demoman.g4Records.bonus,
              },
            },
          },
        };
      } catch (error) {
        this.error = "Failed to fetch player stats. Please try again later.";
        console.error("Error fetching player stats:", error);
      } finally {
        this.loading.stats = false;
      }
    },
    async fetchSharedTimes(playerId) {
      try {
        const soldierTtCount = this.stats.map.topTimesAmount.soldier;
        const demomanTtCount = this.stats.map.topTimesAmount.demoman;
        const stats = {
          soldier_maps_count: soldierTtCount,
          demoman_maps_count: demomanTtCount,
        };
        const fetchSharedData = async (classType, placement, isGroupOne) => {
          try {
            const response = await axios.get(
              `${API_BASE_URL}/players/shared-names/${playerId}/${classType}/${placement}`,
            );
            return response.data;
          } catch (e) {
            console.error(
              `${classType} ${
                isGroupOne ? "group1" : "top time"
              } fetch failed:`,
              e,
            );
            return [];
          }
        };
        const processClassData = async (
          classType,
          countProperty,
          sharedTypeProperty,
          groupOnePlacement,
        ) => {
          const placement = stats[countProperty] === 0 ? groupOnePlacement : 10;
          const isGroupOne = stats[countProperty] === 0;
          this.player[sharedTypeProperty] = isGroupOne
            ? "Shared Group 1s"
            : "Shared Top Times";
          return await fetchSharedData(classType, placement, isGroupOne);
        };
        const [sharedSoldiers, sharedDemomans] = await Promise.all([
          processClassData(
            "soldier",
            "soldier_maps_count",
            "shared_soldier_type",
            11,
          ),
          processClassData(
            "demoman",
            "demoman_maps_count",
            "shared_demoman_type",
            11,
          ),
        ]);
        this.sharedTimesSoldier = Object.entries(sharedSoldiers).map(
          ([_, data]) => ({
            playerId: data.player_id,
            count: data.record_count,
            playerName: data.player_name,
            avatar: data.avatar,
          }),
        );
        this.sharedTimesDemoman = Object.entries(sharedDemomans).map(
          ([_, data]) => ({
            playerId: data.player_id,
            count: data.record_count,
            playerName: data.player_name,
            avatar: data.avatar,
          }),
        );
      } catch (error) {
        console.error("Error fetching shared top times:", error);
        throw error;
      } finally {
        this.loading.shared = false;
      }
    },
    formatDuration(duration) {
      return formatDuration(duration);
    },
    formatDate(unixTimestamp) {
      return formatDate(unixTimestamp);
    },
  },
  beforeDestroy() {
    clearTimeout(this.debounceTimer);
    this._cachedRankInfo = null;
  },
};
</script>

<style scoped>
.return-button {
  background: var(--color-box);
  color: var(--color-text);
}
.return-button:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
  color: var(--color-text);
}
.global-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  border-radius: 8px;
  max-width: 150px;
}
.stats-boxes {
  flex: 1;
  padding: 10px;
}
.tabs-container {
  flex: 2;
  padding: 10px;
}
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
.map-grid {
  display: grid;
  gap: 30px;
  margin-top: 20px;
  margin-bottom: 20px;
  justify-content: center;
  max-width: 1200px;
}
.map-grid:has(.map-card:nth-child(1):last-child) {
  grid-template-columns: 1fr;
  width: 600px;
}
.map-grid:has(.map-card:nth-child(2):last-child) {
  grid-template-columns: repeat(2, 1fr);
  width: 800px;
}
.map-grid:has(.map-card:nth-child(3)) {
  grid-template-columns: repeat(3, minmax(300px, 1fr));
  width: 1200px;
}
@media (max-width: 1400px) {
  .map-grid {
    grid-template-columns: 1fr !important;
    width: 100% !important;
    max-width: none !important;
    overflow-x: hidden;
    gap: 15px;
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
  .map-search-overlay {
    padding: 20px;
  }
  .map-search-container {
    padding: 15px;
    margin: 10px;
    max-height: 85vh;
    width: 100%;
    max-width: 400px;
  }
  .search-icon {
    left: 10px;
  }
  .search-input {
    padding: 10px 10px 10px 35px;
    font-size: 16px;
  }
  .search-results-dropdown {
    max-height: 150px;
  }
  .search-results-dropdown li {
    padding: 8px 10px;
    font-size: 14px;
  }
  .cancel-button {
    padding: 8px 16px;
    min-width: 70px;
  }
  .class-icons {
    gap: 10px;
  }
  .class-option {
    min-width: 70px;
    padding: 8px;
  }
  .class-icon {
    width: 28px;
    height: 28px;
  }
  .class-option span {
    font-size: 11px;
  }
}
@media (max-width: 567px) {
  .container {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .stat-block {
    margin-left: auto !important;
    margin-right: auto !important;
  }
  .stats-container {
    margin: 0 auto;
  }
  .map-grid {
    grid-template-columns: 1fr !important;
    width: 100% !important;
    overflow-x: hidden;
    gap: 15px;
  }
  .map-container {
    width: 100%;
    margin: 0 auto;
  }
  .main-content-wrapper {
    margin: 0 auto;
  }
  .stats-boxes,
  .tabs-container {
    margin: 0 auto;
  }
  .row.g-0 {
    margin: 0 auto;
  }
  .col-md-4 {
    max-width: 550px;
    margin: 0 auto;
  }
  :deep(.global-btn),
  :deep(.global-btn.active) {
    font-size: 0.8rem !important;
    padding: 10px !important;
  }
}
@media (max-width: 536px) {
  .col-md-4 {
    max-width: 500px;
  }
}
@media (max-width: 485px) {
  .col-md-4 {
    max-width: 465px;
  }
}
@media (max-width: 424px) {
  .col-md-4 {
    max-width: 425px;
  }
}
</style>
