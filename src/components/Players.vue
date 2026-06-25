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
        <div class="d-flex justify-content-center mb-3">
          <button
            class="toggle-overall-btn"
            :class="{ active: showOverallTable }"
            @click="showOverallTable = !showOverallTable"
          >
            <span v-if="showOverallTable">Hide Overall</span>
            <span v-else>Show Overall</span>
          </button>
        </div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>
        <div
          class="tables-wrapper d-flex flex-column flex-md-row justify-content-center"
          :class="{ 'two-tables': !showOverallTable }"
        >
          <div class="soldier-table-container">
            <div class="table-wrapper">
              <div class="header-content">
                <img
                  src="/icons/soldier.png"
                  alt="Soldier Icon"
                  class="class-icon"
                />
                <div class="header-text">
                  <p class="header-title">
                    {{
                      categoryDisplayNames[selectedCategory] ||
                      capitalize(selectedCategory)
                    }}
                    -
                    {{ selectedItem }}
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
              <div class="table-responsive">
                <table class="table table-dark">
                  <thead>
                    <tr>
                      <th>Rank</th>
                      <th>Player</th>
                      <th>
                        {{
                          points
                            ? "Points"
                            : selectedCategory === "completion"
                              ? "Percentage"
                              : "Count"
                        }}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <template v-if="firstLoad">
                      <PlayersSkeleton />
                    </template>
                    <template v-else>
                      <tr
                        v-if="userRankSoldier"
                        class="fade-in user-rank-row"
                        style="border-bottom: 2px solid var(--color-border)"
                      >
                        <td class="rank-column">#{{ userRankSoldier.rank }}</td>
                        <td
                          v-if="
                            selectedCategory === 'countries' &&
                            selectedItem === 'Total'
                          "
                          class="country-cell align-middle fancy-hover clickable name-column"
                          @click="selectCountryFromSearch(userRankSoldier)"
                        >
                          <img
                            :src="userRankSoldier.flag"
                            alt="Country Flag"
                            class="flag"
                            @error="handleError"
                          />
                          {{ userRankSoldier.name }}
                        </td>
                        <SmartLink
                          v-else
                          tag="td"
                          :to="{
                            name: 'PlayerPage',
                            params: { playerId: userRankSoldier.player_id },
                          }"
                          class="name-cell align-middle fancy-hover clickable name-column"
                        >
                          <img
                            :src="userRankSoldier.steam_avatar"
                            class="avatar"
                            @error="handleError"
                          />
                          {{ userRankSoldier.name }}
                        </SmartLink>
                        <td
                          class="points-column"
                          :class="{
                            'percentage-column':
                              selectedCategory === 'completion',
                          }"
                        >
                          {{
                            selectedCategory === "completion"
                              ? userRankSoldier.amount + "%"
                              : (userRankSoldier.amount ?? 0)
                                  .toString()
                                  .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                          }}
                        </td>
                      </tr>
                      <tr
                        v-for="(player, index) in displayedSoldierPlayers"
                        :key="'soldier-' + index"
                        class="fade-in"
                        :class="{
                          'current-user-row':
                            currentUserId && player.player_id === currentUserId,
                        }"
                      >
                        <td class="rank-column">#{{ index + 1 }}</td>
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
                            alt="Country Flag"
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
                          {{
                            selectedCategory === "completion"
                              ? player.percentage + "%"
                              : (player.amount ?? 0)
                                  .toString()
                                  .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                          }}
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
              <div class="players-footer">
                <button
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
                  @click="loadMoreSoldiers"
                  :disabled="loadingSoldiers"
                >
                  <span v-if="!loadingSoldiers">Show more</span>
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
          <div class="demoman-table-container">
            <div class="table-wrapper">
              <div class="header-content">
                <img
                  src="/icons/demoman.png"
                  alt="Demoman Icon"
                  class="class-icon"
                />
                <div class="header-text">
                  <p class="header-title">
                    {{
                      categoryDisplayNames[selectedCategory] ||
                      capitalize(selectedCategory)
                    }}
                    -
                    {{ selectedItem }}
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
              <div class="table-responsive">
                <table class="table table-dark">
                  <thead>
                    <tr>
                      <th>Rank</th>
                      <th>Player</th>
                      <th>
                        {{
                          points
                            ? selectedCategory === "countries"
                              ? selectedItem === "Player Count"
                                ? "Players"
                                : "Points"
                              : "Points"
                            : selectedCategory === "completion"
                              ? "Percentage"
                              : "Count"
                        }}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <template v-if="firstLoad">
                      <PlayersSkeleton />
                    </template>
                    <template v-else>
                      <tr
                        v-if="userRankDemoman"
                        class="fade-in user-rank-row"
                        style="border-bottom: 2px solid var(--color-border)"
                      >
                        <td class="rank-column">#{{ userRankDemoman.rank }}</td>
                        <td
                          v-if="
                            selectedCategory === 'countries' &&
                            selectedItem === 'Total'
                          "
                          class="country-cell align-middle fancy-hover clickable name-column"
                          @click="selectCountryFromSearch(userRankDemoman)"
                        >
                          <img
                            :src="userRankDemoman.flag"
                            alt="Country Flag"
                            class="flag"
                            @error="handleError"
                          />
                          {{ userRankDemoman.name }}
                        </td>
                        <SmartLink
                          v-else
                          tag="td"
                          :to="{
                            name: 'PlayerPage',
                            params: { playerId: userRankDemoman.player_id },
                          }"
                          class="name-cell align-middle fancy-hover clickable name-column"
                        >
                          <img
                            :src="userRankDemoman.steam_avatar"
                            class="avatar"
                            @error="handleError"
                          />
                          {{ userRankDemoman.name }}
                        </SmartLink>
                        <td
                          class="points-column"
                          :class="{
                            'percentage-column':
                              selectedCategory === 'completion',
                          }"
                        >
                          {{
                            selectedCategory === "completion"
                              ? userRankDemoman.amount + "%"
                              : (userRankDemoman.amount ?? 0)
                                  .toString()
                                  .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                          }}
                        </td>
                      </tr>
                      <tr
                        v-for="(player, index) in displayedDemomanPlayers"
                        :key="'demoman-' + index"
                        class="fade-in"
                        :class="{
                          'current-user-row':
                            currentUserId && player.player_id === currentUserId,
                        }"
                      >
                        <td class="rank-column">#{{ index + 1 }}</td>
                        <td
                          v-if="
                            selectedCategory === 'countries' &&
                            selectedItem === 'Total'
                          "
                          class="country-cell align-middle fancy-hover clickable name-column"
                          @click="selectCountryFromSearch(player)"
                        >
                          <img
                            :src="player.flag"
                            alt="Country Flag"
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
                          {{
                            selectedCategory === "completion"
                              ? player.percentage + "%"
                              : (player.amount ?? 0)
                                  .toString()
                                  .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                          }}
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
              <div class="players-footer">
                <button
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
                  @click="loadMoreDemomen"
                  :disabled="loadingDemomen"
                >
                  <span v-if="!loadingDemomen">Show more</span>
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
          <div class="overall-table-container" v-show="showOverallTable">
            <div class="table-wrapper">
              <div class="header-content">
                <img
                  src="/icons/overall.png"
                  alt="Combined Icon"
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
              <div class="table-responsive">
                <table class="table table-dark">
                  <thead>
                    <tr>
                      <th>Rank</th>
                      <th>Player</th>
                      <th>
                        {{
                          points
                            ? "Points"
                            : selectedCategory === "completion"
                              ? "Percentage"
                              : "Count"
                        }}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <template v-if="firstLoad">
                      <PlayersSkeleton />
                    </template>
                    <template v-else>
                      <tr
                        v-if="userRankOverall"
                        class="fade-in user-rank-row"
                        style="border-bottom: 2px solid var(--color-border)"
                      >
                        <td class="rank-column">#{{ userRankOverall.rank }}</td>
                        <td
                          v-if="
                            selectedCategory === 'countries' &&
                            selectedItem === 'Total'
                          "
                          class="country-cell align-middle fancy-hover clickable name-column"
                          @click="selectCountryFromSearch(userRankOverall)"
                        >
                          <img
                            :src="userRankOverall.flag"
                            alt="Country Flag"
                            class="flag"
                            @error="handleError"
                          />
                          {{ userRankOverall.name }}
                        </td>
                        <SmartLink
                          v-else
                          tag="td"
                          :to="{
                            name: 'PlayerPage',
                            params: { playerId: userRankOverall.player_id },
                          }"
                          class="name-cell align-middle fancy-hover clickable name-column"
                        >
                          <img
                            :src="userRankOverall.steam_avatar"
                            class="avatar"
                            @error="handleError"
                          />
                          {{ userRankOverall.name }}
                        </SmartLink>
                        <td
                          class="points-column"
                          :class="{
                            'percentage-column':
                              selectedCategory === 'completion',
                          }"
                        >
                          {{
                            selectedCategory === "completion"
                              ? userRankOverall.amount + "%"
                              : (userRankOverall.amount ?? 0)
                                  .toString()
                                  .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                          }}
                        </td>
                      </tr>
                      <tr
                        v-for="(player, index) in displayedOverallPlayers"
                        :key="'overall-' + index"
                        class="fade-in"
                        :class="{
                          'current-user-row':
                            currentUserId && player.player_id === currentUserId,
                        }"
                      >
                        <td class="rank-column">#{{ index + 1 }}</td>
                        <td
                          v-if="
                            selectedCategory === 'countries' &&
                            selectedItem === 'Total'
                          "
                          class="country-cell align-middle fancy-hover clickable name-column"
                          @click="selectCountryFromSearch(player)"
                        >
                          <img
                            :src="player.flag"
                            alt="Country Flag"
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
                          {{
                            selectedCategory === "completion"
                              ? player.percentage + "%"
                              : (player.amount ?? 0)
                                  .toString()
                                  .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                          }}
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
              <div class="players-footer">
                <button
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
                  @click="loadMoreOverall"
                  :disabled="loadingOverall"
                >
                  <span v-if="!loadingOverall">Show more</span>
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
import PlayersSkeleton from "./Skeletons/PlayersSkeleton.vue";
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Players",
  components: { PlayersSkeleton },
  setup() {
    useHead({
      title: "Leaderboards | Tempus Plaza",
    });
  },
  data: () => ({
    showOverallTable: false,
    soldierPlayers: [],
    demomanPlayers: [],
    overallPlayers: [],
    userRankSoldier: null,
    userRankDemoman: null,
    userRankOverall: null,
    currentUserId: null,
    loadingUserRank: false,
    loading: false,
    firstLoad: true,
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
  }),

  async mounted() {
    this.loading = true;
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

  computed: {
    formattedHeaderTitle() {
      return (
        this.selectedCategory.charAt(0).toUpperCase() +
        this.selectedCategory.slice(1)
      );
    },
    displayedSoldierPlayers() {
      return this.soldierPlayers.slice(0, this.currentSoldierIndex);
    },
    displayedDemomanPlayers() {
      return this.demomanPlayers.slice(0, this.currentDemomanIndex);
    },
    displayedOverallPlayers() {
      return this.overallPlayers.slice(0, this.currentOverallIndex);
    },
  },

  methods: {
    async fetchUser() {
      try {
        const res = await fetch(`${API_BASE_URL}/api/get-user`, {
          credentials: "include",
        });
        const data = await res.json();
        this.currentUserId = data.data?.playerid || null;
      } catch (err) {
        console.error("Error fetching user:", err);
        this.currentUserId = null;
      }
    },
    async fetchUserRank() {
      if (!this.currentUserId) {
        this.userRankSoldier = null;
        this.userRankDemoman = null;
        this.userRankOverall = null;
        return;
      }

      this.loadingUserRank = true;
      try {
        const category = this.selectedCategory;
        const item = this.selectedItem;

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
          if (type === "groups") {
            await this.fetchUserPlayerRank("groups", "total", cat);
            return;
          }
          tableName = type.replace(/group(\d+)/g, (_, n) => `g${n}s`);
          type = "total";
        }

        if (tableName === "tiers") cat = "total";

        await this.fetchUserPlayerRank(tableName, type, cat);
      } catch (error) {
        console.error("Error fetching user rank:", error);
        this.userRankSoldier = null;
        this.userRankDemoman = null;
        this.userRankOverall = null;
      } finally {
        this.loadingUserRank = false;
      }
    },
    async fetchUserPlayerRank(tableName, type, category) {
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}/players/user-rank/${tableName}/${type}/${category}/${this.currentUserId}`,
        );
        this.userRankSoldier = data.soldierRank
          ? {
              ...data.soldierRank,
              player_id: data.soldierRank.player_id ?? this.currentUserId,
            }
          : null;
        this.userRankDemoman = data.demomanRank
          ? {
              ...data.demomanRank,
              player_id: data.demomanRank.player_id ?? this.currentUserId,
            }
          : null;
        this.userRankOverall = data.overallRank
          ? {
              ...data.overallRank,
              player_id: data.overallRank.player_id ?? this.currentUserId,
            }
          : null;
      } catch (error) {
        console.error("Error fetching user player rank:", error);
        this.userRankSoldier = null;
        this.userRankDemoman = null;
        this.userRankOverall = null;
      }
    },

    async fetchUserCompletionRank(type) {
      try {
        const { data } = await axios.get(
          `${API_BASE_URL}/players/user-rank-completion/${type}/${this.currentUserId}`,
        );
        this.userRankSoldier = data.soldierRank
          ? {
              ...data.soldierRank,
              player_id: data.soldierRank.player_id ?? this.currentUserId,
            }
          : null;
        this.userRankDemoman = data.demomanRank
          ? {
              ...data.demomanRank,
              player_id: data.demomanRank.player_id ?? this.currentUserId,
            }
          : null;
        this.userRankOverall = data.overallRank
          ? {
              ...data.overallRank,
              player_id: data.overallRank.player_id ?? this.currentUserId,
            }
          : null;
      } catch (error) {
        console.error("Error fetching user completion rank:", error);
        this.userRankSoldier = null;
        this.userRankDemoman = null;
        this.userRankOverall = null;
      }
    },

    async fetchUserCountryRank() {
      if (this.selectedItem === "Total") {
        try {
          const { data } = await axios.get(
            `${API_BASE_URL}/players/user-country-rank/${this.currentUserId}`,
          );
          this.userRankSoldier = data.soldierRank || null;
          this.userRankDemoman = data.demomanRank || null;
          this.userRankOverall = data.overallRank || null;
        } catch (error) {
          console.error("Error fetching user country rank:", error);
          this.userRankSoldier = null;
          this.userRankDemoman = null;
          this.userRankOverall = null;
        }
      } else if (this.selectedCountry) {
        try {
          const { data } = await axios.get(
            `${API_BASE_URL}/players/user-rank-country/${this.selectedCountry.code}/${this.currentUserId}`,
          );
          const defaultAvatar = `${
            import.meta.env.BASE_URL
          }avatars/default-avatar.jpg`;
          this.userRankSoldier = data.soldierRank
            ? {
                ...data.soldierRank,
                amount: data.soldierRank.soldier_total_points || 0,
                steam_avatar: data.soldierRank.steam_avatar || defaultAvatar,
                player_id: data.soldierRank.id,
              }
            : null;
          this.userRankDemoman = data.demomanRank
            ? {
                ...data.demomanRank,
                amount: data.demomanRank.demoman_total_points || 0,
                steam_avatar: data.demomanRank.steam_avatar || defaultAvatar,
                player_id: data.demomanRank.id,
              }
            : null;
          this.userRankOverall = data.overallRank
            ? {
                ...data.overallRank,
                amount: data.overallRank.overall_total_points || 0,
                steam_avatar: data.overallRank.steam_avatar || defaultAvatar,
                player_id: data.overallRank.id,
              }
            : null;
        } catch (error) {
          console.error("Error fetching user rank in country:", error);
          this.userRankSoldier = null;
          this.userRankDemoman = null;
          this.userRankOverall = null;
        }
      }
    },
    normalizeItemFromRoute(item) {
      return item.replace(/-/g, " ");
    },
    closeDropdown() {
      this.showCountryDropdown = false;
    },
    goToPlayer(playerId) {
      this.$router.push({ name: "PlayerPage", params: { playerId } });
    },
    handleError(e) {
      const fallback = `${import.meta.env.BASE_URL}avatars/default-avatar.jpg`;
      if (e.target.src !== fallback) e.target.src = fallback;
    },
    async fetchDataForCurrentSelection(index, classType = "both") {
      try {
        const category = this.selectedCategory;
        const item = this.selectedItem;

        let tableName = category;
        let type = item
          .replace(/\s+/g, "")
          .toLowerCase()
          .replace(/combined/gi, "total");
        let cat = "points";

        if (category === "completion") {
          this.points = false;
          await this.fetchCompletions(type, index, classType);
          if (index === 0) await this.fetchUserRank();
          return;
        }

        if (category === "countries") {
          this.points = true;
          await this.fetchCountries(index, classType);
          if (index === 0) await this.fetchUserRank();
          return;
        }

        if (type.includes("(count)")) {
          type = type.replace("(count)", "");
          cat = "count";
          this.points = false;
        }

        if (tableName === "ratings") {
          tableName = type.replace(/rating(\d+)/g, (_, n) => `r${n}s`);
          type = "maps";
        } else if (tableName === "groups") {
          if (type === "groups") {
            await this.fetchPlayers(tableName, "total", cat, index, classType);
            if (index === 0) await this.fetchUserRank();
            return;
          }
          tableName = type.replace(/group(\d+)/g, (_, n) => `g${n}s`);
          type = "total";
        }

        if (tableName === "tiers") cat = "total";

        await this.fetchPlayers(tableName, type, cat, index, classType);
        if (index === 0) await this.fetchUserRank();
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        this.loading = false;
        this.firstLoad = false;
        this.loadingSoldiers = false;
        this.loadingDemomen = false;
        this.loadingOverall = false;
      }
    },
    async fetchCompletions(type, index, classType = "both") {
      let indexFix = index > 0 ? 50 : 0;
      const { data } = await axios.get(
        `${API_BASE_URL}/players/players-completion-stats/${type}/${
          index - indexFix
        }`,
      );
      const [soldierData, demomanData, overallData] = data;

      if (index === 0) {
        this.soldierPlayers = soldierData;
        this.demomanPlayers = demomanData;
        this.overallPlayers = overallData;
      } else {
        if (classType === "both" || classType === "soldier")
          this.soldierPlayers = [...this.soldierPlayers, ...soldierData];
        if (classType === "both" || classType === "demoman")
          this.demomanPlayers = [...this.demomanPlayers, ...demomanData];
        if (classType === "both" || classType === "overall")
          this.overallPlayers = [...this.overallPlayers, ...overallData];
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
    async fetchCountries(index, classType = "both") {
      try {
        if (this.selectedItem === "Total") {
          if (index === 0) {
            const { data: countriesData } = await axios.get(
              `${API_BASE_URL}/players/get-countries-data`,
            );

            const toEntry = (country, amountKey, sortKey) => ({
              id: country.country_code,
              player_id: country.country_code,
              name: country.country,
              flag: `https://flagcdn.com/32x24/${country.country_code.toLowerCase()}.png`,
              amount:
                amountKey === "overall"
                  ? country.soldier_total_points + country.demoman_total_points
                  : country[amountKey] || 0,
            });

            this.soldierPlayers = [...countriesData]
              .sort((a, b) => b.soldier_total_points - a.soldier_total_points)
              .map((c) => toEntry(c, "soldier_total_points"));

            this.demomanPlayers = [...countriesData]
              .sort((a, b) => b.demoman_total_points - a.demoman_total_points)
              .map((c) => toEntry(c, "demoman_total_points"));

            this.overallPlayers = [...countriesData]
              .sort(
                (a, b) =>
                  b.soldier_total_points +
                  b.demoman_total_points -
                  (a.soldier_total_points + a.demoman_total_points),
              )
              .map((c) => toEntry(c, "overall"));
          }
        } else if (this.selectedCountry) {
          let indexFix = index > 0 ? 50 : 0;
          const { data: players } = await axios.get(
            `${API_BASE_URL}/players/country-top-players/${
              this.selectedCountry.code
            }/${index - indexFix}`,
          );

          const defaultAvatar = `${
            import.meta.env.BASE_URL
          }avatars/default-avatar.jpg`;
          const normalize = (list, pointsKey) =>
            (list || []).map((p) => ({
              ...p,
              amount: p[pointsKey] ?? 0,
              steam_avatar: p.steam_avatar || defaultAvatar,
              player_id: p.id,
            }));

          if (index === 0) {
            this.soldierPlayers = normalize(
              players.topSoldiers,
              "soldier_total_points",
            );
            this.demomanPlayers = normalize(
              players.topDemomen,
              "demoman_total_points",
            );
            this.overallPlayers = normalize(
              players.topOverall || [],
              "overall_total_points",
            );
          } else {
            if (classType === "both" || classType === "soldier")
              this.soldierPlayers = [
                ...this.soldierPlayers,
                ...normalize(players.topSoldiers, "soldier_total_points"),
              ];
            if (classType === "both" || classType === "demoman")
              this.demomanPlayers = [
                ...this.demomanPlayers,
                ...normalize(players.topDemomen, "demoman_total_points"),
              ];
            if (classType === "both" || classType === "overall")
              this.overallPlayers = [
                ...this.overallPlayers,
                ...normalize(players.topOverall || [], "overall_total_points"),
              ];
          }
        }
      } catch (error) {
        console.error("Error fetching countries data:", error);
      }
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

      this.currentSoldierIndex = 50;
      this.currentDemomanIndex = 50;
      this.currentOverallIndex = 50;

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
    async fetchPlayers(tableName, type, category, index, classType = "both") {
      try {
        let indexFix = index > 0 ? 50 : 0;
        const { data } = await axios.get(
          `${API_BASE_URL}/players/data/${tableName}/${type}/${category}/${
            index - indexFix
          }`,
        );
        const [soldierData, demomanData, overallData] = data;
        if (index === 0) {
          this.soldierPlayers = soldierData;
          this.demomanPlayers = demomanData;
          this.overallPlayers = overallData;
        } else {
          if (classType === "both" || classType === "soldier")
            this.soldierPlayers = [...this.soldierPlayers, ...soldierData];
          if (classType === "both" || classType === "demoman")
            this.demomanPlayers = [...this.demomanPlayers, ...demomanData];
          if (classType === "both" || classType === "overall")
            this.overallPlayers = [...this.overallPlayers, ...overallData];
        }
      } catch (error) {
        console.error("Error fetching players");
      }
    },
    loadMoreDemomen() {
      this.loadingDemomen = true;
      this.currentDemomanIndex += 50;
      this.fetchDataForCurrentSelection(this.currentDemomanIndex, "demoman");
    },
    loadMoreSoldiers() {
      this.loadingSoldiers = true;
      this.currentSoldierIndex += 50;
      this.fetchDataForCurrentSelection(this.currentSoldierIndex, "soldier");
    },
    loadMoreOverall() {
      this.loadingOverall = true;
      this.currentOverallIndex += 50;
      this.fetchDataForCurrentSelection(this.currentOverallIndex, "overall");
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

.table-dark {
  margin: 0px;
  background: transparent;
}

.table-dark th {
  background: var(--color-primary-dark);
  color: var(--color-text);
  text-align: left;
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

.user-rank-row td {
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

.name-cell:has(.inactive-badge) {
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

.update-button {
  border-radius: 0 0 10px 10px;
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

.arrow {
  float: right;
  margin-left: 10px;
  font-size: 0.9em;
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
  .soldier-table-container::before {
    width: 250px;
    right: 60px;
  }
  .demoman-table-container::before {
    width: 300px;
    left: 100px;
  }
}

@media (max-width: 991px) and (min-width: 768px) {
  .soldier-table-container::before {
    width: 200px;
    right: 15px;
  }
  .demoman-table-container::before {
    width: 235px;
    left: 35px;
  }
}

@media (max-width: 767.98px) {
  .soldier-table-container::before {
    width: 300px;
    right: 30px;
  }
  .demoman-table-container::before {
    width: 320px;
    left: 150px;
  }
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
  .tables-wrapper.two-tables :deep(.soldier-table-container::before) {
    width: 350px !important;
    right: 75px !important;
  }
  .tables-wrapper.two-tables :deep(.demoman-table-container::before) {
    width: 400px !important;
    left: 175px !important;
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
</style>
