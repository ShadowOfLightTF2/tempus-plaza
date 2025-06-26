<template>
  <div class="container mx-auto py-4 d-flex flex-column align-items-center">
    <div class="content-container">
      <div class="page-header">
        <h1 class="page-title">
          <span class="title-icon">📊</span>
          Activity dashboard
        </h1>
        <p class="page-subtitle">Track the latest records</p>
      </div>
      <div class="button-group">
        <button
          :class="{ active: currentView === 'worldrecords' }"
          @click="switchView('worldrecords')"
          class="toggle-btn btn btn-dark update-button"
        >
          <span class="btn-text">World Records</span>
        </button>
        <button
          :class="{ active: currentView === 'toptimes' }"
          @click="switchView('toptimes')"
          class="toggle-btn btn btn-dark update-button"
        >
          <span class="btn-text">Top Times</span>
        </button>
        <button
          :class="{ active: currentView === 'group1s' }"
          @click="switchView('group1s')"
          class="toggle-btn btn btn-dark update-button"
        >
          <span class="btn-text">Group 1s</span>
        </button>
      </div>
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-light" role="status">
          <span class="visually-hidden">Loading {{ currentView }}...</span>
        </div>
      </div>
      <div v-else class="table-container">
        <div v-if="currentView === 'worldrecords'" class="table-wrapper">
          <div class="table-header-content">
            <div class="table-header-icon">🏆</div>
            <div class="table-header-text">
              <h3 class="table-header-title">Latest world records</h3>
              <p class="table-header-subtitle">Updates every 5 minutes</p>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-dark">
              <thead>
                <tr>
                  <th>Player</th>
                  <th>Map</th>
                  <th>Type</th>
                  <th>Time</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(record, index) in worldRecordsData"
                  :key="record.id"
                >
                  <td
                    class="name-cell align-middle player-name clickable name-column"
                    @click="goToPlayer(record.player_id)"
                  >
                    <img
                      :src="record.avatar"
                      :alt="record.player + ' avatar'"
                      class="avatar"
                      onerror="this.src='/avatars/golly.jpg'"
                    />
                    {{ record.player }}
                  </td>
                  <td
                    class="name-cell align-middle map-name clickable"
                    @click="goToMap(record.map_id)"
                  >
                    <img
                      :src="`/tempus-plaza/icons/${record.class}.png`"
                      :alt="record.class"
                      class="class-icon-small"
                    />
                    {{ record.map }}
                  </td>
                  <td>
                    <div class="type-cell">
                      <span
                        class="record-type"
                        :style="{ color: 'var(--color-text)' }"
                      >
                        <template v-if="record.recordType === 'map'"
                          >🌍 Map</template
                        >
                        <template v-else-if="record.recordType === 'course'"
                          >🚩 Course {{ record.index }}</template
                        >
                        <template v-else-if="record.recordType === 'bonus'"
                          >⭐ Bonus {{ record.index }}</template
                        >
                      </span>
                    </div>
                  </td>
                  <td class="timestamp-cell">{{ record.timestamp }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-if="currentView === 'toptimes'" class="table-wrapper">
          <div class="table-header-content">
            <div class="table-header-icon">🥇</div>
            <div class="table-header-text">
              <h3 class="table-header-title">Latest top times</h3>
              <p class="table-header-subtitle">Updates twice a day</p>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-dark">
              <thead>
                <tr>
                  <th>Player</th>
                  <th>Map</th>
                  <th>Type</th>
                  <th>Rank</th>
                  <th>Time</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(record, index) in topTimesData" :key="record.id">
                  <td
                    class="name-cell align-middle player-name clickable name-column"
                    @click="goToPlayer(record.player_id)"
                  >
                    <img
                      :src="record.avatar"
                      :alt="record.player + ' avatar'"
                      class="avatar"
                      onerror="this.src='/avatars/golly.jpg'"
                    />
                    {{ record.player }}
                  </td>
                  <td
                    class="name-cell align-middle map-name clickable"
                    @click="goToMap(record.map_id)"
                  >
                    <img
                      :src="`/tempus-plaza/icons/${record.class}.png`"
                      :alt="record.class"
                      class="class-icon-small"
                    />
                    {{ record.map }}
                  </td>
                  <td>
                    <div class="type-cell">
                      <span
                        class="record-type"
                        :style="{ color: 'var(--color-text)' }"
                      >
                        <template v-if="record.recordType === 'map'">
                          🌍 Map
                        </template>
                        <template v-else-if="record.recordType === 'course'">
                          🚩 Course {{ record.index }}
                        </template>
                        <template v-else-if="record.recordType === 'bonus'">
                          ⭐ Bonus {{ record.index }}
                        </template>
                      </span>
                    </div>
                  </td>
                  <td class="rank-cell">#{{ record.rank }}</td>
                  <td class="timestamp-cell">{{ record.timestamp }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-if="currentView === 'group1s'" class="table-wrapper">
          <div class="table-header-content">
            <div class="table-header-icon">⏱️</div>
            <div class="table-header-text">
              <h3 class="table-header-title">Latest group 1s</h3>
              <p class="table-header-subtitle">Updates twice a day</p>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-dark">
              <thead>
                <tr>
                  <th>Player</th>
                  <th>Map</th>
                  <th>Type</th>
                  <th>Rank</th>
                  <th>Time</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(record, index) in group1sData" :key="record.id">
                  <td
                    class="name-cell align-middle player-name clickable name-column"
                    @click="goToPlayer(record.player_id)"
                  >
                    <img
                      :src="record.avatar"
                      :alt="record.player + ' avatar'"
                      class="avatar"
                      onerror="this.src='/avatars/golly.jpg'"
                    />
                    {{ record.player }}
                  </td>
                  <td
                    class="name-cell align-middle map-name clickable"
                    @click="goToMap(record.map_id)"
                  >
                    <img
                      :src="`/tempus-plaza/icons/${record.class}.png`"
                      :alt="record.class"
                      class="class-icon-small"
                    />
                    {{ record.map }}
                  </td>
                  <td>
                    <div class="type-cell">
                      <span
                        class="record-type"
                        :style="{ color: 'var(--color-text)' }"
                      >
                        <template v-if="record.recordType === 'map'">
                          🌍 Map
                        </template>
                        <template v-else-if="record.recordType === 'course'">
                          🚩 Course {{ record.index }}
                        </template>
                        <template v-else-if="record.recordType === 'bonus'">
                          ⭐ Bonus {{ record.index }}
                        </template>
                      </span>
                    </div>
                  </td>
                  <td class="rank-cell">#{{ record.rank }}</td>
                  <td class="timestamp-cell">{{ record.timestamp }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { formatDate, formatDuration } from "@/utils/calculations";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Activity",
  props: {
    view: {
      type: String,
      default: "worldrecords",
    },
  },
  data() {
    return {
      currentView: this.view,
      worldRecordsData: [],
      topTimesData: [],
      group1sData: [],
      loading: false,
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
    document.title = "Tempus plaza - Activity";
    await this.fetchData();
    const { view } = this.$route.params;
    if (view) {
      this.currentView = view;
    }
  },
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        await this.fetchRecordsData();
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        this.loading = false;
      }
    },
    async fetchRecordsData() {
      try {
        const [worldRecordsResponse, topTimesResponse, group1sResponse] =
          await Promise.all([
            fetch(`${API_BASE_URL}/maps/activity`),
            fetch(`${API_BASE_URL}/maps/recent-top-times`),
            fetch(`${API_BASE_URL}/maps/recent-g1s`),
          ]);

        const [worldRecordsData, topTimesData, group1sData] = await Promise.all(
          [
            worldRecordsResponse.json(),
            topTimesResponse.json(),
            group1sResponse.json(),
          ]
        );

        this.worldRecordsData = worldRecordsData.map((item) => ({
          id: item.id,
          player: item.player_name,
          player_id: item.player_id,
          avatar: item.steam_avatar,
          map: item.map_name,
          map_id: item.map_id,
          time: formatDuration(item.duration),
          class: item.class,
          recordType: item.map_type,
          index: item.type_index,
          timestamp: formatDate(item.record_date),
        }));

        this.topTimesData = topTimesData.map((record) => ({
          id: record.id,
          player: record.player_name,
          player_id: record.player_id,
          avatar: record.steam_avatar,
          map: record.map_name,
          map_id: record.map_id,
          time: formatDuration(record.duration),
          rank: record.rank,
          class: record.class,
          recordType: record.record_type,
          index: record.index,
          timestamp: formatDate(record.date),
        }));

        this.group1sData = group1sData.map((record) => ({
          id: record.id,
          player: record.player_name,
          player_id: record.player_id,
          avatar: record.steam_avatar,
          map: record.map_name,
          map_id: record.map_id,
          time: formatDuration(record.duration),
          rank: record.rank,
          class: record.class,
          recordType: record.record_type,
          index: record.index,
          timestamp: formatDate(record.date),
        }));
      } catch (error) {
        console.error("There was an error fetching the records data:", error);
      }
    },
    switchView(view) {
      if (this.currentView === view) return;
      this.currentView = view;
      this.$router.push({ name: "Activity", params: { view } });
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
  },
};
</script>

<style scoped>
.bg-dark-custom {
  background: var(--color-background);
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

.page-header {
  text-align: center;
  margin-bottom: 2rem;
  padding: 1.5rem 0;
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
  justify-content: space-between;
  padding: 1.5rem;
  background: linear-gradient(135deg, var(--color-primary), var(--color-box));
  border-bottom: 1px solid var(--color-border);
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

.table-header-badge {
  display: flex;
  align-items: center;
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

.name-cell {
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
.map-name:hover,
.map-cell:hover {
  background: var(--color-primary);
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

.player-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.timestamp-cell {
  color: var(--color-text);
}

/* Responsive styles for mobile phones */
@media (max-width: 767.98px) {
  .button-group {
    flex-direction: column;
    width: 100%;
    max-width: 300px;
    margin: 0 auto 2rem;
    border-bottom: 1px solid var(--color-border) !important;
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

  .name-cell,
  .name-column {
    white-space: nowrap;
    max-width: 150px;
  }

  .avatar {
    width: 20px;
    height: 20px;
  }

  .table-header-icon {
    font-size: 1.5rem;
  }
}
</style>
