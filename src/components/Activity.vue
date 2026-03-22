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
              v-for="v in views"
              :key="v.key"
              :class="{ active: currentView === v.key }"
              @click="switchView(v.key)"
              class="toggle-btn btn btn-dark update-button"
            >
              <span class="btn-text">{{ v.label }}</span>
            </button>
          </div>
          <ActivitySkeleton v-if="loading" />
          <div v-else class="table-container">
            <div class="activity-cards">
              <div class="table-header-content">
                <div class="table-header-icon">{{ activeView.icon }}</div>
                <div class="table-header-text">
                  <h3 class="table-header-title">{{ activeView.title }}</h3>
                </div>
                <div class="header-right">
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
                          v-for="t in ['map', 'course', 'bonus']"
                          :key="t"
                          @click="toggleTypeFilter(t)"
                          :class="{
                            active: filterOptions.selectedTypes.includes(t),
                          }"
                          class="global-btn"
                        >
                          {{ t.charAt(0).toUpperCase() + t.slice(1) }}
                        </button>
                      </div>
                    </div>
                    <div class="filter-group">
                      <h6 class="filter-title text-light mb-2">
                        Intended Class
                      </h6>
                      <div class="class-filter-container">
                        <button
                          @click="toggleIntendedClassFilter('soldier')"
                          :class="{
                            active:
                              filterOptions.selectedIntendedClasses.includes(
                                'soldier',
                              ),
                          }"
                          class="global-btn"
                        >
                          Soldier
                        </button>
                        <button
                          @click="toggleIntendedClassFilter('demoman')"
                          :class="{
                            active:
                              filterOptions.selectedIntendedClasses.includes(
                                'demoman',
                              ),
                          }"
                          class="global-btn"
                        >
                          Demoman
                        </button>
                      </div>
                    </div>
                    <div
                      v-if="!isMobile"
                      class="filter-group mode-toggle-group"
                    >
                      <h6 class="filter-title text-light mb-2">View</h6>
                      <label class="min-mode-checkbox">
                        <input
                          type="checkbox"
                          :checked="minMode"
                          @change="toggleMinMode"
                        />
                        <span class="checkmark"></span>
                        <span class="checkbox-label">Min Mode</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div
                v-for="group in groupedActiveData"
                :key="group.label"
                class="day-group"
              >
                <div class="day-label">{{ group.label }}</div>
                <div
                  class="activity-cards-container"
                  :class="{ 'normal-mode': !minMode }"
                >
                  <ActivityCard
                    v-for="record in group.records"
                    :key="record.id"
                    :record="record"
                    :current-view="currentView"
                    :active-view="activeView"
                    :min-mode="minMode"
                    :get-time-ago="getTimeAgo"
                    :get-time-ago-short="getTimeAgoShort"
                    :get-improvement-text="getImprovementText"
                    :get-rank-change="getRankChange"
                    :get-flag-image-url="getFlagImageUrl"
                  />
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
import ActivityCard from "./ActivityCard.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

const VIEW_CONFIG = {
  worldrecords: {
    key: "worldrecords",
    label: "World Records",
    icon: "🏆",
    title: "Latest World Records",
    improvementLabel: "WR",
    showRank: false,
    activityType: "wr",
  },
  toptimes: {
    key: "toptimes",
    label: "Top Times",
    icon: "🥇",
    title: "Latest Top Times",
    improvementLabel: "PR",
    showRank: true,
    activityType: "tt",
  },
  group1s: {
    key: "group1s",
    label: "Group 1s",
    icon: "⏱️",
    title: "Latest Group 1s",
    improvementLabel: "PR",
    showRank: true,
    activityType: "g1",
  },
};

export default {
  name: "Activity",
  components: { ActivitySkeleton, ActivityCard },
  setup() {
    useHead({ title: "Activity | Tempus Plaza" });
  },
  props: {
    view: { type: String, default: "worldrecords" },
  },
  data() {
    return {
      views: Object.values(VIEW_CONFIG),
      currentView: this.view,
      worldRecordsData: [],
      topTimesData: [],
      group1sData: [],
      loading: false,
      isMobile: window.innerWidth <= 992,
      manualMinMode: localStorage.getItem("activityMinMode") === "true",
      minMode:
        window.innerWidth <= 992
          ? true
          : localStorage.getItem("activityMinMode") === "true",
      filterOptions: {
        selectedClasses: [],
        selectedTypes: [],
        selectedIntendedClasses: [],
      },
      currentTime: new Date(),
      updateTimer: null,
    };
  },
  computed: {
    activeView() {
      return VIEW_CONFIG[this.currentView];
    },
    activeData() {
      const raw =
        {
          worldrecords: this.worldRecordsData,
          toptimes: this.topTimesData,
          group1s: this.group1sData,
        }[this.currentView] ?? [];
      return this.filterData(raw);
    },
    groupedActiveData() {
      return this.groupByDate(this.activeData);
    },
  },
  watch: {
    $route(to) {
      if (to.params.view) this.currentView = to.params.view;
    },
  },
  async created() {
    await this.fetchData();
    const { view } = this.$route.params;
    if (view) this.currentView = view;
  },
  mounted() {
    this.startUpdateTimer();
    window.addEventListener("resize", this.handleResize);
    window.addEventListener("storage", this.handleStorageChange);
  },
  beforeUnmount() {
    if (this.updateTimer) clearInterval(this.updateTimer);
    window.removeEventListener("resize", this.handleResize);
    window.removeEventListener("storage", this.handleStorageChange);
  },
  methods: {
    toggleMinMode() {
      this.manualMinMode = !this.manualMinMode;
      this.minMode = this.manualMinMode;
      localStorage.setItem("activityMinMode", this.manualMinMode);
    },
    handleResize() {
      this.isMobile = window.innerWidth <= 992;
      this.minMode = this.isMobile ? true : this.manualMinMode;
    },
    handleStorageChange(event) {
      if (event.key === "activityMinMode") {
        this.manualMinMode = event.newValue === "true";
        if (!this.isMobile) this.minMode = this.manualMinMode;
      }
    },
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    toggleIntendedClassFilter(type) {
      const index = this.filterOptions.selectedIntendedClasses.indexOf(type);
      if (index === -1) this.filterOptions.selectedIntendedClasses.push(type);
      else this.filterOptions.selectedIntendedClasses.splice(index, 1);
    },
    getTimeAgo(date) {
      const now = this.currentTime;
      const ms = typeof date === "number" ? date * 1000 : Date.parse(date);
      const diffMs = now - ms;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);
      if (diffMins < 1) return "Just now";
      if (diffMins < 60) return `${diffMins} minutes ago`;
      if (diffHours === 1) return "1 hour ago";
      if (diffHours < 24) return `${diffHours} hours ago`;
      if (diffDays === 1) return "1 day ago";
      if (diffDays < 7) return `${diffDays} days ago`;
      return formatDate(date);
    },
    getTimeAgoShort(date) {
      const now = this.currentTime;
      const ms = typeof date === "number" ? date * 1000 : Date.parse(date);
      const diffMs = now - ms;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);
      if (diffMins < 60) return `${diffMins}M ago`;
      if (diffHours < 24) return `${diffHours}H ago`;
      if (diffDays < 7) return `${diffDays}D ago`;
      return formatDate(date);
    },
    startUpdateTimer() {
      this.updateTimer = setInterval(() => {
        this.currentTime = new Date();
      }, 1000);
    },
    toggleClassFilter(classType) {
      const index = this.filterOptions.selectedClasses.indexOf(classType);
      if (index === -1) this.filterOptions.selectedClasses.push(classType);
      else this.filterOptions.selectedClasses.splice(index, 1);
    },
    toggleTypeFilter(type) {
      const index = this.filterOptions.selectedTypes.indexOf(type);
      if (index === -1) this.filterOptions.selectedTypes.push(type);
      else this.filterOptions.selectedTypes.splice(index, 1);
    },
    filterData(data) {
      let filtered = [...data];
      if (this.filterOptions.selectedClasses.length > 0)
        filtered = filtered.filter((r) =>
          this.filterOptions.selectedClasses.includes(r.class),
        );
      if (this.filterOptions.selectedTypes.length > 0)
        filtered = filtered.filter((r) =>
          this.filterOptions.selectedTypes.includes(r.recordType),
        );
      if (this.filterOptions.selectedIntendedClasses.length > 0)
        filtered = filtered.filter((r) => {
          const ic = r.intended_class;
          return this.filterOptions.selectedIntendedClasses.some((sel) => {
            if (sel === "soldier") return ic === 3 || ic === 5;
            if (sel === "demoman") return ic === 4 || ic === 5;
            return false;
          });
        });
      return filtered;
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
        const mapItem = (item) => ({
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
          intended_class: item.intended_class,
          tier:
            item.class === "soldier" ? item.soldier_tier : item.demoman_tier,
          rating:
            item.class === "soldier"
              ? item.soldier_rating
              : item.demoman_rating,
        });
        this.worldRecordsData = activityData
          .filter((i) => i.activity_type === "wr")
          .map(mapItem);
        this.topTimesData = activityData
          .filter((i) => i.activity_type === "tt")
          .map(mapItem);
        this.group1sData = activityData
          .filter((i) => i.activity_type === "g1")
          .map(mapItem);
      } catch (error) {
        console.error("There was an error fetching the records data:", error);
      }
    },
    getImprovementText(newDuration, oldDuration) {
      if (!oldDuration || oldDuration === newDuration) return "";
      return `-${formatDuration(oldDuration - newDuration)}`;
    },
    getRankChange(newRank, oldRank) {
      if (!oldRank || oldRank === newRank) return "";
      const change = oldRank - newRank;
      return change > 0 ? `-${change}` : `${change}`;
    },
    formatDayLabel(dateVal) {
      const ms =
        typeof dateVal === "number" ? dateVal * 1000 : Date.parse(dateVal);
      const d = new Date(ms);
      return d.toLocaleDateString("en-GB", { day: "2-digit", month: "short" });
    },
    groupByDate(records) {
      const map = new Map();
      for (const record of records) {
        const label = this.formatDayLabel(record.date);
        if (!map.has(label)) map.set(label, []);
        map.get(label).push(record);
      }
      return Array.from(map.entries()).map(([label, recs]) => ({
        label,
        records: recs,
      }));
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

.table-header-content {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
  border-bottom: 1px solid var(--color-border-soft);
  gap: 0.5rem;
}

.table-header-icon {
  font-size: 2rem;
  filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.3));
  flex-shrink: 0;
}

.table-header-text {
  flex-shrink: 0;
}

.table-header-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: bold;
  color: var(--color-text);
}

.header-right {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-left: auto;
}

.filter-container {
  display: flex;
  gap: 20px;
  align-items: center;
}

.filter-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.mode-toggle-group {
  justify-content: center;
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

.min-mode-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  user-select: none;
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
  flex-shrink: 0;
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
  white-space: nowrap;
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

.activity-cards-container.normal-mode {
  gap: 0.6rem;
}

.day-group {
  width: 100%;
}

.day-label {
  font-size: 1.25rem;
  font-weight: bold;
  color: var(--color-text);
  opacity: 0.85;
  padding: 0.75rem 1rem 0.35rem;
  letter-spacing: 0.02em;
}

@media (max-width: 1400px) {
  .table-header-title {
    font-size: 1.4rem;
  }
}

@media (max-width: 1200px) {
  .table-header-content {
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: center;
  }

  .header-right {
    flex-direction: column;
    align-items: center;
    width: 100%;
    margin-left: 0;
    gap: 0.75rem;
    margin-top: 5px;
  }

  .filter-container {
    flex-wrap: wrap;
    gap: 12px;
    width: 100%;
    justify-content: center;
  }

  .filter-group {
    align-items: center;
  }
}

@media (max-width: 767.98px) {
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
}
</style>
