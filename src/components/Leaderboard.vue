<template>
  <div
    class="container d-flex flex-column align-items-center"
    style="z-index: 1"
  >
    <hr class="row-divider-no-top" style="width: 100%" />
    <div
      v-if="courseCount > 0 || bonusCount > 0"
      class="category-tabs-container"
    >
      <div class="category-tabs">
        <button
          v-if="true"
          v-for="type in ['Map']"
          :key="type"
          class="category-tab"
          :class="{
            active: selectedTypePill === type,
            'map-pill': type === 'map',
          }"
          @click="
            selectType(type);
            goTo('map', null);
          "
        >
          {{ type }}
        </button>
        <button
          v-if="courseCount > 0"
          v-for="type in ['Course']"
          :key="type"
          class="category-tab"
          :class="{ active: selectedTypePill === type }"
          @click="selectType(type)"
        >
          {{ type }}
        </button>
        <button
          v-if="bonusCount > 0"
          v-for="type in ['Bonus']"
          :key="type"
          class="category-tab"
          :class="{ active: selectedTypePill === type }"
          @click="selectType(type)"
        >
          {{ type }}
        </button>
      </div>
    </div>
    <div v-if="selectedTypePill !== 'Map'" class="subcategory-container">
      <div class="subcategory-pills">
        <!-- Map 
        <div v-show="selectedTypePill === 'Map'">
          <div class="pill-row">
            <button
              class="subcategory-pill map-pill"
              :class="{ active: type === 'map' }"
              @click="goTo('map', null)"
            >
              Map
            </button>
          </div>
        </div>-->
        <!-- Courses -->
        <div v-show="selectedTypePill === 'Course' && courseCount > 0">
          <div class="pill-row">
            <button
              v-for="courseIndex in courseCount"
              :key="'course-' + courseIndex"
              class="subcategory-pill course-pill"
              :class="{ active: type === 'course' && index === courseIndex }"
              @click="goTo('course', courseIndex)"
            >
              Course {{ courseIndex }}
            </button>
          </div>
        </div>

        <!-- Bonuses -->
        <div v-show="selectedTypePill === 'Bonus' && bonusCount > 0">
          <div class="pill-row">
            <button
              v-for="bonusIndex in bonusCount"
              :key="'bonus-' + bonusIndex"
              class="subcategory-pill bonus-pill"
              :class="{ active: type === 'bonus' && index === bonusIndex }"
              @click="goTo('bonus', bonusIndex)"
            >
              Bonus {{ bonusIndex }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <hr
      v-if="courseCount > 0 || bonusCount > 0"
      class="row-divider"
      style="width: 100%"
    />
    <div
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
                  T{{ currentSoldierTier }} - R{{ currentSoldierRating }}
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
                  v-if="userRecord.soldier && userRecord.soldier.rank > 1"
                  class="fade-in"
                  style="border-bottom: 2px solid var(--color-border)"
                >
                  <td
                    class="rank-column"
                    :class="getPlacementClass(userRecord.soldier.placement)"
                  >
                    <span v-if="userRecord.soldier.rank === 2">🥈</span>
                    <span v-else-if="userRecord.soldier.rank === 3">🥉</span>
                    {{ userRecord.soldier.rank }}
                  </td>
                  <td class="duration-column">
                    {{ formatDuration(userRecord.soldier.duration) }}
                  </td>
                  <SmartLink
                    tag="td"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: userRecord.soldier.id },
                    }"
                    class="name-cell align-middle player-name clickable name-column"
                  >
                    {{ userRecord.soldier.name }}
                  </SmartLink>
                  <td
                    class="date-column"
                    :title="
                      new Date(userRecord.soldier.date * 1000)
                        .toLocaleDateString('en-CA')
                        .replace(/-/g, '/')
                    "
                  >
                    {{ formatDate(userRecord.soldier.date) }}
                  </td>
                </tr>
                <tr v-if="loading">
                  <td colspan="4" class="text-center">
                    <div class="spinner-border text-light" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="error" class="text-center">
                  <td colspan="4" class="alert alert-danger">{{ error }}</td>
                </tr>
                <tr
                  v-else-if="!loading && displayedSoldierEntries.length === 0"
                  class="text-center"
                >
                  <td colspan="4" style="color: var(--color-text-soft)">
                    No one has completed this.
                  </td>
                </tr>
                <tr
                  v-else
                  v-for="(entry, index) in displayedSoldierEntries"
                  :key="'soldier-' + index"
                  class="fade-in"
                >
                  <td
                    class="rank-column"
                    :class="getPlacementClass(entry.placement)"
                  >
                    <span v-if="entry.rank === 1">🥇</span>
                    <span v-else-if="entry.rank === 2">🥈</span>
                    <span v-else-if="entry.rank === 3">🥉</span>
                    {{ entry.rank }}
                  </td>
                  <td class="duration-column">
                    {{ formatDuration(entry.duration) }}
                  </td>
                  <SmartLink
                    tag="td"
                    :to="{ name: 'PlayerPage', params: { playerId: entry.id } }"
                    class="name-cell align-middle player-name clickable name-column"
                    :class="{
                      'rank-1-name': entry.rank === 1,
                      'rank-2-name': entry.rank === 2,
                      'rank-3-name': entry.rank === 3,
                    }"
                  >
                    {{ entry.name }}
                  </SmartLink>
                  <td
                    class="date-column"
                    :title="
                      new Date(entry.date * 1000)
                        .toLocaleDateString('en-CA')
                        .replace(/-/g, '/')
                    "
                  >
                    {{ formatDate(entry.date) }}
                  </td>
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
              v-if="
                displayedSoldierEntries.length < selectedSoldierRecords.length
              "
              @click="showMoreSoldierEntries"
              class="btn btn-dark update-button"
              style="
                background: linear-gradient(
                  to bottom,
                  rgba(74, 111, 165, 0.5),
                  rgba(74, 111, 165, 0.3)
                );
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
                  T{{ currentDemomanTier }} - R{{ currentDemomanRating }}
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
                  v-if="userRecord.demoman && userRecord.demoman.rank > 1"
                  class="fade-in"
                  style="border-bottom: 2px solid var(--color-border)"
                >
                  <td
                    class="rank-column"
                    :class="getPlacementClass(userRecord.demoman.placement)"
                  >
                    <span v-if="userRecord.demoman.rank === 2">🥈</span>
                    <span v-else-if="userRecord.demoman.rank === 3">🥉</span>
                    {{ userRecord.demoman.rank }}
                  </td>
                  <td class="duration-column">
                    {{ formatDuration(userRecord.demoman.duration) }}
                  </td>
                  <SmartLink
                    tag="td"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: userRecord.demoman.id },
                    }"
                    class="name-cell align-middle player-name clickable name-column"
                  >
                    {{ userRecord.demoman.name }}
                  </SmartLink>
                  <td
                    class="date-column"
                    :title="
                      new Date(userRecord.demoman.date * 1000)
                        .toLocaleDateString('en-CA')
                        .replace(/-/g, '/')
                    "
                  >
                    {{ formatDate(userRecord.demoman.date) }}
                  </td>
                </tr>
                <tr v-if="loading">
                  <td colspan="4" class="text-center">
                    <div class="spinner-border text-light" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="error" class="text-center">
                  <td colspan="4" class="alert alert-danger">{{ error }}</td>
                </tr>
                <tr
                  v-else-if="displayedDemomanEntries.length === 0"
                  class="text-center"
                >
                  <td colspan="4" style="color: var(--color-text-soft)">
                    No one has completed this.
                  </td>
                </tr>
                <tr
                  v-else
                  v-for="(entry, index) in displayedDemomanEntries"
                  :key="'demoman-' + index"
                  class="fade-in"
                >
                  <td
                    class="rank-column"
                    :class="getPlacementClass(entry.placement)"
                  >
                    <span v-if="entry.rank === 1">🥇</span>
                    <span v-else-if="entry.rank === 2">🥈</span>
                    <span v-else-if="entry.rank === 3">🥉</span>
                    {{ entry.rank }}
                  </td>
                  <td class="duration-column">
                    {{ formatDuration(entry.duration) }}
                  </td>
                  <SmartLink
                    tag="td"
                    :to="{ name: 'PlayerPage', params: { playerId: entry.id } }"
                    class="name-cell align-middle player-name clickable name-column"
                    :class="{
                      'rank-1-name': entry.rank === 1,
                      'rank-2-name': entry.rank === 2,
                      'rank-3-name': entry.rank === 3,
                    }"
                  >
                    {{ entry.name }}
                  </SmartLink>
                  <td
                    class="date-column"
                    :title="
                      new Date(entry.date * 1000)
                        .toLocaleDateString('en-CA')
                        .replace(/-/g, '/')
                    "
                  >
                    {{ formatDate(entry.date) }}
                  </td>
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
              v-if="
                displayedDemomanEntries.length < selectedDemomanRecords.length
              "
              @click="showMoreDemomanEntries"
              class="btn btn-dark update-button"
              style="
                background: linear-gradient(
                  to bottom,
                  rgba(74, 111, 165, 0.5),
                  rgba(74, 111, 165, 0.3)
                );
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
</template>

<script>
import axios from "axios";
import { formatDuration, formatDate } from "@/utils/calculations.js";
import { ref } from "vue";
import { useHead } from "@vueuse/head";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;
const capitalizeFirstLetter = (str) =>
  str.charAt(0).toUpperCase() + str.slice(1);

export default {
  name: "Leaderboard",
  props: {
    mapId: { type: Number, required: true },
    type: {
      type: String,
      default: "map",
      validator: (val) => ["map", "course", "bonus"].includes(val),
    },
    index: { type: Number, default: null },
  },
  setup() {
    const pageTitle = ref("Tempus Plaza");
    useHead({ title: pageTitle });
    return {
      updateTitle: (mapName) => {
        pageTitle.value = `Tempus Plaza | ${mapName}`;
      },
    };
  },
  data() {
    return {
      map: null,
      courseCount: 0,
      bonusCount: 0,
      mapTiers: {
        soldier: { tier: 0, rating: 0 },
        demoman: { tier: 0, rating: 0 },
      },
      courses: [],
      bonuses: [],
      selectedType: "map",
      selectedIndex: null,
      selectedSoldierRecords: [],
      selectedDemomanRecords: [],
      selectedTypePill: this.type
        ? this.type.charAt(0).toUpperCase() + this.type.slice(1)
        : "Map",
      selectedCourseIndex: this.type === "course" ? this.index : null,
      selectedBonusIndex: this.type === "bonus" ? this.index : null,
      soldierDisplayCount: 50,
      demomanDisplayCount: 50,
      soldierOffset: 0,
      demomanOffset: 0,
      loading: true,
      showMoreLoading: false,
      error: null,
      playerId: null,
      userRecord: { soldier: null, demoman: null },
    };
  },
  computed: {
    currentSoldierTier() {
      if (this.type === "map") return this.mapTiers.soldier.tier;
      if (this.type === "course" && this.index != null)
        return this.courses[this.index - 1]?.soldier.tier || 0;
      if (this.type === "bonus" && this.index != null)
        return this.bonuses[this.index - 1]?.soldier.tier || 0;
      return 0;
    },
    currentSoldierRating() {
      if (this.type === "map") return this.mapTiers.soldier.rating;
      if (this.type === "course" && this.index != null)
        return this.courses[this.index - 1]?.soldier.rating || 0;
      if (this.type === "bonus" && this.index != null)
        return this.bonuses[this.index - 1]?.soldier.rating || 0;
      return 0;
    },
    currentDemomanTier() {
      if (this.type === "map") return this.mapTiers.demoman.tier;
      if (this.type === "course" && this.index != null)
        return this.courses[this.index - 1]?.demoman.tier || 0;
      if (this.type === "bonus" && this.index != null)
        return this.bonuses[this.index - 1]?.demoman.tier || 0;
      return 0;
    },
    currentDemomanRating() {
      if (this.type === "map") return this.mapTiers.demoman.rating;
      if (this.type === "course" && this.index != null)
        return this.courses[this.index - 1]?.demoman.rating || 0;
      if (this.type === "bonus" && this.index != null)
        return this.bonuses[this.index - 1]?.demoman.rating || 0;
      return 0;
    },
    displayedSoldierEntries() {
      return this.selectedSoldierRecords.slice(
        0,
        this.soldierDisplayCount + this.soldierOffset
      );
    },
    displayedDemomanEntries() {
      return this.selectedDemomanRecords.slice(
        0,
        this.demomanDisplayCount + this.demomanOffset
      );
    },
  },
  async mounted() {
    this.selectedType = capitalizeFirstLetter(this.type);
    this.selectedIndex = this.index;
    await this.fetchUser();
    await this.fetchMapData();
    await this.loadRecords(this.type, this.index);
  },
  watch: {
    mapId() {
      this.resetComponents();
      this.fetchMapData().then(() => this.loadRecords(this.type, this.index));
    },
    type(newType) {
      this.selectedTypePill =
        newType.charAt(0).toUpperCase() + newType.slice(1);
      this.resetRecordsAndUserRecord();
      this.loadRecords(newType, this.index);
    },
    index(newIndex) {
      if (this.selectedTypePill === "Course")
        this.selectedCourseIndex = newIndex;
      if (this.selectedTypePill === "Bonus") this.selectedBonusIndex = newIndex;
      this.resetRecordsAndUserRecord();
      this.loadRecords(this.type, newIndex);
    },
  },
  methods: {
    async fetchUser() {
      try {
        const res = await fetch(`${API_BASE_URL}/api/get-user`, {
          credentials: "include",
        });
        const data = await res.json();
        this.playerId = data.data?.playerid || null;
      } catch (err) {
        console.error("Error fetching user:", err);
        this.playerId = null;
      }
    },
    async fetchMapData() {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}/maps/${this.mapId}/all-info`
        );
        const { map, courses, bonuses } = data;

        this.map = map;
        this.updateTitle(map.name);

        this.courseCount = map.course_count || 0;
        this.bonusCount = map.bonus_count || 0;

        this.mapTiers.soldier.tier = map.soldier_tier || 0;
        this.mapTiers.soldier.rating = map.soldier_rating || 0;

        this.mapTiers.demoman.tier = map.demoman_tier || 0;
        this.mapTiers.demoman.rating = map.demoman_rating || 0;

        this.courses = (courses || [])
          .sort((a, b) => a.index - b.index)
          .map((c) => ({
            soldier: {
              tier: c.soldier_tier || 0,
              rating: c.soldier_rating || 0,
            },
            demoman: {
              tier: c.demoman_tier || 0,
              rating: c.demoman_rating || 0,
            },
          }));
        this.bonuses = (bonuses || [])
          .sort((a, b) => a.index - b.index)
          .map((b) => ({
            soldier: {
              tier: b.soldier_tier || 0,
              rating: b.soldier_rating || 0,
            },
            demoman: {
              tier: b.demoman_tier || 0,
              rating: b.demoman_rating || 0,
            },
          }));
      } catch (err) {
        console.error("Error fetching map data:", err);
        this.error = "Failed to fetch map data.";
      } finally {
        this.loading = false;
      }
    },
    async fetchRecords(
      type,
      classType = "both",
      index = null,
      offset = 0,
      limit = 51
    ) {
      if (offset === 0) this.loading = true;
      else this.showMoreLoading = true;
      this.error = null;

      try {
        let url = "",
          userUrl = "";
        switch (type) {
          case "map":
            url = `${API_BASE_URL}/maps/${this.mapId}/null/records/${classType}/${offset}/${limit}`;
            if (this.playerId)
              userUrl = `${API_BASE_URL}/maps/${this.mapId}/${this.playerId}/records/${classType}/${offset}/${limit}`;
            break;
          case "course":
            if (index == null) throw new Error("Course index required");
            url = `${API_BASE_URL}/maps/${this.mapId}/null/course/${classType}/${index}/records/${offset}/${limit}`;
            if (this.playerId)
              userUrl = `${API_BASE_URL}/maps/${this.mapId}/${this.playerId}/course/${classType}/${index}/records/${offset}/${limit}`;
            break;
          case "bonus":
            if (index == null) throw new Error("Bonus index required");
            url = `${API_BASE_URL}/maps/${this.mapId}/null/bonus/${classType}/${index}/records/${offset}/${limit}`;
            if (this.playerId)
              userUrl = `${API_BASE_URL}/maps/${this.mapId}/${this.playerId}/bonus/${classType}/${index}/records/${offset}/${limit}`;
            break;
        }

        const res = await axios.get(url);
        let userRes = null;
        if (userUrl) {
          try {
            userRes = await axios.get(userUrl);
          } catch (e) {
            console.warn("No user records:", e);
          }
        }

        const capClass = capitalizeFirstLetter(classType);
        if (offset === 0) this[`selected${capClass}Records`] = res.data;
        else {
          this[`selected${capClass}Records`].splice(offset, offset + 50);
          this[`selected${capClass}Records`] = [
            ...this[`selected${capClass}Records`],
            ...res.data,
          ];
        }
        if (userRes?.data) this.checkUserRecord(userRes.data);
      } catch (err) {
        console.error(`Error fetching ${type} records:`, err);
        this.error = `Error fetching ${type} records.`;
      } finally {
        this.loading = false;
        this.showMoreLoading = false;
      }
    },
    checkUserRecord(records) {
      if (!this.playerId || !records) return;
      records.forEach((r) => {
        if (r.id === this.playerId) {
          if (r.class === "soldier") this.userRecord.soldier = r;
          else if (r.class === "demoman") this.userRecord.demoman = r;
        }
      });
    },
    async loadRecords(type, index) {
      if (type === "map") {
        this.fetchRecords("map", "soldier");
        this.fetchRecords("map", "demoman");
      } else {
        this.fetchRecords(type, "soldier", index);
        this.fetchRecords(type, "demoman", index);
      }
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
    showMoreSoldierEntries() {
      let type = this.selectedTypePill.toLowerCase();
      let index = null;

      if (type === "course") index = this.selectedCourseIndex;
      else if (type === "bonus") index = this.selectedBonusIndex;

      this.fetchRecords(
        type,
        "soldier",
        index,
        this.soldierDisplayCount + this.soldierOffset
      );
      this.soldierOffset += 50;
    },
    showMoreDemomanEntries() {
      let type = this.selectedTypePill.toLowerCase();
      let index = null;

      if (type === "course") index = this.selectedCourseIndex;
      else if (type === "bonus") index = this.selectedBonusIndex;

      this.fetchRecords(
        type,
        "demoman",
        index,
        this.demomanDisplayCount + this.demomanOffset
      );
      this.demomanOffset += 50;
    },
    goTo(type, index) {
      this.$router.push({
        name: "MapPage",
        params: { mapId: this.mapId, type, index },
      });
      this.selectedType = capitalizeFirstLetter(type);
      this.selectedIndex = index;
    },
    resetRecordsAndUserRecord() {
      this.userRecord = { soldier: null, demoman: null };
      this.soldierDisplayCount = 50;
      this.demomanDisplayCount = 50;
      this.soldierOffset = 0;
      this.demomanOffset = 0;
      this.selectedSoldierRecords = [];
      this.selectedDemomanRecords = [];
    },
    resetComponents() {
      this.resetRecordsAndUserRecord();
      this.loading = false;
      this.showMoreLoading = false;
      this.error = null;
      this.courseCount = 0;
      this.bonusCount = 0;
      this.Map__soldier_tier = 0;
      this.Map__soldier_rating = 0;
      this.Map__demoman_tier = 0;
      this.Map__demoman_rating = 0;
      this.selectedTypePill = "Map";
      this.selectedType = "Map";
      this.selectedIndex = null;
      this.selectedCourseIndex = null;
      this.selectedBonusIndex = null;
    },
    selectType(type) {
      this.selectedTypePill = type;
    },
    formatDuration(totalSeconds) {
      return formatDuration(totalSeconds);
    },
    formatDate(unixTimestamp) {
      return formatDate(unixTimestamp);
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
  overflow-x: auto;
  overflow-y: hidden;
  margin-bottom: 0px;
}
.table-dark {
  margin: 0px;
  border-collapse: collapse;
  min-width: 500px;
}
.table-dark th {
  background: var(--color-primary-dark);
  color: var(--color-text);
  text-align: left;
  font-weight: 600;
  padding-bottom: 5px;
  border-top: 1px solid var(--color-border-soft);
}
.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  font-weight: bold;
  padding: 5px 7px;
}
.table-dark tr:nth-child(odd) .name-cell:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
}
.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}
.table-dark tr .name-cell.rank-1-name,
.table-dark tr .name-cell.rank-2-name,
.table-dark tr .name-cell.rank-3-name {
  position: relative;
}
.table-dark tr .name-cell.rank-1-name::before {
  content: "";
  position: absolute;
  top: 5px;
  left: 0px;
  right: 6px;
  bottom: 5px;
  background: linear-gradient(
    to right,
    rgba(255, 215, 0, 0.25),
    rgba(255, 215, 0, 0.1)
  );
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  border-radius: 0 10px 10px 0;
  pointer-events: none;
  z-index: -1;
}
.table-dark tr .name-cell.rank-2-name::before {
  content: "";
  position: absolute;
  top: 6px;
  left: 0;
  right: 16px;
  bottom: 6px;
  background: linear-gradient(
    to right,
    rgba(192, 192, 192, 0.2),
    rgba(192, 192, 192, 0.08)
  );
  border-radius: 0 10px 10px 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  pointer-events: none;
  z-index: -1;
}
.table-dark tr .name-cell.rank-3-name::before {
  content: "";
  position: absolute;
  top: 6px;
  left: 0;
  right: 26px;
  bottom: 6px;
  background: linear-gradient(
    to right,
    rgba(205, 127, 50, 0.2),
    rgba(205, 127, 50, 0.08)
  );
  border-radius: 0 10px 10px 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
  pointer-events: none;
  z-index: -1;
}
.name-cell {
  max-width: 250px;
  white-space: normal;
  overflow: hidden;
  text-overflow: ellipsis;
  color: var(--color-text-clickable) !important;
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
  font-size: 16px;
  font-weight: bold;
  color: var(--color-text);
}
.header-tier-rating {
  margin: 5px 0 0 0px;
  font-size: 14px;
  font-weight: bold;
  color: var(--color-text);
}
.maps-header {
  color: var(--color-text);
  border-radius: 10px 10px 0 0;
}
.tables-wrapper {
  min-height: 2113px;
  display: flex;
  gap: 50px;
  width: 100%;
  align-items: flex-start;
}
.table-wrapper {
  min-height: 100%;
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
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
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
.class-icon {
  width: 40px;
  height: 40px;
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
    width: 115%;
  }
}
.category-container {
  width: 100%;
  display: flex;
  justify-content: center;
}
.subcategory-container {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-top: 1rem;
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
  border-radius: 25px;
}

.pill-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
  align-items: center;
}

.subcategory-pill {
  color: var(--color-text-soft);
  padding: 10px 20px;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  white-space: nowrap;
  font-size: 14px;
  border: 2px solid transparent;
  background: rgba(255, 255, 255, 0.05);
}

.subcategory-pill.active {
  background: rgba(255, 255, 255, 0.1);
  border-color: var(--color-primary);
  color: white;
  box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

.subcategory-pill:hover:not(.active) {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.1);
  color: var(--color-text);
}

.map-pill,
.course-pill,
.bonus-pill {
  display: flex;
  justify-content: center;
  border-style: solid;
}
.category-pill:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  transform: translateY(-1px);
}
.category-pill.active {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
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
  display: flex;
  justify-content: center;
}

.category-tabs {
  display: flex;
  gap: 10px;
  background: rgba(255, 255, 255, 0.05);
  padding: 5px;
  border-radius: 25px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.category-tab {
  background: transparent;
  border: none;
  color: var(--color-text-soft);
  padding: 10px 20px;
  border-radius: 25px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  white-space: nowrap;
  font-size: 16px;
  border: 2px solid transparent;
}

.category-tab.active {
  background: rgba(255, 255, 255, 0.1);
  border-color: var(--color-primary);
  color: white;
  box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

.category-tab:hover:not(.active) {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.1);
  color: var(--color-text);
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
  margin: 15px 0 30px 0;
  opacity: 0.6;
}
.row-divider-no-top {
  border: none;
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent,
    var(--color-primary),
    transparent
  );
  margin: 5px 0 15px 0;
  opacity: 0.6;
}
</style>
