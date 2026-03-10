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
                  <template v-if="minMode">
                    <div
                      v-for="record in group.records"
                      :key="'min-' + record.id"
                      class="activity-card"
                      :class="{ 'view-hidden': !minMode }"
                    >
                      <div class="activity-player-section">
                        <img
                          :src="`/icons/${record.class}.png`"
                          alt="Class"
                          class="class-icon-row"
                        />
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
                            <div class="player-name-large" v-dragscroll>
                              {{ record.player }}
                            </div>
                          </div>
                        </SmartLink>
                      </div>

                      <SmartLink
                        :to="{
                          name: 'MapPage',
                          params: { mapId: record.map_id },
                        }"
                        class="activity-map-section"
                      >
                        <div class="map-details" v-dragscroll>
                          <div class="map-name-row">
                            <HoverPreview :map-name="record.map">
                              <span class="map-name-large">{{
                                record.map
                              }}</span>
                            </HoverPreview>
                            <span
                              v-if="record.recordType !== 'map'"
                              class="map-type-badge"
                            >
                              <template v-if="record.recordType === 'course'"
                                >🚩 C{{ record.index }}</template
                              >
                              <template
                                v-else-if="record.recordType === 'bonus'"
                                >⭐ B{{ record.index }}</template
                              >
                            </span>
                          </div>
                        </div>
                        <div class="min-mode-meta">
                          <div class="min-mode-intended-class">
                            <template
                              v-if="
                                record.intended_class === 3 ||
                                record.intended_class === '3'
                              "
                            >
                              <img
                                src="/icons/soldier.png"
                                alt="Soldier"
                                class="min-mode-intended-class-icon"
                              />
                            </template>
                            <template
                              v-else-if="
                                record.intended_class === 4 ||
                                record.intended_class === '4'
                              "
                            >
                              <img
                                src="/icons/demoman.png"
                                alt="Demoman"
                                class="min-mode-intended-class-icon"
                              />
                            </template>
                            <template
                              v-else-if="
                                record.intended_class === 5 ||
                                record.intended_class === '5'
                              "
                            >
                              <img
                                src="/icons/soldier.png"
                                alt="Soldier"
                                class="min-mode-intended-class-icon dual"
                              />
                              <img
                                src="/icons/demoman.png"
                                alt="Demoman"
                                class="min-mode-intended-class-icon dual"
                              />
                            </template>
                          </div>
                          <div class="map-tier-rating">
                            <span
                              v-if="record.tier != null"
                              class="tier-badge"
                              :class="`tier-${record.tier}`"
                              >T{{ record.tier }}</span
                            >
                            <span
                              v-if="record.rating != null"
                              class="rating-badge"
                              :class="`rating-${record.rating}`"
                              >R{{ record.rating }}</span
                            >
                          </div>
                        </div>
                      </SmartLink>
                      <div class="activity-time-section">
                        <div class="rank-time-section">
                          <div class="time-display">
                            {{ record.time }}
                            <span
                              v-if="record.old_duration"
                              class="improvement-text"
                            >
                              {{ activeView.improvementLabel }}
                              {{
                                getImprovementText(
                                  record.duration,
                                  record.old_duration,
                                )
                              }}
                            </span>
                          </div>
                          <div
                            v-if="activeView.showRank"
                            class="rank-display"
                            :class="{
                              'placement-silver': record.rank === 2,
                              'placement-bronze': record.rank === 3,
                              'placement-group-1': currentView === 'group1s',
                            }"
                          >
                            #{{ record.rank
                            }}<span
                              v-if="record.old_rank"
                              class="rank-change"
                              >{{
                                getRankChange(record.rank, record.old_rank)
                              }}</span
                            >
                          </div>
                        </div>
                      </div>
                      <div class="activity-timeago-section">
                        <span class="timeago-long">{{
                          getTimeAgo(record.date)
                        }}</span>
                        <span class="timeago-short">{{
                          getTimeAgoShort(record.date)
                        }}</span>
                      </div>
                    </div>
                  </template>
                  <template v-else>
                    <div
                      v-for="record in group.records"
                      :key="'normal-' + record.id"
                      class="activity-card-normal"
                      :class="{ 'view-hidden': minMode }"
                    >
                      <div class="normal-class-section">
                        <img
                          :src="`/icons/${record.class}.png`"
                          alt="Class"
                          class="normal-class-icon"
                        />
                      </div>
                      <div class="normal-player-section">
                        <SmartLink
                          :to="{
                            name: 'PlayerPage',
                            params: { playerId: record.player_id },
                          }"
                          class="normal-player-link"
                        >
                          <img
                            :src="record.avatar"
                            alt="Avatar"
                            class="normal-avatar"
                            onerror="this.src='/avatars/golly.jpg'"
                          />
                          <div class="normal-player-details">
                            <div class="normal-player-name">
                              {{ record.player }}
                            </div>
                            <div class="normal-player-country">
                              <img
                                :src="getFlagImageUrl(record.country_code)"
                                alt="Flag"
                                class="flag-icon-normal"
                              />
                              {{ record.country }}
                            </div>
                          </div>
                        </SmartLink>
                      </div>
                      <SmartLink
                        :to="{
                          name: 'MapPage',
                          params: { mapId: record.map_id },
                        }"
                        class="normal-map-section"
                        :style="`background-image: url('/map-backgrounds/medium/${record.map}.webp')`"
                      >
                        <div class="normal-map-overlay"></div>
                        <div class="normal-map-details">
                          <div class="normal-map-name-row">
                            <HoverPreview :map-name="record.map">
                              <span class="normal-map-name">{{
                                record.map
                              }}</span>
                            </HoverPreview>
                            <span
                              v-if="record.recordType !== 'map'"
                              class="map-type-badge"
                            >
                              <template v-if="record.recordType === 'course'"
                                >🚩 C{{ record.index }}</template
                              >
                              <template
                                v-else-if="record.recordType === 'bonus'"
                                >⭐ B{{ record.index }}</template
                              >
                            </span>
                          </div>
                        </div>
                        <div class="normal-map-top-right">
                          <div class="normal-map-intended-class">
                            <template
                              v-if="
                                record.intended_class === 3 ||
                                record.intended_class === '3'
                              "
                            >
                              <img
                                src="/icons/soldier.png"
                                alt="Soldier"
                                class="normal-map-class-icon"
                              />
                            </template>
                            <template
                              v-else-if="
                                record.intended_class === 4 ||
                                record.intended_class === '4'
                              "
                            >
                              <img
                                src="/icons/demoman.png"
                                alt="Demoman"
                                class="normal-map-class-icon"
                              />
                            </template>
                            <template
                              v-else-if="
                                record.intended_class === 5 ||
                                record.intended_class === '5'
                              "
                            >
                              <img
                                src="/icons/soldier.png"
                                alt="Soldier"
                                class="normal-map-class-icon dual"
                              />
                              <img
                                src="/icons/demoman.png"
                                alt="Demoman"
                                class="normal-map-class-icon dual"
                              />
                            </template>
                          </div>
                          <div class="normal-map-tier-rating">
                            <span
                              v-if="record.tier != null"
                              class="tier-badge"
                              :class="`tier-${record.tier}`"
                              >T{{ record.tier }}</span
                            >
                            <span
                              v-if="record.rating != null"
                              class="rating-badge"
                              :class="`rating-${record.rating}`"
                              >R{{ record.rating }}</span
                            >
                          </div>
                        </div>
                      </SmartLink>
                      <div class="normal-time-section">
                        <div class="normal-time-rank-row">
                          <div class="normal-time-display">
                            {{ record.time }}
                            <span
                              v-if="record.old_duration"
                              class="improvement-text"
                            >
                              {{ activeView.improvementLabel }}
                              {{
                                getImprovementText(
                                  record.duration,
                                  record.old_duration,
                                )
                              }}
                            </span>
                          </div>
                          <div
                            v-if="activeView.showRank"
                            class="normal-rank-display"
                            :class="{
                              'placement-silver': record.rank === 2,
                              'placement-bronze': record.rank === 3,
                              'placement-group-1': currentView === 'group1s',
                            }"
                          >
                            #{{ record.rank
                            }}<span
                              v-if="record.old_rank"
                              class="rank-change"
                              >{{
                                getRankChange(record.rank, record.old_rank)
                              }}</span
                            >
                          </div>
                        </div>
                        <div class="normal-timeago">
                          {{ getTimeAgo(record.date) }}
                        </div>
                      </div>
                    </div>
                  </template>
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
  components: { ActivitySkeleton },
  directives: {
    dragscroll: {
      mounted(el) {
        let isDown = false;
        let startX;
        let scrollLeft;

        el.addEventListener("mousedown", (e) => {
          isDown = true;
          startX = e.pageX - el.offsetLeft;
          scrollLeft = el.scrollLeft;
          e.preventDefault();
        });
        el.addEventListener("mouseleave", () => {
          isDown = false;
        });
        el.addEventListener("mouseup", () => {
          isDown = false;
        });
        el.addEventListener("mousemove", (e) => {
          if (!isDown) return;
          const x = e.pageX - el.offsetLeft;
          el.scrollLeft = scrollLeft - (x - startX);
        });
      },
    },
  },
  setup() {
    useHead({ title: "Tempus Plaza | Activity" });
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

.activity-card {
  display: grid;
  grid-template-columns: minmax(0, 250px) 1fr 250px 100px;
  padding: 0.5rem;
  gap: 0.75rem;
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
  gap: 0.4rem;
  overflow: hidden;
  cursor: pointer;
}

.player-link {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: inherit;
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.player-avatar-large {
  width: 24px;
  height: 24px;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
  flex-shrink: 0;
}

.player-details {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.player-name-large {
  font-size: 0.9rem;
  font-weight: bold;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  color: var(--color-text-clickable);
}

.activity-map-section {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0.35rem;
  text-decoration: none;
  color: inherit;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 0.75rem;
  cursor: pointer;
  overflow: hidden;
}

.map-details {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.15rem;
  width: 100%;
  overflow: hidden;
}

.map-name-row {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  width: 100%;
  overflow: hidden;
}

.map-name-large {
  font-size: 0.9rem;
  font-weight: bold;
  color: var(--color-text-clickable);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex-shrink: 1;
  min-width: 0;
}

.map-type-badge {
  display: inline-flex;
  align-items: center;
  flex-shrink: 0;
  font-size: 0.8rem;
  font-weight: 700;
  color: #ffffff;
  background: rgba(74, 111, 165, 0.45);
  border: 1px solid rgba(74, 111, 165, 0.5);
  border-radius: 4px;
  padding: 0.05rem 0.35rem;
  white-space: nowrap;
}

.class-icon-row {
  width: 26px;
  height: 26px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  padding: 3px;
  border: 1px solid rgba(74, 111, 165, 0.4);
  flex-shrink: 0;
}

.activity-time-section {
  display: flex;
  justify-content: center;
  flex-direction: column;
  gap: 0.4rem;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 0.75rem;
  white-space: nowrap;
  overflow: hidden;
}

.rank-time-section {
  display: flex;
  flex-direction: row;
  gap: 12px;
  align-items: center;
}

.rank-display {
  font-size: 0.9rem;
  font-weight: bold;
  color: var(--color-primary);
  padding-left: 12px;
  border-left: 1px solid var(--color-border-semi-soft);
}

.placement-group-1 {
  color: #ff9797af !important;
}

.time-display {
  font-size: 0.9rem;
  font-weight: bold;
  color: var(--color-text);
}

.activity-timeago-section {
  display: flex;
  align-items: center;
  justify-content: center;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 0.75rem;
  font-size: 0.75rem;
  color: var(--color-text);
  opacity: 0.65;
  white-space: nowrap;
}

.improvement-text {
  font-size: 0.75rem;
  color: #4ade80;
  margin-left: 0.4rem;
  font-weight: normal;
}

.rank-change {
  font-size: 0.73rem;
  color: #4ade80;
  margin-left: 0.2rem;
  font-weight: normal;
}

.map-tier-rating {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 3px;
}

.min-mode-meta {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
  margin-left: auto;
  padding-right: 0.5rem;
}

.min-mode-meta .map-tier-rating {
  margin-left: 0;
  padding-right: 0;
}

.min-mode-intended-class {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 3px;
  flex-shrink: 0;
}

.min-mode-intended-class-icon {
  width: 22px;
  height: 22px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  padding: 3px;
  border: 1px solid rgba(74, 111, 165, 0.4);
  display: block;
}

.min-mode-intended-class-icon.dual {
  width: 19px;
  height: 19px;
  padding: 2px;
}

.tier-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 800;
  border-radius: 4px;
  padding: 0.1rem 0.35rem;
  white-space: nowrap;
  z-index: 10;
}

.rating-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 700;
  border-radius: 4px;
  padding: 0.1rem 0.3rem;
  white-space: nowrap;
  z-index: 10;
}

.placement-silver {
  color: #c0c0c0 !important;
}

.placement-bronze {
  color: #cd7f32 !important;
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

.timeago-short {
  display: none;
}

.activity-cards-container.normal-mode {
  gap: 0.6rem;
}

.activity-card-normal {
  display: grid;
  grid-template-columns: 0.2fr 1.2fr 2fr 1.5fr;
  gap: 0.5rem;
  padding: 0.3rem 0.6rem;
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  transition: all 0.2s ease;
  min-height: 75px;
  align-items: stretch;
}

.activity-card-normal:nth-child(odd) {
  background: rgba(255, 255, 255, 0.05);
}

.activity-card-normal:nth-child(even) {
  background: rgba(255, 255, 255, 0.08);
}

.activity-card-normal:hover {
  border-color: var(--color-border-semi-soft);
  background: rgba(74, 111, 165, 0.2) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.normal-player-section {
  display: flex;
  align-items: center;
  padding-left: 0.5rem;
  overflow: hidden;
  border-right: 1px solid var(--color-border-soft);
}

.normal-player-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  text-decoration: none;
  color: inherit;
  width: 100%;
  cursor: pointer;
}

.normal-avatar {
  width: 54px;
  height: 54px;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
  flex-shrink: 0;
}

.normal-player-details {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  flex: 1;
  overflow: hidden;
}

.normal-player-name {
  font-size: 1.1rem;
  font-weight: bold;
  color: var(--color-text-clickable);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 200px;
}

.normal-player-country {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--color-text);
  font-size: 0.85rem;
  opacity: 0.85;
}

.flag-icon-normal {
  width: 18px;
  height: auto;
}

.normal-class-section {
  display: flex;
  align-items: center;
  justify-content: center;
}

.normal-class-icon {
  width: 36px;
  height: 36px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  padding: 4px;
  border: 2px solid rgba(74, 111, 165, 0.3);
}

.normal-map-top-right {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  margin-left: auto;
  flex-shrink: 0;
}

.normal-map-intended-class {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 3px;
  z-index: 10;
}

.normal-map-class-icon {
  width: 26px;
  height: 26px;
  background: rgba(0, 0, 0, 0.45);
  border-radius: 50%;
  padding: 4px;
  border: 2px solid rgba(74, 111, 165, 0.4);
  display: block;
}

.normal-map-class-icon.dual {
  width: 22px;
  height: 22px;
  padding: 3px;
}

.normal-map-tier-rating {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 3px;
}

.normal-map-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  color: inherit;
  cursor: pointer;
  border-radius: 8px;
  padding: 0 0.75rem 0 1.5rem;
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  position: relative;
  align-self: stretch;
}

.normal-map-overlay {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  border-radius: inherit;
  pointer-events: none;
}

.normal-map-details {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  flex: 1;
  min-width: 0;
  overflow: hidden;
}

.normal-map-name-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.normal-map-name {
  font-size: 1.1rem;
  font-weight: bold;
  color: var(--color-text-clickable);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 255px;
  display: inline-block;
}

.normal-time-rank-row {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0.75rem;
}

.normal-time-section {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 0.3rem;
  border-left: 1px solid var(--color-border-soft);
  padding: 0.5rem 1rem;
  text-align: center;
}

.normal-time-display {
  font-size: 1rem;
  font-weight: bold;
  color: var(--color-text);
  white-space: nowrap;
}

.normal-rank-display {
  font-size: 1rem;
  font-weight: bold;
  color: var(--color-primary);
}

.normal-timeago {
  font-size: 0.75rem;
  color: var(--color-text);
  opacity: 0.65;
  white-space: nowrap;
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

  .activity-card {
    grid-template-columns: minmax(0, 200px) 1fr 250px 100px;
  }

  .activity-card-normal {
    min-height: 62px;
    gap: 0.4rem;
    padding: 0.25rem 0.5rem;
  }

  .normal-avatar {
    width: 40px;
    height: 40px;
  }

  .normal-player-link {
    gap: 0.6rem;
  }

  .normal-player-name {
    font-size: 0.88rem;
    max-width: 140px;
  }

  .normal-player-country {
    font-size: 0.75rem;
  }

  .normal-map-name {
    font-size: 0.88rem;
    max-width: 165px;
  }

  .normal-time-display {
    font-size: 0.88rem;
  }

  .normal-rank-display {
    font-size: 0.88rem;
  }

  .normal-timeago {
    font-size: 0.7rem;
  }

  .normal-class-icon {
    width: 28px;
    height: 28px;
  }

  .normal-map-class-icon {
    width: 22px;
    height: 22px;
    padding: 3px;
  }

  .normal-map-class-icon.dual {
    width: 19px;
    height: 19px;
    padding: 2px;
  }

  .normal-time-section {
    padding: 0.4rem 0.6rem;
  }

  .map-type-badge {
    font-size: 0.75rem;
  }

  .improvement-text {
    font-size: 0.68rem;
  }
}

@media (max-width: 992px) {
  .rating-badge,
  .tier-badge {
    font-size: 0.55rem;
  }
  .timeago-long {
    display: none;
  }
  .timeago-short {
    display: inline;
  }
  .activity-card {
    grid-template-columns: minmax(0, 150px) minmax(140px, 1fr) 210px 55px;
  }
  .activity-card .player-name-large,
  .activity-card .map-name-large,
  .activity-card .time-display,
  .activity-card .rank-display,
  .activity-card .map-type-badge,
  .activity-card .rank-change,
  .activity-card .improvement-text {
    font-size: 0.72rem;
  }
  .activity-card .map-type-badge,
  .activity-card .rank-change,
  .activity-card .improvement-text {
    font-size: 0.65rem;
  }
  .class-icon-row {
    width: 24px;
    height: 24px;
  }

  .min-mode-meta {
    display: none;
  }

  .activity-card-normal {
    grid-template-columns: 1fr;
    gap: 0.6rem;
    padding: 0.75rem;
  }

  .normal-class-section {
    display: none;
  }

  .normal-map-section {
    border-left: none;
    border-top: 1px solid var(--color-border-soft);
    padding-left: 1rem;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    min-height: 50px;
    border-radius: 6px;
  }

  .normal-time-section {
    border-left: none;
    border-top: 1px solid var(--color-border-soft);
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0 0 0;
    text-align: left;
  }

  .normal-timeago {
    margin-left: auto;
  }
}

@media (max-width: 767.98px) {
  .class-icon-row {
    width: 22px;
    height: 22px;
  }

  .activity-card {
    grid-template-columns: minmax(0, 130px) 1fr auto;
    grid-template-rows: auto;
    gap: 0.5rem;
  }

  .map-tier-rating,
  .normal-map-tier-rating,
  .normal-map-top-right .normal-map-tier-rating {
    display: none;
  }

  .activity-player-section {
    grid-column: 1;
    grid-row: 1;
    overflow: hidden;
  }

  .activity-map-section {
    grid-column: 2;
    grid-row: 1;
    border-left: 1px solid var(--color-border-soft);
    border-top: none;
    padding-left: 0.75rem;
    padding-top: 0;
    overflow: hidden;
  }

  .activity-time-section {
    grid-column: 3;
    grid-row: 1;
    border-left: 1px solid var(--color-border-soft);
    border-top: none;
    padding-left: 0.75rem;
    padding-top: 0;
  }

  .activity-timeago-section {
    display: none;
  }

  .player-link {
    overflow: hidden;
    min-width: 0;
  }

  .player-details {
    overflow: hidden;
    min-width: 0;
  }

  .map-details,
  .map-name-row {
    overflow: hidden;
    min-width: 0;
  }

  .player-name-large {
    white-space: nowrap;
    overflow-x: scroll;
    text-overflow: unset;
    scrollbar-width: none;
    cursor: grab;
  }

  .map-details {
    white-space: nowrap;
    overflow-x: scroll;
    scrollbar-width: none;
    cursor: grab;
  }

  .map-name-large {
    white-space: nowrap;
    overflow: visible;
    text-overflow: unset;
  }

  .map-name-row {
    overflow: visible;
    min-width: max-content;
  }

  .player-name-large::-webkit-scrollbar,
  .map-details::-webkit-scrollbar {
    display: none;
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

  .improvement-text {
    display: none;
  }

  .rank-change {
    display: none;
  }

  .rank-time-section {
    flex-direction: column;
    gap: 2px;
    align-items: flex-start;
  }

  .rank-display {
    border-left: none;
    padding-left: 0;
  }

  .time-display {
    font-size: 0.7rem;
  }

  .activity-card .player-name-large,
  .activity-card .map-name-large,
  .activity-card .time-display,
  .activity-card .rank-display {
    font-size: 0.8rem;
  }
}
</style>
