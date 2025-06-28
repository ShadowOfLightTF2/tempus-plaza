<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div
      class="container py-4 d-flex flex-column align-items-center"
      style="z-index: 1"
    >
      <div class="content-container" style="z-index: 1"></div>
      <div class="page-header">
        <h1 class="page-title">
          <span class="title-icon">🗺️</span>
          Map leaderboards
        </h1>
        <p class="page-subtitle">
          Leaderboards for maps, courses and bonuses records
        </p>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div v-if="!loading && selectedRecords.length > 0">
        <div class="map-name-container clickable" @click="goToMap(mapId)">
          <h1 class="text-center maps-title">
            {{ selectedRecords[0].map_name }}
          </h1>
        </div>
      </div>
      <div class="category-tabs-container my-4">
        <div class="category-tabs">
          <button
            v-for="type in ['Map', 'Course', 'Bonus']"
            :key="type"
            class="category-tab"
            :class="{ active: selectedTypePill === type }"
            @click="selectType(type)"
          >
            {{ type }}
          </button>
        </div>
      </div>
      <div class="subcategory-container my-3">
        <div class="subcategory-pills">
          <template v-if="selectedTypePill === 'Map'">
            <div class="pill-row">
              <button
                class="subcategory-pill map-pill"
                :class="{ active: selectedIndex === '' }"
                @click="selectRecords('map', '')"
              >
                Map
              </button>
            </div>
          </template>
          <template v-if="selectedTypePill === 'Course' && courseCount > 0">
            <div class="pill-row">
              <button
                v-for="courseIndex in courseCount"
                :key="'course-' + courseIndex"
                class="subcategory-pill course-pill"
                :class="{ active: selectedCourseIndex === courseIndex }"
                @click="selectRecords('course', courseIndex)"
              >
                Course {{ courseIndex }}
              </button>
            </div>
          </template>
          <template v-if="selectedTypePill === 'Bonus' && bonusCount > 0">
            <div class="pill-row">
              <button
                v-for="bonusIndex in bonusCount"
                :key="'bonus-' + bonusIndex"
                class="subcategory-pill bonus-pill"
                :class="{ active: selectedBonusIndex === bonusIndex }"
                @click="selectRecords('bonus', bonusIndex)"
              >
                Bonus {{ bonusIndex }}
              </button>
            </div>
          </template>
        </div>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div v-if="loading" class="text-center">
        <div class="spinner-border text-light" role="status">
          <span class="visually-hidden">Loading records...</span>
        </div>
      </div>
      <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
      <div
        v-else
        class="tables-wrapper d-flex flex-column flex-md-row justify-content-center"
      >
        <div class="soldier-table-container">
          <div class="table-wrapper">
            <div
              class="maps-header"
              style="
                background: linear-gradient(
                  135deg,
                  rgba(74, 111, 165, 0.3),
                  rgba(37, 55, 82, 0.3)
                );
              "
            >
              <div class="header-content">
                <img
                  src="/icons/soldier.png"
                  alt="Soldier Icon"
                  class="class-icon"
                />
                <div class="header-text">
                  <p class="header-type">
                    {{ selectedType }}
                    {{ selectedIndex !== null ? selectedIndex : "" }}
                  </p>
                  <p class="header-tier-rating">
                    T{{ selectedTier("soldier") }} - R{{
                      selectedRating("soldier")
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-dark">
                <thead>
                  <tr>
                    <th>Rank</th>
                    <th>Duration</th>
                    <th>Player</th>
                    <th style="text-align: right">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(entry, index) in displayedSoldierEntries"
                    :key="'soldier-' + index"
                    class="fade-in"
                  >
                    <td
                      class="rank-column"
                      :class="getPlacementClass(entry.placement)"
                    >
                      <span v-if="index + 1 === 1">🥇</span>
                      <span v-else-if="index + 1 === 2">🥈</span>
                      <span v-else-if="index + 1 === 3">🥉</span>
                      {{ index + 1 }}
                    </td>
                    <td class="duration-column">
                      {{ formatDuration(entry.duration) }}
                    </td>
                    <td
                      class="name-cell align-middle player-name clickable name-column"
                      @click="goToPlayer(entry.id)"
                    >
                      {{ entry.name }}
                    </td>
                    <td class="date-column">{{ formatDate(entry.date) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="showMoreLoading" class="text-center">
              <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading records...</span>
              </div>
            </div>
            <div v-else class="maps-footer">
              <button
                v-if="displayedSoldierEntries.length < selectedRecords.length"
                @click="showMoreSoldierEntries"
                class="btn btn-dark update-button"
                style="
                  background: rgba(74, 111, 165, 0.8);
                  font-weight: bold;
                  width: 100%;
                "
              >
                Show more
              </button>
            </div>
          </div>
        </div>
        <div class="demoman-table-container">
          <div class="table-wrapper">
            <div
              class="maps-header"
              style="
                background: linear-gradient(
                  135deg,
                  rgba(74, 111, 165, 0.3),
                  rgba(37, 55, 82, 0.3)
                );
              "
            >
              <div class="header-content">
                <img
                  src="/icons/demoman.png"
                  alt="Demoman Icon"
                  class="class-icon"
                />
                <div class="header-text">
                  <p class="header-type">
                    {{ selectedType }}
                    {{ selectedIndex !== null ? selectedIndex : "" }}
                  </p>
                  <p class="header-tier-rating">
                    T{{ selectedTier("demoman") }} - R{{
                      selectedRating("demoman")
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-dark">
                <thead>
                  <tr>
                    <th>Rank</th>
                    <th>Duration</th>
                    <th>Player</th>
                    <th style="text-align: right">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(entry, index) in displayedDemomanEntries"
                    :key="'demoman-' + index"
                    class="fade-in"
                  >
                    <td
                      class="rank-column"
                      :class="getPlacementClass(entry.placement)"
                    >
                      <span v-if="index + 1 === 1">🥇</span>
                      <span v-else-if="index + 1 === 2">🥈</span>
                      <span v-else-if="index + 1 === 3">🥉</span>
                      {{ index + 1 }}
                    </td>
                    <td class="duration-column">
                      {{ formatDuration(entry.duration) }}
                    </td>
                    <td
                      class="name-cell align-middle player-name clickable name-column"
                      @click="goToPlayer(entry.id)"
                    >
                      {{ entry.name }}
                    </td>
                    <td class="date-column">{{ formatDate(entry.date) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="showMoreLoading" class="text-center">
              <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading records...</span>
              </div>
            </div>
            <div v-else class="maps-footer">
              <button
                v-if="displayedDemomanEntries.length < selectedRecords.length"
                @click="showMoreDemomanEntries"
                class="btn btn-dark update-button"
                style="
                  background: rgba(74, 111, 165, 0.8);
                  font-weight: bold;
                  width: 100%;
                "
              >
                Show more
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { formatDuration } from "@/utils/calculations.js";
import { formatDate } from "@/utils/calculations.js";
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Records",
  data() {
    return {
      selectedRecords: [],
      loading: false,
      showMoreLoading: false,
      error: null,
      soldierDisplayCount: 50,
      demomanDisplayCount: 50,
      courseCount: 0,
      bonusCount: 0,
      Map__soldier_tier: 0,
      Map__soldier_rating: 0,
      Map__demoman_tier: 0,
      Map__demoman_rating: 0,
      selectedType: "Map",
      selectedTypePill: "Map",
      selectedIndex: "",
      selectedCourseIndex: "",
      selectedBonusIndex: "",
      soldierOffset: 0,
      demomanOffset: 0,
    };
  },
  computed: {
    mapId() {
      return Number(this.$route.params.mapId);
    },
    displayedSoldierEntries() {
      return this.selectedRecords
        .filter((record) => record.class === "soldier")
        .slice(0, this.soldierDisplayCount + this.soldierOffset);
    },
    displayedDemomanEntries() {
      return this.selectedRecords
        .filter((record) => record.class === "demoman")
        .slice(0, this.demomanDisplayCount + this.demomanOffset);
    },
  },
  mounted() {
    this.fetchMapData();
  },
  watch: {
    mapId() {
      this.fetchMapData();
    },
  },
  methods: {
    selectType(type) {
      this.selectedTypePill = type;
    },
    selectedTier(className) {
      if (this.selectedType && this.selectedIndex !== null) {
        const type = this.selectedType;
        const tier = `${type}_${this.selectedIndex}_${className}_tier`;
        return this[tier] || 0;
      }
      return 0;
    },
    selectedRating(className) {
      if (this.selectedType && this.selectedIndex !== null) {
        const type = this.selectedType;
        const rating = `${type}_${this.selectedIndex}_${className}_rating`;
        return this[rating] || 0;
      }
      return 0;
    },
    async fetchMapData() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/${this.mapId}/all-info`
        );
        const { map, courses, bonuses } = response.data;

        this.courseCount = map.course_count || 0;
        this.bonusCount = map.bonus_count || 0;

        this.Map__soldier_tier = map.soldier_tier || 0;
        this.Map__soldier_rating = map.soldier_rating || 0;
        this.Map__demoman_tier = map.demoman_tier || 0;
        this.Map__demoman_rating = map.demoman_rating || 0;

        for (let i = 0; i < this.courseCount; i++) {
          const courseIndex = i + 1;
          const course = courses[i];

          this[`Course_${courseIndex}_soldier_tier`] = course.soldier_tier || 0;
          this[`Course_${courseIndex}_soldier_rating`] =
            course.soldier_rating || 0;
          this[`Course_${courseIndex}_demoman_tier`] = course.demoman_tier || 0;
          this[`Course_${courseIndex}_demoman_rating`] =
            course.demoman_rating || 0;
        }

        for (let i = 0; i < this.bonusCount; i++) {
          const bonusIndex = i + 1;
          const bonus = bonuses[i];

          this[`Bonus_${bonusIndex}_soldier_tier`] = bonus.soldier_tier || 0;
          this[`Bonus_${bonusIndex}_soldier_rating`] =
            bonus.soldier_rating || 0;
          this[`Bonus_${bonusIndex}_demoman_tier`] = bonus.demoman_tier || 0;
          this[`Bonus_${bonusIndex}_demoman_rating`] =
            bonus.demoman_rating || 0;
        }

        this.fetchLeaderboardData();
      } catch (error) {
        this.error = "Error fetching map data.";
        console.error("Error fetching map data:", error);
      } finally {
        this.loading = false;
      }
    },
    async fetchLeaderboardData(offset = 0, limit = 100) {
      if ((offset = 0)) {
        this.loading = true;
      } else {
        this.showMoreLoading = true;
      }
      this.error = null;
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/${this.mapId}/records/${offset}/${limit}`
        );
        if (offset === 0) {
          this.selectedRecords = response.data;
        } else {
          this.selectedRecords = [...this.selectedRecords, ...response.data];
        }
        document.title = "Tempus plaza - " + response.data[0].map_name;
      } catch (error) {
        this.error = "Error fetching leaderboard data.";
        console.error("Error fetching leaderboard data:", error);
      } finally {
        this.loading = false;
        this.showMoreLoading = false;
      }
    },
    async fetchCourseRecords(courseIndex, offset = 0, limit = 100) {
      if ((offset = 0)) {
        this.loading = true;
      } else {
        this.showMoreLoading = true;
      }
      this.error = null;
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/${this.mapId}/course/${courseIndex}/records/${offset}/${limit}`
        );
        if (offset === 0) {
          this.selectedRecords = response.data;
        } else {
          this.selectedRecords = [...this.selectedRecords, ...response.data];
        }
      } catch (error) {
        this.error = "Error fetching course records.";
        console.error("Error fetching course records:", error);
      } finally {
        this.loading = false;
        this.showMoreLoading = false;
      }
    },
    async fetchBonusRecords(bonusIndex, offset = 0, limit = 100) {
      if ((offset = 0)) {
        this.loading = true;
      } else {
        this.showMoreLoading = true;
      }
      this.error = null;
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/${this.mapId}/bonus/${bonusIndex}/records/${offset}/${limit}`
        );
        if (offset === 0) {
          this.selectedRecords = response.data;
        } else {
          this.selectedRecords = [...this.selectedRecords, ...response.data];
        }
      } catch (error) {
        this.error = "Error fetching bonus records.";
        console.error("Error fetching bonus records:", error);
      } finally {
        this.loading = false;
        this.showMoreLoading = false;
      }
    },
    selectRecords(type, index) {
      this.selectedType = type.charAt(0).toUpperCase() + type.slice(1);
      this.selectedIndex = index !== undefined ? index : null;
      this.soldierOffset = 0;
      this.demomanOffset = 0;

      if (type === "map") {
        this.fetchLeaderboardData();
      } else if (type === "course") {
        this.selectedCourseIndex = index;
        this.selectedBonusIndex = null;
        this.fetchCourseRecords(index);
      } else if (type === "bonus") {
        this.selectedCourseIndex = null;
        this.selectedBonusIndex = index;
        this.fetchBonusRecords(index);
      }

      this.soldierDisplayCount = 50;
      this.demomanDisplayCount = 50;
    },
    formatDuration(totalSeconds) {
      return formatDuration(totalSeconds);
    },
    formatDate(unixTimestamp) {
      return formatDate(unixTimestamp);
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
    async goToPlayer(playerId) {
      this.$router.push({
        name: "PlayerPage",
        params: { playerId: playerId },
      });
    },
    goToMap(mapId) {
      this.$router.push({
        name: "MapPage",
        params: { mapId: mapId },
      });
    },
    showMoreSoldierEntries() {
      this.soldierOffset += 50;
      if (this.selectedTypePill === "Map") {
        this.fetchLeaderboardData(
          0,
          this.soldierDisplayCount + this.soldierOffset
        );
      } else if (
        this.selectedTypePill === "Course" &&
        this.selectedCourseIndex
      ) {
        this.fetchCourseRecords(
          this.selectedCourseIndex,
          0,
          this.soldierDisplayCount + this.soldierOffset
        );
      } else if (this.selectedTypePill === "Bonus" && this.selectedBonusIndex) {
        this.fetchBonusRecords(
          this.selectedBonusIndex,
          0,
          this.soldierDisplayCount + this.soldierOffset
        );
      }
    },

    showMoreDemomanEntries() {
      this.demomanOffset += 50;
      if (this.selectedTypePill === "Map") {
        this.fetchLeaderboardData(
          0,
          this.demomanDisplayCount + this.demomanOffset
        );
      } else if (
        this.selectedTypePill === "Course" &&
        this.selectedCourseIndex
      ) {
        this.fetchCourseRecords(
          this.selectedCourseIndex,
          0,
          this.demomanDisplayCount + this.demomanOffset
        );
      } else if (this.selectedTypePill === "Bonus" && this.selectedBonusIndex) {
        this.fetchBonusRecords(
          this.selectedBonusIndex,
          0,
          this.demomanDisplayCount + this.demomanOffset
        );
      }
    },
  },
};
</script>

<style scoped>
.record-selector {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.table-responsive {
  overflow: hidden;
  margin-bottom: 0px;
}

.table-dark {
  margin: 0px;
  border-collapse: collapse;
}

.table-dark th {
  background: var(--color-primary-dark);
  color: var(--color-text);
  text-align: left;
  font-weight: 600;
  padding-bottom: 7px;
  border-top: 1px solid var(--color-border-soft);
}

.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  font-weight: bold;
  padding: 7px;
}

.table-dark tr:nth-child(odd) .name-cell:hover {
  background: rgba(74, 111, 165, 0.8);
}

.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}

.name-cell {
  max-width: 250px;
  white-space: normal;
  overflow: hidden;
  text-overflow: ellipsis;
  color: var(--color-text-clickable) !important;
}

.maps-title {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 2.5rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  color: var(--color-text-clickable);
}

.header-content {
  display: flex;
  align-items: center;
  padding: 10px;
}

.header-text {
  margin-left: 10px;
  text-align: left;
  font-weight: bold;
}

.header-type {
  margin: 0;
  font-size: 18px;
  font-weight: bold;
  color: var(--color-text);
}

.header-tier-rating {
  margin: 5px 0 0 0px;
  font-size: 16px;
  color: var(--color-text);
}

.maps-header {
  color: var(--color-text);
  border-radius: 10px 10px 0 0;
}

.selected-record {
  color: var(--color-text);
  font-size: 20px;
  font-weight: bold;
  margin-top: 10px;
  margin-bottom: 0px;
}

.tables-wrapper {
  display: flex;
  gap: 50px;
  width: 100%;
  align-items: flex-start;
}

.table-wrapper {
  width: 100%;
  flex: 1;
  border-radius: 12px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.rank-column {
  width: auto;
  white-space: nowrap;
  text-align: right;
}

.duration-column {
  width: auto;
  white-space: nowrap;
  text-align: right;
}

.points-column {
  width: auto;
  white-space: nowrap;
}

.name-column {
  width: 100%;
  white-space: nowrap;
}

.date-column {
  width: auto;
  white-space: nowrap;
  text-align: right;
}

.player-name:hover {
  background: rgba(74, 111, 165, 0.8);
}

.clickable {
  cursor: pointer;
}

.maps-footer {
  z-index: 10;
}

.update-button {
  position: relative;
  z-index: 10;
  border-top-left-radius: 0px;
  border-top-right-radius: 0px;
  border-bottom-left-radius: 12px;
  border-bottom-right-radius: 12px;
  pointer-events: auto;
}

.update-button:hover {
  background: var(--color-row) !important;
}

.map-name-container {
  border-radius: 16px;
  padding: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
  transition: all 0.3s ease;
  border: 1px solid var(--color-border-soft);
  box-shadow: 0 0px 20px rgb(0, 0, 0, 0.5);
  position: relative;
  overflow: hidden;
}

.map-name-container:hover {
  background: rgba(74, 111, 165, 0.8);
}

.class-icon {
  width: 48px;
  height: 48px;
  margin: 8px;
}

.placement-gold {
  color: #ffd700 !important;
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
.placement-blue {
  color: #30869b !important;
  font-weight: bold;
}
.placement-g1 {
  color: #ff9797af !important;
  font-weight: bold;
}
.placement-g2 {
  color: #f7cf84 !important;
  font-weight: bold;
}
.placement-g3 {
  color: #d27d2dbb !important;
  font-weight: bold;
}
.placement-g4 {
  color: #b3b3b3ce !important;
  font-weight: bold;
}

@media (max-width: 767.98px) {
  .tables-wrapper {
    flex-direction: column;
    align-items: center;
  }
}

.category-container {
  width: 100%;
  display: flex;
  justify-content: center;
}

.category-pills {
  display: flex;
  flex-direction: column;
  gap: 12px;
  justify-content: center;
  align-items: center;
  max-width: 100%;
}

.subcategory-container {
  width: 100%;
  display: flex;
  justify-content: center;
}

.subcategory-pills {
  display: flex;
  flex-direction: column;
  gap: 12px;
  justify-content: center;
  align-items: center;
  max-width: 100%;
}

.pill-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
  align-items: center;
}

.subcategory-pill {
  padding: 8px 16px;
  border: 1px solid var(--color-border-soft);
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  font-weight: bold;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: transform 0.3s ease;
}

.subcategory-pill:hover {
  background: rgba(74, 111, 165, 0.8);
  transform: scale(1.03);
}

.subcategory-pill.active {
  background: rgba(74, 111, 165, 0.8);
  border-color: var(--color-primary);
  color: var(--color-text);
  box-shadow: 0 0px 20px rgb(0, 0, 0, 0.5);
}

.map-pill {
  display: flex;
  min-width: 200px;
  justify-content: center;
  border-style: solid;
}

.course-pill {
  border-style: solid;
}

.bonus-pill {
  border-style: solid;
}

.category-pill:hover {
  background: rgba(74, 111, 165, 0.8);
  transform: translateY(-1px);
}

.category-pill.active {
  background: rgba(74, 111, 165, 0.8);
  border-color: var(--color-primary);
  color: var(--color-text);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

@media (max-width: 767.98px) {
  .category-pill {
    padding: 6px 12px;
    font-size: 12px;
  }
}

.category-tabs-container {
  width: 100%;
  display: flex;
  justify-content: center;
}

.category-tabs {
  display: flex;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 4px;
  gap: 2px;
  box-shadow: 0 0px 20px rgb(0, 0, 0, 0.5);
  border: 1px solid var(--color-border-soft);
}

.category-tab {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  background: transparent;
  color: var(--color-text);
  font-weight: bold;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  transition: transform 0.3s ease;
}

.category-tab:hover {
  background: rgba(74, 111, 165, 0.8);
  transform: scale(1.03);
}

.category-tab.active {
  background: rgba(74, 111, 165, 0.8);
  color: var(--color-text);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.row-divider {
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
</style>
