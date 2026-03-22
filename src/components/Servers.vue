<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div
      class="container mx-auto py-4 d-flex flex-column align-items-center"
      style="z-index: 1"
    >
      <div class="content-container" style="z-index: 1">
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">🌐</span>
            Server Dashboard
          </h1>
          <p class="page-subtitle">Top players online and server information</p>
        </div>
        <hr class="divider" style="width: 100%" />
        <div class="table-container-wrapper">
          <div class="button-group">
            <button
              :class="{ active: currentView === 'topplayers' }"
              @click="switchView('topplayers')"
              class="toggle-btn btn btn-dark update-button"
            >
              Top Players Online
            </button>
            <button
              :class="{ active: currentView === 'servers' }"
              @click="switchView('servers')"
              class="toggle-btn btn btn-dark update-button"
            >
              Servers
            </button>
          </div>
          <ServersSkeleton v-if="loading" :view="currentView" />
          <div v-else class="table-container">
            <div v-if="currentView === 'topplayers'" class="top-players-cards">
              <div class="table-header-content topplayers-header">
                <div class="table-header-icon">🏆</div>
                <div class="table-header-text">
                  <h3 class="table-header-title">Top Players Online</h3>
                  <p class="table-header-subtitle">Updates every minute</p>
                </div>
                <label v-if="!isMobile" class="min-mode-checkbox">
                  <input
                    type="checkbox"
                    :checked="minMode"
                    @change="toggleMinMode"
                  />
                  <span class="checkmark"></span>
                  <span class="checkbox-label">Min Mode</span>
                </label>
              </div>
              <div
                class="players-cards-container"
                :class="{ 'min-mode': minMode }"
              >
                <div
                  v-for="(player, index) in topPlayersData"
                  :key="player.player_id"
                  class="player-card"
                  :class="{ 'min-mode': minMode }"
                >
                  <div class="ranks-section">
                    <div class="player-ranks">
                      <template v-if="!minMode">
                        <div class="rank-badge soldier">
                          <img
                            src="/icons/soldier.png"
                            alt="Soldier"
                            class="class-icon-small"
                          />
                          <span class="rank-value">{{
                            player.soldier_rank || "N/A"
                          }}</span>
                        </div>
                        <div class="rank-badge demoman">
                          <img
                            src="/icons/demoman.png"
                            alt="Demoman"
                            class="class-icon-small"
                          />
                          <span class="rank-value">{{
                            player.demoman_rank || "N/A"
                          }}</span>
                        </div>
                      </template>
                      <template v-else>
                        <div
                          class="rank-badge"
                          :class="
                            player.highest_rank === player.soldier_rank
                              ? 'soldier'
                              : 'demoman'
                          "
                        >
                          <img
                            :src="`/icons/${
                              player.highest_rank === player.soldier_rank
                                ? 'soldier'
                                : 'demoman'
                            }.png`"
                            :alt="
                              player.highest_rank === player.soldier_rank
                                ? 'Soldier'
                                : 'Demoman'
                            "
                            class="class-icon-small"
                          />
                          <span class="rank-value">{{
                            player.highest_rank || "N/A"
                          }}</span>
                        </div>
                      </template>
                    </div>
                  </div>
                  <div class="player-info-section">
                    <SmartLink
                      :to="{
                        name: 'PlayerPage',
                        params: { playerId: player.player_id },
                      }"
                      class="player-link"
                    >
                      <img
                        v-if="!minMode"
                        :src="player.steam_avatar"
                        alt="Avatar"
                        class="player-avatar-large"
                      />
                      <div class="player-details">
                        <div class="player-name-large">
                          <img
                            v-if="minMode"
                            :src="player.steam_avatar"
                            alt="Avatar"
                            class="player-avatar-small"
                          />
                          {{ player.player_name }}
                        </div>
                        <div v-if="!minMode" class="player-country">
                          <img
                            :src="getFlagImageUrl(player.country_code)"
                            alt="Flag"
                            class="flag-icon-large"
                          />
                          {{ player.country }}
                        </div>
                      </div>
                    </SmartLink>
                  </div>
                  <SmartLink
                    :to="{
                      name: 'MapPage',
                      params: { mapId: player.map_id },
                    }"
                    class="map-info-section"
                    :style="
                      !minMode && !failedMapImages.has(player.current_map)
                        ? `background-image: url('/map-backgrounds/medium/${player.current_map}.webp')`
                        : ''
                    "
                  >
                    <div class="map-details">
                      <div class="map-name-row">
                        <div class="map-class">
                          <img
                            v-if="
                              player.intended_class === 'soldier' ||
                              player.intended_class === 'demoman'
                            "
                            :src="`/icons/${player.intended_class}.png`"
                            :alt="player.intended_class"
                            class="class-icon-medium"
                          />
                          <template v-if="player.intended_class === 'both'">
                            <img
                              src="/icons/soldier.png"
                              alt="Soldier"
                              class="class-icon-medium dual-icon"
                            />
                            <img
                              src="/icons/demoman.png"
                              alt="Demoman"
                              class="class-icon-medium dual-icon"
                            />
                          </template>
                        </div>
                        <HoverPreview :map-name="player.current_map">
                          <div class="map-name-large">
                            {{ player.current_map }}
                          </div>
                        </HoverPreview>
                      </div>
                    </div>
                  </SmartLink>
                  <div class="server-info-section">
                    <div class="server-name-large">
                      {{ player.shortname }} | {{ player.server_name }}
                    </div>
                    <button
                      @click.stop="connectToServer(player.ipaddr, player.port)"
                      class="global-btn"
                    >
                      Connect
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="currentView === 'servers'" class="table-wrapper">
              <div class="table-header-content">
                <div class="table-header-top">
                  <div class="table-header-icon">🌍</div>
                  <div class="table-header-title-section">
                    <h3 class="table-header-title">Server Status</h3>
                    <p class="table-header-subtitle">Updates every minute</p>
                  </div>
                </div>
                <div class="table-header-filters">
                  <div class="region-buttons">
                    <button
                      :class="{ active: selectedRegion === 'all' }"
                      @click="filterByRegion('all')"
                      class="global-btn"
                    >
                      All Regions
                    </button>
                    <button
                      v-for="region in availableRegions"
                      :key="region.key"
                      :class="{ active: selectedRegion === region.key }"
                      @click="filterByRegion(region.key)"
                      class="global-btn"
                    >
                      {{ region.name }}
                    </button>
                  </div>
                  <div class="server-type-buttons">
                    <button
                      v-for="serverType in availableServerTypes"
                      :key="serverType.key"
                      :class="getServerTypeButtonClass(serverType.key)"
                      @click="toggleServerType(serverType.key)"
                      class="server-type-btn"
                    >
                      {{ serverType.name }}
                    </button>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-dark">
                  <thead>
                    <tr>
                      <th @click="sortServersByRegion" style="cursor: pointer">
                        Location
                        <span v-if="sortBy === 'region' && sortDirection === 1"
                          >↑</span
                        >
                        <span
                          v-else-if="
                            sortBy === 'region' && sortDirection === -1
                          "
                          >↓</span
                        >
                      </th>
                      <th @click="sortServersByName" style="cursor: pointer">
                        Server
                        <span v-if="sortBy === 'name' && sortDirection === 1"
                          >↑</span
                        >
                        <span
                          v-else-if="sortBy === 'name' && sortDirection === -1"
                          >↓</span
                        >
                      </th>
                      <th @click="sortServersByMap" style="cursor: pointer">
                        Map
                        <span v-if="sortBy === 'map' && sortDirection === 1"
                          >↑</span
                        >
                        <span
                          v-else-if="sortBy === 'map' && sortDirection === -1"
                          >↓</span
                        >
                      </th>
                      <th @click="sortServersByPlayers" style="cursor: pointer">
                        Players
                        <span v-if="sortBy === 'players' && sortDirection === 1"
                          >↓</span
                        >
                        <span
                          v-else-if="
                            sortBy === 'players' && sortDirection === -1
                          "
                          >↑</span
                        >
                      </th>
                      <th>Connect</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template
                      v-for="(server, index) in filteredServersData"
                      :key="server.id"
                    >
                      <tr
                        class="fade-in server-row"
                        @click="toggleServerExpansion(server.id)"
                        style="cursor: pointer"
                      >
                        <td class="align-middle">
                          <div class="server-location">
                            <img
                              :src="getFlagImageUrl(server.country_code)"
                              alt="Flag"
                              class="flag-icon"
                            />
                            {{ server.country }}
                          </div>
                        </td>
                        <td class="align-middle">
                          <div class="server-name">
                            <span v-if="expandedServerId === server.id">▼</span>
                            <span v-else>▶</span>
                            {{ server.name }}
                          </div>
                        </td>
                        <SmartLink
                          tag="td"
                          :to="{
                            name: 'MapPage',
                            params: { mapId: server.map_id },
                          }"
                          class="map-cell align-middle map-name clickable"
                          style="color: var(--color-text-clickable)"
                          @click.stop
                        >
                          <HoverPreview :map-name="server.currentMap">
                            {{ server.currentMap }}
                          </HoverPreview>
                        </SmartLink>
                        <td class="align-middle">
                          <div class="player-info">
                            <span v-if="server.hostname !== null">
                              {{ server.playerCount }}/{{ server.maxPlayers }}
                            </span>
                            <div
                              v-if="server.hostname !== null"
                              class="server-status"
                              :class="
                                getServerStatusClass(
                                  server.playerCount,
                                  server.maxPlayers,
                                )
                              "
                            ></div>
                            <span v-else class="server-offline">Offline</span>
                          </div>
                        </td>
                        <td class="align-middle">
                          <button
                            @click.stop="
                              connectToServer(server.ipAddr, server.port)
                            "
                            class="global-btn connect-btn"
                          >
                            Connect
                          </button>
                        </td>
                      </tr>
                      <tr
                        v-if="expandedServerId === server.id"
                        class="server-players-row"
                      >
                        <td colspan="5" class="server-players-container">
                          <div class="players-list">
                            <h5>Players in {{ server.name }}:</h5>
                            <div
                              v-if="
                                !server.players || server.players.length === 0
                              "
                              class="no-players"
                            >
                              No players currently online
                            </div>
                            <div v-else class="players-grid">
                              <SmartLink
                                v-for="player in server.players"
                                :key="player.player_id"
                                :to="{
                                  name: 'PlayerPage',
                                  params: { playerId: player.player_id },
                                }"
                                class="player-item"
                                style="text-decoration: none; color: inherit"
                              >
                                <img
                                  :src="player.steam_avatar"
                                  alt="Avatar"
                                  class="avatar"
                                />
                                <span class="player-name-server">{{
                                  player.name
                                }}</span>
                              </SmartLink>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
              <div
                v-if="filteredServersData.length === 0 && !loading"
                class="no-servers-message"
              >
                <div class="no-servers-content">
                  <span class="no-servers-icon">🔍</span>
                  <h4>No servers found</h4>
                  <p>
                    No servers are currently available for
                    {{ getRegionName(selectedRegion) }}.
                  </p>
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
import ServersSkeleton from "./Skeletons/ServersSkeleton.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Servers",
  components: { ServersSkeleton },
  setup() {
    useHead({ title: "Servers | Tempus Plaza" });
  },
  props: {
    view: {
      type: String,
      default: "topplayers",
    },
  },
  data() {
    return {
      currentView: "topplayers",
      loading: false,
      failedMapImages: new Set(),
      isMobile: window.innerWidth <= 992,
      manualMinMode: localStorage.getItem("minMode") === "true",
      minMode:
        window.innerWidth <= 992
          ? true
          : localStorage.getItem("minMode") === "true",
      topPlayersData: [],
      serversData: [],
      expandedServerId: null,
      sortDirection: 1,
      sortBy: "region",
      selectedRegion: "all",
      selectedServerTypes: ["all"],
      excludedServerTypes: [],
      selectedRank: "all",
      availableRanks: [],
      availableRegions: [
        { key: "north_america", name: "North America" },
        { key: "europe", name: "Europe" },
        { key: "asia", name: "Asia" },
        { key: "oceania", name: "Oceania" },
        { key: "south_america", name: "South America" },
        { key: "africa", name: "Africa" },
        { key: "middle_east", name: "Middle East" },
        { key: "unknown", name: "Unknown" },
      ],
      availableServerTypes: [
        { key: "all", name: "All Maps" },
        { key: "beginner", name: "Beginner" },
        { key: "rank_only", name: "Rank Only" },
        { key: "empty", name: "Empty" },
      ],
    };
  },
  computed: {
    filteredServersData() {
      let filtered = this.serversData;
      if (this.selectedRegion !== "all") {
        filtered = filtered.filter(
          (server) => server.region === this.selectedRegion,
        );
      }
      if (!this.selectedServerTypes.includes("all")) {
        filtered = filtered.filter((server) => {
          const matchesIncluded = this.selectedServerTypes.some((type) =>
            this.matchesServerType(server.name, type),
          );
          const matchesExcluded = this.excludedServerTypes.some((type) =>
            this.matchesServerType(server.name, type),
          );
          return matchesIncluded && !matchesExcluded;
        });
      } else {
        filtered = filtered.filter((server) => {
          const matchesExcluded = this.excludedServerTypes.some((type) =>
            this.matchesServerType(server.name, type),
          );
          return !matchesExcluded;
        });
      }
      return filtered;
    },
  },
  watch: {
    $route(to) {
      if (to.params.view) this.currentView = to.params.view;
    },
    serversData: {
      handler() {
        this.extractAvailableRanks();
      },
      immediate: true,
    },
  },
  async created() {
    await this.fetchData();
    const { view } = this.$route.params;
    if (view) this.currentView = view;
  },
  mounted() {
    window.addEventListener("resize", this.handleResize);
    window.addEventListener("storage", this.handleStorageChange);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.handleResize);
    window.removeEventListener("storage", this.handleStorageChange);
  },
  methods: {
    toggleMinMode() {
      this.manualMinMode = !this.manualMinMode;
      this.minMode = this.manualMinMode;
      localStorage.setItem("minMode", this.manualMinMode);
    },
    handleResize() {
      this.isMobile = window.innerWidth <= 992;
      this.minMode = this.isMobile ? true : this.manualMinMode;
    },
    handleStorageChange(event) {
      if (event.key === "minMode") {
        this.manualMinMode = event.newValue === "true";
        if (!this.isMobile) this.minMode = this.manualMinMode;
      }
    },
    handleMapImageError(mapName) {
      this.failedMapImages.add(mapName);
    },
    extractAvailableRanks() {
      const ranks = new Set();
      this.serversData.forEach((server) => {
        const rankMatch = server.name.match(/rank (\d+)/i);
        if (rankMatch) ranks.add(parseInt(rankMatch[1]));
      });
      this.availableRanks = Array.from(ranks).sort((a, b) => a - b);
    },
    getServerTypeButtonClass(serverType) {
      const isSelected = this.selectedServerTypes.includes(serverType);
      const isExcluded = this.excludedServerTypes.includes(serverType);
      return { active: isSelected && !isExcluded, excluded: isExcluded };
    },
    filterByRegion(region) {
      this.selectedRegion = region;
      this.expandedServerId = null;
    },
    toggleServerType(serverType) {
      if (serverType === "all") {
        this.selectedServerTypes = ["all"];
        this.excludedServerTypes = [];
      } else {
        const isSelected = this.selectedServerTypes.includes(serverType);
        const isExcluded = this.excludedServerTypes.includes(serverType);
        if (!isSelected && !isExcluded) {
          if (this.selectedServerTypes.includes("all")) {
            this.selectedServerTypes = [serverType];
          } else {
            this.selectedServerTypes.push(serverType);
          }
        } else if (isSelected && !isExcluded) {
          this.selectedServerTypes = this.selectedServerTypes.filter(
            (t) => t !== serverType,
          );
          this.excludedServerTypes.push(serverType);
          if (this.selectedServerTypes.length === 0)
            this.selectedServerTypes = ["all"];
        } else if (isExcluded) {
          this.excludedServerTypes = this.excludedServerTypes.filter(
            (t) => t !== serverType,
          );
        }
      }
      this.expandedServerId = null;
    },
    matchesServerType(serverName, type) {
      const name = serverName.toLowerCase();
      switch (type) {
        case "beginner":
          return name.includes("beginner") || name.includes("iniciantes");
        case "rank_only": {
          const hasRankPattern = /rank (\d+)/i.test(name);
          if (!hasRankPattern) return false;
          if (this.selectedRank !== "all") {
            const rankMatch = name.match(/rank (\d+)/i);
            return rankMatch && parseInt(rankMatch[1]) === this.selectedRank;
          }
          return true;
        }
        case "empty": {
          const server = this.serversData.find((s) => s.name === serverName);
          return server && server.playerCount === 0;
        }
        case "all":
          return true;
        default:
          return false;
      }
    },
    getRegionName(regionKey) {
      if (regionKey === "all") return "All Regions";
      const region = this.availableRegions.find((r) => r.key === regionKey);
      return region ? region.name : regionKey;
    },
    toggleServerExpansion(serverId) {
      this.expandedServerId =
        this.expandedServerId === serverId ? null : serverId;
    },
    async fetchData() {
      this.loading = true;
      try {
        await Promise.all([
          this.fetchTopPlayersData(),
          this.fetchServersData(),
        ]);
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        this.loading = false;
      }
    },
    sortServersByPlayers() {
      if (this.sortBy === "players") this.sortDirection *= -1;
      else {
        this.sortBy = "players";
        this.sortDirection = -1;
      }
      this.serversData.sort(
        (a, b) => (a.playerCount - b.playerCount) * this.sortDirection,
      );
    },
    sortServersByMap() {
      if (this.sortBy === "map") this.sortDirection *= -1;
      else {
        this.sortBy = "map";
        this.sortDirection = 1;
      }
      this.serversData.sort(
        (a, b) =>
          (a.currentMap || "").localeCompare(b.currentMap || "") *
          this.sortDirection,
      );
    },
    sortServersByRegion() {
      if (this.sortBy === "region") this.sortDirection *= -1;
      else {
        this.sortBy = "region";
        this.sortDirection = 1;
      }
      const regionOrder = [
        "north_america",
        "europe",
        "asia",
        "oceania",
        "south_america",
        "africa",
        "middle_east",
        "unknown",
      ];
      this.serversData.sort((a, b) => {
        const diff =
          regionOrder.indexOf(a.region) - regionOrder.indexOf(b.region);
        if (diff !== 0) return diff * this.sortDirection;
        return a.country.localeCompare(b.country) * this.sortDirection;
      });
    },
    sortServersByName() {
      if (this.sortBy === "name") this.sortDirection *= -1;
      else {
        this.sortBy = "name";
        this.sortDirection = 1;
      }
      this.serversData.sort(
        (a, b) => a.name.localeCompare(b.name) * this.sortDirection,
      );
    },
    async fetchTopPlayersData() {
      try {
        const response = await fetch(`${API_BASE_URL}/servers/top-players`);
        const data = await response.json();
        this.topPlayersData = data
          .sort((a, b) => {
            if (a.highest_rank === null) return 1;
            if (b.highest_rank === null) return -1;
            return a.highest_rank - b.highest_rank;
          })
          .slice(0, 20)
          .map((player) => ({
            ...player,
            intended_class:
              player.intended_class === "3"
                ? "soldier"
                : player.intended_class === "4"
                ? "demoman"
                : player.intended_class === "5"
                ? "both"
                : player.intended_class || "soldier",
          }));
      } catch (error) {
        console.error("Error fetching top players data:", error);
      }
    },
    async fetchServersData() {
      try {
        const response = await fetch(`${API_BASE_URL}/servers`);
        const data = await response.json();
        const regionOrder = [
          "north_america",
          "europe",
          "asia",
          "oceania",
          "south_america",
          "africa",
          "middle_east",
          "unknown",
        ];
        this.serversData = data
          .filter((server) => server.hidden !== 1)
          .map((server) => ({
            ...server,
            players: server.players ? JSON.parse(server.players) : [],
          }))
          .sort((a, b) => {
            const diff =
              regionOrder.indexOf(a.region) - regionOrder.indexOf(b.region);
            if (diff !== 0) return diff;
            return a.country.localeCompare(b.country);
          });
      } catch (error) {
        console.error("Error fetching servers data:", error);
      }
    },
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    getServerStatusClass(players, maxPlayers) {
      const ratio = players / maxPlayers;
      if (ratio > 0.8) return "status-high";
      if (ratio > 0.4) return "status-medium";
      return "status-low";
    },
    connectToServer(ip, port) {
      window.location.href = `steam://connect/${ip}:${port}`;
    },
    switchView(view) {
      if (this.currentView === view) return;
      this.currentView = view;
      this.$router.push({ name: "Servers", params: { view } });
    },
  },
};
</script>

<style scoped>
.page-header {
  margin-bottom: 2rem;
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

.content-container {
  width: 100%;
  max-width: 1200px;
}

.table-container-wrapper {
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.button-group {
  border: none;
}

.flag-icon {
  width: 20px;
  height: auto;
  margin-right: 5px;
}

.region-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 1rem;
}

.server-type-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.server-type-btn {
  padding: 0.4rem 0.8rem;
  border: 2px solid var(--color-border-soft);
  background: rgba(255, 255, 255, 0.08);
  color: var(--color-text);
  border-radius: 5px;
  cursor: pointer;
  font-weight: 500;
  font-size: 0.85rem;
}

.server-type-btn:hover {
  background: rgba(74, 111, 165, 0.25);
  border-color: var(--color-primary);
}

.server-type-btn.active {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  border-color: var(--color-primary);
  color: white;
}

.server-type-btn.excluded {
  background: rgba(239, 68, 68, 0.6);
  border-color: #ef4444;
  color: white;
}

.server-type-btn.excluded:hover {
  background: rgba(239, 68, 68, 0.8);
}

.no-servers-message {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 3rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
}

.no-servers-content {
  text-align: center;
  color: var(--color-text);
}

.no-servers-icon {
  font-size: 3rem;
  display: block;
  margin-bottom: 1rem;
}

.no-servers-content h4 {
  margin-bottom: 0.5rem;
}

.no-servers-content p {
  opacity: 0.8;
  margin: 0;
}

.table-wrapper {
  width: 100%;
  border-radius: 0 0 8px 8px;
  overflow: hidden;
  border-bottom: 1px solid var(--color-border-soft);
}

.table-container {
  border-radius: 0 0 8px 8px;
}

.table-header-content {
  display: flex;
  flex-direction: column;
  padding: 1.5rem;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
  border-bottom: 1px solid var(--color-border-soft);
}

.topplayers-header {
  display: flex !important;
  flex-direction: row !important;
  align-items: center !important;
  justify-content: space-between;
}

.topplayers-header .table-header-icon {
  margin-right: 1rem;
  margin-bottom: 0;
  flex-shrink: 0;
}

.topplayers-header .table-header-text {
  flex: 1;
}

.min-mode-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  user-select: none;
  margin-left: auto;
  flex-shrink: 0;
}

.min-mode-checkbox input[type="checkbox"] {
  display: none;
}

.checkmark {
  width: 20px;
  height: 20px;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
  background: rgba(255, 255, 255, 0.1);
  position: relative;
  transition: all 0.2s ease;
}

.min-mode-checkbox:hover .checkmark {
  background: rgba(74, 111, 165, 0.3);
}

.min-mode-checkbox input[type="checkbox"]:checked + .checkmark {
  background: var(--color-primary);
}

.min-mode-checkbox input[type="checkbox"]:checked + .checkmark::after {
  content: "✓";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 14px;
  font-weight: bold;
}

.checkbox-label {
  color: var(--color-text);
  font-size: 0.9rem;
  font-weight: 500;
}

.table-header-top {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.table-header-icon {
  font-size: 2rem;
  margin-right: 1rem;
  filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.3));
  flex-shrink: 0;
}

.table-header-title-section {
  flex: 1;
}

.table-header-filters {
  width: 100%;
}

.table-header-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: bold;
  color: var(--color-text);
}

.table-header-subtitle {
  margin: 0.2rem 0 0 0;
  font-size: 0.9rem;
  color: var(--color-text);
  opacity: 0.8;
}

.table-responsive {
  overflow: hidden;
  margin-bottom: 0;
}

.table-dark {
  margin: 0;
}

.table-dark thead {
  border-top: 1px solid var(--color-border-soft);
}

.table-dark th {
  background: rgba(74, 111, 165, 0.3) !important;
  color: var(--color-text);
  text-align: left;
  font-weight: bold;
}

.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  font-weight: bold;
  padding: 4px 6px;
  border: 2px solid var(--color-border-soft);
}

.table-dark td:first-child {
  border-left: none !important;
}
.table-dark td:last-child {
  border-right: none !important;
}
.table-dark tr:last-child td {
  border-bottom: none !important;
}

.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}

.clickable {
  cursor: pointer;
}

.class-icon-small {
  width: 20px;
  height: 20px;
  vertical-align: middle;
}

.avatar {
  width: 25px;
  height: 25px;
  margin-right: 8px;
  border: 1px solid var(--color-primary);
  border-radius: 2px;
}

.player-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.server-status {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
}

.server-offline {
  color: #ef4444;
  font-weight: bold;
  font-size: 0.85rem;
}

.status-high {
  background: #ef4444;
}
.status-medium {
  background: #eab308;
}
.status-low {
  background: #22c55e;
}

.connect-btn {
  padding: 0.25rem 0.75rem;
}

.server-row:hover {
  background: rgba(74, 111, 165, 0.2) !important;
}

.server-players-row {
  background: rgba(37, 55, 82, 0.3) !important;
}

.server-players-container {
  padding: 1rem !important;
}

.players-list h5 {
  color: var(--color-text);
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.players-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 0.5rem;
}

.player-item {
  display: flex;
  align-items: center;
  padding: 0.5rem;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
  cursor: pointer;
}

.player-item:hover {
  background: rgba(74, 111, 165, 0.4);
}

.player-name-server {
  color: var(--color-text-clickable);
  font-weight: bold;
  font-size: 0.9rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.no-players {
  color: var(--color-text);
  opacity: 0.7;
  font-style: italic;
  text-align: center;
  padding: 1rem;
}

.top-players-cards {
  width: 100%;
  border-radius: 0 0 8px 8px;
  overflow: hidden;
}

.players-cards-container {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.02);
}

.players-cards-container.min-mode {
  gap: 0.3rem;
}

.player-card {
  display: grid;
  grid-template-columns: 0.6fr 2fr 2fr minmax(400px, 1.5fr);
  gap: 0.5rem;
  padding: 0.3rem 0.6rem;
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
}

.player-card:hover {
  border-color: var(--color-border-semi-soft);
  background: rgba(74, 111, 165, 0.2);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.player-card:nth-child(odd) {
  background: rgba(255, 255, 255, 0.05);
}
.player-card:nth-child(even) {
  background: rgba(255, 255, 255, 0.08);
}
.player-card:nth-child(odd):hover,
.player-card:nth-child(even):hover {
  background: rgba(74, 111, 165, 0.2);
}

.player-card.min-mode {
  padding: 0.2rem 0.4rem 0.2rem 0.4rem;
  gap: 1rem;
}

.ranks-section {
  display: flex;
  align-items: center;
  justify-content: left;
}

.player-ranks {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.rank-value {
  color: var(--color-text);
  font-weight: bold;
  margin-left: 4px;
}

.player-info-section {
  display: flex;
  align-items: center;
  cursor: pointer;
  border-left: 1px solid var(--color-border-soft);
  border-right: 1px solid var(--color-border-soft);
  padding-left: 1.5rem;
}

.player-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  text-decoration: none;
  color: inherit;
  width: 100%;
}

.player-card.min-mode .player-link {
  gap: 0.5rem;
}

.player-avatar-large {
  width: 54px;
  height: 54px;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
  flex-shrink: 0;
}

.player-avatar-small {
  width: 24px;
  height: 24px;
  border: 1px solid var(--color-primary);
  border-radius: 2px;
  margin-right: 0.5rem;
  vertical-align: middle;
}

.player-details {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  flex: 1;
}

.player-name-large {
  font-size: 1.2rem;
  font-weight: bold;
  width: 210px;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  color: var(--color-text-clickable);
  display: block;
  align-items: center;
  max-width: 250px;
}

.player-card.min-mode .player-name-large {
  font-size: 0.9rem;
  width: auto;
}

.player-country {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--color-text);
  font-size: 0.9rem;
}

.flag-icon-large {
  width: 18px;
  height: auto;
}

.map-info-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  text-decoration: none;
  color: inherit;
  cursor: pointer;
  border-radius: 10px;
  padding-left: 1.5rem;
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  position: relative;
}

.map-info-section::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
  border-radius: inherit;
}

.player-card.min-mode .map-info-section {
  padding-left: 1rem;
}

.player-card.min-mode .map-info-section::before {
  display: none;
}

.map-details {
  display: flex;
  flex-direction: row;
  gap: 0.5rem;
  position: relative;
  z-index: 1;
}

.map-name-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.map-name-large {
  font-size: 1.1rem;
  font-weight: bold;
  color: var(--color-text-clickable);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 235px;
}

.player-card.min-mode .map-name-large {
  font-size: 0.85rem;
}

.map-class {
  display: flex;
  gap: 4px;
  align-items: center;
}

.class-icon-medium {
  width: 36px;
  height: 36px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  padding: 4px;
  border: 2px solid rgba(74, 111, 165, 0.3);
}

.class-icon-medium.dual-icon {
  width: 32px;
  height: 32px;
}

.player-card.min-mode .class-icon-medium {
  width: 24px;
  height: 24px;
  padding: 3px;
}

.player-card.min-mode .class-icon-medium.dual-icon {
  width: 22px;
  height: 22px;
}

.server-info-section {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  border-left: 1px solid var(--color-border-soft);
  min-width: 0;
}

.player-card.min-mode .server-info-section {
  flex-direction: row;
  gap: 1rem;
  padding-left: 1rem;
}

.server-name-large {
  font-size: 0.95rem;
  font-weight: 1000;
  color: var(--color-text);
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
}

.player-card.min-mode .server-name-large {
  font-size: 0.75rem;
}

.server-info-section .global-btn {
  padding: 0.35rem 0.8rem;
  font-size: 0.85rem;
}

.player-card.min-mode .global-btn {
  font-size: 0.75rem;
  padding: 0.25rem 0.6rem;
}

.player-card.min-mode .rank-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  padding: 0.2rem 0.4rem;
}

.player-card.min-mode .rank-value {
  font-size: 1rem;
}

.player-card.min-mode .class-icon-small {
  width: 16px;
  height: 16px;
}

@media (max-width: 1400px) {
  .player-card:not(.min-mode) {
    grid-template-columns: 0.4fr 1.3fr 1.8fr 1.8fr;
  }

  .player-avatar-large {
    width: 40px;
    height: 40px;
  }

  .player-name-large {
    font-size: 1rem;
    max-width: 160px;
  }

  .player-country {
    font-size: 0.8rem;
  }

  .map-name-large {
    font-size: 0.95rem;
  }

  .class-icon-medium {
    width: 28px;
    height: 28px;
  }

  .class-icon-medium.dual-icon {
    width: 26px;
    height: 26px;
  }

  .server-name-large {
    font-size: 0.8rem;
  }

  .player-info-section {
    padding-left: 1rem;
  }
  .map-info-section {
    padding-left: 1rem;
  }
}

@media (max-width: 1199px) {
  .player-card:not(.min-mode) {
    grid-template-columns: 0.4fr 1.3fr 1.6fr 1.6fr;
  }

  .player-avatar-large {
    width: 34px;
    height: 34px;
  }

  .player-name-large {
    font-size: 0.88rem;
    max-width: 120px;
    width: auto;
  }

  .player-country {
    font-size: 0.75rem;
  }

  .map-name-large {
    font-size: 0.85rem;
  }

  .class-icon-medium {
    width: 24px;
    height: 24px;
    padding: 3px;
  }

  .class-icon-medium.dual-icon {
    width: 22px;
    height: 22px;
  }

  .server-name-large {
    font-size: 0.72rem;
  }

  .server-info-section .global-btn {
    padding: 0.28rem 0.6rem;
    font-size: 0.78rem;
  }

  .player-info-section {
    padding-left: 0.75rem;
  }

  .map-info-section {
    padding-left: 0.75rem;
  }

  .rank-value {
    font-size: 0.85rem;
  }

  .class-icon-small {
    width: 16px;
    height: 16px;
  }
}

@media (max-width: 992px) {
  .player-card {
    grid-template-columns: 1fr;
    gap: 0.8rem;
  }

  .player-card.min-mode {
    grid-template-columns: 65px minmax(100px, 200px) minmax(110px, auto) auto;
    gap: 0.5rem;
  }

  .ranks-section {
    border-right: none;
    border-bottom: 1px solid var(--color-border-soft);
    justify-content: center;
  }

  .player-card.min-mode .ranks-section {
    border-bottom: none;
    border-right: 1px solid var(--color-border-soft);
    padding: 0 0.5rem;
  }

  .player-info-section,
  .map-info-section,
  .server-info-section {
    border-left: none;
    padding-left: 0;
  }

  .player-info-section {
    border-right: none;
    border-bottom: 1px solid var(--color-border-soft);
    padding-bottom: 0.8rem;
  }

  .player-card.min-mode .player-info-section {
    border-bottom: none;
    border-right: 1px solid var(--color-border-soft);
    padding: 0 0.5rem 0 0;
  }

  .map-info-section {
    border-bottom: 1px solid var(--color-border-soft);
    padding-bottom: 0.8rem;
    flex-direction: column;
    gap: 0.5rem;
  }

  .player-card.min-mode .map-info-section {
    border-bottom: none;
    flex-direction: row;
    padding: 0 0.5rem 0 0;
    white-space: nowrap;
    overflow: hidden;
  }

  .player-link {
    gap: 0.5rem;
  }

  .player-card.min-mode .player-details {
    flex-direction: row;
    align-items: center;
    gap: 0.3rem;
    white-space: nowrap;
    overflow: hidden;
  }

  .player-card.min-mode .player-country {
    display: none;
  }

  .server-info-section {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.5rem;
  }

  .server-name-large {
    text-align: left;
  }

  .player-card.min-mode .server-info-section {
    display: none;
  }
  .player-card.min-mode .server-name-large {
    display: none;
  }
  .player-card.min-mode .player-name-large {
    max-width: 200px;
  }
}

@media (max-width: 767.98px) {
  .player-card.min-mode {
    grid-template-columns: 65px minmax(100px, 150px) minmax(110px, auto) auto;
    gap: 0.5rem;
  }

  .button-group {
    flex-direction: column;
    width: 100%;
    margin: 0 auto 2rem;
    border-radius: 12px;
  }

  .toggle-btn {
    justify-content: center;
    margin-bottom: 0.5rem;
  }

  .region-buttons,
  .server-type-buttons {
    justify-content: center;
  }

  .server-type-btn {
    padding: 0.35rem 0.7rem;
    font-size: 0.75rem;
  }

  .table-header-content {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }

  .table-responsive {
    display: block;
    overflow-x: auto;
  }

  .table-dark td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .avatar {
    width: 20px;
    height: 20px;
  }

  .table-header-icon {
    font-size: 1.5rem;
  }

  .players-grid {
    grid-template-columns: 1fr;
  }

  .player-item {
    padding: 0.75rem;
  }
}
</style>
