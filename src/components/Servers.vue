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

          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-light" role="status">
              <span class="visually-hidden">Loading {{ currentView }}...</span>
            </div>
          </div>
          <div v-else class="table-container">
            <div v-if="currentView === 'topplayers'" class="table-wrapper">
              <div class="table-header-content topplayers-header">
                <div class="table-header-icon">🏆</div>
                <div class="table-header-text">
                  <h3 class="table-header-title">Top Players Online</h3>
                  <p class="table-header-subtitle">Updates every 5 minutes</p>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-dark">
                  <thead>
                    <tr>
                      <th>Rank</th>
                      <th>Player</th>
                      <th>Map</th>
                      <th>Server</th>
                      <th>Connect</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(player, index) in topPlayersData"
                      :key="player.player_id"
                      class="fade-in"
                    >
                      <td>
                        {{ player.highest_rank }} {{ player.highest_rank_type }}
                      </td>
                      <SmartLink
                        tag="td"
                        :to="{
                          name: 'PlayerPage',
                          params: { playerId: player.player_id },
                        }"
                        class="name-cell align-middle player-name clickable"
                      >
                        <img
                          :src="player.steam_avatar"
                          alt="Avatar"
                          class="avatar"
                        />
                        {{ player.player_name }}
                      </SmartLink>
                      <SmartLink
                        tag="td"
                        :to="{
                          name: 'MapPage',
                          params: { mapId: player.map_id },
                        }"
                        class="map-cell align-middle map-name clickable"
                      >
                        {{ player.current_map }}
                      </SmartLink>
                      <td>{{ player.shortname }} | {{ player.server_name }}</td>
                      <td class="align-middle">
                        <button
                          @click.stop="
                            connectToServer(player.ipaddr, player.port)
                          "
                          class="btn btn-primary btn-sm connect-btn"
                          style="
                            background: var(--color-primary);
                            border: none;
                            font-weight: bold;
                          "
                        >
                          Connect
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div v-if="currentView === 'servers'" class="table-wrapper">
              <div class="table-header-content">
                <div class="table-header-top">
                  <div class="table-header-icon">🌍</div>
                  <div class="table-header-title-section">
                    <h3 class="table-header-title">Server Status</h3>
                    <p class="table-header-subtitle">Updates every 5 minutes</p>
                  </div>
                </div>
                <div class="table-header-filters">
                  <!-- Region Filters -->
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
                  <!-- Server Type Filters -->
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
                      <th>Server</th>
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
                          {{ server.currentMap }}
                        </SmartLink>
                        <td class="align-middle">
                          <div class="player-info">
                            <span
                              >{{ server.playerCount }}/{{
                                server.maxPlayers
                              }}</span
                            >
                            <div
                              class="server-status"
                              :class="
                                getServerStatusClass(
                                  server.playerCount,
                                  server.maxPlayers
                                )
                              "
                            ></div>
                          </div>
                        </td>
                        <td class="align-middle">
                          <button
                            @click.stop="
                              connectToServer(server.ipAddr, server.port)
                            "
                            class="btn btn-primary btn-sm connect-btn"
                            style="
                              background: var(--color-primary);
                              border: none;
                              font-weight: bold;
                            "
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

              <!-- No servers message when filtered -->
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
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Servers",
  setup() {
    useHead({
      title: "Tempus Plaza | Servers",
    });
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

      // Filter by region
      if (this.selectedRegion !== "all") {
        filtered = filtered.filter(
          (server) => server.region === this.selectedRegion
        );
      }

      // Filter by server type (include/exclude logic)
      if (!this.selectedServerTypes.includes("all")) {
        filtered = filtered.filter((server) => {
          const matchesIncluded = this.selectedServerTypes.some((type) =>
            this.matchesServerType(server.name, type)
          );

          const matchesExcluded = this.excludedServerTypes.some((type) =>
            this.matchesServerType(server.name, type)
          );

          return matchesIncluded && !matchesExcluded;
        });
      } else {
        // When "all" is selected, only apply exclusions
        filtered = filtered.filter((server) => {
          const matchesExcluded = this.excludedServerTypes.some((type) =>
            this.matchesServerType(server.name, type)
          );

          return !matchesExcluded;
        });
      }

      return filtered;
    },

    canIncrementRank() {
      if (this.selectedRank === "all") return this.availableRanks.length > 0;
      const currentIndex = this.availableRanks.indexOf(this.selectedRank);
      return currentIndex < this.availableRanks.length - 1;
    },

    canDecrementRank() {
      if (this.selectedRank === "all") return this.availableRanks.length > 0;
      const currentIndex = this.availableRanks.indexOf(this.selectedRank);
      return currentIndex > 0;
    },
  },
  watch: {
    $route(to) {
      if (to.params.view) {
        this.currentView = to.params.view;
      }
    },
    serversData: {
      handler() {
        this.extractAvailableRanks();
      },
      immediate: true,
    },
  },
  async created() {
    document.title = "Tempus plaza - Home";
    await this.fetchData();
    const { view } = this.$route.params;
    if (view) {
      this.currentView = view;
    }
  },
  methods: {
    extractAvailableRanks() {
      const ranks = new Set();

      this.serversData.forEach((server) => {
        const name = server.name.toLowerCase();
        const rankMatch = name.match(/rank (\d+)/i);
        if (rankMatch) {
          ranks.add(parseInt(rankMatch[1]));
        }
      });

      this.availableRanks = Array.from(ranks).sort((a, b) => a - b);
    },

    getServerTypeButtonClass(serverType) {
      const isSelected = this.selectedServerTypes.includes(serverType);
      const isExcluded = this.excludedServerTypes.includes(serverType);

      return {
        active: isSelected && !isExcluded,
        excluded: isExcluded,
      };
    },

    filterByRegion(region) {
      this.selectedRegion = region;
      // Reset expanded server when filtering
      this.expandedServerId = null;
    },

    toggleServerType(serverType) {
      if (serverType === "all") {
        // If "All Maps" is clicked, clear other selections and exclusions
        this.selectedServerTypes = ["all"];
        this.excludedServerTypes = [];
      } else {
        const isCurrentlySelected =
          this.selectedServerTypes.includes(serverType);
        const isCurrentlyExcluded =
          this.excludedServerTypes.includes(serverType);

        if (!isCurrentlySelected && !isCurrentlyExcluded) {
          // First click: add to selected
          if (this.selectedServerTypes.includes("all")) {
            this.selectedServerTypes = [serverType];
          } else {
            this.selectedServerTypes.push(serverType);
          }
        } else if (isCurrentlySelected && !isCurrentlyExcluded) {
          // Second click: move to excluded
          this.selectedServerTypes = this.selectedServerTypes.filter(
            (type) => type !== serverType
          );
          this.excludedServerTypes.push(serverType);

          // If no types are selected, default to "all"
          if (this.selectedServerTypes.length === 0) {
            this.selectedServerTypes = ["all"];
          }
        } else if (isCurrentlyExcluded) {
          // Third click: remove from excluded
          this.excludedServerTypes = this.excludedServerTypes.filter(
            (type) => type !== serverType
          );
        }
      }

      // Reset expanded server when filtering
      this.expandedServerId = null;
    },

    matchesServerType(serverName, type) {
      const name = serverName.toLowerCase();

      switch (type) {
        case "beginner":
          return name.includes("beginner") || name.includes("iniciantes");
        case "rank_only":
          const hasRankPattern = /rank (\d+)/i.test(name);
          if (!hasRankPattern) return false;

          // If a specific rank is selected, match only that rank
          if (this.selectedRank !== "all") {
            const rankMatch = name.match(/rank (\d+)/i);
            return rankMatch && parseInt(rankMatch[1]) === this.selectedRank;
          }

          return true;
        case "empty":
          // Find the server in serversData to check player count
          const server = this.serversData.find((s) => s.name === serverName);
          return server && server.playerCount === 0;
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
      if (this.expandedServerId === serverId) {
        this.expandedServerId = null;
      } else {
        this.expandedServerId = serverId;
      }
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
      if (this.sortBy === "players") {
        this.sortDirection *= -1;
      } else {
        this.sortBy = "players";
        this.sortDirection = -1;
      }

      this.serversData.sort((a, b) => {
        return (a.playerCount - b.playerCount) * this.sortDirection;
      });
    },

    sortServersByMap() {
      if (this.sortBy === "map") {
        this.sortDirection *= -1;
      } else {
        this.sortBy = "map";
        this.sortDirection = 1;
      }

      this.serversData.sort((a, b) => {
        const mapA = a.currentMap || "";
        const mapB = b.currentMap || "";
        return mapA.localeCompare(mapB) * this.sortDirection;
      });
    },

    sortServersByRegion() {
      if (this.sortBy === "region") {
        this.sortDirection *= -1;
      } else {
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
        const regionA = regionOrder.indexOf(a.region);
        const regionB = regionOrder.indexOf(b.region);

        if (regionA !== regionB)
          return (regionA - regionB) * this.sortDirection;

        return a.country.localeCompare(b.country) * this.sortDirection;
      });
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
          .slice(0, 20);
      } catch (error) {
        console.error(
          "There was an error fetching the top players data:",
          error
        );
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
            const regionA = regionOrder.indexOf(a.region);
            const regionB = regionOrder.indexOf(b.region);

            if (regionA !== regionB) return regionA - regionB;
            return a.country.localeCompare(b.country);
          });
      } catch (error) {
        console.error("There was an error fetching the servers data:", error);
      }
    },

    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },

    switchView(view) {
      if (this.currentView === view) return;
      this.currentView = view;
    },

    getServerStatusClass(players, maxPlayers) {
      const ratio = players / maxPlayers;
      if (ratio > 0.8) return "status-high";
      if (ratio > 0.4) return "status-medium";
      return "status-low";
    },

    connectToServer(ip, port) {
      window.open(`steam://connect/${ip}:${port}`, "_blank");
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

.flag-icon {
  width: 20px;
  height: auto;
  margin-right: 5px;
}

.content-container {
  width: 100%;
  max-width: 1200px;
}

.button-group {
  border: none;
}

/* Region Filter Styles */
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
  transition: all 0.2s ease;
  font-weight: 500;
  font-size: 0.85rem;
}

.server-type-btn:hover {
  background: rgba(74, 111, 165, 0.25);
  border-color: var(--color-primary);
}

.server-type-btn.active {
  background: rgba(74, 111, 165, 0.8);
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

/* Rank Selector Styles */
.rank-selector-container {
  margin-top: 1rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  border: 1px solid var(--color-border-soft);
}

.rank-selector-label {
  display: block;
  color: var(--color-text);
  font-weight: 600;
  margin-bottom: 0.75rem;
  font-size: 0.9rem;
}

.rank-selector-wrapper {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.rank-all {
  align-self: flex-start;
}

/* Rank Picker Styles */
.rank-picker {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: rgba(255, 255, 255, 0.08);
  border: 2px solid var(--color-border-soft);
  border-radius: 6px;
  width: 4rem;
  user-select: none;
}

.rank-picker-btn {
  width: 100%;
  padding: 0.25rem;
  border: none;
  background: rgba(74, 111, 165, 0.3);
  color: var(--color-text);
  cursor: pointer;
  transition: all 0.15s ease;
  font-size: 0.8rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
}

.rank-picker-btn:hover:not(:disabled) {
  background: rgba(74, 111, 165, 0.6);
}

.rank-picker-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.rank-picker-up {
  border-radius: 4px 4px 0 0;
  border-bottom: 1px solid var(--color-border-soft);
}

.rank-picker-down {
  border-radius: 0 0 4px 4px;
  border-top: 1px solid var(--color-border-soft);
}

.rank-picker-display {
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 2rem;
  background: rgba(255, 255, 255, 0.05);
  width: 100%;
}

.rank-picker-value {
  font-weight: bold;
  font-size: 1rem;
  color: var(--color-text);
}

.rank-btn {
  padding: 0.5rem 1rem;
  border: 2px solid var(--color-border-soft);
  background: rgba(255, 255, 255, 0.08);
  color: var(--color-text);
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 500;
  font-size: 0.85rem;
  white-space: nowrap;
  min-width: fit-content;
}

.rank-btn:hover {
  background: rgba(74, 111, 165, 0.25);
  border-color: var(--color-primary);
}

.rank-btn.active {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: white;
}

.rank-number {
  min-width: 3rem;
  text-align: center;
}

.filter-indicator {
  color: var(--color-primary);
  font-weight: bold;
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
  color: var(--color-text);
}

.no-servers-content p {
  opacity: 0.8;
  margin: 0;
}

.table-wrapper {
  width: 100%;
  border-radius: 0 0 8px 8px;
  overflow: hidden;
  border-top: none;
  border-bottom: 1px solid var(--color-border-soft);
}

.table-container-wrapper {
  box-shadow: 0 0px 20px rgb(0, 0, 0);
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
}

.topplayers-header .table-header-icon {
  margin-right: 1rem;
  margin-bottom: 0;
}

.topplayers-header .table-header-text {
  flex: 1;
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
  margin-bottom: 0px;
}

.table-dark {
  margin: 0px;
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
  padding: 6px;
  border: 2px solid var(--color-border-soft);
}

.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}
.table-dark tr:nth-child(odd) .name-cell:hover {
  background: rgba(74, 111, 165, 0.8) !important;
}

.name-cell,
.map-cell {
  max-width: 250px;
  white-space: normal;
  overflow: hidden;
  text-overflow: ellipsis;
  color: var(--color-text-clickable) !important;
}

.name-column {
  width: auto;
  white-space: nowrap;
}

.player-name:hover,
.map-name:hover {
  background: rgba(74, 111, 165, 0.8) !important;
}

.clickable {
  cursor: pointer;
}

.class-icon-small {
  width: 20px;
  height: 20px;
  margin-right: 8px;
  vertical-align: middle;
}

.avatar {
  width: 25px;
  height: 25px;
  margin-right: 8px;
  border: 1px solid var(--color-primary);
  border-radius: 2px;
}

.type-cell {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.record-type {
  padding: 0.125rem 0.5rem;
  border-radius: 0.25rem;
  font-weight: bold;
}

.server-info {
  display: flex;
  flex-direction: column;
}

.server-name {
  font-weight: bold;
  color: var(--color-text);
}

.server-ip {
  color: var(--color-text);
  opacity: 0.7;
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

.status-high {
  background: #ef4444;
}

.status-medium {
  background: #eab308;
}

.status-low {
  background: #22c55e;
}

.location-info {
  color: var(--color-text);
}

.connect-btn {
  padding: 0.25rem 0.75rem;
  background: rgba(57, 106, 141, 0.432) !important;
}

.connect-btn:hover {
  background: var(--color-row) !important;
}

.timestamp-cell {
  color: var(--color-text);
}

@media (max-width: 767.98px) {
  .button-group {
    flex-direction: column;
    width: 100%;
    max-width: 300px;
    margin: 0 auto 2rem;
    border-radius: 12px;
  }

  .toggle-btn {
    justify-content: center;
    margin-bottom: 0.5rem;
  }

  .region-filters {
    padding: 1rem;
  }

  .region-buttons {
    justify-content: center;
  }

  .server-type-buttons {
    justify-content: center;
  }

  .region-btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
  }

  .server-type-btn {
    padding: 0.35rem 0.7rem;
    font-size: 0.75rem;
  }

  .rank-selector-container {
    padding: 0.75rem;
  }

  .rank-btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
  }

  .rank-picker {
    width: 3.5rem;
  }

  .rank-picker-btn {
    padding: 0.2rem;
    font-size: 0.7rem;
  }

  .rank-picker-display {
    padding: 0.4rem;
    min-height: 1.5rem;
  }

  .rank-picker-value {
    font-size: 0.9rem;
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

@media (max-width: 767.98px) {
  .players-grid {
    grid-template-columns: 1fr;
  }

  .player-item {
    padding: 0.75rem;
  }
}
</style>
