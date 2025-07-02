<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container py-4">
      <div
        v-if="playerNotFound"
        class="alert alert-warning player-not-found text-center"
      >
        <p class="mb-3">No player found</p>
        <button @click="returnToAllPlayers" class="btn return-button">
          Return to all players
        </button>
      </div>
      <div v-else>
        <div
          class="profile-banner mb-4 shadow"
          :class="{ 'golden-border': player.donator }"
          style="
            background: linear-gradient(
              135deg,
              rgba(74, 111, 165, 0.3),
              rgba(37, 55, 82, 0.3)
            );
            border: 1px solid rgba(42, 42, 42, 0.99);
          "
        >
          <div v-if="loading.ranks" class="text-center">
            <div class="spinner-border text-light" role="status">
              <span class="visually-hidden">Loading ranks...</span>
            </div>
          </div>

          <div
            class="row g-0"
            style="height: 100%; display: flex; align-items: center"
          >
            <div
              class="col-md-4 d-flex flex-column align-items-center profile-left p-4"
              style="height: 100%"
            >
              <div v-if="player.donator" class="donator-badge">
                <span class="badge-text">Donator</span>
              </div>
              <img
                :src="`${player.steam_avatar}`"
                alt="Avatar"
                class="rounded-circle avatar mb-3"
                onerror="this.src='/avatars/golly.jpg'"
              />
              <div v-if="player.donator" class="donator-badge">
                <span class="badge-text">Donator</span>
              </div>
              <div class="profile-info text-center">
                <h1 v-if="player.name" class="player-name">
                  {{ player.name }}
                </h1>
                <p class="rank-name mb-2">
                  <span style="color: var(--color-text-soft)">[</span>
                  <span
                    :class="getRankName(getHighestRank(), player.gender).color"
                  >
                    {{ getRankName(getHighestRank(), player.gender).title }}
                  </span>
                  <span style="color: var(--color-text-soft)">]</span>
                </p>
                <!-- <img
                :src="getLauncherIcon(player.launcher_pref)"
                alt="Launcher"
                class="launcher-icon"
                style="width: 64px; height: 64px; cursor: pointer; background: rgba(29, 29, 29, 0.5); border-radius: 50%;"
                @click="showLauncherMenu = !showLauncherMenu"
              />
              <div v-if="showLauncherMenu" class="launcher-dropdown">
                <img
                  v-for="option in launcherOptions"
                  :key="option.value"
                  :src="option.icon"
                  :alt="option.value"
                  class="launcher-option"
                  :class="{ selected: player.launcher_pref === option.value }"
                  @click="setLauncherPreference(option.value)"
                  style="width: 32px; height: 32px; margin: 4px; cursor: pointer;"
                />
              </div> -->
                <p
                  class="country mb-3"
                  style="font-weight: bold; color: #d5d5d5"
                >
                  <img
                    :src="getFlagImageUrl(player.country_code)"
                    alt="flag"
                    class="flag-icon"
                    @error="handleImageError"
                  />
                  {{ player.country }} ({{ player.country_code }})
                </p>
              </div>
            </div>
            <div class="col-md-8 d-flex align-items-center profile-right">
              <div class="row p-3 profile-overview">
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Overall rank</h3>
                      <p class="card-text player-stats">
                        #{{ player.overall_rank }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Soldier rank</h3>
                      <p class="card-text player-stats">
                        #{{ player.soldier_rank }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Demoman rank</h3>
                      <p class="card-text player-stats">
                        #{{ player.demoman_rank }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Overall points</h3>
                      <p class="card-text player-stats">
                        {{ player.overall_points }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Soldier points</h3>
                      <p class="card-text player-stats">
                        {{ player.soldier_points }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Demoman points</h3>
                      <p class="card-text player-stats">
                        {{ player.demoman_points }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="card chart-container">
              <div class="chart-header">
                <p class="section-header">Soldier points history</p>
              </div>
              <div class="chart-body">
                <div v-if="loading.points" class="text-center">
                  <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading chart...</span>
                  </div>
                </div>
                <apexchart
                  v-else
                  type="line"
                  height="250"
                  :options="soldierChartOptions"
                  :series="soldierChartSeries"
                />
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card chart-container">
              <div class="chart-header">
                <p class="section-header">Overall points history</p>
              </div>
              <div class="chart-body">
                <div v-if="loading.points" class="text-center">
                  <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading chart...</span>
                  </div>
                </div>
                <apexchart
                  v-else
                  type="line"
                  height="250"
                  :options="overallChartOptions"
                  :series="overallChartSeries"
                />
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card chart-container">
              <div class="chart-header">
                <p class="section-header">Demoman points history</p>
              </div>
              <div class="chart-body">
                <div v-if="loading.points" class="text-center">
                  <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading chart...</span>
                  </div>
                </div>
                <apexchart
                  v-else
                  type="line"
                  height="250"
                  :options="demomanChartOptions"
                  :series="demomanChartSeries"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="row main-content-wrapper">
          <div class="col-md-3 stats-boxes">
            <!-- Soldier stats on the left -->
            <div class="stats-container">
              <div class="class-section">
                <div class="card stat-block mb-3">
                  <div class="card-header">
                    <p class="section-header">Soldier stats (map)</p>
                  </div>
                  <div class="card-body">
                    <div v-if="loading.stats" class="text-center">
                      <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                    <div v-else>
                      <div class="row card-row">
                        <div class="col-md-12 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h4 class="card-title">Completion</h4>
                            <p class="card-text player-stats">
                              {{ stats.map.completion.soldier }}%
                            </p>
                          </div>
                        </div>
                        <div class="col-md-6 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h6 class="card-title">World records</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.worldRecordAmount.soldier }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-6 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h6 class="card-title">Top times</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.topTimesAmount.soldier }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G1</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.placement.soldier.G1 }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G2</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.placement.soldier.G2 }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G3</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.placement.soldier.G3 }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G4</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.placement.soldier.G4 }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-if="
                  stats.map.placement.soldier.G1 > 0 ||
                  stats.map.topTimesAmount.soldier > 0
                "
                class="class-section"
              >
                <div class="card stat-block mb-3">
                  <div class="card-header">
                    <p class="section-header">
                      {{ player.shared_soldier_type }}
                    </p>
                  </div>
                  <div class="card-body">
                    <div class="row shared-card-row">
                      <div v-if="loading.shared" class="text-center">
                        <div class="spinner-border text-light" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                      </div>
                      <div v-else class="shared-body">
                        <!-- Header Row -->
                        <div
                          class="d-flex align-items-center justify-content-between px-2 mb-2"
                          style="
                            background-color: transparent;
                            color: #aaa;
                            font-size: 0.85rem;
                            font-weight: 600;
                          "
                        >
                          <div class="d-flex align-items-center">
                            <i class="bi bi-people-fill me-2"></i>
                            <span>Player</span>
                          </div>
                          <div class="text-end">Count</div>
                        </div>

                        <!-- Player Rows -->
                        <div
                          v-for="(sharedPlayer, index) in sharedTimesSoldier"
                          :key="index"
                          class="d-flex align-items-center justify-content-between px-2 py-2 mb-2 shared-row"
                          @click="goToPlayer(sharedPlayer.playerId)"
                        >
                          <!-- Avatar + Name -->
                          <div
                            class="d-flex align-items-center shared-row-name"
                          >
                            <img
                              :src="sharedPlayer.avatar"
                              alt="Avatar"
                              class="rounded me-2 shared-avatar"
                            />
                            <span
                              class="fw-semibold"
                              style="font-size: 0.95rem"
                            >
                              {{ sharedPlayer.playerName }}
                            </span>
                          </div>

                          <!-- Count -->
                          <div
                            class="text-end fw-bold shared-count"
                            style="min-width: 24px"
                          >
                            {{ sharedPlayer.count }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 tabs-container">
            <!-- Recent runs in the middle -->
            <div class="records-card">
              <div
                class="card-header tabs-header text-white"
                style="background: rgba(255, 255, 255, 0.05)"
              >
                <div class="button-group">
                  <button
                    v-for="(tab, index) in tabs"
                    :key="index"
                    @click="currentTab = tab"
                    class="toggle-btn btn"
                    :class="{ active: currentTab === tab, 'btn-dark': true }"
                    :style="{
                      color: 'var(--color-text)',
                      border: '1px solid var(--color-border-soft)',
                      'font-weight': 'bold',
                    }"
                  >
                    {{ tab }}
                  </button>
                </div>
              </div>
              <div
                class="card-body tabs-content"
                style="background: rgba(255, 255, 255, 0.05)"
              >
                <div class="records-section">
                  <div v-if="loading[currentTab]" class="text-center">
                    <div class="spinner-border text-light" role="status">
                      <span class="visually-hidden">Loading records...</span>
                    </div>
                  </div>
                  <div v-else>
                    <div
                      v-for="(group, date) in paginatedRecords"
                      :key="date"
                      class="date-group fade-in"
                    >
                      <div class="date-header">
                        <h5>{{ formatDateHeader(date) }}</h5>
                      </div>
                      <ul class="list-group">
                        <li
                          v-for="record in group"
                          :key="record.id"
                          class="list-group-item record-item"
                          style="background: rgba(255, 255, 255, 0.05)"
                          @click="goToRecords(record.map_id)"
                        >
                          <div
                            class="d-flex align-items-center record-class-map"
                          >
                            <img
                              :src="`/icons/${record.class}.png`"
                              :alt="record.class"
                              class="class-icon"
                              loading="lazy"
                            />
                            <span class="ms-2 record-map">
                              {{ record.map_name }}
                              <span v-if="record.type !== 'map'"
                                >|
                                <template v-if="record.type === 'course'"
                                  >🚩</template
                                >
                                <template v-if="record.type === 'bonus'"
                                  >⭐</template
                                >
                                {{ record.type.slice(0, 1).toUpperCase()
                                }}{{ record.index }}
                              </span>
                            </span>
                          </div>
                          <div
                            class="text-end align-items-center record-time-detail"
                          >
                            <div class="d-flex flex-column align-items-end">
                              <div class="d-flex align-items-center gap-2">
                                <span
                                  class="record-detail record-duration"
                                  :class="[
                                    record.rank >= 1 && record.rank <= 3
                                      ? getPlacementClass(record.rank)
                                      : '',
                                  ]"
                                >
                                  {{ formatDuration(record.duration) }}
                                </span>
                                <span
                                  class="record-rank"
                                  :class="getPlacementClass(record.placement)"
                                  >{{ getMedal(record.rank) }} #{{
                                    record.rank
                                  }}</span
                                >
                              </div>
                              <span class="record-detail record-date">
                                {{ formatDate(record.date) }}
                              </span>
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                    <div class="pagination-controls">
                      <button
                        v-if="currentPage > 1"
                        @click="prevPage"
                        class="btn latest-runs-btn"
                      >
                        Previous
                      </button>
                      <div class="pagination-spacer"></div>
                      <button
                        v-if="currentPage * pageSize < filteredRecords.length"
                        @click="nextPage"
                        class="btn latest-runs-btn"
                      >
                        Next
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 stats-boxes">
            <!-- Demoman stats on the right -->
            <div class="stats-container">
              <div class="class-section">
                <div class="card stat-block mb-3">
                  <div class="card-header">
                    <p class="section-header">Demoman stats (map)</p>
                  </div>
                  <div class="card-body">
                    <div v-if="loading.stats" class="text-center">
                      <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                    <div v-else>
                      <div class="row card-row">
                        <div class="col-md-12 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h4 class="card-title">Completion</h4>
                            <p class="card-text player-stats">
                              {{ stats.map.completion.demoman }}%
                            </p>
                          </div>
                        </div>
                        <div class="col-md-6 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h6 class="card-title">World records</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.worldRecordAmount.demoman }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-6 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h6 class="card-title">Top times</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.topTimesAmount.demoman }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G1</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.placement.demoman.G1 }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G2</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.placement.demoman.G2 }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G3</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.placement.demoman.G3 }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G4</h6>
                            <p class="card-text player-stats">
                              {{ stats.map.placement.demoman.G4 }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-if="
                  stats.map.placement.demoman.G1 > 0 ||
                  stats.map.topTimesAmount.demoman > 0
                "
                class="class-section"
              >
                <div class="card stat-block mb-3">
                  <div class="card-header">
                    <p class="section-header">
                      {{ player.shared_demoman_type }}
                    </p>
                  </div>
                  <div class="card-body">
                    <div class="row shared-card-row">
                      <div v-if="loading.shared" class="text-center">
                        <div class="spinner-border text-light" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                      </div>
                      <div v-else class="shared-body">
                        <!-- Header Row -->
                        <div
                          class="d-flex align-items-center justify-content-between px-2 mb-2"
                          style="
                            background-color: transparent;
                            color: #aaa;
                            font-size: 0.85rem;
                            font-weight: 600;
                          "
                        >
                          <div class="d-flex align-items-center">
                            <i class="bi bi-people-fill me-2"></i>
                            <span>Player</span>
                          </div>
                          <div class="text-end">Count</div>
                        </div>

                        <!-- Player Rows -->
                        <div
                          v-for="(sharedPlayer, index) in sharedTimesDemoman"
                          :key="index"
                          class="d-flex align-items-center justify-content-between px-2 py-2 mb-2 shared-row"
                          @click="goToPlayer(sharedPlayer.playerId)"
                        >
                          <!-- Avatar + Name -->
                          <div
                            class="d-flex align-items-center shared-row-name"
                          >
                            <img
                              :src="sharedPlayer.avatar"
                              alt="Avatar"
                              class="rounded me-2 shared-avatar"
                            />
                            <span
                              class="fw-semibold"
                              style="font-size: 0.95rem"
                            >
                              {{ sharedPlayer.playerName }}
                            </span>
                          </div>

                          <!-- Count -->
                          <div
                            class="text-end fw-bold shared-count"
                            style="min-width: 24px"
                          >
                            {{ sharedPlayer.count }}
                          </div>
                        </div>
                      </div>
                    </div>
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
import axios from "axios";
import VueApexCharts from "vue3-apexcharts";
import { ref } from "vue";
import { useHead } from "@vueuse/head";
import { formatDuration } from "@/utils/calculations.js";
import { formatDate } from "@/utils/calculations.js";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "PlayerPage",
  setup() {
    const pageTitle = ref("Tempus Plaza | Player");

    useHead({
      title: pageTitle,
    });

    return {
      updateTitle: (playerName) => {
        pageTitle.value = `Tempus Plaza | ${playerName}`;
      },
    };
  },
  props: {
    playerId: {
      type: Number,
      required: true,
    },
  },
  components: {
    apexchart: VueApexCharts,
  },
  data: () => ({
    player: {
      id: null,
      steamid: null,
      avatar: null,
      name: null,
      country: "unknown",
      country_code: "unknown",
      overall_rank: null,
      soldier_rank: null,
      demoman_rank: null,
      overall_points: null,
      soldier_points: null,
      demoman_points: null,
      rank_name: null,
      gender: "male",
      launcher_pref: 1,
      rank_pref: "",
      donator: false,
      shared_soldier_type: "",
      shared_demoman_type: "",
    },
    launcherOptions: [
      { value: 1, icon: "/icons/rocketlauncher.png" },
      { value: 2, icon: "/icons/cowmangler.png" },
      { value: 3, icon: "/icons/original.png" },
    ],
    showLauncherMenu: false,
    rankOptions: [
      { value: 1, label: "Overall Rank" },
      { value: 2, label: "Soldier Rank" },
      { value: 3, label: "Demoman Rank" },
    ],
    showRankMenu: false,
    records: {
      recentRecords: [],
      topTimes: [],
      worldRecords: [],
    },
    tabs: ["Latest runs", "Latest top times", "Latest world records"],
    currentTab: "Latest runs",
    loading: {
      "Latest runs": true,
      "Latest top times": true,
      "Latest world records": true,
      shared: true,
      ranks: true,
      stats: true,
      points: true,
    },
    currentPage: 1,
    pageSize: 10,
    playerNotFound: false,
    stats: {
      total: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
      map: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
      course: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
      bonus: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
    },
    sharedTimesSoldier: [],
    sharedTimesDemoman: [],
    pointsHistory: [],
    overallChartSeries: [],
    soldierChartSeries: [],
    demomanChartSeries: [],
    overallChartOptions: {
      chart: {
        type: "line",
        height: 250,
        background: "rgba(255, 255, 255, 0.05)",
        dropShadow: {
          enabled: true,
          color: "red",
          top: 5,
          left: 3,
          blur: 8,
          opacity: 0.5,
        },
        toolbar: {
          show: false,
        },
        zoom: {
          enabled: false,
        },
      },
      markers: {
        size: 1,
      },
      theme: {
        mode: "dark",
      },
      colors: ["#FF6B6B"],
      stroke: {
        curve: "straight",
        width: 3,
      },
      grid: {
        borderColor: "#444",
        strokeDashArray: 5,
      },
      xaxis: {
        type: "datetime",
        labels: {
          style: {
            colors: "#aaa",
          },
        },
      },
      yaxis: {
        labels: {
          style: {
            colors: "#aaa",
          },
        },
      },
      tooltip: {
        theme: "dark",
        x: {
          format: "dd MMM yyyy",
        },
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          const data =
            w.globals.initialSeries[seriesIndex].data[dataPointIndex];
          const date = new Date(data.x).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
          });
          const points = data.y;
          const rank = data.overall_rank;

          return `
        <div class="apexcharts-tooltip-title" style="font-size: 12px; font-weight:bold; margin: 2px 0 5px; padding: 4px;">${date}</div>
        <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: center; padding: 0 10px 0 10px;">
          <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #FF6B6B;"></span>
          <div class="apexcharts-tooltip-text" style="font-size: 12px;">
            <div class="apexcharts-tooltip-y-group">
              <span class="apexcharts-tooltip-text-y-label">Overall points: </span>
              <span class="apexcharts-tooltip-text-y-value">${points}</span>
            </div>
          </div>
        </div>
        <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 2; display: flex; align-items: center; padding: 0 10px 7px 10px;">
          <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #FF6B6B;"></span>
          <div class="apexcharts-tooltip-text" style="font-size: 12px;">
            <div class="apexcharts-tooltip-y-group">
              <span class="apexcharts-tooltip-text-y-label">Overall rank: </span>
              <span class="apexcharts-tooltip-text-y-value">#${rank}</span>
            </div>
          </div>
        </div>
      `;
        },
      },
      legend: {
        labels: {
          colors: "#fff",
        },
      },
    },

    soldierChartOptions: {
      chart: {
        type: "line",
        height: 250,
        background: "rgba(255, 255, 255, 0.05)",
        dropShadow: {
          enabled: true,
          color: "cyan",
          top: 5,
          left: 3,
          blur: 8,
          opacity: 0.5,
        },
        toolbar: {
          show: false,
        },
        zoom: {
          enabled: false,
        },
      },
      markers: {
        size: 1,
      },
      theme: {
        mode: "dark",
      },
      colors: ["#4ECDC4"],
      stroke: {
        curve: "straight",
        width: 3,
      },
      grid: {
        borderColor: "#444",
        strokeDashArray: 5,
      },
      xaxis: {
        type: "datetime",
        labels: {
          style: {
            colors: "#aaa",
          },
        },
      },
      yaxis: {
        labels: {
          style: {
            colors: "#aaa",
          },
        },
      },
      tooltip: {
        theme: "dark",
        x: {
          format: "dd MMM yyyy",
        },
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          const data =
            w.globals.initialSeries[seriesIndex].data[dataPointIndex];
          const date = new Date(data.x).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
          });
          const points = data.y;
          const rank = data.soldier_rank;

          return `
        <div class="apexcharts-tooltip-title" style="font-size: 12px; font-weight:bold; margin: 2px 0 5px; padding: 4px;">${date}</div>
        <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: center; padding: 0 10px 0 10px;">
          <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #4ECDC4;"></span>
          <div class="apexcharts-tooltip-text" style="font-size: 12px;">
            <div class="apexcharts-tooltip-y-group">
              <span class="apexcharts-tooltip-text-y-label">Soldier points: </span>
              <span class="apexcharts-tooltip-text-y-value">${points}</span>
            </div>
          </div>
        </div>
        <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 2; display: flex; align-items: center; padding: 0 10px 7px 10px;">
          <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #4ECDC4;"></span>
          <div class="apexcharts-tooltip-text" style="font-size: 12px;">
            <div class="apexcharts-tooltip-y-group">
              <span class="apexcharts-tooltip-text-y-label">Soldier rank: </span>
              <span class="apexcharts-tooltip-text-y-value">#${rank}</span>
            </div>
          </div>
        </div>
      `;
        },
      },
      legend: {
        labels: {
          colors: "#fff",
        },
      },
    },

    demomanChartOptions: {
      chart: {
        type: "line",
        height: 250,
        background: "rgba(255, 255, 255, 0.05)",
        dropShadow: {
          enabled: true,
          color: "blue",
          top: 5,
          left: 3,
          blur: 8,
          opacity: 0.5,
        },
        toolbar: {
          show: false,
        },
        zoom: {
          enabled: false,
        },
      },
      markers: {
        size: 1,
      },
      theme: {
        mode: "dark",
      },
      colors: ["#45B7D1"],
      stroke: {
        curve: "straight",
        width: 3,
      },
      grid: {
        borderColor: "#444",
        strokeDashArray: 5,
      },
      xaxis: {
        type: "datetime",
        labels: {
          style: {
            colors: "#aaa",
          },
        },
      },
      yaxis: {
        labels: {
          style: {
            colors: "#aaa",
          },
        },
      },
      tooltip: {
        theme: "dark",
        x: {
          format: "dd MMM yyyy",
        },
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          const data =
            w.globals.initialSeries[seriesIndex].data[dataPointIndex];
          const date = new Date(data.x).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
          });
          const points = data.y;
          const rank = data.demoman_rank;

          return `
        <div class="apexcharts-tooltip-title" style="font-size: 12px; font-weight:bold; margin: 2px 0 5px; padding: 4px;">${date}</div>
        <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: center; padding: 0 10px 0 10px;">
          <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #45B7D1;"></span>
          <div class="apexcharts-tooltip-text" style="font-size: 12px;">
            <div class="apexcharts-tooltip-y-group">
              <span class="apexcharts-tooltip-text-y-label">Demoman points: </span>
              <span class="apexcharts-tooltip-text-y-value">${points}</span>
            </div>
          </div>
        </div>
        <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 2; display: flex; align-items: center; padding: 0 10px 7px 10px;">
          <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #45B7D1;"></span>
          <div class="apexcharts-tooltip-text" style="font-size: 12px;">
            <div class="apexcharts-tooltip-y-group">
              <span class="apexcharts-tooltip-text-y-label">Demoman rank: </span>
              <span class="apexcharts-tooltip-text-y-value">#${rank}</span>
            </div>
          </div>
        </div>
      `;
        },
      },
      legend: {
        labels: {
          colors: "#fff",
        },
      },
    },
  }),
  computed: {
    filteredRecords() {
      const tab = this.currentTab;
      if (tab === "Latest runs") {
        return this.records.recentRecords;
      } else if (tab === "Latest top times") {
        return this.records.topTimes;
      } else if (tab === "Latest world records") {
        return this.records.worldRecords;
      }
      return [];
    },
    groupedRecords() {
      const grouped = {};
      this.filteredRecords.forEach((record) => {
        const date = new Date(record.date * 1000).toDateString();
        if (!grouped[date]) {
          grouped[date] = [];
        }
        grouped[date].push(record);
      });
      return grouped;
    },
    paginatedRecords() {
      const start = (this.currentPage - 1) * this.pageSize;
      const end = start + this.pageSize;
      const grouped = this.groupedRecords;
      const dates = Object.keys(grouped);

      const paginated = {};
      let count = 0;

      for (const date of dates) {
        for (const record of grouped[date]) {
          if (count >= start && count < end) {
            if (!paginated[date]) {
              paginated[date] = [];
            }
            paginated[date].push(record);
          }
          count++;
        }
      }

      return paginated;
    },
  },
  async mounted() {
    try {
      await Promise.all([
        this.fetchPlayerData(this.playerId),
        this.fetchUserData(this.playerId),
        this.fetchPlayerRanks(this.playerId),
        this.fetchRecentRecords(this.playerId),
        this.fetchPlayerPoints(this.playerId),
      ]);
      await this.fetchPlayerStats(this.playerId);
      await this.fetchSharedTimes(this.playerId);
    } catch (error) {
      console.error("Error during mounted operations:", error);
    }
  },
  watch: {
    playerId: {
      immediate: false,
      async handler(newId, oldId) {
        if (newId !== oldId) {
          this.playerNotFound = false;
          this.loading = {
            ...this.loading,
            ranks: true,
            stats: true,
            shared: true,
            "Latest runs": true,
            "Latest top times": true,
            "Latest world records": true,
          };
          this.currentPage = 1;
          this.currentTab = "Latest runs";
          try {
            await Promise.all([
              this.fetchPlayerData(newId),
              this.fetchUserData(newId),
              this.fetchPlayerRanks(newId),
              this.fetchRecentRecords(newId),
              this.fetchPlayerPoints(newId),
            ]);
            await this.fetchPlayerStats(newId);
            await this.fetchSharedTimes(newId);
          } catch (error) {
            console.error("Error during watch operations:", error);
          }
        }
      },
    },
  },
  methods: {
    nextPage() {
      this.currentPage++;
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    formatDateHeader(dateString) {
      const date = new Date(dateString);
      const day = String(date.getDate()).padStart(2, "0");
      const month = date.toLocaleDateString("en-US", { month: "short" });
      return `${day} ${month}`;
    },
    async fetchPlayerPoints(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/points`
        );
        this.pointsHistory = response.data;
        this.updateChartData();
      } catch (error) {
        console.error("Error fetching player points:", error);
      }
    },

    updateChartData() {
      if (!this.pointsHistory.length) return;

      const sortedData = [...this.pointsHistory].sort(
        (a, b) => a.date - b.date
      );

      this.overallChartSeries = [
        {
          name: "Overall Points",
          data: sortedData.map((point) => ({
            x: point.date * 1000,
            y: point.overall_points,
            overall_rank: point.overall_rank,
          })),
        },
      ];

      this.soldierChartSeries = [
        {
          name: "Soldier Points",
          data: sortedData.map((point) => ({
            x: point.date * 1000,
            y: point.soldier_points,
            soldier_rank: point.soldier_rank,
          })),
        },
      ];

      this.demomanChartSeries = [
        {
          name: "Demoman Points",
          data: sortedData.map((point) => ({
            x: point.date * 1000,
            y: point.demoman_points,
            demoman_rank: point.demoman_rank,
          })),
        },
      ];

      this.loading.points = false;
    },
    goToRecords(mapId) {
      console.log("Navigating to records with mapId:", mapId);
      this.$router.push({
        name: "MapPage",
        params: { mapId: mapId },
      });
    },
    goToPlayer(playerId) {
      console.log("Navigating to player with playerId:", playerId);
      this.$router.push({
        name: "PlayerPage",
        params: { playerId: playerId },
      });
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
    getMedal(rank) {
      return ["🥇", "🥈", "🥉"][rank - 1] || "";
    },
    getRankName(rank, gender) {
      const titles = [
        { range: [1, 1], male: "Emperor", female: "Empress" },
        { range: [2, 2], male: "King", female: "Queen" },
        { range: [3, 3], male: "Archduke", female: "Archduchess" },
        { range: [4, 4], male: "Lord", female: "Lady" },
        { range: [5, 5], male: "Duke", female: "Duchess" },
        { range: [6, 10], male: "Prince V", female: "Princess V" },
        { range: [6, 10], male: "Prince IV", female: "Princess IV" },
        { range: [6, 10], male: "Prince III", female: "Princess III" },
        { range: [6, 10], male: "Prince II", female: "Princess II" },
        { range: [6, 10], male: "Prince I", female: "Princess I" },
        { range: [11, 11], male: "Earl V", female: "Gearl V" },
        { range: [12, 12], male: "Earl IV", female: "Gearl IV" },
        { range: [13, 13], male: "Earl III", female: "Gearl III" },
        { range: [14, 14], male: "Earl II", female: "Gearl II" },
        { range: [15, 15], male: "Earl I", female: "Gearl I" },
        { range: [16, 16], male: "Sir V", female: "Madam V" },
        { range: [17, 17], male: "Sir IV", female: "Madam IV" },
        { range: [18, 18], male: "Sir III", female: "Madam III" },
        { range: [19, 19], male: "Sir II", female: "Madam II" },
        { range: [20, 20], male: "Sir I", female: "Madam I" },
        { range: [21, 21], male: "Count V", female: "Countess" },
        { range: [22, 22], male: "Count IV", female: "Countess" },
        { range: [23, 23], male: "Count III", female: "Countess" },
        { range: [24, 24], male: "Count II", female: "Countess" },
        { range: [25, 25], male: "Count I", female: "Countess" },
        { range: [26, 30], male: "Baron V", female: "Baroness V" },
        { range: [31, 35], male: "Baron IV", female: "Baroness IV" },
        { range: [36, 40], male: "Baron III", female: "Baroness III" },
        { range: [41, 45], male: "Baron II", female: "Baroness II" },
        { range: [46, 50], male: "Baron I", female: "Baroness I" },
        { range: [51, 60], male: "Knight V", female: "Dame V" },
        { range: [61, 70], male: "Knight IV", female: "Dame IV" },
        { range: [71, 80], male: "Knight III", female: "Dame III" },
        { range: [81, 90], male: "Knight II", female: "Dame II" },
        { range: [91, 100], male: "Knight I", female: "Dame I" },
        { range: [101, 120], male: "Noble V", female: "Noblewoman V" },
        { range: [121, 140], male: "Noble IV", female: "Noblewoman IV" },
        { range: [141, 160], male: "Noble III", female: "Noblewoman III" },
        { range: [161, 180], male: "Noble II", female: "Noblewoman II" },
        { range: [181, 200], male: "Noble I", female: "Noblewoman I" },
        { range: [201, 260], male: "Esquire V", female: "Esquire V" },
        { range: [261, 320], male: "Esquire IV", female: "Esquire IV" },
        { range: [321, 380], male: "Esquire III", female: "Esquire III" },
        { range: [381, 440], male: "Esquire II", female: "Esquire II" },
        { range: [441, 500], male: "Esquire I", female: "Esquire I" },
        { range: [501, 600], male: "Jester V", female: "Jester V" },
        { range: [601, 700], male: "Jester IV", female: "Jester IV" },
        { range: [701, 800], male: "Jester III", female: "Jester III" },
        { range: [801, 900], male: "Jester II", female: "Jester II" },
        { range: [901, 1000], male: "Jester I", female: "Jester I" },
        { range: [1001, 1300], male: "Plebeian I", female: "Plebeian I" },
        { range: [1301, 1600], male: "Plebeian II", female: "Plebeian II" },
        { range: [1601, 1900], male: "Plebeian III", female: "Plebeian III" },
        { range: [1901, 2200], male: "Plebeian IV", female: "Plebeian IV" },
        { range: [2201, 2500], male: "Plebeian V", female: "Plebeian V" },
        { range: [2501, 2700], male: "Peasant I", female: "Peasant I" },
        { range: [2701, 2900], male: "Peasant II", female: "Peasant II" },
        { range: [2901, 3100], male: "Peasant III", female: "Peasant III" },
        { range: [3101, 3300], male: "Peasant IV", female: "Peasant IV" },
        { range: [3301, 3500], male: "Peasant V", female: "Peasant V" },
        { range: [3501, 4000], male: "Peasant VI", female: "Peasant VI" },
        { range: [4001, 5000], male: "Peasant VII", female: "Peasant VII" },
        { range: [5001, 999999], male: "Peon", female: "Peon" },
      ];

      for (const title of titles) {
        if (rank >= title.range[0] && rank <= title.range[1]) {
          const selectedTitle = gender === "male" ? title.male : title.female;
          let cleanedTitle = title.male.replace(/\s[IVX]+$/, "").toLowerCase();
          if (selectedTitle === "Queen" || selectedTitle === "Empress")
            cleanedTitle = title.female.replace(/\s[IVX]+$/, "").toLowerCase();
          return {
            title: selectedTitle,
            color: `rank-color-${cleanedTitle}`,
          };
        }
      }

      return { title: "Unranked", color: "--color-peon" };
    },
    getHighestRank() {
      if (this.player.rank_pref === "overall") return this.player.overall_rank;
      else if (this.player.rank_pref === "soldier")
        return this.player.soldier_rank;
      else if (this.player.rank_pref === "demoman")
        return this.player.demoman_rank;
      return Math.min(
        this.player.overall_rank,
        this.player.soldier_rank,
        this.player.demoman_rank
      );
    },
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    getLauncherIcon(preference) {
      const option = this.launcherOptions.find(
        (opt) => opt.value === preference
      );
      return option ? option.icon : "/icons/rocketlauncher.png";
    },
    setLauncherPreference(preference) {
      this.player.launcher_pref = preference;
      this.showLauncherMenu = false;
    },
    handleImageError(event) {
      event.target.src = "/icons/default-flag.jpg";
    },
    returnToAllPlayers() {
      this.$router.push({ name: "Players" });
    },
    async fetchUserData(playerId) {
      try {
        const response = await axios.get(`${API_BASE_URL}/users/${playerId}`);
        const data = response.data;
        if (!data || data.length === 0) {
          this.player = {
            ...this.player,
            gender: "male",
            donator: false,
          };
          return;
        }
        console.log("Fetched user data:", data);
        this.player = {
          ...this.player,
          gender: data.gender,
          rank_pref: data.rank_pref,
          donator: Boolean(data.donator),
          // launcher_pref: data.launcher_pref
        };
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    },
    async fetchPlayerData(playerId) {
      try {
        const response = await axios.get(`${API_BASE_URL}/players/${playerId}`);
        if (response.data.length === 0) {
          this.playerNotFound = true;
          return;
        }
        const playerData = response.data[0];
        this.player = {
          ...this.player,
          ...playerData,
          avatar: playerData.steam_avatar || "golly.jpg",
          country: playerData.country || "unknown",
          country_code: playerData.country_code || "unknown",
        };
        this.updateTitle(this.player.name);
      } catch (error) {
        this.error = "Failed to fetch player data. Please try again later.";
        console.error("Error fetching player data:", error);
        this.playerNotFound = true;
      } finally {
        this.loading.ranks = false;
      }
    },

    async fetchPlayerRanks(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/ranks`
        );
        const playerRanks = response.data[0];
        this.player.overall_rank = playerRanks.overall_rank;
        this.player.soldier_rank = playerRanks.soldier_rank;
        this.player.demoman_rank = playerRanks.demoman_rank;
        this.player.overall_points =
          playerRanks.soldier_points + playerRanks.demoman_points;
        this.player.soldier_points = playerRanks.soldier_points;
        this.player.demoman_points = playerRanks.demoman_points;
      } catch (error) {
        this.error = "Failed to fetch player ranks. Please try again later.";
        console.error("Error fetching player ranks:", error);
      } finally {
        this.loading.ranks = false;
      }
    },
    async fetchRecentRecords(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/recent-records`
        );
        const recentRecords = response.data;

        this.records.recentRecords = recentRecords.allRecords;
        this.records.topTimes = recentRecords.top10Records;
        this.records.worldRecords = recentRecords.worldRecords;
      } catch (error) {
        this.error =
          "Failed to fetch player recent records. Please try again later.";
        console.error("Error fetching recent records:", error);
      } finally {
        this.loading["Latest runs"] = false;
        this.loading["Latest top times"] = false;
        this.loading["Latest world records"] = false;
      }
    },
    async fetchPlayerStats(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/stats`
        );
        const stats = response.data;

        this.stats = {
          total: {
            completion: {
              soldier: stats.soldier.completion.total,
              demoman: stats.demoman.completion.total,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.total,
              demoman: stats.demoman.worldRecords.total,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.total,
              demoman: stats.demoman.top10Records.total,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.total,
                G2: stats.soldier.g2Records.total,
                G3: stats.soldier.g3Records.total,
                G4: stats.soldier.g4Records.total,
              },
              demoman: {
                G1: stats.demoman.g1Records.total,
                G2: stats.demoman.g2Records.total,
                G3: stats.demoman.g3Records.total,
                G4: stats.demoman.g4Records.total,
              },
            },
          },
          map: {
            completion: {
              soldier:
                Math.round(
                  (stats.soldier.completion.map + Number.EPSILON) * 100
                ) / 100,
              demoman:
                Math.round(
                  (stats.demoman.completion.map + Number.EPSILON) * 100
                ) / 100,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.map,
              demoman: stats.demoman.worldRecords.map,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.map,
              demoman: stats.demoman.top10Records.map,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.map,
                G2: stats.soldier.g2Records.map,
                G3: stats.soldier.g3Records.map,
                G4: stats.soldier.g4Records.map,
              },
              demoman: {
                G1: stats.demoman.g1Records.map,
                G2: stats.demoman.g2Records.map,
                G3: stats.demoman.g3Records.map,
                G4: stats.demoman.g4Records.map,
              },
            },
          },
          course: {
            completion: {
              soldier: stats.soldier.completion.course,
              demoman: stats.demoman.completion.course,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.course,
              demoman: stats.demoman.worldRecords.course,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.course,
              demoman: stats.demoman.top10Records.course,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.course,
                G2: stats.soldier.g2Records.course,
                G3: stats.soldier.g3Records.course,
                G4: stats.soldier.g4Records.course,
              },
              demoman: {
                G1: stats.demoman.g1Records.course,
                G2: stats.demoman.g2Records.course,
                G3: stats.demoman.g3Records.course,
                G4: stats.demoman.g4Records.course,
              },
            },
          },
          bonus: {
            completion: {
              soldier: stats.soldier.completion.bonus,
              demoman: stats.demoman.completion.bonus,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.bonus,
              demoman: stats.demoman.worldRecords.bonus,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.bonus,
              demoman: stats.demoman.top10Records.bonus,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.bonus,
                G2: stats.soldier.g2Records.bonus,
                G3: stats.soldier.g3Records.bonus,
                G4: stats.soldier.g4Records.bonus,
              },
              demoman: {
                G1: stats.demoman.g1Records.bonus,
                G2: stats.demoman.g2Records.bonus,
                G3: stats.demoman.g3Records.bonus,
                G4: stats.demoman.g4Records.bonus,
              },
            },
          },
        };
      } catch (error) {
        this.error = "Failed to fetch player stats. Please try again later.";
        console.error("Error fetching player stats:", error);
      } finally {
        this.loading.stats = false;
      }
    },
    async fetchSharedTimes(playerId) {
      try {
        const soldierTtCount = this.stats.map.topTimesAmount.soldier;
        const demomanTtCount = this.stats.map.topTimesAmount.demoman;
        const stats = {
          soldier_maps_count: soldierTtCount,
          demoman_maps_count: demomanTtCount,
        };

        const fetchSharedData = async (classType, placement, isGroupOne) => {
          try {
            const response = await axios.get(
              `${API_BASE_URL}/players/shared-names/${playerId}/${classType}/${placement}`
            );
            return response.data;
          } catch (e) {
            console.error(
              `${classType} ${
                isGroupOne ? "group1" : "top time"
              } fetch failed:`,
              e
            );
            return [];
          }
        };

        const processClassData = async (
          classType,
          countProperty,
          sharedTypeProperty,
          groupOnePlacement
        ) => {
          const placement = stats[countProperty] === 0 ? groupOnePlacement : 10;
          const isGroupOne = stats[countProperty] === 0;
          this.player[sharedTypeProperty] = isGroupOne
            ? "Shared group 1s"
            : "Shared top times";
          return await fetchSharedData(classType, placement, isGroupOne);
        };

        const [sharedSoldiers, sharedDemomans] = await Promise.all([
          processClassData(
            "soldier",
            "soldier_maps_count",
            "shared_soldier_type",
            11
          ),
          processClassData(
            "demoman",
            "demoman_maps_count",
            "shared_demoman_type",
            11
          ),
        ]);

        this.sharedTimesSoldier = Object.entries(sharedSoldiers).map(
          ([_, data]) => ({
            playerId: data.player_id,
            count: data.record_count,
            playerName: data.player_name,
            avatar: data.avatar,
          })
        );

        this.sharedTimesDemoman = Object.entries(sharedDemomans).map(
          ([_, data]) => ({
            playerId: data.player_id,
            count: data.record_count,
            playerName: data.player_name,
            avatar: data.avatar,
          })
        );
      } catch (error) {
        console.error("Error fetching shared top times:", error);
        throw error;
      } finally {
        this.loading.shared = false;
      }
    },
    formatDuration(duration) {
      return formatDuration(duration);
    },
    formatDate(unixTimestamp) {
      return formatDate(unixTimestamp);
    },
  },
};
</script>

<style scoped>
.return-button {
  background: var(--color-box);
  color: var(--color-text);
}

.return-button:hover {
  background: rgba(74, 111, 165, 0.8) !important;
  color: var(--color-text);
}

.profile-banner {
  position: relative;
  border-radius: 12px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.vue-apexcharts {
  min-height: 200px !important;
  box-shadow: 0 0px 20px rgba(0, 0, 0, 0.75);
}
.avatar {
  width: 96px;
  height: 96px;
  border: 3px solid #000e25;
}

.shared-avatar {
  width: 25px;
  height: 25px;
  border: 2px solid var(--color-primary);
}

.shared-row {
  border: 1px solid var(--color-border-soft);
  background: rgba(255, 255, 255, 0.05);
  box-shadow: 0 0px 15px rgb(0, 0, 0, 0.5);
  color: var(--color-text-clickable);
  cursor: pointer;
  transition: transform 0.3s ease;
}

.shared-row-name {
  white-space: nowrap;
  overflow: hidden;
  width: 80%;
}

.shared-count {
  color: var(--color-text);
  font-weight: bold;
}

.profile-info {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  max-width: 100%;
}

.player-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  color: var(--color-text);
  font-size: 2.5rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.flag-icon {
  width: 24px;
  height: 18px;
  vertical-align: middle;
  border-radius: 2px;
}

.banner-block {
  background: rgba(255, 255, 255, 0.05);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  border-radius: 10px;
  transition: transform 0.3s ease;
}

.shared-row:hover,
.record-item:hover {
  transform: scale(1.03);
  background-color: var(--color-primary);
}

.stat-item {
  background: rgba(255, 255, 255, 0.05);
  box-shadow: 0 0px 15px rgb(0, 0, 0, 0.5);
  border: 1px solid var(--color-border-soft);
  border-collapse: collapse;
  border-radius: 2px;
  padding: 10px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  transition: transform 0.3s ease;
}

.banner-block:hover,
.stat-item:hover {
  transform: translateY(-2px);
}
.stat-block {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.stat-block .card-title,
.banner-block .card-title {
  color: #aaa;
  font-weight: bold;
}

.stat-block .card-text,
.banner-block .card-text {
  font-size: 1.4rem;
  font-weight: bold;
}

.player-stats {
  color: var(--color-text) !important;
}

.player-shared-stats {
  color: var(--color-text) !important;
  font-size: 14px !important;
  font-weight: bold !important;
}

.section-header {
  color: var(--color-text);
  font-size: 20px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 0;
}

.button-group {
  border-radius: 8px;
  border: 1px solid var(--color-border-soft);
}
.card-header {
  margin-bottom: 0;
  border-bottom: none;
  color: var(--color-text);
  border-top-left-radius: 10px !important;
  border-top-right-radius: 10px !important;
}

.card-header.tabs-header .button-group {
  width: 100%;
  display: flex;
  padding: 0;
  margin: 0;
}

.card-header.tabs-header .btn {
  flex: 1;
  margin: 0;
  padding: 10px;
  text-align: center;
}

.card-body.tabs-content .records-section {
  width: 100%;
  padding: 0;
}

.chart-body {
  padding: 0px 10px !important;
  border-radius: 0 0 10px 10px !important;
  margin-bottom: 20px;
}

.card-row {
  padding: 0px 15px 10px 15px;
  display: flex;
}
.rank-card-body {
  padding: 15px;
}
.shared-card-row {
  padding: 5px 5px 5px 5px;
}

.list-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0;
  margin: 0;
  width: 100%;
}

.list-group-item.record-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: none;
  border-radius: 8px;
  padding: 12px 16px;
  border: 1px solid var(--color-border-soft);
  width: 100%;
  box-sizing: border-box;
  box-shadow: 0 0px 10px rgba(0, 0, 0, 0.75);
}

.list-group-item.record-item:hover {
  background: rgba(74, 111, 165, 0.8) !important;
  cursor: pointer;
}

.card-body,
.card-header {
  padding: 10px;
}
.records-card {
  border-radius: 10px !important;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.completion-boxes,
.records-boxes {
  display: flex !important;
  gap: 10px !important;
}

.completion-boxes .card,
.records-boxes .card {
  flex: 1;
}

.record-class-map {
  display: flex;
  align-items: center;
}

.shared-avatar {
  width: 25px;
  height: 25px;
  border: 2px solid var(--color-primary);
}

.record-map {
  color: var(--color-text-clickable);
  font-size: 15px;
  font-weight: bold;
}

.record-time-detail {
  min-width: 180px;
}

.record-detail {
  font-size: 14px;
  color: #aaa;
}

.record-duration {
  font-weight: bold;
  color: var(--color-text);
  font-size: 15px;
  white-space: nowrap;
}

.record-rank {
  min-width: 40px;
  text-align: right;
  white-space: nowrap;
}

.record-date {
  white-space: nowrap;
}

.record-item {
  background: var(--color-border-soft);
  border-radius: 5px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  transition: transform 0.3s ease;
}

.record-time-detail-grid {
  display: grid;
  grid-template-rows: auto auto auto;
  gap: 2px;
  justify-items: end;
  min-width: 180px;
}

.pagination-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
  padding-bottom: 10px;
}

.pagination-spacer {
  flex-grow: 1;
}

.latest-runs-btn {
  background: var(--color-box);
  color: var(--color-text);
  border: 1px solid var(--color-border-soft);
  font-weight: bold;
  padding: 8px 16px;
  border-radius: 8px;
}

.latest-runs-btn:hover {
  color: var(--color-text);
  background: rgba(74, 111, 165, 0.8) !important;
}

.date-header {
  color: var(--color-text);
  padding-top: 5px;
}

.placement-gold {
  color: #ffd700;
  font-weight: bold;
}

.placement-silver {
  color: #c0c0c0;
  font-weight: bold;
}

.placement-bronze {
  color: #cd7f32;
  font-weight: bold;
}

.placement-blue {
  color: #30869b;
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

.placement-g5 {
  color: var(--color-text) !important;
  font-weight: bold;
}

.class-icon {
  width: 36px;
  height: 36px;
}

.tabs-header .btn-group {
  width: 100%;
}

.tabs-header .btn {
  flex: 1;
}

.chart-container {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  padding: 0 5px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  margin-bottom: 20px;
  color: #fff;
}
.chart-header {
  margin-bottom: 10px;
  margin-top: 10px;
  color: var(--color-text);
}

.stats-boxes {
  flex: 1;
  padding: 10px;
}

.tabs-container {
  flex: 2;
  padding: 10px;
}

.record-item {
  font-size: 0.9rem;
  padding: 8px;
}

.clickable {
  cursor: pointer;
}

.clickable:hover {
  background: rgba(74, 111, 165, 0.8) !important;
}

.clickable-text {
  color: var(--color-text-clickable) !important;
}

.launcher-dropdown {
  position: absolute;
  background: var(--color-dark);
  border: 1px solid var(--color-border);
  z-index: 10;
  border-radius: 8px;
  left: 100%;
  transform: translateX(-60%);
}
.launcher-option.selected {
  border: 2px solid #ffd700;
  border-radius: 4px;
}

.rank-name {
  color: var(--color-text);
  font-weight: bold;
  font-size: large;
  margin-bottom: 10px;
}

.rank-dropdown {
  position: absolute;
  background: var(--color-dark);
  border: 1px solid var(--color-border);
  z-index: 10;
  border-radius: 8px;
  left: 50%;
  transform: translateX(-50%);
  padding: 5px;
}

.rank-option {
  padding: 5px;
  cursor: pointer;
  color: var(--color-text);
}

.rank-option:hover {
  background: var(--color-primary);
}

.rank-option.selected {
  background: var(--color-primary);
  border-radius: 4px;
}

.rank-color-emperor {
  color: var(--color-emperor);
}
.rank-color-empress {
  color: var(--color-empress);
}
.rank-color-king {
  color: var(--color-king);
}
.rank-color-queen {
  color: var(--color-queen);
}
.rank-color-archduke {
  color: var(--color-archduke);
}
.rank-color-lord {
  color: var(--color-lord);
}
.rank-color-duke {
  color: var(--color-duke);
}
.rank-color-prince {
  color: var(--color-prince);
}
.rank-color-earl {
  color: var(--color-earl);
}
.rank-color-sir {
  color: var(--color-sir);
}
.rank-color-count {
  color: var(--color-count);
}
.rank-color-baron {
  color: var(--color-baron);
}
.rank-color-knight {
  color: var(--color-knight);
}
.rank-color-noble {
  color: var(--color-noble);
}
.rank-color-esquire {
  color: var(--color-esquire);
}
.rank-color-jester {
  color: var(--color-jester);
}
.rank-color-plebeian {
  color: var(--color-plebeian);
}
.rank-color-peasant {
  color: var(--color-peasant);
}
.rank-color-peon {
  color: var(--color-peon);
}

.donator-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: gold;
  color: black;
  padding: 5px 10px;
  border-radius: 20px;
  font-weight: bold;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(255, 215, 0, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(255, 215, 0, 0);
  }
}

.golden-border {
  border-radius: 12px;
  border: 2px solid gold !important;
  animation: goldenGlow 2s infinite;
}

@keyframes goldenGlow {
  0% {
    box-shadow: 0 0 5px gold;
  }
  50% {
    box-shadow: 0 0 20px gold;
  }
  100% {
    box-shadow: 0 0 5px gold;
  }
}
</style>
