<template>
  <div class="min-h-screen bg-dark-custom">
    <div class="header-hero">
      <div class="header-overlay"></div>
      <div class="header-content">
        <h1 class="header-title">Tempus plaza</h1>
        <p class="header-subtitle">Information about the Tempus network</p>
      </div>
    </div>
    <div class="container mx-auto py-4 d-flex flex-column align-items-center">
      <div class="content-container">
        <div class="button-group">
          <button
            :class="{ active: currentView === 'topplayers' }"
            @click="switchView('topplayers')"
            class="toggle-btn btn btn-dark update-button"
          >
            Top players online
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
                  >
                    <td>
                      {{ player.highest_rank }} {{ player.highest_rank_type }}
                    </td>
                    <td
                      class="name-cell align-middle player-name clickable"
                      @click="goToPlayer(player.player_id)"
                      style="color: var(--color-text-clickable)"
                    >
                      <img
                        :src="player.steam_avatar"
                        alt="Avatar"
                        class="avatar"
                      />
                      {{ player.player_name }}
                    </td>
                    <td
                      class="map-cell align-middle map-name clickable"
                      @click="goToMap(player.map_id)"
                      style="color: var(--color-text-clickable)"
                    >
                      {{ player.current_map }}
                    </td>
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
                  <tr
                    v-for="(server, index) in serversData"
                    :key="server.id"
                    @click="selectServer(server)"
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
                      <div class="server-name">{{ server.name }}</div>
                    </td>
                    <td
                      class="map-cell align-middle map-name clickable"
                      @click="goToMap(server.map_id)"
                    >
                      {{ server.currentMap }}
                    </td>
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
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Home",
  data() {
    return {
      currentView: "topplayers",
      loading: false,
      topPlayersData: [],
      serversData: [],
      selectedServer: null,
      sortDirection: 1,
    };
  },
  async created() {
    document.title = "Tempus plaza - Home";
    await this.fetchData();
  },
  methods: {
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
        const response = await fetch(
          "http://localhost:3000/servers/top-players"
        );
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
        const response = await fetch("http://localhost:3000/servers");
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
        this.serversData = data.sort((a, b) => {
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
    goToPlayer(playerId) {
      this.$router.push({
        name: "PlayerPage",
        params: { playerId: playerId },
      });
    },
    goToMap(mapId) {
      this.$router.push({
        name: "Records",
        params: { mapId: mapId },
      });
    },
    selectServer(server) {
      this.selectedServer = server;
    },
  },
};
</script>

<style scoped>
.header-hero {
  position: relative;
  height: 16rem;
  background: linear-gradient(90deg, var(--color-primary), var(--color-box));
  display: flex;
  align-items: center;
  justify-content: center;
}

.header-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
}
.header-content {
  position: relative;
  text-align: center;
  color: white;
  z-index: 10;
}

.header-title {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  color: var(--color-text);
  margin: 0 auto;
  font-size: 2.5rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.header-subtitle {
  font-size: 1.25rem;
  opacity: 0.9;
}

.header-text {
  margin-left: 10px;
  text-align: left;
  font-weight: bold;
}

.header-title {
  font-size: 3rem;
  font-weight: bold;
}
.bg-dark-custom {
  background: var(--color-background);
}

.players-title {
  color: var(--color-text);
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

.table-wrapper {
  width: 100%;
  border-radius: 0 0 8px 8px;
  border-top: none;
  border: 1px solid var(--color-border);
}

.table-header-content {
  display: flex;
  align-items: center;
  padding: 10px;
  background: linear-gradient(135deg, var(--color-primary), var(--color-box));
  border-bottom: 2px solid var(--color-border);
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
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  margin-bottom: 0px;
}

.table-dark {
  margin: 0px;
}

.table-dark th {
  background: var(--color-primary-dark);
  color: var(--color-text);
  text-align: left;
  font-weight: bold;
  border-top: 1px solid var(--color-border-soft);
  border-bottom: 1px solid var(--color-border-soft);
}

.table-dark td {
  background: var(--color-box);
  color: var(--color-text);
  font-weight: bold;
  padding: 6px;
}

.table-dark tr:nth-child(odd) td {
  background: var(--color-row-odd);
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
  background: var(--color-primary) !important;
}

.table-dark tr:nth-child(odd) .name-cell:hover {
  background: var(--color-primary);
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
  transition: background-color 0.2s ease;
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
  }
}
</style>
