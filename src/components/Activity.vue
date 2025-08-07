<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div
      class="container mx-auto py-4 d-flex flex-column align-items-center position-relative"
      style="z-index: 1"
    >
      <div class="content-container">
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">📊</span>
            Activity Dashboard
          </h1>
          <p class="page-subtitle">Track the latest records</p>
        </div>
        <hr class="divider" style="width: 100%" />
        <div class="table-container-wrapper">
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
                  <h3 class="table-header-title">Latest World Records</h3>
                  <p class="table-header-subtitle">Updates every 5 minutes</p>
                </div>
                <div class="filter-container">
                  <div class="filter-group">
                    <h6 class="filter-title text-light mb-2">Class</h6>
                    <div class="class-filter-container">
                      <button
                        @click="toggleClassFilter('soldier')"
                        :class="{
                          active:
                            filterOptions.selectedClasses.includes('soldier'),
                        }"
                        class="global-btn"
                      >
                        Soldier
                      </button>
                      <button
                        @click="toggleClassFilter('demoman')"
                        :class="{
                          active:
                            filterOptions.selectedClasses.includes('demoman'),
                        }"
                        class="global-btn"
                      >
                        Demoman
                      </button>
                    </div>
                  </div>
                  <div class="filter-group">
                    <h6 class="filter-title text-light mb-2">Type</h6>
                    <div class="type-filter-container">
                      <button
                        @click="toggleTypeFilter('map')"
                        :class="{
                          active: filterOptions.selectedTypes.includes('map'),
                        }"
                        class="global-btn"
                      >
                        Map
                      </button>
                      <button
                        @click="toggleTypeFilter('course')"
                        :class="{
                          active:
                            filterOptions.selectedTypes.includes('course'),
                        }"
                        class="global-btn"
                      >
                        Course
                      </button>
                      <button
                        @click="toggleTypeFilter('bonus')"
                        :class="{
                          active: filterOptions.selectedTypes.includes('bonus'),
                        }"
                        class="global-btn"
                      >
                        Bonus
                      </button>
                    </div>
                  </div>
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
                      v-for="(record, index) in filteredWorldRecordsData"
                      :key="record.id"
                      class="fade-in"
                    >
                      <SmartLink
                        tag="td"
                        :to="{
                          name: 'PlayerPage',
                          params: { playerId: record.player_id },
                        }"
                        class="name-cell align-middle player-name clickable name-column"
                      >
                        <img
                          :src="record.avatar"
                          :alt="record.player + ' avatar'"
                          class="avatar"
                          onerror="this.src='/avatars/golly.jpg'"
                        />
                        {{ record.player }}
                      </SmartLink>
                      <SmartLink
                        tag="td"
                        :to="{
                          name: 'MapPage',
                          params: { mapId: record.map_id },
                        }"
                        class="name-cell align-middle map-name clickable"
                      >
                        <img
                          :src="`/icons/${record.class}.png`"
                          :alt="record.class"
                          class="class-icon-small"
                        />
                        {{ record.map }}
                      </SmartLink>
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
                  <h3 class="table-header-title">Latest Top Times</h3>
                  <p class="table-header-subtitle">Updates every 6 hours</p>
                </div>
                <div class="filter-container">
                  <div class="filter-group">
                    <h6 class="filter-title text-light mb-2">Class</h6>
                    <div class="class-filter-container">
                      <button
                        @click="toggleClassFilter('soldier')"
                        :class="{
                          active:
                            filterOptions.selectedClasses.includes('soldier'),
                        }"
                        class="global-btn"
                      >
                        Soldier
                      </button>
                      <button
                        @click="toggleClassFilter('demoman')"
                        :class="{
                          active:
                            filterOptions.selectedClasses.includes('demoman'),
                        }"
                        class="global-btn"
                      >
                        Demoman
                      </button>
                    </div>
                  </div>
                  <div class="filter-group">
                    <h6 class="filter-title text-light mb-2">Type</h6>
                    <div class="type-filter-container">
                      <button
                        @click="toggleTypeFilter('map')"
                        :class="{
                          active: filterOptions.selectedTypes.includes('map'),
                        }"
                        class="global-btn"
                      >
                        Map
                      </button>
                      <button
                        @click="toggleTypeFilter('course')"
                        :class="{
                          active:
                            filterOptions.selectedTypes.includes('course'),
                        }"
                        class="global-btn"
                      >
                        Course
                      </button>
                      <button
                        @click="toggleTypeFilter('bonus')"
                        :class="{
                          active: filterOptions.selectedTypes.includes('bonus'),
                        }"
                        class="global-btn"
                      >
                        Bonus
                      </button>
                    </div>
                  </div>
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
                    <tr
                      v-for="(record, index) in filteredTopTimesData"
                      :key="record.id"
                      class="fade-in"
                    >
                      <SmartLink
                        tag="td"
                        :to="{
                          name: 'PlayerPage',
                          params: { playerId: record.player_id },
                        }"
                        class="name-cell align-middle player-name clickable name-column"
                      >
                        <img
                          :src="record.avatar"
                          :alt="record.player + ' avatar'"
                          class="avatar"
                          onerror="this.src='/avatars/golly.jpg'"
                        />
                        {{ record.player }}
                      </SmartLink>
                      <SmartLink
                        tag="td"
                        :to="{
                          name: 'MapPage',
                          params: { mapId: record.map_id },
                        }"
                        class="name-cell align-middle map-name clickable"
                      >
                        <img
                          :src="`/icons/${record.class}.png`"
                          :alt="record.class"
                          class="class-icon-small"
                        />
                        {{ record.map }}
                      </SmartLink>
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
                  <h3 class="table-header-title">Latest Group 1s</h3>
                  <p class="table-header-subtitle">Updates every 6 hours</p>
                </div>
                <div class="filter-container">
                  <div class="filter-group">
                    <h6 class="filter-title text-light mb-2">Class</h6>
                    <div class="class-filter-container">
                      <button
                        @click="toggleClassFilter('soldier')"
                        :class="{
                          active:
                            filterOptions.selectedClasses.includes('soldier'),
                        }"
                        class="global-btn"
                      >
                        Soldier
                      </button>
                      <button
                        @click="toggleClassFilter('demoman')"
                        :class="{
                          active:
                            filterOptions.selectedClasses.includes('demoman'),
                        }"
                        class="global-btn"
                      >
                        Demoman
                      </button>
                    </div>
                  </div>
                  <div class="filter-group">
                    <h6 class="filter-title text-light mb-2">Type</h6>
                    <div class="type-filter-container">
                      <button
                        @click="toggleTypeFilter('map')"
                        :class="{
                          active: filterOptions.selectedTypes.includes('map'),
                        }"
                        class="global-btn"
                      >
                        Map
                      </button>
                      <button
                        @click="toggleTypeFilter('course')"
                        :class="{
                          active:
                            filterOptions.selectedTypes.includes('course'),
                        }"
                        class="global-btn"
                      >
                        Course
                      </button>
                      <button
                        @click="toggleTypeFilter('bonus')"
                        :class="{
                          active: filterOptions.selectedTypes.includes('bonus'),
                        }"
                        class="global-btn"
                      >
                        Bonus
                      </button>
                    </div>
                  </div>
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
                    <tr
                      v-for="(record, index) in filteredGroup1sData"
                      :key="record.id"
                      class="fade-in"
                    >
                      <SmartLink
                        tag="td"
                        :to="{
                          name: 'PlayerPage',
                          params: { playerId: record.player_id },
                        }"
                        class="name-cell align-middle player-name clickable name-column"
                      >
                        <img
                          :src="record.avatar"
                          :alt="record.player + ' avatar'"
                          class="avatar"
                          onerror="this.src='/avatars/golly.jpg'"
                        />
                        {{ record.player }}
                      </SmartLink>
                      <SmartLink
                        tag="td"
                        :to="{
                          name: 'MapPage',
                          params: { mapId: record.map_id },
                        }"
                        class="name-cell align-middle map-name clickable"
                      >
                        <img
                          :src="`/icons/${record.class}.png`"
                          :alt="record.class"
                          class="class-icon-small"
                        />
                        {{ record.map }}
                      </SmartLink>
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
    </div>
  </div>
</template>

<script>
import { formatDate, formatDuration } from "@/utils/calculations";
import { useHead } from "@vueuse/head";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Activity",
  setup() {
    useHead({
      title: "Tempus Plaza | Activity",
    });
  },
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
      filterOptions: {
        selectedClasses: [],
        selectedTypes: [],
      },
    };
  },
  computed: {
    filteredWorldRecordsData() {
      return this.filterData(this.worldRecordsData);
    },
    filteredTopTimesData() {
      return this.filterData(this.topTimesData);
    },
    filteredGroup1sData() {
      return this.filterData(this.group1sData);
    },
  },
  watch: {
    $route(to) {
      if (to.params.view) {
        this.currentView = to.params.view;
      }
    },
  },
  async created() {
    await this.fetchData();
    const { view } = this.$route.params;
    if (view) {
      this.currentView = view;
    }
  },
  methods: {
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
    filterData(data) {
      let filteredData = [...data];
      if (this.filterOptions.selectedClasses.length > 0) {
        filteredData = filteredData.filter((record) =>
          this.filterOptions.selectedClasses.includes(record.class)
        );
      }
      if (this.filterOptions.selectedTypes.length > 0) {
        filteredData = filteredData.filter((record) =>
          this.filterOptions.selectedTypes.includes(record.recordType)
        );
      }
      return filteredData;
    },
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
  },
};
</script>

<style scoped>
.filter-container {
  display: flex;
  gap: 20px;
  align-items: center;
  margin-left: auto;
}

.filter-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.filter-title {
  font-weight: bold;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-align: center;
}

.class-filter-container,
.type-filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
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

.page-header {
  text-align: center;
  margin-bottom: 2rem;
  padding: 1.5rem 0;
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

.table-wrapper {
  width: 100%;
  border-radius: 0 0 8px 8px;
  border-top: none;
  border: 1px solid var(--color-border-soft);
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
  border: 2px solid var(--color-border-soft);
}

.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}

.table-dark tr:nth-child(odd) .name-cell:hover {
  background: rgba(74, 111, 165, 0.8) !important;
}

.name-cell {
  max-width: 200px;
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

.player-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.timestamp-cell {
  max-width: 100px;
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
</style>
