<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container py-4 d-flex flex-column align-items-center">
      <div class="content-container">
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">🏆</span>
            Tempus top players
          </h1>
          <p class="page-subtitle">
            View the top players in various categories
          </p>
        </div>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div class="category-tabs-container my-4">
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
      <div class="subcategory-container my-3">
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
                    <td
                      class="name-cell align-middle player-name clickable name-column"
                      @click="goToPlayer(player.player_id)"
                    >
                      <img
                        :src="`${player.steam_avatar}`"
                        alt="Steam Avatar"
                        class="avatar"
                        @error="handleError"
                      />
                      {{ player.name }}
                    </td>
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
                  background: rgba(74, 111, 165, 0.8);
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
                    v-for="(player, index) in displayedDemomanPlayers"
                    :key="'demoman-' + player.id"
                    class="fade-in"
                  >
                    <td class="rank-column">#{{ index + 1 }}</td>
                    <td
                      class="name-cell align-middle player-name clickable name-column"
                      @click="goToPlayer(player.player_id)"
                    >
                      <img
                        :src="`${player.steam_avatar}`"
                        alt="Avatar"
                        class="avatar"
                        @error="handleError"
                      />
                      {{ player.name }}
                    </td>
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
                  background: rgba(74, 111, 165, 0.8);
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
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "Players",
  data: () => ({
    soldierPlayers: [],
    demomanPlayers: [],
    loading: false,
    loadingSoldiers: false,
    loadingDemomen: false,
    initialLoad: true,
    error: null,
    points: true,
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
    ],
    categoryDisplayNames: {
      wrs: "World records",
      tts: "Top times",
      points: "Points",
      groups: "Groups",
      ratings: "Ratings (maps)",
      tiers: "Tiers",
      completion: "Completion",
    },
    currentSoldierIndex: 50,
    currentDemomanIndex: 50,
  }),
  mounted() {
    document.title = "Tempus plaza - Players";
    this.fillDropdowns();
    const { category, item } = this.$route.params;
    if (category && item) {
      this.selectedCategory = category;
      this.selectedItem = item;
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
        const response = await axios.get(
          `${API_BASE_URL}/players/data/${tableName}/${type}/${category}/${index}`
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
  padding-bottom: 6px;
  border-top: 1px solid var(--color-border-soft);
}

.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  font-weight: bold;
  padding: 6px;
}

.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}
.table-dark tr:nth-child(odd) .name-cell:hover {
  background: rgba(74, 111, 165, 0.8);
}

.name-cell {
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

.player-name:hover {
  background: rgba(74, 111, 165, 0.8) !important;
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
  box-shadow: 0 0px 20px rgb(0, 0, 0);
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
  text-transform: uppercase;
  transition: transform 0.3s ease;
}

.category-tab:hover {
  background: rgba(74, 111, 165, 0.8) !important;
  transform: scale(1.03);
}

.category-tab.active {
  background: rgba(74, 111, 165, 0.8) !important;
  color: var(--color-text);
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
  background: rgba(74, 111, 165, 0.8) !important;
  transform: scale(1.03);
}

.subcategory-pill.active {
  background: rgba(74, 111, 165, 0.8) !important;
  border-color: rgba(74, 111, 165, 0.8) !important;
  color: var(--color-text);
  box-shadow: 0 0px 20px rgb(0, 0, 0, 0.5);
}

.count-pill {
  border-style: dashed;
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
</style>
