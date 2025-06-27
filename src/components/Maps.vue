<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container" style="z-index: 1">
      <div
        class="content-container mx-auto py-4 d-flex flex-column align-items-center"
        style="z-index: 1"
      >
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">🗺️</span>
            Tempus maps
          </h1>
          <p class="page-subtitle">
            Explore and filter maps, courses and bonuses
          </p>
        </div>
        <hr class="row-divider" style="width: 75%" />
        <div class="filter-section">
          <div class="filter-content">
            <div class="filter-columns">
              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title text-light mb-2">Soldier tiers</h6>
                  <div class="tier-filter-container">
                    <div class="tier-filters">
                      <label
                        v-for="tier in availableTiers"
                        :key="'soldier-tier-' + tier"
                        class="tier-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="tier"
                          v-model="selectedSoldierTiers"
                          @change="onFilterChange"
                        />
                        <span :class="`tier-badge tier-${tier}`">{{
                          tier
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="filter-group mt-3">
                  <h6 class="filter-title text-light mb-2">Soldier ratings</h6>
                  <div class="rating-filter-container">
                    <div class="rating-filters">
                      <label
                        v-for="rating in availableRatings"
                        :key="'soldier-rating-' + rating"
                        class="rating-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="rating"
                          v-model="selectedSoldierRatings"
                          @change="onFilterChange"
                        />
                        <span :class="`rating-badge rating-${rating}`">{{
                          rating
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title text-light mb-2">Demoman tiers</h6>
                  <div class="tier-filter-container">
                    <div class="tier-filters">
                      <label
                        v-for="tier in availableTiers"
                        :key="'demo-tier-' + tier"
                        class="tier-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="tier"
                          v-model="selectedDemomanTiers"
                          @change="onFilterChange"
                        />
                        <span :class="`tier-badge tier-${tier}`">{{
                          tier
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="filter-group mt-3">
                  <h6 class="filter-title text-light mb-2">Demoman ratings</h6>
                  <div class="rating-filter-container">
                    <div class="rating-filters">
                      <label
                        v-for="rating in availableRatings"
                        :key="'demo-rating-' + rating"
                        class="rating-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="rating"
                          v-model="selectedDemomanRatings"
                          @change="onFilterChange"
                        />
                        <span :class="`rating-badge rating-${rating}`">{{
                          rating
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="filter-actions">
              <button @click="clearAllFilters" class="btn btn-secondary">
                Clear filters
              </button>
              <span class="text-light"
                >Showing {{ filteredAndSortedItems.length }} of
                {{ currentItems.length }} items</span
              >
              <div class="search-input-wrapper">
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
                <input
                  type="text"
                  v-model="searchQuery"
                  placeholder="Search for map..."
                  class="search-input"
                />
              </div>
            </div>
          </div>
        </div>
        <hr class="row-divider" style="width: 75%" />
        <div v-if="loading" class="text-center">
          <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading {{ currentView }}...</span>
          </div>
        </div>
        <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
        <div v-else class="table-container">
          <div class="table-header-section">
            <div
              class="picker-section"
              v-if="filteredAndSortedItems.length > 0"
            >
              <div class="picker-controls">
                <button
                  @click="startRandomPicker"
                  :disabled="
                    isPickerActive || filteredAndSortedItems.length <= 1
                  "
                  class="btn picker-btn"
                  :class="{ 'picker-btn-active': isPickerActive }"
                >
                  <span v-if="!isPickerActive">Random map picker</span>
                  <span v-else>Eliminating...</span>
                </button>
                <button
                  @click="resetPicker"
                  v-if="eliminatedRows.size > 0"
                  class="btn btn-secondary ml-2"
                >
                  Reset
                </button>
                <div
                  class="picker-status"
                  v-if="isPickerActive || eliminatedRows.size > 0"
                >
                  <span class="remaining-count"
                    >{{ remainingCount }} remaining</span
                  >
                </div>
              </div>
            </div>
            <div class="button-group">
              <button
                :class="{ active: currentView === 'maps' }"
                @click="switchView('maps')"
                class="toggle-btn btn btn-dark update-button"
              >
                Maps
              </button>
              <button
                :class="{ active: currentView === 'courses' }"
                @click="switchView('courses')"
                class="toggle-btn btn btn-dark update-button"
              >
                Courses
              </button>
              <button
                :class="{ active: currentView === 'bonuses' }"
                @click="switchView('bonuses')"
                class="toggle-btn btn btn-dark update-button"
              >
                Bonuses
              </button>
            </div>
          </div>
          <div class="table-responsive w-100" style="max-width: 1296px">
            <table class="table table-dark">
              <thead class="table-header">
                <tr>
                  <th style="vertical-align: middle">Name</th>
                  <th
                    @click="sortBy('soldier_tier')"
                    class="d-none d-sm-table-cell fixed-width"
                  >
                    [S] tier
                    <span v-if="sortField === 'soldier_tier'">{{
                      getSortArrowSymbol("soldier_tier")
                    }}</span>
                  </th>
                  <th
                    @click="sortBy('soldier_rating')"
                    class="d-none d-sm-table-cell fixed-width"
                  >
                    [S] rating
                    <span v-if="sortField === 'soldier_rating'">{{
                      getSortArrowSymbol("soldier_rating")
                    }}</span>
                  </th>
                  <th class="divider d-none d-sm-table-cell"></th>
                  <th
                    @click="sortBy('demoman_tier')"
                    class="d-none d-sm-table-cell fixed-width"
                  >
                    [D] tier
                    <span v-if="sortField === 'demoman_tier'">{{
                      getSortArrowSymbol("demoman_tier")
                    }}</span>
                  </th>
                  <th
                    @click="sortBy('demoman_rating')"
                    class="d-none d-sm-table-cell fixed-width"
                  >
                    [D] rating
                    <span v-if="sortField === 'demoman_rating'">{{
                      getSortArrowSymbol("demoman_rating")
                    }}</span>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  ref="tableRows"
                  v-for="(item, index) in filteredAndSortedItems"
                  :key="currentView + '-' + index"
                  :class="{
                    'row-eliminated': eliminatedRows.has(getRowId(item, index)),
                    'row-winner':
                      pickerComplete &&
                      !eliminatedRows.has(getRowId(item, index)),
                    'row-eliminating':
                      eliminatingRowId === getRowId(item, index),
                  }"
                  class="fade-in"
                >
                  <td
                    class="align-middle map-name clickable"
                    @click="goToRecords(item)"
                  >
                    {{
                      currentView === "maps"
                        ? item.name
                        : `${item.map_name} (${currentView
                            .slice(0, 1)
                            .toUpperCase()}${item.index})`
                    }}
                  </td>
                  <td
                    class="align-middle text-center d-none d-sm-table-cell"
                    :class="`tier-color tier-${item.soldier_tier}`"
                  >
                    T{{ item.soldier_tier }}
                  </td>
                  <td
                    class="align-middle text-center d-none d-sm-table-cell"
                    :class="`rating-color rating-${item.soldier_rating}`"
                  >
                    R{{ item.soldier_rating }}
                  </td>
                  <td class="divider d-none d-sm-table-cell"></td>
                  <td
                    class="align-middle text-center d-none d-sm-table-cell"
                    :class="`tier-color tier-${item.demoman_tier}`"
                  >
                    T{{ item.demoman_tier }}
                  </td>
                  <td
                    class="align-middle text-center d-none d-sm-table-cell"
                    :class="`rating-color rating-${item.demoman_rating}`"
                  >
                    R{{ item.demoman_rating }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;
const TIER_ORDER = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 0];

export default {
  name: "Maps",
  data() {
    return {
      maps: [],
      courses: [],
      bonuses: [],
      currentView: "maps",
      loading: false,
      error: null,
      sortField: "soldier_tier",
      sortDirection: 1,
      selectedSoldierTiers: [],
      selectedSoldierRatings: [],
      selectedDemomanTiers: [],
      selectedDemomanRatings: [],
      availableTiers: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 0],
      availableRatings: [1, 2, 3, 4],
      eliminatedRows: new Set(),
      isPickerActive: false,
      pickerComplete: false,
      eliminatingRowId: null,
      shouldContinuePicker: true,
      searchQuery: "",
    };
  },
  mounted() {
    this.fetchData();
    this.updateDocumentTitle();
  },
  computed: {
    currentItems() {
      switch (this.currentView) {
        case "courses":
          return this.courses;
        case "bonuses":
          return this.bonuses;
        default:
          return this.maps;
      }
    },
    currentTitle() {
      switch (this.currentView) {
        case "courses":
          return "Courses";
        case "bonuses":
          return "Bonuses";
        default:
          return "Maps";
      }
    },
    filteredItems() {
      let filtered = this.currentItems.filter((item) => {
        if (
          this.selectedSoldierTiers.length > 0 &&
          !this.selectedSoldierTiers.includes(item.soldier_tier)
        )
          return false;
        if (
          this.selectedSoldierRatings.length > 0 &&
          !this.selectedSoldierRatings.includes(item.soldier_rating)
        )
          return false;
        if (
          this.selectedDemomanTiers.length > 0 &&
          !this.selectedDemomanTiers.includes(item.demoman_tier)
        )
          return false;
        if (
          this.selectedDemomanRatings.length > 0 &&
          !this.selectedDemomanRatings.includes(item.demoman_rating)
        )
          return false;
        return true;
      });

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter((item) => {
          const name =
            this.currentView === "maps"
              ? item.name
              : `${item.map_name} (${this.currentView
                  .slice(0, 1)
                  .toUpperCase()}${item.index})`;
          return name.toLowerCase().includes(query);
        });
      }

      return filtered;
    },
    filteredAndSortedItems() {
      return this.filteredItems.slice().sort((a, b) => {
        const fieldA = a[this.sortField];
        const fieldB = b[this.sortField];

        if (["soldier_tier", "demoman_tier"].includes(this.sortField)) {
          const indexA = TIER_ORDER.indexOf(fieldA);
          const indexB = TIER_ORDER.indexOf(fieldB);
          const primaryComparison = (indexA - indexB) * this.sortDirection;

          if (primaryComparison !== 0) return primaryComparison;
        }

        if (fieldA < fieldB) return -1 * this.sortDirection;
        if (fieldA > fieldB) return 1 * this.sortDirection;

        if (this.sortField === "demoman_tier") {
          const ratingA = a.demoman_rating;
          const ratingB = b.demoman_rating;
          if (ratingA !== ratingB) {
            return (ratingA - ratingB) * this.sortDirection;
          }
        } else if (this.sortField === "soldier_tier") {
          const ratingA = a.soldier_rating;
          const ratingB = b.soldier_rating;
          if (ratingA !== ratingB) {
            return (ratingA - ratingB) * this.sortDirection;
          }
        }

        const otherFields = [
          "soldier_tier",
          "soldier_rating",
          "demoman_tier",
          "demoman_rating",
        ].filter((field) => {
          const sortFieldClass = this.sortField.includes("soldier")
            ? "soldier"
            : "demoman";
          return field.includes(sortFieldClass) && field !== this.sortField;
        });

        for (const field of otherFields) {
          const aValue = a[field];
          const bValue = b[field];

          if (["soldier_tier", "demoman_tier"].includes(field)) {
            const indexA = TIER_ORDER.indexOf(aValue);
            const indexB = TIER_ORDER.indexOf(bValue);
            if (indexA !== indexB) {
              return indexA - indexB;
            }
          } else {
            if (aValue < bValue) return -1;
            if (aValue > bValue) return 1;
          }
        }

        return 0;
      });
    },
    remainingCount() {
      return this.filteredAndSortedItems.length - this.eliminatedRows.size;
    },
  },

  methods: {
    async switchView(view) {
      if (this.currentView === view) return;

      this.currentView = view;
      this.updateDocumentTitle();

      this.sortField = "soldier_tier";
      this.sortDirection = 1;

      this.clearAllFilters();
      this.resetPicker();

      await this.fetchData();
    },

    async fetchData() {
      if (this.currentView === "maps" && this.maps.length === 0) {
        await this.fetchMapsData();
      } else if (this.currentView === "courses" && this.courses.length === 0) {
        await this.fetchCoursesData();
      } else if (this.currentView === "bonuses" && this.bonuses.length === 0) {
        await this.fetchBonusesData();
      }
    },

    async fetchMapsData() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`${API_BASE_URL}/maps`);
        this.maps = response.data;
      } catch (error) {
        this.error = "Error fetching maps data.";
        console.error("Error fetching maps data:", error);
      } finally {
        this.loading = false;
      }
    },

    async fetchCoursesData() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`${API_BASE_URL}/maps/courses`);
        this.courses = response.data;
      } catch (error) {
        this.error = "Error fetching courses data.";
        console.error("Error fetching courses data:", error);
      } finally {
        this.loading = false;
      }
    },

    async fetchBonusesData() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`${API_BASE_URL}/maps/bonuses`);
        this.bonuses = response.data;
      } catch (error) {
        this.error = "Error fetching bonuses data.";
        console.error("Error fetching bonuses data:", error);
      } finally {
        this.loading = false;
      }
    },

    async goToRecords(item) {
      const itemId = this.currentView === "maps" ? item.id : item.map_id;
      console.log(`Navigating to more info for ${this.currentView}:`, itemId);
      this.$router.push({
        name: "Records",
        params: { mapId: itemId },
      });
    },

    sortBy(field) {
      if (this.sortField === field) {
        this.sortDirection *= -1;
      } else {
        this.sortField = field;
        this.sortDirection = 1;
      }
    },

    getSortArrowSymbol(field) {
      if (this.sortField !== field) return "";
      return this.sortDirection === 1 ? "↑" : "↓";
    },

    updateDocumentTitle() {
      document.title = `Tempus plaza - ${this.currentTitle}`;
    },

    clearAllFilters() {
      this.selectedSoldierTiers = [];
      this.selectedSoldierRatings = [];
      this.selectedDemomanTiers = [];
      this.selectedDemomanRatings = [];
      this.resetPicker();
    },

    onFilterChange() {
      this.resetPicker();
    },

    getRowId(item, index) {
      return `${this.currentView}-${item.id || item.map_id}-${index}`;
    },

    async startRandomPicker() {
      if (this.isPickerActive || this.filteredAndSortedItems.length <= 1)
        return;

      this.isPickerActive = true;
      this.pickerComplete = false;
      this.shouldContinuePicker = true;

      const totalItems = this.filteredAndSortedItems.length;
      const maxEliminationTime = 5000;
      const eliminationInterval = maxEliminationTime / (totalItems - 1);

      await this.eliminateRandomly(eliminationInterval);
    },

    async eliminateRandomly(eliminationInterval) {
      if (!this.shouldContinuePicker) return;

      const availableRows = this.filteredAndSortedItems
        .map((item, index) => ({ item, index, id: this.getRowId(item, index) }))
        .filter((row) => !this.eliminatedRows.has(row.id));

      if (availableRows.length <= 1) {
        this.isPickerActive = false;
        this.pickerComplete = true;

        if (availableRows.length === 1) {
          await this.$nextTick();
          const rowIndex = availableRows[0].index;
          this.$refs.tableRows[rowIndex].scrollIntoView({
            behavior: "smooth",
            block: "center",
          });
        }
        return;
      }

      const randomIndex = Math.floor(Math.random() * availableRows.length);
      const rowToEliminate = availableRows[randomIndex];

      this.eliminatingRowId = rowToEliminate.id;

      await new Promise((resolve) =>
        setTimeout(resolve, eliminationInterval / 2)
      );

      this.eliminatedRows.add(rowToEliminate.id);
      this.eliminatingRowId = null;

      await new Promise((resolve) =>
        setTimeout(resolve, eliminationInterval / 2)
      );

      await this.eliminateRandomly(eliminationInterval);
    },

    resetPicker() {
      this.eliminatedRows.clear();
      this.shouldContinuePicker = false;
      this.isPickerActive = false;
      this.pickerComplete = false;
      this.eliminatingRowId = null;
    },
  },
};
</script>

<style scoped>
.maps-container {
  background-image: url("data:image/svg+xml, %3Csvg xmlns='http://www.w3.org/2000/svg' width='1728' height='1180' viewBox='0 0 1728 1180' fill='currentColor' class='position-absolute w-100 top-40 pointer-events-none' style='opacity: 0.3; z-index: 0' %3E%3Cg filter='url(%23filter0_f_226_149)'%3E%3Cpath d='M2123.75 293.105C1333.23 688.128 703.641 515.327 508.724 374.099C452.243 349.269 313.169 228.867 73.6286 197.922C-225.797 159.242 110.578 663.582 380.092 782.356C649.606 901.131 1580.73 925.224 2029.92 784.99C2601.98 606.399 3111.92 -200.674 2123.75 293.105Z' fill='url(%23paint0_radial_226_149)' fill-opacity='0.45' %3E%3C/path%3E%3C/g%3E%3Cdefs%3E%3Cfilter id='filter0_f_226_149' x='-342.14' y='-163' width='3323.17' height='1342.42' filterUnits='userSpaceOnUse' color-interpolation-filters='sRGB' %3E%3CfeFlood flood-opacity='0' result='BackgroundImageFix'%3E%3C/feFlood%3E%3CfeBlend mode='normal' in='SourceGraphic' in2='BackgroundImageFix' result='shape' %3E%3C/feBlend%3E%3CfeGaussianBlur stdDeviation='149.181' result='effect1_foregroundBlur_226_149' %3E%3C/feGaussianBlur%3E%3C/filter%3E%3CradialGradient id='paint0_radial_226_149' cx='0' cy='0' r='1' gradientUnits='userSpaceOnUse' gradientTransform='translate(2522.36 771.207) rotate(-178.79) scale(2230.36 879.25)' %3E%3Cstop stop-color='%236248FF'%3E%3C/stop%3E%3Cstop offset='0.369278' stop-color='%23E5FF48'%3E%3C/stop%3E%3Cstop offset='0.588842' stop-color='%23FF48ED'%3E%3C/stop%3E%3Cstop offset='0.708333' stop-color='%2348BDFF'%3E%3C/stop%3E%3Cstop offset='0.932292' stop-color='%236248FF'%3E%3C/stop%3E%3C/radialGradient%3E%3C/defs%3E%3C/svg%3E");
  background-repeat: repeat-y;
  background-size: 100% auto;
  min-height: 100vh;
}

.content-container {
  width: 100%;
  max-width: 1200px;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 16px;
  color: #888;
  z-index: 2;
}

.search-input {
  width: 200px;
  padding: 8px 8px 8px 40px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  color: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.1);
}

.search-input::placeholder {
  color: #888;
}

.picker-section {
  background: rgba(255, 255, 255, 0.05);
  border-bottom: 1px solid var(--color-border);
  border: 1px solid var(--color-border);
  border-radius: 12px 12px 0px 0px !important;
  padding: 20px;
}

.picker-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
}

.picker-btn {
  background: rgba(255, 255, 255, 0.164);
  border: 1px solid var(--color-border-soft);
  color: #ffffff;
  font-weight: bold;
  font-size: 16px;
  padding: 12px 24px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.picker-btn:hover:not(:disabled) {
  background: rgba(74, 111, 165, 0.8) !important;
  color: #ffffff;
  cursor: pointer;
}

.picker-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.picker-btn-active {
  background: var(--color-primary) !important;
  color: var(--color-text) !important;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

.picker-status {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}

.remaining-count {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: bold;
}

.table tbody tr {
  transition: all 0.5s ease;
  position: relative;
}

.row-eliminated {
  opacity: 0.3;
  transform: scale(0.95);
  filter: grayscale(100%);
  background: #4a4a4a !important;
}

.row-eliminated td {
  background: #4a4a4a !important;
  text-decoration: line-through;
}

.row-eliminating {
  animation: eliminate 0.8s ease-in-out;
  background: linear-gradient(90deg, #ff4757, #ff6b6b, #ff4757);
  background-size: 200% 100%;
  animation: eliminate 0.8s ease-in-out, shimmer 0.8s ease-in-out;
}

@keyframes eliminate {
  0% {
    transform: scale(1);
    background-position: 0% 50%;
  }
  50% {
    transform: scale(1.05);
    background-position: 100% 50%;
    box-shadow: 0 0 20px rgba(255, 71, 87, 0.8);
  }
  100% {
    transform: scale(0.95);
    background-position: 0% 50%;
  }
}

.row-winner {
  background: linear-gradient(45deg, #ffd700, #ffed4e, #ffd700);
  background-size: 200% 100%;
  position: relative;
  overflow: hidden;
}

.row-winner::before {
  position: absolute;
  top: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.4),
    transparent
  );
}

.row-winner td {
  background: transparent !important;
  color: #333 !important;
  font-weight: bold !important;
}

.filter-section {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border, #444);
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  display: flex;
  justify-content: center;
  width: fit-content;
  max-width: 100%;
  box-shadow: 0 6px 20px rgb(0, 0, 0);
}

.filter-content {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.filter-columns {
  display: flex;
  gap: 40px;
  align-items: flex-start;
}

.filter-column {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.filter-title {
  color: #ffffff;
  font-weight: 600;
  margin-bottom: 10px;
  text-align: center;
  text-transform: uppercase;
}

.tier-filter-container,
.rating-filter-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
}

.tier-filters,
.rating-filters {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.tier-checkbox,
.rating-checkbox {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin: 0;
}
.tier-checkbox:hover,
.rating-checkbox:hover {
  border-radius: 4px;
  border-color: var(--color-border, #444);
  box-shadow: 0 0 0 1px var(--color-border, #444);
}

.tier-checkbox input,
.rating-checkbox input {
  display: none;
}

.tier-badge,
.rating-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 12px;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  min-width: 28px;
  text-align: center;
}

.tier-checkbox input:checked + .tier-badge,
.rating-checkbox input:checked + .rating-badge {
  border-color: var(--color-border, #444);
  box-shadow: 0 0 0 1px var(--color-border, #444);
}

.filter-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
  margin-top: 35px;
  margin-bottom: 0;
  padding-bottom: 0;
}

.text-light {
  color: #ffffff;
  font-weight: bold;
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.164);
  border: 1px solid var(--color-border-soft);
  font-weight: bold;
}

.btn-secondary:hover {
  background: rgba(74, 111, 165, 0.8) !important;
}

.table-responsive {
  overflow: hidden;
  border-left: 1px solid var(--color-border);
  border-right: 1px solid var(--color-border);
  border-bottom: 1px solid var(--color-border);
  border-radius: 0 0 8px 8px;
}

.table-container {
  border-radius: 8px !important;
  border-top: none;
  box-shadow: 0 6px 20px rgb(0, 0, 0);
}

.button-group {
  border-top: none;
  border-radius: 0;
}

.table th {
  background: var(--color-primary);
  color: white;
  padding: 12px;
  text-align: left;
  font-weight: 600;
  cursor: pointer;
}

.table th:hover {
  background: rgba(74, 111, 165, 0.8) !important;
}

.table td {
  padding: 12px;
  border-bottom: 1px solid var(--color-border);
}

.table-dark thead th {
  background: rgba(74, 111, 165, 0.3) !important;
  border-top: 1px solid var(--color-border);
  border-bottom: 1px solid var(--color-border);
}

.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  border: 1px solid var(--color-border-soft);
  font-weight: bold;
  padding: 8px;
}

.table-dark tr:nth-child(odd) .map-name:hover {
  background: rgba(74, 111, 165, 0.8) !important;
}

.table-dark tr:nth-child(odd) .map-name {
  background: rgba(119, 119, 119, 0.05);
}

.table-dark {
  margin: 0;
}

.map-name:hover {
  background: rgba(74, 111, 165, 0.8) !important;
  color: white;
}

.map-name {
  color: var(--color-text-clickable) !important;
}

.maps-title {
  color: #d1d1d1;
  margin-bottom: 25px;
}

.clickable {
  cursor: pointer;
}

.fixed-width {
  width: 100px;
  min-width: 100px;
}

.tier-color.tier-0 {
  background: var(--color-dark);
  color: var(--color-text);
}
.tier-color.tier-1 {
  background: #6ed0f681;
  color: var(--color-text);
}
.tier-color.tier-2 {
  background: #56b3e977;
  color: var(--color-text);
}
.tier-color.tier-3 {
  background: #45b8ad6e;
  color: var(--color-text);
}
.tier-color.tier-4 {
  background: #65c18b75;
  color: var(--color-text);
}
.tier-color.tier-5 {
  background: #a3d97775;
  color: var(--color-text);
}
.tier-color.tier-6 {
  background: #f3e68370;
  color: var(--color-text);
}
.tier-color.tier-7 {
  background: #f6c26767;
  color: var(--color-text);
}
.tier-color.tier-8 {
  background: #f08d5b65;
  color: var(--color-text);
}
.tier-color.tier-9 {
  background: #e6695e6b;
  color: var(--color-text);
}
.tier-color.tier-10 {
  background: #d6454569;
  color: var(--color-text);
}

.rating-color.rating-1 {
  background: #94c47d69;
  color: var(--color-text);
}
.rating-color.rating-2 {
  background: #abd0996b;
  color: var(--color-text);
}
.rating-color.rating-3 {
  background: #c3b29365;
  color: var(--color-text);
}
.rating-color.rating-4 {
  background: #e0666669;
  color: var(--color-text);
}

.tier-badge.tier-0 {
  background: var(--color-dark);
  color: var(--color-text);
}
.tier-badge.tier-1 {
  background: #6ed0f681;
  color: var(--color-text);
}
.tier-badge.tier-2 {
  background: #56b3e977;
  color: var(--color-text);
}
.tier-badge.tier-3 {
  background: #45b8ad6e;
  color: var(--color-text);
}
.tier-badge.tier-4 {
  background: #65c18b75;
  color: var(--color-text);
}
.tier-badge.tier-5 {
  background: #a3d97775;
  color: var(--color-text);
}
.tier-badge.tier-6 {
  background: #f3e68370;
  color: var(--color-text);
}
.tier-badge.tier-7 {
  background: #f6c26767;
  color: var(--color-text);
}
.tier-badge.tier-8 {
  background: #f08d5b65;
  color: var(--color-text);
}
.tier-badge.tier-9 {
  background: #e6695e6b;
  color: var(--color-text);
}
.tier-badge.tier-10 {
  background: #d6454569;
  color: var(--color-text);
}

.rating-badge.rating-1 {
  background: #94c47d69;
  color: var(--color-text);
}
.rating-badge.rating-2 {
  background: #abd0996b;
  color: var(--color-text);
}
.rating-badge.rating-3 {
  background: #c3b29365;
  color: var(--color-text);
}
.rating-badge.rating-4 {
  background: #e0666669;
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
  margin: 30px 0;
  opacity: 0.6;
}

@media (max-width: 767.98px) {
  .table-responsive {
    width: 100%;
    overflow-x: auto;
  }

  .filter-section {
    width: 100%;
    padding: 15px;
  }

  .filter-columns {
    flex-direction: column;
    gap: 20px;
    width: 100%;
  }

  .filter-column {
    width: 100%;
  }

  .tier-filters,
  .rating-filters {
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
  }

  .filter-actions {
    flex-direction: column;
    gap: 10px;
    align-items: center;
  }

  .picker-controls {
    flex-direction: column;
    align-items: center;
  }

  .button-group {
    flex-direction: row;
    justify-content: center;
    width: 100%;
    gap: 10px;
  }

  .toggle-btn {
    flex: 1;
    margin: 5px;
  }
}
</style>
