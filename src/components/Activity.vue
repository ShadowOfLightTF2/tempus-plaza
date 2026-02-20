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
          <ActivitySkeleton v-if="loading" />
          <div v-else class="table-container">
            <div v-if="currentView === 'worldrecords'" class="activity-cards">
              <div class="table-header-content">
                <div class="table-header-icon">🏆</div>
                <div class="table-header-text">
                  <h3 class="table-header-title">Latest World Records</h3>
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
              <div class="activity-cards-container">
                <div
                  v-for="record in filteredWorldRecordsData"
                  :key="record.id"
                  class="activity-card fade-in"
                >
                  <!-- Player Info Section -->
                  <div class="activity-player-section">
                    <SmartLink
                      :to="{
                        name: 'PlayerPage',
                        params: { playerId: record.player_id },
                      }"
                      class="player-link"
                    >
                      <img
                        :src="record.avatar"
                        alt="Avatar"
                        class="player-avatar-large"
                        onerror="this.src='/avatars/golly.jpg'"
                      />
                      <div class="player-details">
                        <div class="player-name-large">{{ record.player }}</div>
                        <div class="player-country">
                          <img
                            :src="getFlagImageUrl(record.country_code)"
                            alt="Flag"
                            class="flag-icon-large"
                          />
                          {{ record.country }}
                        </div>
                      </div>
                    </SmartLink>
                  </div>

                  <!-- Map Info Section -->
                  <SmartLink
                    :to="{
                      name: 'MapPage',
                      params: { mapId: record.map_id },
                    }"
                    class="activity-map-section"
                  >
                    <div class="map-thumbnail-wrapper">
                      <HoverPreview :map-name="record.map">
                        <img
                          v-if="!failedMapImages.has(record.map)"
                          :src="`/map-backgrounds/thumbnails/${record.map}.jpg`"
                          @error="handleMapImageError(record.map)"
                          alt="Map"
                          class="map-thumbnail"
                        />
                        <div
                          v-else
                          class="map-thumbnail map-thumbnail-placeholder"
                        ></div>
                      </HoverPreview>
                    </div>
                    <div class="map-details">
                      <div class="map-name-large">{{ record.map }}</div>
                      <div class="map-meta">
                        <div class="map-type">
                          <template v-if="record.recordType === 'map'"
                            >🌍 Map</template
                          >
                          <template v-else-if="record.recordType === 'course'"
                            >🚩 Course {{ record.index }}</template
                          >
                          <template v-else-if="record.recordType === 'bonus'"
                            >⭐ Bonus {{ record.index }}</template
                          >
                        </div>
                      </div>
                    </div>
                    <div class="map-class-container">
                      <div class="map-class">
                        <img
                          :src="`/icons/${record.class}.png`"
                          alt="Class"
                          class="class-icon-large"
                        />
                      </div>
                    </div>
                  </SmartLink>
                  <!-- Time Section -->
                  <div class="activity-time-section">
                    <div class="time-display">
                      {{ record.time }}
                      <span v-if="record.old_duration" class="improvement-text">
                        WR
                        {{
                          getImprovementText(
                            record.duration,
                            record.old_duration,
                          )
                        }}
                      </span>
                    </div>
                    <div class="time-ago">{{ getTimeAgo(record.date) }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="currentView === 'toptimes'" class="activity-cards">
              <div class="table-header-content">
                <div class="table-header-icon">🥇</div>
                <div class="table-header-text">
                  <h3 class="table-header-title">Latest Top Times</h3>
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
              <div class="activity-cards-container">
                <div
                  v-for="record in filteredTopTimesData"
                  :key="record.id"
                  class="activity-card fade-in"
                >
                  <!-- Player Info Section -->
                  <div class="activity-player-section">
                    <SmartLink
                      :to="{
                        name: 'PlayerPage',
                        params: { playerId: record.player_id },
                      }"
                      class="player-link"
                    >
                      <img
                        :src="record.avatar"
                        alt="Avatar"
                        class="player-avatar-large"
                        onerror="this.src='/avatars/golly.jpg'"
                      />
                      <div class="player-details">
                        <div class="player-name-large">{{ record.player }}</div>
                        <div class="player-country">
                          <img
                            :src="getFlagImageUrl(record.country_code)"
                            alt="Flag"
                            class="flag-icon-large"
                          />
                          {{ record.country }}
                        </div>
                      </div>
                    </SmartLink>
                  </div>

                  <!-- Map Info Section -->
                  <SmartLink
                    :to="{
                      name: 'MapPage',
                      params: { mapId: record.map_id },
                    }"
                    class="activity-map-section"
                  >
                    <div class="map-thumbnail-wrapper">
                      <HoverPreview :map-name="record.map">
                        <img
                          v-if="!failedMapImages.has(record.map)"
                          :src="`/map-backgrounds/thumbnails/${record.map}.jpg`"
                          @error="handleMapImageError(record.map)"
                          alt="Map"
                          class="map-thumbnail"
                        />
                        <div
                          v-else
                          class="map-thumbnail map-thumbnail-placeholder"
                        ></div>
                      </HoverPreview>
                    </div>
                    <div class="map-details">
                      <div class="map-name-large">{{ record.map }}</div>
                      <div class="map-meta">
                        <div class="map-type">
                          <template v-if="record.recordType === 'map'"
                            >🌍 Map</template
                          >
                          <template v-else-if="record.recordType === 'course'"
                            >🚩 Course {{ record.index }}</template
                          >
                          <template v-else-if="record.recordType === 'bonus'"
                            >⭐ Bonus {{ record.index }}</template
                          >
                        </div>
                      </div>
                    </div>
                    <div class="map-class-container">
                      <div class="map-class">
                        <img
                          :src="`/icons/${record.class}.png`"
                          alt="Class"
                          class="class-icon-large"
                        />
                      </div>
                    </div>
                  </SmartLink>

                  <!-- Time Section -->
                  <div class="activity-time-section">
                    <div class="rank-time-section">
                      <div class="time-display">
                        {{ record.time }}
                        <span
                          v-if="record.old_duration"
                          class="improvement-text"
                        >
                          PR
                          {{
                            getImprovementText(
                              record.duration,
                              record.old_duration,
                            )
                          }}
                        </span>
                      </div>
                      <div
                        class="rank-display"
                        :class="{
                          'placement-silver': record.rank === 2,
                          'placement-bronze': record.rank === 3,
                        }"
                      >
                        #{{ record.rank }}
                        <span v-if="record.old_rank" class="rank-change">
                          {{ getRankChange(record.rank, record.old_rank) }}
                        </span>
                      </div>
                    </div>
                    <div class="time-ago">{{ getTimeAgo(record.date) }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="currentView === 'group1s'" class="activity-cards">
              <div class="table-header-content">
                <div class="table-header-icon">⏱️</div>
                <div class="table-header-text">
                  <h3 class="table-header-title">Latest Group 1s</h3>
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
              <div class="activity-cards-container">
                <div
                  v-for="record in filteredGroup1sData"
                  :key="record.id"
                  class="activity-card fade-in"
                >
                  <!-- Player Info Section -->
                  <div class="activity-player-section">
                    <SmartLink
                      :to="{
                        name: 'PlayerPage',
                        params: { playerId: record.player_id },
                      }"
                      class="player-link"
                    >
                      <img
                        :src="record.avatar"
                        alt="Avatar"
                        class="player-avatar-large"
                        onerror="this.src='/avatars/golly.jpg'"
                      />
                      <div class="player-details">
                        <div class="player-name-large">{{ record.player }}</div>
                        <div class="player-country">
                          <img
                            :src="getFlagImageUrl(record.country_code)"
                            alt="Flag"
                            class="flag-icon-large"
                          />
                          {{ record.country }}
                        </div>
                      </div>
                    </SmartLink>
                  </div>

                  <!-- Map Info Section -->
                  <SmartLink
                    :to="{
                      name: 'MapPage',
                      params: { mapId: record.map_id },
                    }"
                    class="activity-map-section"
                  >
                    <div class="map-thumbnail-wrapper">
                      <HoverPreview :map-name="record.map">
                        <img
                          v-if="!failedMapImages.has(record.map)"
                          :src="`/map-backgrounds/thumbnails/${record.map}.jpg`"
                          @error="handleMapImageError(record.map)"
                          alt="Map"
                          class="map-thumbnail"
                        />
                        <div
                          v-else
                          class="map-thumbnail map-thumbnail-placeholder"
                        ></div>
                      </HoverPreview>
                    </div>
                    <div class="map-details">
                      <div class="map-name-large">{{ record.map }}</div>
                      <div class="map-meta">
                        <div class="map-type">
                          <template v-if="record.recordType === 'map'"
                            >🌍 Map</template
                          >
                          <template v-else-if="record.recordType === 'course'"
                            >🚩 Course {{ record.index }}</template
                          >
                          <template v-else-if="record.recordType === 'bonus'"
                            >⭐ Bonus {{ record.index }}</template
                          >
                        </div>
                      </div>
                    </div>
                    <div class="map-class-container">
                      <div class="map-class">
                        <img
                          :src="`/icons/${record.class}.png`"
                          alt="Class"
                          class="class-icon-large"
                        />
                      </div>
                    </div>
                  </SmartLink>

                  <!-- Time Section -->
                  <div class="activity-time-section">
                    <div class="rank-time-section">
                      <div class="time-display">
                        {{ record.time }}
                        <span
                          v-if="record.old_duration"
                          class="improvement-text"
                        >
                          PR
                          {{
                            getImprovementText(
                              record.duration,
                              record.old_duration,
                            )
                          }}
                        </span>
                      </div>
                      <div class="rank-display-group">
                        #{{ record.rank }}
                        <span v-if="record.old_rank" class="rank-change">
                          {{ getRankChange(record.rank, record.old_rank) }}
                        </span>
                      </div>
                    </div>
                    <div class="time-ago">{{ getTimeAgo(record.date) }}</div>
                  </div>
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
import { formatDate, formatDuration } from "@/utils/calculations";
import { useHead } from "@vueuse/head";
import ActivitySkeleton from "./Skeletons/ActivitySkeleton.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Activity",
  components: { ActivitySkeleton },
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
      failedMapImages: new Set(),
      worldRecordsData: [],
      topTimesData: [],
      group1sData: [],
      loading: false,
      filterOptions: {
        selectedClasses: [],
        selectedTypes: [],
      },
      currentTime: new Date(),
      updateTimer: null,
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
  mounted() {
    this.startUpdateTimer();
  },
  beforeUnmount() {
    if (this.updateTimer) {
      clearInterval(this.updateTimer);
    }
  },
  methods: {
    handleMapImageError(mapName) {
      this.failedMapImages.add(mapName);
    },
    getTimeAgo(date) {
      const now = this.currentTime;
      const recordDate = new Date(date);
      const diffMs = now - recordDate;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);

      if (diffMins < 1) return "Just now";
      if (diffMins < 60) return `${diffMins}m ago`;
      if (diffHours < 24) return `${diffHours}h ago`;
      if (diffDays === 1) return "Yesterday";
      if (diffDays < 7) return `${diffDays}d ago`;
      return formatDate(date);
    },

    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },

    getRecordTypeDisplay(record) {
      if (record.recordType === "map") return "Map";
      if (record.recordType === "course") return `Course ${record.index}`;
      if (record.recordType === "bonus") return `Bonus ${record.index}`;
      return "";
    },
    startUpdateTimer() {
      this.updateTimer = setInterval(() => {
        this.currentTime = new Date();
      }, 1000);
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
    filterData(data) {
      let filteredData = [...data];
      if (this.filterOptions.selectedClasses.length > 0) {
        filteredData = filteredData.filter((record) =>
          this.filterOptions.selectedClasses.includes(record.class),
        );
      }
      if (this.filterOptions.selectedTypes.length > 0) {
        filteredData = filteredData.filter((record) =>
          this.filterOptions.selectedTypes.includes(record.recordType),
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
        const response = await fetch(`${API_BASE_URL}/maps/activity`);
        const activityData = await response.json();

        this.worldRecordsData = activityData
          .filter((item) => item.activity_type === "wr")
          .map((item) => ({
            id: item.id,
            player: item.player_name,
            player_id: item.player_id,
            avatar: item.steam_avatar,
            country: item.country,
            country_code: item.country_code,
            soldier_rank: item.soldier_rank,
            demoman_rank: item.demoman_rank,
            map: item.map_name,
            map_id: item.map_id,
            time: formatDuration(item.duration),
            duration: item.duration,
            class: item.class,
            recordType: item.record_type,
            index: item.index,
            timestamp: formatDate(item.date),
            date: item.date,
            old_duration: item.old_duration,
            old_rank: item.old_rank,
            old_placement: item.old_placement,
          }));

        this.topTimesData = activityData
          .filter((item) => item.activity_type === "tt")
          .map((item) => ({
            id: item.id,
            player: item.player_name,
            player_id: item.player_id,
            avatar: item.steam_avatar,
            country: item.country,
            country_code: item.country_code,
            soldier_rank: item.soldier_rank,
            demoman_rank: item.demoman_rank,
            map: item.map_name,
            map_id: item.map_id,
            time: formatDuration(item.duration),
            duration: item.duration,
            rank: item.rank,
            placement: item.placement,
            class: item.class,
            recordType: item.record_type,
            index: item.index,
            timestamp: formatDate(item.date),
            date: item.date,
            old_duration: item.old_duration,
            old_rank: item.old_rank,
            old_placement: item.old_placement,
          }));

        this.group1sData = activityData
          .filter((item) => item.activity_type === "g1")
          .map((item) => ({
            id: item.id,
            player: item.player_name,
            player_id: item.player_id,
            avatar: item.steam_avatar,
            country: item.country,
            country_code: item.country_code,
            soldier_rank: item.soldier_rank,
            demoman_rank: item.demoman_rank,
            map: item.map_name,
            map_id: item.map_id,
            time: formatDuration(item.duration),
            duration: item.duration,
            rank: item.rank,
            placement: item.placement,
            class: item.class,
            recordType: item.record_type,
            index: item.index,
            timestamp: formatDate(item.date),
            date: item.date,
            old_duration: item.old_duration,
            old_rank: item.old_rank,
            old_placement: item.old_placement,
          }));
      } catch (error) {
        console.error("There was an error fetching the records data:", error);
      }
    },
    getImprovementText(newDuration, oldDuration) {
      if (!oldDuration || oldDuration === newDuration) return "";
      const improvement = oldDuration - newDuration;
      return `-${formatDuration(improvement)}`;
    },
    getRankChange(newRank, oldRank) {
      if (!oldRank || oldRank === newRank) return "";
      const change = oldRank - newRank;
      return change > 0 ? `-${change}` : `${change}`;
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

@media (max-width: 767.98px) {
  .filter-container {
    flex-direction: column;
    gap: 12px;
    width: 100%;
  }

  .filter-group {
    width: 100%;
  }

  .filter-title {
    font-size: 12px;
    margin-bottom: 0.25rem !important;
  }

  .class-filter-container,
  .type-filter-container {
    justify-content: center;
    width: 100%;
  }

  .global-btn {
    flex: 1;
    min-width: 80px;
    font-size: 0.85rem;
    padding: 0.4rem 0.6rem;
  }
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
  border-bottom: 1px solid var(--color-border-soft);
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

.activity-cards {
  width: 100%;
  border-radius: 0 0 8px 8px;
  overflow: hidden;
}

.activity-cards-container {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.02);
}

.activity-card {
  display: grid;
  grid-template-columns: minmax(250px, 1.5fr) minmax(300px, 2fr) minmax(
      275px,
      1.2fr
    );
  gap: 1.5rem;
  padding: 0.3rem 0.6rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  transition: all 0.2s ease;
}

.activity-card:hover {
  border: 1px solid var(--color-border-semi-soft);
  background: rgba(74, 111, 165, 0.2);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.activity-player-section {
  display: flex;
  align-items: center;
  cursor: pointer;
}

.player-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  text-decoration: none;
  color: inherit;
  width: 100%;
}

.player-avatar-large {
  width: 36px;
  height: 36x;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
  flex-shrink: 0;
}

.player-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.player-name-large {
  font-size: 0.9rem;
  font-weight: bold;
  width: 360px;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  color: var(--color-text-clickable);
}

.player-country {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--color-text);
  font-size: 0.8rem;
}

.flag-icon-large {
  width: 16px;
  height: auto;
}

.activity-map-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  text-decoration: none;
  color: inherit;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 1.5rem;
  cursor: pointer;
}

.map-thumbnail-wrapper {
  width: 40px;
  height: 40px;
  flex-shrink: 0;
}

.map-thumbnail {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 4px;
  border: 2px solid var(--color-primary);
}

.map-thumbnail-placeholder {
  background: linear-gradient(
    135deg,
    rgba(0, 0, 0, 0.7) 0%,
    rgba(0, 0, 0, 0.4) 50%,
    rgba(0, 0, 0, 0.8) 100%
  );
}

.map-details {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.map-name-large {
  font-size: 0.9rem;
  font-weight: bold;
  color: var(--color-text-clickable);
}

.map-meta {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.map-class {
  display: flex;
  gap: 4px;
  align-items: center;
}

.class-icon-large {
  width: 35px;
  height: 35px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  padding: 8px;
  border: 2px solid rgba(74, 111, 165, 0.3);
}

.map-type {
  color: var(--color-text);
  font-size: 0.8rem;
  font-weight: 600;
}

.map-class-container {
  display: flex;
  align-items: center;
  margin-left: auto;
}

.activity-time-section {
  display: flex;
  justify-content: center;
  flex-direction: column;
  gap: 0.5rem;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 1.5rem;
}

.rank-time-section {
  display: flex;
  flex-direction: row;
  gap: 15px;
}

.rank-display,
.rank-display-group {
  font-size: 0.9rem;
  font-weight: bold;
  color: var(--color-primary);
  padding-left: 15px;
  border-left: 1px solid var(--color-border-semi-soft);
}

.rank-display-group {
  color: var(--color-text);
  font-weight: bold;
}

.placement-silver {
  color: #c0c0c0 !important;
  font-weight: bold;
}

.placement-bronze {
  color: #cd7f32 !important;
  font-weight: bold;
}

.time-display {
  font-size: 0.9rem;
  font-weight: bold;
  color: var(--color-text);
}

.time-ago {
  font-size: 0.85rem;
  color: var(--color-text);
  opacity: 0.7;
}

@media (max-width: 1024px) {
  .activity-card {
    grid-template-columns: minmax(50px, 0.8fr) minmax(100px, 1.5fr) 0.7fr;
    padding: 0.5rem;
    gap: 0.75rem;
  }

  .activity-player-section {
    justify-content: flex-start;
  }

  .player-avatar-large {
    width: 24px;
    height: 24px;
  }

  .player-link {
    flex-direction: row;
    gap: 0.5rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .player-name-large {
    font-size: 0.9rem;
    width: 100%;
    text-align: center;
  }

  .player-country {
    display: none;
  }

  .flag-icon-large {
    width: 18px;
  }

  .activity-map-section {
    border-left: 1px solid var(--color-border-soft);
    border-top: none;
    padding-left: 0.75rem;
    padding-top: 0;
    justify-content: flex-start;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    position: relative;
  }

  .activity-time-section {
    border-left: 1px solid var(--color-border-soft);
    padding-left: 0.75rem;
    border-top: none;
    padding-top: 0;
    justify-content: center;
    flex-direction: column;
    gap: 0.5rem;
    white-space: nowrap;
    text-overflow: ellipsis;
  }

  .map-thumbnail-wrapper {
    display: none;
  }

  .map-details {
    align-items: center;
    gap: 0.25rem;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .map-name-large {
    text-align: center;
    font-size: 0.9rem;
  }

  .map-type {
    font-size: 0.8rem;
  }

  .map-class-container {
    position: absolute;
    bottom: 0;
    left: 0;
    padding-left: 5px;
  }

  .class-icon-large {
    width: 24px;
    height: 24px;
    padding: 3px;
  }

  .map-meta {
    justify-content: center;
  }

  .rank-display {
    font-size: 0.9rem;
  }

  .time-display {
    font-size: 0.9rem;
  }

  .time-ago {
    font-size: 0.75rem;
  }

  .improvement-text {
    display: none;
  }

  .rank-change {
    display: none;
  }
}

.improvement-text {
  font-size: 0.75rem;
  color: #4ade80;
  margin-left: 0.5rem;
  font-weight: normal;
}

.rank-change {
  font-size: 0.75rem;
  color: #4ade80;
  margin-left: 0.25rem;
  font-weight: normal;
}
</style>
