<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
    @click="closeDropdown"
  >
    <div class="container py-4 d-flex flex-column align-items-center">
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
      </div>
      <hr class="row-divider" style="width: 75%" />
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
        style="width: 75%"
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
              <img :src="country.flag" :alt="country.name" class="flag-icon" />
              {{ country.name }}
            </li>
          </ul>
        </div>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div v-if="loading && initialLoad" class="text-center">
        <div
          class="spinner-border text-light"
          role="status"
          style="margin-top: 100px"
        >
          <span class="visually-hidden">Loading players...</span>
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
              class="header-content"
              style="
                background: linear-gradient(
                  135deg,
                  rgba(74, 111, 165, 0.3),
                  rgba(37, 55, 82, 0.3)
                );
              "
            >
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
                  - {{ selectedItem }}
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
                  <tr
                    v-for="(player, index) in displayedSoldierPlayers"
                    :key="'soldier-' + player.id"
                    class="fade-in"
                  >
                    <td class="rank-column">#{{ index + 1 }}</td>
                    <SmartLink
                      v-if="
                        selectedCategory === 'countries' &&
                        selectedItem !== 'Total'
                      "
                      tag="td"
                      :to="{
                        name: 'PlayerPage',
                        params: { playerId: player.player_id },
                      }"
                      class="name-cell align-middle fancy-hover clickable name-column"
                    >
                      <img
                        :src="player.steam_avatar"
                        alt="Steam Avatar"
                        class="avatar"
                        @error="handleError"
                      />
                      {{ player.name }}
                    </SmartLink>
                    <td
                      v-else-if="
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
                        alt="Steam Avatar"
                        class="avatar"
                        @error="handleError"
                      />
                      {{ player.name }}
                    </SmartLink>
                    <td
                      class="points-column"
                      :class="{
                        'percentage-column': selectedCategory === 'completion',
                      }"
                    >
                      {{
                        selectedCategory === "completion"
                          ? player.percentage + "%"
                          : player.amount
                              .toString()
                              .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                      }}
                    </td>
                  </tr>
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
                  - {{ selectedItem }}
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
                  <tr
                    v-for="(player, index) in displayedDemomanPlayers"
                    :key="'demoman-' + player.id"
                    class="fade-in"
                  >
                    <td class="rank-column">#{{ index + 1 }}</td>
                    <SmartLink
                      v-if="
                        selectedCategory === 'countries' &&
                        selectedItem !== 'Total'
                      "
                      tag="td"
                      :to="{
                        name: 'PlayerPage',
                        params: { playerId: player.player_id },
                      }"
                      class="name-cell align-middle fancy-hover clickable name-column"
                    >
                      <img
                        :src="player.steam_avatar"
                        alt="Steam Avatar"
                        class="avatar"
                        @error="handleError"
                      />
                      {{ player.name }}
                    </SmartLink>
                    <td
                      v-else-if="
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
                        alt="Steam Avatar"
                        class="avatar"
                        @error="handleError"
                      />
                      {{ player.name }}
                    </SmartLink>
                    <td
                      class="points-column"
                      :class="{
                        'percentage-column': selectedCategory === 'completion',
                      }"
                    >
                      {{
                        selectedCategory === "completion"
                          ? player.percentage + "%"
                          : player.amount
                              .toString()
                              .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                      }}
                    </td>
                  </tr>
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
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { useHead } from "@vueuse/head";
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Players",
  setup() {
    useHead({
      title: "Tempus Plaza | Players",
    });
  },
  data: () => ({
    soldierPlayers: [],
    demomanPlayers: [],
    loading: false,
    loadingSoldiers: false,
    loadingDemomen: false,
    initialLoad: true,
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
  }),
  async mounted() {
    this.fillDropdowns();
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
      this.selectedItem = item;
      if (category === "countries" && item !== "Total") {
        const foundCountry = this.allCountries.find(
          (c) => c.name.toLowerCase() === item.toLowerCase()
        );
        if (foundCountry) {
          this.selectedCountry = foundCountry;
        }
      }
    }
    this.fetchDataForCurrentSelection(0);
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
  },
  methods: {
    closeDropdown() {
      this.showCountryDropdown = false;
    },
    goToPlayer(playerId) {
      this.$router.push({
        name: "PlayerPage",
        params: { playerId: playerId },
      });
    },
    handleError(e) {
      const fallback = `${import.meta.env.BASE_URL}avatars/default-avatar.jpg`;
      if (e.target.src !== fallback) {
        e.target.src = fallback;
      }
    },
    async fetchDataForCurrentSelection(index) {
      if (this.initialLoad) {
        this.loading = true;
      }
      try {
        const category = this.selectedCategory;
        const item = this.selectedItem;

        let tableName = category;
        let type = item
          .replace(/\s+/g, "")
          .toLowerCase()
          .replace(/\s*combined\s*/gi, "total");
        let cat = "points";

        if (category === "completion") {
          this.points = false;
          await this.fetchCompletions(type, index);
          return;
        }

        if (category === "countries") {
          this.points = true;
          await this.fetchCountries(index);
          return;
        }

        if (type.includes("(count)")) {
          type = type.replace("(count)", "");
          cat = "count";
          this.points = false;
        }

        if (tableName === "ratings") {
          const newName = type.replace(
            /rating(\d+)/g,
            (match, num) => `r${num}s`
          );
          tableName = newName;
          type = "maps";
        } else if (tableName === "groups") {
          if (type === "groups") {
            await this.fetchPlayers(type, "total", cat, index);
            return;
          } else {
            const newName = type.replace(
              /group(\d+)/g,
              (match, num) => `g${num}s`
            );
            tableName = newName;
            type = "total";
          }
        }

        if (tableName === "tiers") cat = "total";

        await this.fetchPlayers(tableName, type, cat, index);
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        this.loading = false;
        this.initialLoad = false;
        this.loadingSoldiers = false;
        this.loadingDemomen = false;
      }
    },
    async fetchCompletions(type, index) {
      let indexFix = 0;
      if (index > 0) indexFix = 50;

      const response = await axios.get(
        `${API_BASE_URL}/players/players-completion-stats/${type}/${
          index - indexFix
        }`
      );
      const playersResponse = response.data;

      const soldierPlayers = playersResponse[0];
      const demomanPlayers = playersResponse[1];

      if (index === 0) {
        this.soldierPlayers = soldierPlayers;
        this.demomanPlayers = demomanPlayers;
      } else {
        this.soldierPlayers = [...this.soldierPlayers, ...soldierPlayers];
        this.demomanPlayers = [...this.demomanPlayers, ...demomanPlayers];
      }
    },
    async loadCountriesList() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/get-countries-data`
        );
        this.allCountries = response.data
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
        (country) =>
          country.name.toLowerCase().includes(query) ||
          country.code.toLowerCase().includes(query)
      );
    },

    selectCountryFromSearch(country) {
      this.selectedCountry = country;
      this.showCountryDropdown = false;
      this.resetCountryFilter();
      this.selectItem("countries", country.name);
    },
    resetCountryFilter() {
      this.filteredCountries = [...this.allCountries];
      this.searchQuery = "";
    },
    async fetchCountries(index) {
      try {
        if (this.selectedItem === "Total") {
          if (index === 0) {
            const response = await axios.get(
              `${API_BASE_URL}/players/get-countries-data`
            );
            const countriesData = response.data;

            const soldierData = countriesData
              .sort((a, b) => b.soldier_total_points - a.soldier_total_points)
              .map((country) => ({
                id: country.country_code,
                player_id: country.country_code,
                name: country.country,
                flag: `https://flagcdn.com/32x24/${country.country_code.toLowerCase()}.png`,
                amount: country.soldier_total_points,
              }));

            const demomanData = countriesData
              .sort((a, b) => b.demoman_total_points - a.demoman_total_points)
              .map((country) => ({
                id: country.country_code,
                player_id: country.country_code,
                name: country.country,
                flag: `https://flagcdn.com/32x24/${country.country_code.toLowerCase()}.png`,
                amount: country.demoman_total_points,
              }));

            this.soldierPlayers = soldierData;
            this.demomanPlayers = demomanData;
          }
        } else if (this.selectedCountry) {
          let indexFix = 0;
          if (index > 0) indexFix = 50;

          const response = await axios.get(
            `${API_BASE_URL}/players/country-top-players/${
              this.selectedCountry.code
            }/${index - indexFix}`
          );

          const players = response.data;
          const normalizePlayers = (playerList, pointsKey) => {
            return (playerList || []).map((player) => ({
              ...player,
              amount: player[pointsKey] || 0,
              steam_avatar:
                player.steam_avatar ||
                `${import.meta.env.BASE_URL}avatars/default-avatar.jpg`,
              player_id: player.id,
            }));
          };

          if (index === 0) {
            this.soldierPlayers = normalizePlayers(
              players.topSoldiers,
              "soldier_total_points"
            );
            this.demomanPlayers = normalizePlayers(
              players.topDemomen,
              "demoman_total_points"
            );
          } else {
            this.soldierPlayers = [
              ...this.soldierPlayers,
              ...normalizePlayers(players.topSoldiers, "soldier_total_points"),
            ];
            this.demomanPlayers = [
              ...this.demomanPlayers,
              ...normalizePlayers(players.topDemomen, "demoman_total_points"),
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
      if (category === "groups") {
        return this.dropdowns[category].filter((item) => item !== "Groups");
      }
      return this.dropdowns[category];
    },
    selectCategory(category) {
      this.selectedCategory = category;
      const defaultItem = this.dropdowns[category][0];
      this.$router.push({
        name: "Players",
        params: { category: category, item: defaultItem },
      });
      this.selectItem(category, defaultItem);
    },
    async selectItem(category, item) {
      this.initialLoad = true;
      this.points = !item.includes("(count)");
      this.selectedCategory = category;
      this.selectedItem = item;

      if (category !== "countries" || item === "Total") {
        this.selectedCountry = null;
        this.searchQuery = "";
      }

      this.currentSoldierIndex = 50;
      this.currentDemomanIndex = 50;
      this.soldierPlayers = [];
      this.demomanPlayers = [];

      await this.fetchDataForCurrentSelection(0);

      this.$router.push({
        name: "Players",
        params: { category: category, item: item },
      });
    },
    hasCountSubmenu(cat) {
      return ["wrs", "tts", "groups"].includes(cat);
    },
    async fetchPlayers(tableName, type, category, index) {
      try {
        let indexFix = 0;
        if (index > 0) indexFix = 50;
        const response = await axios.get(
          `${API_BASE_URL}/players/data/${tableName}/${type}/${category}/${
            index - indexFix
          }`
        );
        const players = response.data;
        if (index === 0) {
          this.soldierPlayers = players[0];
          this.demomanPlayers = players[1];
        } else {
          this.soldierPlayers = [...this.soldierPlayers, ...players[0]];
          this.demomanPlayers = [...this.demomanPlayers, ...players[1]];
        }
      } catch (error) {
        console.error("Error fetching players");
      }
    },
    loadMoreDemomen() {
      this.loadingDemomen = true;
      this.currentDemomanIndex += 50;
      this.fetchDataForCurrentSelection(this.currentDemomanIndex);
    },
    loadMoreSoldiers() {
      this.loadingSoldiers = true;
      this.currentSoldierIndex += 50;
      this.fetchDataForCurrentSelection(this.currentSoldierIndex);
    },
  },
  watch: {
    "$route.params": {
      handler: function (params) {
        if (params.category && params.item) {
          this.selectedCategory = params.category;
          this.selectedItem = params.item;
          if (params.category === "countries" && params.item !== "Total") {
            const foundCountry = this.allCountries.find(
              (c) => c.name.toLowerCase() === params.item.toLowerCase()
            );
            if (foundCountry) {
              this.selectedCountry = foundCountry;
            }
          }
          this.fetchDataForCurrentSelection(0);
        }
      },
      immediate: true,
    },
  },
};
</script>

<style scoped>
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
  width: 48px;
  height: 48px;
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

@media (max-width: 767.98px) {
  .tables-wrapper {
    flex-direction: column;
    align-items: center;
  }
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

@media (max-width: 767.98px) {
  .category-tabs {
    flex-wrap: wrap;
    justify-content: center;
  }

  .category-tab {
    padding: 10px 16px;
    font-size: 13px;
  }

  .subcategory-pill {
    padding: 6px 12px;
    font-size: 12px;
  }
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

@media (max-width: 767.98px) {
  .search-results-dropdown {
    min-width: 150px;
    max-width: 300px;
  }
}
</style>
