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
              <div class="table-header-content">
                <div class="table-header-icon">🏆</div>
                <div class="table-header-text">
                  <h3 class="table-header-title">Top players</h3>
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
                <div class="table-header-icon">🌍</div>
                <div class="table-header-text">
                  <h3 class="table-header-title">Server status</h3>
                  <p class="table-header-subtitle">Updates every 5 minutes</p>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-dark">
                  <thead>
                    <tr>
                      <th @click="sortServersByRegion" style="cursor: pointer">
                        Location
                        <span v-if="sortDirection === 1">↑</span>
                        <span v-else>↓</span>
                      </th>
                      <th>Server</th>
                      <th>Map</th>
                      <th @click="sortServersByPlayers" style="cursor: pointer">
                        Players
                        <span v-if="sortDirection === 1">↓</span>
                        <span v-else>↑</span>
                      </th>
                      <th>Connect</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template
                      v-for="(server, index) in serversData"
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
    };
  },
  watch: {
    $route(to) {
      if (to.params.view) {
        this.currentView = to.params.view;
      }
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
      this.sortDirection *= -1;
      this.serversData.sort((a, b) => {
        return (a.playerCount - b.playerCount) * this.sortDirection;
      });
    },
    sortServersByRegion() {
      this.sortDirection *= -1;
      const regionOrder = [
        "north_america",
        "europe",
        "asia",
        "oceania",
        "south_america",
        "africa",
        "middle_east",
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
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
  border-bottom: 1px solid var(--color-border-soft);
}

.table-header-icon {
  font-size: 2rem;
  margin-right: 1rem;
  filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.3));
}

.table-header-text {
  flex: 1;
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
