<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
    @click="closeDropdown"
  >
    <div class="w-95 mx-auto py-4 d-flex flex-column align-items-center">
      <div class="content-container">
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">🏆</span>
            Tempus Top Players
          </h1>
          <p class="page-subtitle">
            View the top players in various categories
          </p>
        </div>
        <hr class="row-divider" style="width: 100%" />
        <div class="category-tabs-container">
          <div class="category-tabs">
            <button
              v-for="cat in categoryNames"
              :key="cat"
              class="category-tab"
              :class="{ active: selectedCategory === cat }"
              @click="selectCategory(cat)"
            >
              {{ categoryDisplayNames[cat] || capitalize(cat) }}
            </button>
          </div>
        </div>
        <div class="subcategory-container">
          <div class="subcategory-pills">
            <template v-if="hasCountSubmenu(selectedCategory)">
              <div class="pill-row">
                <button
                  v-for="item in dropdowns[selectedCategory]"
                  :key="item"
                  class="subcategory-pill"
                  :class="{ active: selectedItem === item && points }"
                  @click="selectItem(selectedCategory, item)"
                >
                  {{ item }}
                </button>
              </div>
              <div class="pill-row">
                <button
                  v-for="item in getCountItems(selectedCategory)"
                  :key="item + '-count'"
                  class="subcategory-pill count-pill"
                  :class="{
                    active: selectedItem === item + ' (count)' && !points,
                  }"
                  @click="selectItem(selectedCategory, item + ' (count)')"
                >
                  {{ item }} <span class="count-badge">count</span>
                </button>
              </div>
            </template>
            <template v-else>
              <div class="pill-row">
                <button
                  v-for="item in dropdowns[selectedCategory]"
                  :key="item"
                  class="subcategory-pill"
                  :class="{ active: selectedItem === item }"
                  @click="selectItem(selectedCategory, item)"
                >
                  {{ item }}
                </button>
              </div>
            </template>
          </div>
        </div>
        <hr
          v-if="selectedCategory === 'countries'"
          class="row-divider"
          style="width: 100%"
        />
        <div
          v-if="selectedCategory === 'countries'"
          class="search-container"
          @click.stop
        >
          <div class="search-box">
            <div class="search-icon-container">
              <svg
                class="search-icon"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
            </div>
            <input
              v-model="searchQuery"
              @input="filterCountries"
              @focus="
                showCountryDropdown = true;
                resetCountryFilter();
              "
              type="text"
              class="search-input"
              placeholder="Search for a country..."
            />
          </div>
          <div
            v-if="showCountryDropdown && filteredCountries.length > 0"
            class="search-results-dropdown"
          >
            <ul>
              <li
                v-for="country in filteredCountries.slice(0, 20)"
                :key="country.code"
                @click="selectCountryFromSearch(country)"
                class="search-result-item"
              >
                <img
                  :src="country.flag || '/icons/default-flag.jpg'"
                  :alt="country.name"
                  class="flag-icon"
                />
                {{ country.name }}
              </li>
            </ul>
          </div>
        </div>
        <hr class="row-divider" style="width: 100%" />
        <div class="d-flex justify-content-center mb-3" style="gap: 10px">
          <button
            class="toggle-overall-btn"
            :class="{ active: showOverallTable }"
            @click="showOverallTable = !showOverallTable"
          >
            <span v-if="showOverallTable">Hide Overall</span>
            <span v-else>Show Overall</span>
          </button>
          <button
            class="toggle-overall-btn mode-toggle-btn"
            @click="toggleMinMode"
          >
            <span v-if="minMode">Min Mode</span>
            <span v-else>Podium Mode</span>
          </button>
        </div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>
        <div
          class="tables-wrapper d-flex flex-column flex-md-row justify-content-center"
          :class="{ 'two-tables': !showOverallTable }"
        >
          <div
            v-for="table in tableConfigs"
            :key="table.key"
            :class="`${table.key}-table-container`"
            v-show="table.key !== 'overall' || showOverallTable"
          >
            <div class="table-wrapper">
              <div class="header-content">
                <img
                  :src="table.icon"
                  :alt="`${table.label} Icon`"
                  class="class-icon"
                />
                <div class="header-text">
                  <p class="header-title">
                    {{
                      categoryDisplayNames[selectedCategory] ||
                      capitalize(selectedCategory)
                    }}
                    - {{ selectedItem }}
                    <img
                      v-if="
                        selectedCategory === 'countries' &&
                        selectedCountry &&
                        selectedItem !== 'Total'
                      "
                      :src="selectedCountry.flag"
                      class="flag"
                    />
                  </p>
                </div>
              </div>
              <div class="your-rank-banner">
                <span class="your-rank-position">{{
                  table.userRank ? "#" + table.userRank.rank : "-"
                }}</span>
                <div
                  v-if="
                    selectedCategory === 'countries' && selectedItem === 'Total'
                  "
                  class="your-rank-player"
                  :class="{ clickable: !!table.userRank }"
                  @click="
                    table.userRank && selectCountryFromSearch(table.userRank)
                  "
                >
                  <img
                    :src="
                      table.userRank
                        ? table.userRank.flag
                        : '/icons/default-flag.jpg'
                    "
                    class="your-rank-flag"
                    @error="handleError"
                  />
                  <span class="your-rank-name">{{
                    table.userRank ? table.userRank.name : "-"
                  }}</span>
                </div>
                <SmartLink
                  v-else-if="table.userRank"
                  tag="div"
                  :to="{
                    name: 'PlayerPage',
                    params: { playerId: table.userRank.player_id },
                  }"
                  class="your-rank-player clickable"
                >
                  <img
                    :src="
                      table.userRank.steam_avatar ||
                      currentUserAvatar ||
                      defaultAvatarPath
                    "
                    class="your-rank-avatar"
                    @error="handleError"
                  />
                  <span class="your-rank-name">{{ table.userRank.name }}</span>
                </SmartLink>
                <div v-else class="your-rank-player">
                  <img
                    :src="currentUserAvatar || defaultAvatarPath"
                    class="your-rank-avatar"
                    @error="handleError"
                  />
                  <span class="your-rank-name">-</span>
                </div>
                <span class="your-rank-amount">{{
                  formatAmount(table.userRank)
                }}</span>
              </div>
              <div
                v-if="!minMode && (firstLoad || isSwitching)"
                class="podium-container"
              >
                <PlayersSkeleton type="podium" />
              </div>
              <div v-else-if="table.topPlayers.length" class="podium-container">
                <div
                  v-for="(player, index) in table.topPlayers"
                  :key="`top-${table.key}-${index}`"
                  class="podium-card"
                  :class="`podium-rank-${index + 1}`"
                >
                  <span class="podium-rank-number">{{ index + 1 }}</span>
                  <img
                    v-if="
                      !(
                        selectedCategory === 'countries' &&
                        selectedItem === 'Total'
                      ) && player.podiumFlag
                    "
                    :src="player.podiumFlag"
                    class="podium-flag"
                    @error="handleError"
                  />
                  <div
                    v-if="
                      selectedCategory === 'countries' &&
                      selectedItem === 'Total'
                    "
                    class="podium-player-link clickable"
                    @click="selectCountryFromSearch(player)"
                  >
                    <img
                      :src="player.flag || '/icons/default-flag.jpg'"
                      class="flag-avatar"
                      @error="handleError"
                    />
                    <div class="podium-name">{{ player.name }}</div>
                  </div>
                  <SmartLink
                    v-else
                    tag="div"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: player.player_id },
                    }"
                    class="podium-player-link clickable"
                  >
                    <img
                      :src="player.steam_avatar"
                      class="podium-avatar"
                      @error="handleError"
                    />
                    <div class="podium-name">
                      {{ player.name }}
                      <span
                        v-if="player.inactive"
                        class="inactive-badge"
                        title="Not seen in 3+ months"
                        >Inactive</span
                      >
                    </div>
                  </SmartLink>
                  <div class="podium-main-stat">
                    <span class="podium-stat-value">{{
                      formatPlayerAmount(player)
                    }}</span>
                  </div>
                  <div
                    v-if="
                      selectedCategory === 'countries' &&
                      selectedItem === 'Total'
                    "
                    class="podium-country-players"
                  >
                    <SmartLink
                      v-for="(topPlayer, tIndex) in player.podiumTopPlayers"
                      :key="`top-${table.key}-country-player-${tIndex}`"
                      tag="div"
                      :to="{
                        name: 'PlayerPage',
                        params: { playerId: topPlayer.player_id },
                      }"
                      class="podium-country-player clickable"
                    >
                      <img
                        :src="topPlayer.steam_avatar"
                        class="podium-country-avatar"
                        @error="handleError"
                      />
                      <span class="podium-country-player-name">{{
                        topPlayer.name
                      }}</span>
                      <span class="podium-country-player-points">
                        {{
                          (topPlayer.points ?? 0)
                            .toString()
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                        }}
                      </span>
                    </SmartLink>
                    <div
                      v-if="
                        !player.podiumTopPlayers ||
                        !player.podiumTopPlayers.length
                      "
                      class="podium-country-player-empty"
                    >
                      No players yet
                    </div>
                  </div>
                  <div v-else class="podium-substats">
                    <div class="podium-substat">
                      <span class="podium-substat-label">{{
                        selectedCategory === "completion"
                          ? "Points"
                          : "Completion"
                      }}</span>
                      <span class="podium-substat-value">{{
                        formatPodiumSubstat(player)
                      }}</span>
                    </div>
                    <div class="podium-substat">
                      <span class="podium-substat-label">WRs</span>
                      <span class="podium-substat-value">{{
                        player.podiumWr ?? "—"
                      }}</span>
                    </div>
                    <div class="podium-substat">
                      <span class="podium-substat-label">Top Times</span>
                      <span class="podium-substat-value">{{
                        player.podiumTt ?? "—"
                      }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-dark">
                  <thead>
                    <tr>
                      <th>Rank</th>
                      <th>Player</th>
                      <th>{{ getTableHeader() }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template v-if="firstLoad || isSwitching">
                      <PlayersSkeleton type="rows" />
                    </template>
                    <template v-else>
                      <tr
                        v-for="(player, index) in table.displayedPlayers"
                        :key="`${table.key}-${index}`"
                        class="fade-in"
                        :class="{
                          'current-user-row':
                            currentUserId && player.player_id === currentUserId,
                        }"
                      >
                        <td class="rank-column">
                          #{{ index + table.rankOffset }}
                        </td>
                        <td
                          v-if="
                            selectedCategory === 'countries' &&
                            selectedItem === 'Total'
                          "
                          class="country-cell align-middle fancy-hover clickable name-column"
                          @click="selectCountryFromSearch(player)"
                        >
                          <img
                            :src="player.flag || '/icons/default-flag.jpg'"
                            class="flag"
                            @error="handleError"
                          />
                          {{ player.name }}
                          <span
                            v-if="player.inactive"
                            class="inactive-badge"
                            title="Not seen in 3+ months"
                            >Inactive</span
                          >
                        </td>
                        <SmartLink
                          v-else
                          tag="td"
                          :to="{
                            name: 'PlayerPage',
                            params: { playerId: player.player_id },
                          }"
                          class="name-cell align-middle fancy-hover clickable name-column"
                        >
                          <img
                            :src="player.steam_avatar"
                            class="avatar"
                            @error="handleError"
                          />
                          {{ player.name }}
                          <span
                            v-if="player.inactive"
                            class="inactive-badge"
                            title="Not seen in 3+ months"
                            >Inactive</span
                          >
                        </SmartLink>
                        <td
                          class="points-column"
                          :class="{
                            'percentage-column':
                              selectedCategory === 'completion',
                          }"
                        >
                          {{ formatPlayerAmount(player) }}
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
              <div class="players-footer">
                <div class="load-size-selector">
                  <span class="load-size-label">Load</span>
                  <div class="load-size-toggle">
                    <button
                      v-for="size in loadSizeOptions"
                      :key="`${table.key}-size-${size}`"
                      class="load-size-btn"
                      :class="{ active: loadSize === size }"
                      @click="loadSize = size"
                    >
                      {{ size }}
                    </button>
                  </div>
                </div>
                <button
                  class="btn btn-dark update-button"
                  style="
                    background: linear-gradient(
                      to bottom,
                      rgba(74, 111, 165, 0.5),
                      rgba(74, 111, 165, 0.3)
                    );
                    font-weight: bold;
                  "
                  @click="table.loadMore()"
                  :disabled="table.loading"
                >
                  <span v-if="!table.loading">Show more</span>
                  <span
                    v-else
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"
                  ></span>
                </button>
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
import { useHead } from "@vueuse/head";
import PlayersSkeleton from "@/components/skeletons/PlayersSkeleton.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Players",
  components: { PlayersSkeleton },
  setup() {
    useHead({ title: "Leaderboards | Tempus Plaza" });
  },
  data: () => ({
    loadSize: 50,
    loadSizeOptions: [50, 100, 200],
    showOverallTable: true,
    minMode: false,
    soldierPlayers: [],
    demomanPlayers: [],
    overallPlayers: [],
    userRankSoldier: null,
    userRankDemoman: null,
    userRankOverall: null,
    currentUserId: null,
    currentUserAvatar: null,
    firstLoad: true,
    isSwitching: false,
    loadingSoldiers: false,
    loadingDemomen: false,
    loadingOverall: false,
    error: null,
    points: true,
    searchQuery: "",
    filteredCountries: [],
    allCountries: [],
    selectedCountry: null,
    showCountryDropdown: false,
    selectedCategory: "points",
    selectedItem: "Combined",
    dropdowns: {
      points: [],
      wrs: [],
      tts: [],
      groups: [],
      ratings: [],
      tiers: [],
    },
    categoryNames: [
      "points",
      "wrs",
      "tts",
      "groups",
      "ratings",
      "tiers",
      "completion",
      "countries",
    ],
    categoryDisplayNames: {
      wrs: "World records",
      tts: "Top times",
      points: "Points",
      groups: "Groups",
      ratings: "Ratings (maps)",
      tiers: "Tiers",
      completion: "Completion",
      countries: "Countries",
    },
    currentSoldierIndex: 50,
    currentDemomanIndex: 50,
    currentOverallIndex: 50,
    _internalNavigation: false,
  }),

  async mounted() {
    this.minMode = localStorage.getItem("playersMinMode") === "true";
    window.addEventListener("storage", this.handleStorageChange);

    this.fillDropdowns();
    await this.fetchUser();
    await this.loadCountriesList();

    const { category, item } = this.$route.params;
    if (!category || !item) {
      this.selectedCategory = "points";
      this.selectedItem = "Total";
      this.$router.push({
        name: "Players",
        params: { category: "points", item: "Total" },
      });
    } else {
      this.selectedCategory = category;
      if (category === "countries" && item !== "Total") {
        const foundCountry = this.allCountries.find(
          (c) =>
            c.name.replace(/\s+/g, "-").toLowerCase() === item.toLowerCase(),
        );
        if (foundCountry) {
          this.selectedCountry = foundCountry;
          this.selectedItem = foundCountry.name;
        }
      } else {
        this.selectedItem = this.normalizeItemFromRoute(item);
      }
    }
    this.fetchDataForCurrentSelection(0, "both");
  },
  beforeUnmount() {
    window.removeEventListener("storage", this.handleStorageChange);
  },

  computed: {
    defaultAvatarPath() {
      return `${import.meta.env.BASE_URL}avatars/default-avatar.jpg`;
    },
    topSoldierPlayers() {
      return this.soldierPlayers.slice(0, 3);
    },
    topDemomanPlayers() {
      return this.demomanPlayers.slice(0, 3);
    },
    topOverallPlayers() {
      return this.overallPlayers.slice(0, 3);
    },
    displayedSoldierPlayers() {
      return this.soldierPlayers.slice(3, this.currentSoldierIndex);
    },
    displayedDemomanPlayers() {
      return this.demomanPlayers.slice(3, this.currentDemomanIndex);
    },
    displayedOverallPlayers() {
      return this.overallPlayers.slice(3, this.currentOverallIndex);
    },
    tableConfigs() {
      const rankOffset = this.minMode ? 1 : 4;
      return [
        {
          key: "soldier",
          label: "Soldier",
          icon: "/icons/soldier.png",
          userRank: this.userRankSoldier,
          topPlayers: this.minMode ? [] : this.topSoldierPlayers,
          displayedPlayers: this.minMode
            ? this.soldierPlayers.slice(0, this.currentSoldierIndex)
            : this.displayedSoldierPlayers,
          rankOffset,
          loading: this.loadingSoldiers,
          loadMore: () => this.loadMoreSoldiers(),
        },
        {
          key: "demoman",
          label: "Demoman",
          icon: "/icons/demoman.png",
          userRank: this.userRankDemoman,
          topPlayers: this.minMode ? [] : this.topDemomanPlayers,
          displayedPlayers: this.minMode
            ? this.demomanPlayers.slice(0, this.currentDemomanIndex)
            : this.displayedDemomanPlayers,
          rankOffset,
          loading: this.loadingDemomen,
          loadMore: () => this.loadMoreDemomen(),
        },
        {
          key: "overall",
          label: "Overall",
          icon: "/icons/overall.png",
          userRank: this.userRankOverall,
          topPlayers: this.minMode ? [] : this.topOverallPlayers,
          displayedPlayers: this.minMode
            ? this.overallPlayers.slice(0, this.currentOverallIndex)
            : this.displayedOverallPlayers,
          rankOffset,
          loading: this.loadingOverall,
          loadMore: () => this.loadMoreOverall(),
        },
      ];
    },
  },

  methods: {
    toggleMinMode() {
      this.minMode = !this.minMode;
      localStorage.setItem("playersMinMode", this.minMode);
    },
    handleStorageChange(event) {
      if (event.key === "playersMinMode") {
        this.minMode = event.newValue === "true";
      }
    },
    formatAmount(userRank) {
      if (!userRank) return 0;
      if (this.selectedCategory === "completion") return userRank.amount + "%";
      return (userRank.amount ?? 0)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    formatPlayerAmount(player) {
      if (this.selectedCategory === "completion")
        return player.percentage + "%";
      return (player.amount ?? 0)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    formatPodiumSubstat(player) {
      if (this.selectedCategory === "completion") {
        return player.podiumPoints != null
          ? player.podiumPoints.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
          : "—";
      }
      return player.podiumCompletion != null
        ? player.podiumCompletion + "%"
        : "—";
    },
    getTableHeader() {
      if (this.points) {
        if (this.selectedCategory === "countries")
          return this.selectedItem === "Player Count" ? "Players" : "Points";
        return "Points";
      }
      if (this.selectedCategory === "completion") return "Percentage";
      return "Count";
    },
    async fetchPodiumStats(lists) {
      const soldierList = lists ? lists.soldierData : this.soldierPlayers;
      const demomanList = lists ? lists.demomanData : this.demomanPlayers;
      const overallList = lists ? lists.overallData : this.overallPlayers;

      if (
        this.selectedCategory === "countries" &&
        this.selectedItem === "Total"
      ) {
        await this.fetchCountryPodiumPlayers({
          soldierList,
          demomanList,
          overallList,
        });
        return;
      }

      const targets = [
        { list: soldierList, statKey: "soldier" },
        { list: demomanList, statKey: "demoman" },
        { list: overallList, statKey: "overall" },
      ];

      await Promise.all(
        targets.flatMap(({ list, statKey }) =>
          (list || [])
            .slice(0, 3)
            .map((player) => this.applyPodiumStats(player, statKey)),
        ),
      );
    },
    async applyPodiumStats(player, statKey) {
      if (!player || !player.player_id) return;
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}/players/${player.player_id}/full`,
        );
        const info = data["0"] || {};
        const statsKey = statKey === "overall" ? "combined" : statKey;
        const stats = data.stats ? data.stats[statsKey] : null;
        const rank = data.ranks && data.ranks[0] ? data.ranks[0] : null;

        player.podiumFlag = info.country_code
          ? `https://flagcdn.com/32x24/${info.country_code.toLowerCase()}.png`
          : null;
        player.podiumCompletion =
          stats?.completion?.total != null
            ? Math.round(stats.completion.total * 100) / 100
            : null;
        player.podiumPoints = rank ? (rank[`${statKey}_points`] ?? null) : null;
        player.podiumWr = stats?.worldRecords
          ? (stats.worldRecords.total ?? null)
          : null;
        player.podiumTt = stats?.top10Records
          ? (stats.top10Records.total ?? null)
          : null;
      } catch (error) {
        console.error(
          `Error fetching podium stats for player ${player.player_id}:`,
          error,
        );
        player.podiumFlag = null;
        player.podiumCompletion = null;
        player.podiumPoints = null;
        player.podiumWr = null;
        player.podiumTt = null;
      }
    },
    async fetchCountryPodiumPlayers(lists) {
      const targets = [
        { list: lists.soldierList || this.soldierPlayers, key: "topSoldiers" },
        { list: lists.demomanList || this.demomanPlayers, key: "topDemomen" },
        { list: lists.overallList || this.overallPlayers, key: "topOverall" },
      ];

      await Promise.all(
        targets.flatMap(({ list, key }) =>
          (list || [])
            .slice(0, 3)
            .map((countryEntry) =>
              this.applyCountryPodiumPlayers(countryEntry, key),
            ),
        ),
      );
    },
    async applyCountryPodiumPlayers(countryEntry, key) {
      if (!countryEntry || !countryEntry.player_id) return;
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}/players/country-top-players/${countryEntry.player_id}/0/3`,
        );
        const list = data[key] || [];
        const pointsKey =
          key === "topSoldiers"
            ? "soldier_total_points"
            : key === "topDemomen"
              ? "demoman_total_points"
              : "overall_total_points";

        countryEntry.podiumTopPlayers = list.slice(0, 3).map((p) => ({
          name: p.name,
          steam_avatar: p.steam_avatar || this.defaultAvatarPath,
          player_id: p.id,
          points: p[pointsKey] ?? 0,
        }));
      } catch (error) {
        console.error(
          `Error fetching top players for country ${countryEntry.player_id}:`,
          error,
        );
        countryEntry.podiumTopPlayers = [];
      }
    },
    async fetchUser() {
      try {
        const res = await fetch(`${API_BASE_URL}/api/get-user`, {
          credentials: "include",
        });
        const data = await res.json();
        this.currentUserId = data.data?.playerid || null;
        await this.fetchCurrentUserAvatar();
      } catch (err) {
        console.error("Error fetching user:", err);
        this.currentUserId = null;
      }
    },
    async fetchCurrentUserAvatar() {
      if (!this.currentUserId) {
        this.currentUserAvatar = null;
        return;
      }
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}/players/${this.currentUserId}`,
        );
        this.currentUserAvatar =
          data?.steam_avatar ||
          data?.["0"]?.steam_avatar ||
          this.defaultAvatarPath;
      } catch (error) {
        console.error(
          `Error fetching avatar for player ${this.currentUserId}:`,
          error,
        );
        this.currentUserAvatar = this.defaultAvatarPath;
      }
    },
    async fetchUserRank() {
      if (!this.currentUserId) {
        this.userRankSoldier =
          this.userRankDemoman =
          this.userRankOverall =
            null;
        return;
      }

      try {
        const { selectedCategory: category, selectedItem: item } = this;

        if (category === "completion") {
          const type = item
            .replace(/\s+/g, "")
            .toLowerCase()
            .replace(/combined/gi, "total");
          await this.fetchUserCompletionRank(type);
          return;
        }
        if (category === "countries") {
          await this.fetchUserCountryRank();
          return;
        }

        const { tableName, type, cat } = this.getFetchParams(category, item);
        await this.fetchUserPlayerRank(tableName, type, cat);
      } catch (error) {
        console.error("Error fetching user rank:", error);
        this.userRankSoldier =
          this.userRankDemoman =
          this.userRankOverall =
            null;
      }
    },
    async fetchUserPlayerRank(tableName, type, category) {
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}/players/user-rank/${tableName}/${type}/${category}/${this.currentUserId}`,
        );
        const mapRank = (rank) =>
          rank
            ? { ...rank, player_id: rank.player_id ?? this.currentUserId }
            : null;

        this.userRankSoldier = mapRank(data.soldierRank);
        this.userRankDemoman = mapRank(data.demomanRank);
        this.userRankOverall = mapRank(data.overallRank);
      } catch (error) {
        console.error("Error fetching user player rank:", error);
        this.userRankSoldier =
          this.userRankDemoman =
          this.userRankOverall =
            null;
      }
    },
    async fetchUserCompletionRank(type) {
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}/players/user-rank-completion/${type}/${this.currentUserId}`,
        );
        const mapRank = (rank) =>
          rank
            ? { ...rank, player_id: rank.player_id ?? this.currentUserId }
            : null;

        this.userRankSoldier = mapRank(data.soldierRank);
        this.userRankDemoman = mapRank(data.demomanRank);
        this.userRankOverall = mapRank(data.overallRank);
      } catch (error) {
        console.error("Error fetching user completion rank:", error);
        this.userRankSoldier =
          this.userRankDemoman =
          this.userRankOverall =
            null;
      }
    },
    async fetchUserCountryRank() {
      if (!this.currentUserId) return;

      const resetRanks = () => {
        this.userRankSoldier =
          this.userRankDemoman =
          this.userRankOverall =
            null;
      };

      try {
        if (this.selectedItem === "Total") {
          const { data } = await axios.get(
            `${API_BASE_URL}/players/user-country-rank/${this.currentUserId}`,
          );
          this.userRankSoldier = data.soldierRank || null;
          this.userRankDemoman = data.demomanRank || null;
          this.userRankOverall = data.overallRank || null;
        } else if (this.selectedCountry) {
          const { data } = await axios.get(
            `${API_BASE_URL}/players/user-rank-country/${this.selectedCountry.code}/${this.currentUserId}`,
          );
          const mapRank = (rank, type) =>
            rank
              ? {
                  ...rank,
                  amount: rank[`${type}_total_points`] || 0,
                  steam_avatar: rank.steam_avatar || null,
                  player_id: rank.id,
                }
              : null;

          this.userRankSoldier = mapRank(data.soldierRank, "soldier");
          this.userRankDemoman = mapRank(data.demomanRank, "demoman");
          this.userRankOverall = mapRank(data.overallRank, "overall");
        }
      } catch (error) {
        console.error("Error fetching user country rank:", error);
        resetRanks();
      }
    },
    normalizeItemFromRoute(item) {
      return item.replace(/-/g, " ");
    },
    closeDropdown() {
      this.showCountryDropdown = false;
    },
    handleError(e) {
      const fallback = `${import.meta.env.BASE_URL}avatars/default-avatar.jpg`;
      if (e.target.src !== fallback) e.target.src = fallback;
    },
    async commitFetchResult(result, offset) {
      if (offset !== 0 || !result) return;
      await this.fetchPodiumStats(result);
      this.soldierPlayers = result.soldierData || [];
      this.demomanPlayers = result.demomanData || [];
      this.overallPlayers = result.overallData || [];
      await this.fetchUserRank();
    },
    getFetchParams(category, item) {
      let tableName = category;
      let type = item
        .replace(/\s+/g, "")
        .toLowerCase()
        .replace(/combined/gi, "total");
      let cat = "points";

      if (type.includes("(count)")) {
        type = type.replace("(count)", "");
        cat = "count";
      }

      if (tableName === "ratings") {
        tableName = type.replace(/rating(\d+)/g, (_, n) => `r${n}s`);
        type = "maps";
      } else if (tableName === "groups") {
        if (type !== "groups") {
          tableName = type.replace(/group(\d+)/g, (_, n) => `g${n}s`);
        }
        type = "total";
      } else if (tableName === "tiers") {
        cat = "total";
      }

      return { tableName, type, cat };
    },
    async fetchDataForCurrentSelection(
      offset,
      classType = "both",
      limit = this.loadSize,
    ) {
      if (offset === 0 && this.selectedCategory === "countries") {
        this.isSwitching = true;
      }

      try {
        const { selectedCategory: category, selectedItem: item } = this;

        if (category === "completion") {
          this.points = false;
          const type = item
            .replace(/\s+/g, "")
            .toLowerCase()
            .replace(/combined/gi, "total");
          const result = await this.fetchCompletions(
            type,
            offset,
            classType,
            limit,
          );
          await this.commitFetchResult(result, offset);
          return;
        }

        if (category === "countries") {
          this.points = true;
          const result = await this.fetchCountries(offset, classType, limit);
          await this.commitFetchResult(result, offset);
          return;
        }

        this.points = !item.includes("(count)");
        const { tableName, type, cat } = this.getFetchParams(category, item);
        const result = await this.fetchPlayers(
          tableName,
          type,
          cat,
          offset,
          classType,
          limit,
        );
        await this.commitFetchResult(result, offset);
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        this.firstLoad = false;
        this.isSwitching = false;
        this.loadingSoldiers = false;
        this.loadingDemomen = false;
        this.loadingOverall = false;
      }
    },
    async loadCountriesList() {
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}/players/get-countries-data`,
        );
        this.allCountries = data
          .map((country) => ({
            code: country.country_code,
            name: country.country,
            flag: `https://flagcdn.com/32x24/${country.country_code.toLowerCase()}.png`,
            totalPoints:
              country.soldier_total_points + country.demoman_total_points,
          }))
          .sort((a, b) => b.totalPoints - a.totalPoints);
        this.filteredCountries = [...this.allCountries];
      } catch (error) {
        console.error("Error loading countries list:", error);
      }
    },
    filterCountries() {
      if (!this.searchQuery.trim()) {
        this.filteredCountries = [...this.allCountries];
        return;
      }
      const query = this.searchQuery.toLowerCase();
      this.filteredCountries = this.allCountries.filter(
        (c) =>
          c.name.toLowerCase().includes(query) ||
          c.code.toLowerCase().includes(query),
      );
    },
    selectCountryFromSearch(country) {
      const found = this.allCountries.find(
        (c) =>
          c.code.toLowerCase() ===
          (country.code || country.player_id || "").toLowerCase(),
      );
      this.selectedCountry = found || country;
      this.showCountryDropdown = false;
      this.resetCountryFilter();
      this.selectItem("countries", this.selectedCountry.name);
    },
    resetCountryFilter() {
      this.filteredCountries = [...this.allCountries];
      this.searchQuery = "";
    },
    fillDropdowns() {
      this.dropdowns.points = ["Total", "Maps", "Courses", "Bonuses"];
      this.dropdowns.wrs = ["Total", "Maps", "Courses", "Bonuses"];
      this.dropdowns.tts = ["Total", "Maps", "Courses", "Bonuses"];
      this.dropdowns.groups = [
        "Groups",
        "Group 1",
        "Group 2",
        "Group 3",
        "Group 4",
      ];
      this.dropdowns.ratings = ["Rating 1", "Rating 2", "Rating 3"];
      this.dropdowns.tiers = [
        "Tier 1",
        "Tier 2",
        "Tier 3",
        "Tier 4",
        "Tier 5",
        "Tier 6",
        "Tier 7",
        "Tier 8",
        "Tier 9",
        "Tier 10",
      ];
      this.dropdowns.completion = ["Total", "Maps", "Courses", "Bonuses"];
      this.dropdowns.countries = ["Total"];
    },
    capitalize(str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
    getCountItems(category) {
      if (category === "groups")
        return this.dropdowns[category].filter((item) => item !== "Groups");
      return this.dropdowns[category];
    },
    selectCategory(category) {
      this.selectedCategory = category;
      const defaultItem = this.dropdowns[category][0];
      this.$router.push({
        name: "Players",
        params: {
          category,
          item:
            category === "countries" && this.selectedCountry
              ? this.selectedCountry.name.replace(/\s+/g, "-")
              : defaultItem.replace(/\s+/g, ""),
        },
      });
      this.selectItem(category, defaultItem);
    },
    async selectItem(category, item) {
      this.points = !item.includes("(count)");
      this.selectedCategory = category;
      this.selectedItem = item;

      if (category !== "countries" || item === "Total") {
        this.selectedCountry = null;
        this.searchQuery = "";
      }

      this.loadSize = 50;
      this.currentSoldierIndex =
        this.currentDemomanIndex =
        this.currentOverallIndex =
          this.loadSize;

      this._internalNavigation = true;
      this.$router.push({
        name: "Players",
        params: {
          category,
          item:
            category === "countries" && this.selectedCountry
              ? this.selectedCountry.name.replace(/\s+/g, "-")
              : item.replace(/\s+/g, "-"),
        },
      });

      await this.fetchDataForCurrentSelection(0, "both");
    },
    hasCountSubmenu(cat) {
      return ["wrs", "tts", "groups"].includes(cat);
    },
    async fetchListData(
      endpoint,
      offset,
      classType = "both",
      limit = this.loadSize,
    ) {
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}${endpoint}/${offset}/${limit}`,
        );
        const [soldierData, demomanData, overallData] = data || [];

        if (offset === 0) return { soldierData, demomanData, overallData };

        if (classType === "both" || classType === "soldier")
          this.soldierPlayers = [
            ...this.soldierPlayers,
            ...(soldierData || []),
          ];
        if (classType === "both" || classType === "demoman")
          this.demomanPlayers = [
            ...this.demomanPlayers,
            ...(demomanData || []),
          ];
        if (classType === "both" || classType === "overall")
          this.overallPlayers = [
            ...this.overallPlayers,
            ...(overallData || []),
          ];

        return null;
      } catch (error) {
        console.error(`Error fetching data from ${endpoint}:`, error);
        return null;
      }
    },
    async fetchPlayers(tableName, type, category, offset, classType, limit) {
      return this.fetchListData(
        `/players/data/${tableName}/${type}/${category}`,
        offset,
        classType,
        limit,
      );
    },
    async fetchCompletions(type, offset, classType, limit) {
      return this.fetchListData(
        `/players/players-completion-stats/${type}`,
        offset,
        classType,
        limit,
      );
    },
    async fetchCountries(offset, classType = "both", limit = this.loadSize) {
      try {
        if (this.selectedItem === "Total") {
          if (offset !== 0) return null;
          const { data: countriesData } = await axios.get(
            `${API_BASE_URL}/players/get-countries-data`,
          );

          const toEntry = (country, amountKey) => ({
            id: country.country_code,
            player_id: country.country_code,
            name: country.country,
            flag: `https://flagcdn.com/32x24/${country.country_code.toLowerCase()}.png`,
            amount:
              amountKey === "overall"
                ? country.soldier_total_points + country.demoman_total_points
                : country[amountKey] || 0,
          });

          return {
            soldierData: [...countriesData]
              .sort((a, b) => b.soldier_total_points - a.soldier_total_points)
              .map((c) => toEntry(c, "soldier_total_points")),
            demomanData: [...countriesData]
              .sort((a, b) => b.demoman_total_points - a.demoman_total_points)
              .map((c) => toEntry(c, "demoman_total_points")),
            overallData: [...countriesData]
              .sort(
                (a, b) =>
                  b.soldier_total_points +
                  b.demoman_total_points -
                  (a.soldier_total_points + a.demoman_total_points),
              )
              .map((c) => toEntry(c, "overall")),
          };
        }

        if (this.selectedCountry) {
          const { data: players } = await axios.get(
            `${API_BASE_URL}/players/country-top-players/${this.selectedCountry.code}/${offset}/${limit}`,
          );
          const normalize = (list, pointsKey) =>
            (list || []).map((p) => ({
              ...p,
              amount: p[pointsKey] ?? 0,
              steam_avatar: p.steam_avatar || this.defaultAvatarPath,
              player_id: p.id,
            }));

          if (offset === 0) {
            return {
              soldierData: normalize(
                players.topSoldiers,
                "soldier_total_points",
              ),
              demomanData: normalize(
                players.topDemomen,
                "demoman_total_points",
              ),
              overallData: normalize(
                players.topOverall || [],
                "overall_total_points",
              ),
            };
          }

          const append = (current, newData) => [...current, ...newData];
          if (classType === "both" || classType === "soldier")
            this.soldierPlayers = append(
              this.soldierPlayers,
              normalize(players.topSoldiers, "soldier_total_points"),
            );
          if (classType === "both" || classType === "demoman")
            this.demomanPlayers = append(
              this.demomanPlayers,
              normalize(players.topDemomen, "demoman_total_points"),
            );
          if (classType === "both" || classType === "overall")
            this.overallPlayers = append(
              this.overallPlayers,
              normalize(players.topOverall || [], "overall_total_points"),
            );

          return null;
        }
        return null;
      } catch (error) {
        console.error("Error fetching countries data:", error);
        return null;
      }
    },
    async loadMoreSoldiers() {
      this.loadingSoldiers = true;
      await this.fetchDataForCurrentSelection(
        this.currentSoldierIndex,
        "soldier",
        this.loadSize,
      );
      this.currentSoldierIndex = this.soldierPlayers.length;
    },
    async loadMoreDemomen() {
      this.loadingDemomen = true;
      await this.fetchDataForCurrentSelection(
        this.currentDemomanIndex,
        "demoman",
        this.loadSize,
      );
      this.currentDemomanIndex = this.demomanPlayers.length;
    },
    async loadMoreOverall() {
      this.loadingOverall = true;
      await this.fetchDataForCurrentSelection(
        this.currentOverallIndex,
        "overall",
        this.loadSize,
      );
      this.currentOverallIndex = this.overallPlayers.length;
    },
  },

  watch: {
    "$route.params": {
      handler(params) {
        if (this._internalNavigation) {
          this._internalNavigation = false;
          return;
        }
        if (params.category && params.item) {
          this.loadSize = 50;
          this.currentSoldierIndex =
            this.currentDemomanIndex =
            this.currentOverallIndex =
              50;
          this.selectedCategory = params.category;

          if (params.category === "countries" && params.item !== "Total") {
            const found = this.allCountries.find(
              (c) =>
                c.name.replace(/\s+/g, "-").toLowerCase() ===
                params.item.toLowerCase(),
            );
            if (found) {
              this.selectedCountry = found;
              this.selectedItem = found.name;
            }
          } else {
            this.selectedItem = this.normalizeItemFromRoute(params.item);
          }
          this.fetchDataForCurrentSelection(0, "both");
        }
      },
      immediate: false,
    },
  },
};
</script>

<style scoped>
.content-container {
  max-width: 1320px;
  width: 100%;
}
.header-content {
  border-radius: 10px 10px 0 0;
  display: flex;
  align-items: center;
  padding: 10px;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
}
.header-text {
  margin-left: 10px;
  text-align: left;
  font-weight: bold;
}
.header-title {
  margin: 5px 0 0 0px;
  font-size: 20px;
  color: var(--color-text);
}
.podium-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 14px 14px 9px 14px;
  background: rgba(255, 255, 255, 0.02);
  border-left: 1px solid var(--color-border-soft);
  border-right: 1px solid var(--color-border-soft);
}
.podium-card {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0 16px 10px;
  border-radius: 12px;
  background: linear-gradient(
    160deg,
    rgba(74, 111, 165, 0.18),
    rgba(37, 55, 82, 0.18)
  );
  border: 1px solid var(--color-border-soft);
}
.podium-rank-1 {
  border-color: #f4c430;
  box-shadow: 0 0 16px rgba(244, 196, 48, 0.3);
}
.podium-rank-2 {
  border-color: #c0c0c0;
  box-shadow: 0 0 16px rgba(192, 192, 192, 0.22);
}
.podium-rank-3 {
  border-color: #cd7f32;
  box-shadow: 0 0 16px rgba(205, 127, 50, 0.22);
}
.podium-rank-number {
  position: absolute;
  top: 12px;
  left: 16px;
  font-size: 24px;
  font-weight: 800;
  line-height: 1;
  color: var(--color-text-soft);
}
.podium-rank-1 .podium-rank-number {
  color: #f4c430;
}
.podium-rank-2 .podium-rank-number {
  color: #c0c0c0;
}
.podium-rank-3 .podium-rank-number {
  color: #cd7f32;
}
.podium-flag {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 28px;
  height: 20px;
  border-radius: 2px;
}
.podium-player-link {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  margin-top: 26px;
  max-width: 100%;
  text-decoration: none;
  color: var(--color-text-clickable) !important;
}
.podium-avatar {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  border: 2px solid var(--color-primary);
}
.flag-avatar {
  width: 38px;
  height: 30px;
}
.podium-name {
  font-weight: 700;
  font-size: 17px;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 220px;
}
.podium-main-stat {
  margin-top: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  font-weight: 800;
  font-size: 18px;
  color: var(--color-text);
}
.podium-substats {
  display: flex;
  gap: 22px;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  width: 100%;
  justify-content: center;
}
.podium-substat {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 58px;
}
.podium-substat-label {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  color: var(--color-text-soft);
  white-space: nowrap;
}
.podium-substat-value {
  font-size: 16px;
  font-weight: 700;
  color: var(--color-text);
  margin-top: 2px;
}
.podium-country-players {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  width: 100%;
}
.podium-country-player {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 6px;
  border-radius: 8px;
  text-decoration: none;
  color: var(--color-text-clickable) !important;
  transition: background 0.18s ease;
}
.podium-country-player:hover {
  background: rgba(255, 255, 255, 0.06);
}
.podium-country-avatar {
  width: 22px;
  height: 22px;
  border-radius: 5px;
  border: 1px solid var(--color-primary);
  flex-shrink: 0;
}
.podium-country-player-name {
  font-size: 13px;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
  min-width: 0;
}
.podium-country-player-points {
  font-size: 12px;
  font-weight: 700;
  color: var(--color-text);
  flex-shrink: 0;
  white-space: nowrap;
}
.podium-country-player-empty {
  font-size: 12px;
  color: var(--color-text-soft);
  text-align: center;
  padding: 6px 0;
}
@media (max-width: 767.98px) {
  .podium-card {
    padding: 16px 12px 12px;
  }
  .podium-rank-number {
    font-size: 24px;
  }
  .podium-avatar {
    width: 52px;
    height: 52px;
  }
  .podium-name {
    font-size: 14px;
  }
  .podium-main-stat {
    font-size: 20px;
  }
  .podium-substats {
    gap: 14px;
  }
  .podium-substat-label {
    font-size: 9px;
  }
  .podium-substat-value {
    font-size: 14px;
  }
}
.your-rank-banner {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 5px 12px 0px 12px;
  padding: 10px 14px;
  border-radius: 8px;
  background: rgba(74, 111, 165, 0.18);
  border: 1px solid var(--color-primary);
}
.your-rank-position {
  font-weight: 800;
  color: var(--color-text);
  flex-shrink: 0;
  font-size: 14px;
}
.your-rank-player {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
  min-width: 0;
  text-decoration: none;
  color: var(--color-text-clickable) !important;
}
.your-rank-flag {
  width: 28px;
  height: 20px;
  border-radius: 2px;
  flex-shrink: 0;
  object-fit: cover;
}
.your-rank-avatar {
  width: 26px;
  height: 26px;
  border-radius: 3px;
  border: 1px solid var(--color-primary);
  flex-shrink: 0;
}
.your-rank-name {
  font-weight: 700;
  font-size: 14px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.your-rank-amount {
  font-weight: 800;
  color: var(--color-text);
  flex-shrink: 0;
  font-size: 14px;
}
@media (max-width: 767.98px) {
  .your-rank-label {
    display: none;
  }
}
.tables-wrapper {
  display: flex;
  gap: 50px;
  width: 100%;
  align-items: flex-start;
}
.table-wrapper {
  position: relative;
  width: 100%;
  flex: 1;
  border-radius: 10px;
  border: 1px solid var(--color-border-soft);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  background: transparent;
  z-index: 1;
}
.table-responsive {
  margin-top: 5px;
}
.table-dark {
  margin: 0px;
  background: transparent;
}
.table-dark th {
  color: var(--color-text);
  text-align: left;
  font-size: 14px;
  font-weight: 600;
  padding-bottom: 4px;
  border-top: 1px solid var(--color-border-soft);
}
.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  font-weight: bold;
  padding: 4px 6px;
}
.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}
.current-user-row td {
  background: var(--color-primary-dark) !important;
}
.inactive-badge {
  background: rgba(255, 80, 80, 0.15);
  color: #ff7b7b;
  padding: 2px 6px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  margin-left: 6px;
  vertical-align: middle;
  letter-spacing: 0.5px;
  white-space: nowrap;
}
.name-cell,
.country-cell {
  max-width: 250px;
  white-space: normal;
  overflow: hidden;
  text-overflow: ellipsis;
  color: var(--color-text-clickable) !important;
}
.rank-column {
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
.players-footer {
  display: flex;
  align-items: stretch;
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
  overflow: hidden;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}
.load-size-selector {
  display: flex;
  align-items: center;
  background: rgba(30, 50, 80, 0.6);
  padding: 8px 10px;
  border-right: 1px solid rgba(255, 255, 255, 0.08);
  flex-shrink: 0;
}
.load-size-label {
  font-size: 11px;
  font-weight: 600;
  color: var(--color-text-soft);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-right: 7px;
  white-space: nowrap;
}
.load-size-toggle {
  display: flex;
  gap: 3px;
}
.load-size-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: var(--color-text-soft);
  font-size: 12px;
  font-weight: 600;
  padding: 5px 10px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.18s ease;
}
.load-size-btn:hover:not(.active) {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.25);
  color: var(--color-text);
}
.load-size-btn.active {
  background: rgba(74, 111, 165, 0.55);
  border-color: var(--color-primary);
  color: #fff;
  box-shadow: 0 1px 6px rgba(74, 111, 165, 0.45);
}
.update-button {
  flex: 1;
  border-radius: 0 !important;
}
@media (max-width: 767.98px) {
  .load-size-label {
    display: none;
  }
}
.clickable:hover {
  cursor: pointer;
}
.update-button:hover {
  background: var(--color-row) !important;
}
.class-icon {
  width: 40px;
  height: 40px;
  margin: 8px;
}
.avatar {
  width: 25px;
  height: 25px;
  margin-right: 1px;
  border: 1px solid var(--color-primary);
  border-radius: 2px;
}
.flag {
  width: 28px;
  height: 20px;
  margin-right: 1px;
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
  border-radius: 30px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.category-tab {
  background: transparent;
  border: none;
  color: var(--color-text-soft);
  padding: 15px 25px;
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
.subcategory-container {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-top: 1.5rem;
}
.subcategory-pills {
  display: flex;
  flex-direction: column;
  gap: 12px;
  justify-content: center;
  align-items: center;
  max-width: 100%;
  padding: 15px;
  border-radius: 25px;
  background: transparent;
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
.pill-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
  align-items: center;
}
.count-badge {
  background: rgba(255, 255, 255, 0.2);
  padding: 2px 6px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
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
.percentage-column {
  text-align: left;
  padding-left: 3% !important;
}
.search-container {
  margin: 20px 0;
  position: relative;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}
.search-box {
  display: flex;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 25px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  min-width: 300px;
}
.search-box:hover,
.search-box:focus-within {
  border-color: var(--color-primary);
  box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
  transform: translateY(-2px);
}
.search-icon-container {
  display: flex;
  align-items: center;
  justify-content: center;
  padding-left: 15px;
}
.search-icon {
  width: 20px;
  height: 20px;
  color: rgba(255, 255, 255, 0.5);
}
.search-input {
  flex: 1;
  padding: 18px 10px;
  background: transparent;
  border: none;
  color: #ffffff;
  font-size: 16px;
  outline: none;
}
.search-input::placeholder {
  color: rgba(255, 255, 255, 0.5);
}
.search-results-dropdown {
  position: absolute;
  background: var(--color-box);
  border: 1px solid rgba(68, 68, 68, 0.3);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  min-width: 300px;
  max-height: 400px;
  overflow-y: auto;
  z-index: 1000;
  margin-top: 8px;
}
.search-results-dropdown ul {
  list-style: none;
  padding: 8px;
  margin: 0;
}
.search-result-item {
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 4px;
  background: var(--color-box);
  color: #ffffff;
  cursor: pointer;
  font-size: 18px;
  font-weight: bold;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
}
.search-result-item:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  transform: translateX(4px);
}
.flag-icon {
  margin-right: 10px;
  width: 24px;
  height: 18px;
}
@media (min-width: 1400px) {
  .tables-wrapper {
    flex-wrap: nowrap;
    gap: 30px;
  }
  .soldier-table-container,
  .demoman-table-container,
  .overall-table-container {
    flex: 1;
    min-width: 0;
    max-width: 33%;
  }
}
@media (min-width: 992px) and (max-width: 1399px) {
  .tables-wrapper {
    flex-wrap: nowrap;
    gap: 20px;
  }
  .soldier-table-container,
  .demoman-table-container,
  .overall-table-container {
    flex: 1;
    min-width: 0;
    max-width: 33%;
  }
  .inactive-badge {
    font-size: 9px;
    padding: 1px 5px;
    margin-left: 4px;
  }
}
@media (max-width: 1400px) {
  .category-tabs {
    flex-wrap: wrap;
    justify-content: center;
  }
  .category-tab {
    padding: 12px 18px;
    font-size: 14px;
  }
  .class-icon {
    width: 38px;
    height: 38px;
    margin: 6px;
  }
  .table-dark td,
  .table-dark th {
    font-size: 12px;
    padding: 3px 5px;
  }
  .header-title {
    font-size: 15px;
  }
  .avatar {
    width: 20px;
    height: 20px;
  }
}
@media (max-width: 992px) {
  .tables-wrapper {
    flex-direction: row;
    align-items: flex-start;
    gap: 10px;
  }
  .soldier-table-container,
  .demoman-table-container,
  .overall-table-container {
    flex: 1;
    min-width: 0;
    overflow: hidden;
  }
  .category-tab {
    padding: 12px 16px;
    font-size: 13px;
    border: 1px solid var(--color-border-soft);
  }
}
@media (min-width: 768px) and (max-width: 991.98px) {
  .table-responsive {
    overflow-x: hidden;
  }
  .table-dark td,
  .table-dark th {
    font-size: 11px;
    padding: 3px 4px;
  }
  .name-cell,
  .country-cell {
    max-width: 120px;
  }
  .avatar {
    width: 18px;
    height: 18px;
  }
  .flag {
    width: 20px;
    height: 14px;
  }
  .header-title {
    font-size: 14px;
  }
  .class-icon {
    width: 32px;
    height: 32px;
  }
}
@media (max-width: 767.98px) {
  .subcategory-pill {
    padding: 10px 12px;
    font-size: 12px;
  }
  .tables-wrapper {
    flex-direction: column;
    align-items: center;
  }
  .search-results-dropdown {
    min-width: 150px;
    max-width: 300px;
  }
  .class-icon {
    width: 35px;
    height: 35px;
  }
  .header-title {
    font-size: 18px;
  }
  .inactive-badge {
    font-size: 8px;
    padding: 1px 4px;
    margin-left: 3px;
    border-radius: 8px;
    letter-spacing: 0;
  }
}
@media (min-width: 1400px) {
  .tables-wrapper.two-tables {
    gap: 50px;
  }
  .tables-wrapper.two-tables .soldier-table-container,
  .tables-wrapper.two-tables .demoman-table-container {
    max-width: 50%;
  }
}
@media (min-width: 992px) and (max-width: 1399px) {
  .tables-wrapper.two-tables {
    gap: 30px;
  }
  .tables-wrapper.two-tables .soldier-table-container,
  .tables-wrapper.two-tables .demoman-table-container {
    max-width: 50%;
  }
}
@media (max-width: 1200px) {
  .tables-wrapper.two-tables .category-tabs {
    flex-wrap: wrap;
    justify-content: center;
  }
  .tables-wrapper.two-tables .category-tab {
    padding: 12px 18px;
    font-size: 14px;
  }
  .tables-wrapper.two-tables .class-icon {
    width: 38px;
    height: 38px;
    margin: 6px;
  }
}
@media (max-width: 991px) and (min-width: 768px) {
  .tables-wrapper.two-tables .soldier-table-container,
  .tables-wrapper.two-tables .demoman-table-container {
    flex: 1;
    min-width: 0;
    overflow: hidden;
  }
}
@media (max-width: 767.98px) {
  .soldier-table-container,
  .demoman-table-container,
  .overall-table-container {
    width: 100%;
    flex: 1;
    min-width: 0;
  }
}
.toggle-overall-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(255, 255, 255, 0.15);
  color: var(--color-text-soft);
  padding: 8px 20px;
  border-radius: 20px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
}
.toggle-overall-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: var(--color-primary);
  color: white;
  box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}
.toggle-overall-btn.active {
  background: rgba(255, 255, 255, 0.1);
  border-color: var(--color-primary);
  color: white;
  box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}
.mode-toggle-btn {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}
.mode-toggle-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}
</style>
