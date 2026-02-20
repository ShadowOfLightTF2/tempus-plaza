<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container" style="z-index: 1">
      <div
        class="content-container mx-auto py-4 d-flex flex-column align-items-center"
        style="z-index: 1"
      >
        <div
          v-if="showTagFilterModal"
          class="tag-modal-backdrop"
          @click="closeTagFilterModal"
        >
          <div class="tag-modal" @click.stop>
            <div class="tag-modal-header">
              <h3><i class="bi bi-funnel-fill me-2"></i>Filter by Tags</h3>
              <button @click="closeTagFilterModal" class="close-btn">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <div class="tag-modal-description">
              <p class="description-text">
                <i class="bi bi-info-circle me-2"></i>
                Filter maps by their gameplay characteristics and features. Note
                that not every map has tags yet, you can help by voting for tags
                on the individual map pages to improve the filtering system for
                everyone!
              </p>
            </div>

            <div class="tag-modal-body">
              <div class="tag-section">
                <div
                  class="tag-class-group"
                  v-if="getTagsByClass('soldier').length > 0"
                >
                  <h5>
                    <img
                      src="/icons/soldier.png"
                      alt="Soldier"
                      class="class-icon me-1"
                    />
                    Soldier
                  </h5>
                  <div class="tag-selector">
                    <div class="available-tags">
                      <div
                        v-for="tag in getTagsByClass('soldier')"
                        :key="'filter-soldier-' + tag.id"
                        class="tag-option"
                        :class="{ active: isTagSelected(tag.id) }"
                        @click="toggleTag(tag.id)"
                        :style="{
                          backgroundColor: isTagSelected(tag.id)
                            ? tag.color + '40'
                            : 'transparent',
                          borderColor: tag.color,
                        }"
                      >
                        <i
                          class="bi bi-tag-fill me-1"
                          :style="{ color: tag.color }"
                        ></i>
                        {{ tag.name }}
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="tag-class-group"
                  v-if="getTagsByClass('demoman').length > 0"
                >
                  <h5>
                    <img
                      src="/icons/demoman.png"
                      alt="Demoman"
                      class="class-icon me-1"
                    />
                    Demoman
                  </h5>
                  <div class="tag-selector">
                    <div class="available-tags">
                      <div
                        v-for="tag in getTagsByClass('demoman')"
                        :key="'filter-demoman-' + tag.id"
                        class="tag-option"
                        :class="{ active: isTagSelected(tag.id) }"
                        @click="toggleTag(tag.id)"
                        :style="{
                          backgroundColor: isTagSelected(tag.id)
                            ? tag.color + '40'
                            : 'transparent',
                          borderColor: tag.color,
                        }"
                      >
                        <i
                          class="bi bi-tag-fill me-1"
                          :style="{ color: tag.color }"
                        ></i>
                        {{ tag.name }}
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="tag-class-group"
                  v-if="getTagsByClass('both').length > 0"
                >
                  <h5><i class="bi bi-people me-1"></i>Both Classes</h5>
                  <div class="tag-selector">
                    <div class="available-tags">
                      <div
                        v-for="tag in getTagsByClass('both')"
                        :key="'filter-both-' + tag.id"
                        class="tag-option"
                        :class="{ active: isTagSelected(tag.id) }"
                        @click="toggleTag(tag.id)"
                        :style="{
                          backgroundColor: isTagSelected(tag.id)
                            ? tag.color + '40'
                            : 'transparent',
                          borderColor: tag.color,
                        }"
                      >
                        <i
                          class="bi bi-tag-fill me-1"
                          :style="{ color: tag.color }"
                        ></i>
                        {{ tag.name }}
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="tag-class-group"
                  v-if="getTagsByClass('extra').length > 0"
                >
                  <h5><i class="bi bi-gear me-1"></i>Extra</h5>
                  <div class="tag-selector">
                    <div class="available-tags">
                      <div
                        v-for="tag in getTagsByClass('extra')"
                        :key="'filter-extra-' + tag.id"
                        class="tag-option"
                        :class="{ active: isTagSelected(tag.id) }"
                        @click="toggleTag(tag.id)"
                        :style="{
                          backgroundColor: isTagSelected(tag.id)
                            ? tag.color + '40'
                            : 'transparent',
                          borderColor: tag.color,
                        }"
                      >
                        <i
                          class="bi bi-tag-fill me-1"
                          :style="{ color: tag.color }"
                        ></i>
                        {{ tag.name }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tag-modal-footer"></div>
          </div>
        </div>
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">🗺️</span>
            Tempus Maps
          </h1>
          <p class="page-subtitle">
            Explore and filter maps, courses, and bonuses
          </p>
        </div>
        <hr class="row-divider" style="width: 75%" />
        <div class="filter-section">
          <div class="filter-content">
            <div
              class="filter-columns"
              style="flex-direction: column; gap: 20px"
            >
              <div
                class="filter-row"
                style="display: flex; gap: 40px; justify-content: center"
              >
                <div class="filter-column">
                  <div class="filter-group">
                    <h6 class="filter-title mb-2">Soldier tiers</h6>
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
                </div>

                <div class="filter-column">
                  <div class="filter-group">
                    <h6 class="filter-title mb-2">Demoman tiers</h6>
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
                </div>
              </div>
              <div
                class="filter-row responsive-ratings-row"
                style="
                  display: flex;
                  gap: 40px;
                  justify-content: center;
                  align-items: flex-start;
                  width: 100%;
                "
              >
                <div class="filter-column">
                  <div class="filter-group">
                    <h6 class="filter-title mb-2">Soldier ratings</h6>
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
                <div class="filter-column intended-class-column">
                  <div class="filter-group">
                    <h6 class="filter-title mb-2">Intended Class</h6>
                    <div class="intended-class-buttons">
                      <button
                        v-for="cls in availableIntendedClasses"
                        :key="cls.id"
                        @click="toggleIntendedClass(cls.id)"
                        :class="{
                          active: selectedIntendedClasses.includes(cls.id),
                        }"
                        class="intended-class-btn"
                      >
                        <img :src="cls.icon" :alt="cls.label" />
                      </button>
                    </div>
                  </div>
                </div>
                <div class="filter-column">
                  <div class="filter-group">
                    <h6 class="filter-title mb-2">Demoman ratings</h6>
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
            </div>
            <div
              class="column-toggles-section"
              :class="{ 'grid-mode': currentLayout === 'grid' }"
            >
              <div class="column-toggles" v-if="currentLayout === 'table'">
                <button
                  @click="toggleColumns('soldier')"
                  class="global-btn"
                  :class="{ active: !showSoldierColumns }"
                >
                  <span v-if="showSoldierColumns">Hide</span>
                  <span v-else>Show</span>
                  Soldier
                </button>
                <button
                  @click="toggleColumns('demoman')"
                  class="global-btn"
                  :class="{ active: !showDemomanColumns }"
                >
                  <span v-if="showDemomanColumns">Hide</span>
                  <span v-else>Show</span>
                  Demoman
                </button>
                <button
                  @click="toggleColumns('completions')"
                  class="global-btn"
                  :class="{ active: !showCompletionColumns }"
                >
                  <span v-if="showCompletionColumns">Hide</span>
                  <span v-else>Show</span>
                  Completions
                </button>
              </div>
            </div>
            <div class="compact-controls">
              <button @click="openTagFilterModal" class="filter-btn">
                <i class="bi bi-tags me-1"></i>Tags
              </button>

              <button @click="clearAllFilters" class="filter-btn">Clear</button>

              <span class="filter-count">
                {{ filteredAndSortedItems.length }}/{{ currentItems.length }}
              </span>

              <div class="search-input-wrapper compact-search">
                <svg
                  class="search-icon"
                  width="18"
                  height="18"
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
                  placeholder="Search..."
                  class="search-input"
                />
              </div>

              <div
                v-if="filteredAndSortedItems.length > 1"
                class="compact-picker"
              >
                <button
                  @click="startRandomPicker"
                  :disabled="isPickerActive"
                  class="filter-btn"
                  :class="{ 'picker-btn-active': isPickerActive }"
                >
                  <span v-if="!isPickerActive">Random</span>
                  <span v-else>Eliminating...</span>
                </button>

                <button
                  v-if="eliminatedRows.size > 0"
                  @click="resetPicker"
                  class="filter-btn"
                >
                  Reset
                </button>

                <span
                  v-if="isPickerActive || eliminatedRows.size > 0"
                  class="remaining-count small"
                >
                  {{ remainingCount }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <hr class="row-divider" style="width: 75%" />
        <div v-if="error" class="alert alert-danger">{{ error }}</div>
        <div v-else class="table-container">
          <div class="table-header-section">
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
              <div class="layout-divider"></div>
              <button
                @click="switchLayout('table')"
                :class="{ active: currentLayout === 'table' }"
                class="toggle-btn btn btn-dark update-button layout-toggle"
                title="Table View"
              >
                ⊟
              </button>
              <button
                @click="switchLayout('grid')"
                :class="{ active: currentLayout === 'grid' }"
                class="toggle-btn btn btn-dark update-button layout-toggle"
                title="Grid View"
              >
                ⊞
              </button>
            </div>
          </div>
          <div v-if="currentLayout === 'table'" class="table-responsive">
            <table class="table table-dark">
              <thead class="table-header">
                <tr>
                  <th style="vertical-align: middle" @click="sortBy('name')">
                    Name
                    <span v-if="sortField === 'name'">{{
                      getSortArrowSymbol("name")
                    }}</span>
                  </th>
                  <th
                    v-if="showSoldierColumns"
                    @click="sortBy('soldier_tier')"
                    class="fixed-width"
                  >
                    [S] tier
                    <span v-if="sortField === 'soldier_tier'">{{
                      getSortArrowSymbol("soldier_tier")
                    }}</span>
                  </th>
                  <th
                    v-if="showSoldierColumns"
                    @click="sortBy('soldier_rating')"
                    class="fixed-width"
                  >
                    [S] rating
                    <span v-if="sortField === 'soldier_rating'">{{
                      getSortArrowSymbol("soldier_rating")
                    }}</span>
                  </th>
                  <th
                    v-if="showSoldierColumns && showDemomanColumns"
                    class="divider"
                  ></th>
                  <th
                    v-if="showDemomanColumns"
                    @click="sortBy('demoman_tier')"
                    class="fixed-width"
                  >
                    [D] tier
                    <span v-if="effectiveSortField === 'demoman_tier'">{{
                      getSortArrowSymbol("demoman_tier")
                    }}</span>
                  </th>
                  <th
                    v-if="showDemomanColumns"
                    @click="sortBy('demoman_rating')"
                    class="fixed-width"
                  >
                    [D] rating
                    <span v-if="effectiveSortField === 'demoman_rating'">{{
                      getSortArrowSymbol("demoman_rating")
                    }}</span>
                  </th>
                  <th
                    v-if="
                      (showSoldierColumns || showDemomanColumns) &&
                      showCompletionColumns
                    "
                    class="divider"
                  ></th>
                  <th
                    v-if="showSoldierColumns && showCompletionColumns"
                    @click="sortBy('soldier_completion_count')"
                    class="fixed-width"
                    title="Soldier Completions"
                  >
                    [S] compl
                    <span v-if="sortField === 'soldier_completion_count'">{{
                      getSortArrowSymbol("soldier_completion_count")
                    }}</span>
                  </th>
                  <th
                    v-if="showDemomanColumns && showCompletionColumns"
                    @click="sortBy('demoman_completion_count')"
                    class="fixed-width"
                    title="Demoman Completions"
                  >
                    [D] compl
                    <span v-if="sortField === 'demoman_completion_count'">{{
                      getSortArrowSymbol("demoman_completion_count")
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
                  <SmartLink
                    tag="td"
                    :to="getMapRoute(item)"
                    class="align-middle map-name clickable"
                  >
                    {{
                      currentView === "maps"
                        ? item.name
                        : `${item.map_name} (${currentView
                            .slice(0, 1)
                            .toUpperCase()}${item.index})`
                    }}
                  </SmartLink>
                  <td
                    v-if="showSoldierColumns"
                    class="align-middle text-center"
                    :class="`tier-color-tier-${item.soldier_tier}`"
                  >
                    T{{ item.soldier_tier }}
                  </td>
                  <td
                    v-if="showSoldierColumns"
                    class="align-middle text-center"
                    :class="`rating-color-rating-${item.soldier_rating}`"
                  >
                    R{{ item.soldier_rating }}
                  </td>
                  <td
                    v-if="showSoldierColumns && showDemomanColumns"
                    class="divider"
                  ></td>
                  <td
                    v-if="showDemomanColumns"
                    class="align-middle text-center"
                    :class="`tier-color-tier-${item.demoman_tier}`"
                  >
                    T{{ item.demoman_tier }}
                  </td>
                  <td
                    v-if="showDemomanColumns"
                    class="align-middle text-center"
                    :class="`rating-color-rating-${item.demoman_rating}`"
                  >
                    R{{ item.demoman_rating }}
                  </td>
                  <td
                    v-if="
                      (showSoldierColumns || showDemomanColumns) &&
                      showCompletionColumns
                    "
                    class="divider"
                  ></td>
                  <td
                    v-if="showSoldierColumns && showCompletionColumns"
                    class="align-middle text-center"
                  >
                    {{ item.soldier_completion_count }}
                  </td>
                  <td
                    v-if="showDemomanColumns && showCompletionColumns"
                    class="align-middle text-center"
                  >
                    {{ item.demoman_completion_count }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="grid-container">
            <div class="maps-grid">
              <template v-if="loading">
                <div
                  v-for="i in 24"
                  :key="'skel-' + i"
                  class="map-card skeleton-card"
                >
                  <div class="shimmer"></div>
                </div>
              </template>
              <template v-else>
                <div
                  ref="gridCards"
                  v-for="(item, index) in filteredAndSortedItems"
                  :key="currentView + '-grid-' + index"
                  :class="{
                    'card-eliminated': eliminatedRows.has(
                      getRowId(item, index),
                    ),
                    'card-winner':
                      pickerComplete &&
                      !eliminatedRows.has(getRowId(item, index)),
                    'card-eliminating':
                      eliminatingRowId === getRowId(item, index),
                  }"
                  class="map-card fade-in"
                >
                  <SmartLink :to="getMapRoute(item)" class="card-link">
                    <MapCard :item="item" :current-view="currentView" />
                  </SmartLink>
                </div>
              </template>
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
import { useRoute, useRouter } from "vue-router";
import MapCard from "./MapCard.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;
const TIER_ORDER = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 0];

export default {
  components: {
    MapCard,
  },
  name: "Maps",
  setup() {
    const route = useRoute();
    const router = useRouter();
    useHead({
      title: "Tempus Plaza | Maps",
    });
    return { route, router };
  },
  data() {
    return {
      maps: [],
      courses: [],
      bonuses: [],
      currentView: "maps",
      currentLayout: "grid",
      loading: true,
      error: null,
      sortField: "soldier_tier",
      sortDirection: 1,
      availableTags: [],
      selectedTags: [],
      showTagFilterModal: false,
      selectedSoldierTiers: [],
      selectedSoldierRatings: [],
      selectedDemomanTiers: [],
      selectedDemomanRatings: [],
      availableTiers: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 0],
      availableRatings: [1, 2, 3, 4],
      selectedIntendedClasses: [],
      availableIntendedClasses: [
        { id: 3, label: "Soldier", icon: "/icons/soldier.png" },
        { id: 4, label: "Demoman", icon: "/icons/demoman.png" },
      ],
      eliminatedRows: new Set(),
      isPickerActive: false,
      pickerComplete: false,
      eliminatingRowId: null,
      shouldContinuePicker: true,
      searchQuery: "",
      showSoldierColumns: true,
      showDemomanColumns: true,
      showCompletionColumns: false,
    };
  },
  async mounted() {
    await this.fetchAvailableTags();
    this.parseUrlFilters();
    await this.fetchData();
  },
  computed: {
    effectiveSortField() {
      if (
        (this.selectedDemomanTiers.length > 0 ||
          this.selectedDemomanRatings.length > 0) &&
        this.sortField === "soldier_tier" &&
        this.selectedSoldierTiers.length === 0 &&
        this.selectedSoldierRatings.length === 0
      ) {
        return "demoman_tier";
      }
      return this.sortField;
    },
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
      if (this.selectedIntendedClasses.length > 0) {
        filtered = filtered.filter((item) => {
          if (
            this.selectedIntendedClasses.includes(3) &&
            this.selectedIntendedClasses.includes(4)
          ) {
            return item.intended_class === 5;
          }
          return (
            this.selectedIntendedClasses.includes(item.intended_class) ||
            item.intended_class === 5
          );
        });
      }

      if (this.selectedTags.length > 0) {
        filtered = filtered.filter((item) => {
          if (!item.tags || item.tags.length === 0) return false;
          const itemTagIds = item.tags.map((tag) => tag.id);
          return this.selectedTags.some((tagId) => itemTagIds.includes(tagId));
        });
      }

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
      let items = this.filteredItems.slice();

      // if (this.currentLayout === "grid") {
      //   return items.sort((a, b) => {
      //     const nameA =
      //       this.currentView === "maps"
      //         ? a.name
      //         : `${a.map_name} (${this.currentView.slice(0, 1).toUpperCase()}${
      //             a.index
      //           })`;
      //     const nameB =
      //       this.currentView === "maps"
      //         ? b.name
      //         : `${b.map_name} (${this.currentView.slice(0, 1).toUpperCase()}${
      //             b.index
      //           })`;
      //     return nameA.localeCompare(nameB);
      //   });
      // }

      if (this.currentLayout === "grid") {
        return items.sort((a, b) => {
          if (this.currentView === "maps") {
            const dateA = a.date_added * 1000;
            const dateB = b.date_added * 1000;
            return dateB - dateA;
          } else {
            const nameComparison = a.map_name.localeCompare(b.map_name);
            if (nameComparison !== 0) {
              return nameComparison;
            }

            // If map names are the same, sort by index
            return a.index - b.index;
          }
        });
      }

      return items.sort((a, b) => {
        let effectiveSortField = this.sortField;
        let effectiveSortDirection = this.sortDirection;

        if (
          (this.selectedDemomanTiers.length > 0 ||
            this.selectedDemomanRatings.length > 0) &&
          this.sortField === "soldier_tier" &&
          this.selectedSoldierTiers.length === 0 &&
          this.selectedSoldierRatings.length === 0
        ) {
          effectiveSortField = "demoman_tier";
        }

        if (effectiveSortField === "name") {
          const nameA =
            this.currentView === "maps"
              ? a.name
              : `${a.map_name} (${this.currentView.slice(0, 1).toUpperCase()}${
                  a.index
                })`;
          const nameB =
            this.currentView === "maps"
              ? b.name
              : `${b.map_name} (${this.currentView.slice(0, 1).toUpperCase()}${
                  b.index
                })`;
          return nameA.localeCompare(nameB) * effectiveSortDirection;
        }

        const fieldA = a[effectiveSortField];
        const fieldB = b[effectiveSortField];

        if (["soldier_tier", "demoman_tier"].includes(effectiveSortField)) {
          const indexA = TIER_ORDER.indexOf(fieldA);
          const indexB = TIER_ORDER.indexOf(fieldB);
          const primaryComparison = (indexA - indexB) * effectiveSortDirection;

          if (primaryComparison !== 0) return primaryComparison;
        }

        if (
          ["soldier_completion_count", "demoman_completion_count"].includes(
            effectiveSortField,
          )
        ) {
          return (fieldB - fieldA) * effectiveSortDirection;
        }

        if (fieldA < fieldB) return -1 * effectiveSortDirection;
        if (fieldA > fieldB) return 1 * effectiveSortDirection;

        if (effectiveSortField === "demoman_tier") {
          const ratingA = a.demoman_rating;
          const ratingB = b.demoman_rating;
          if (ratingA !== ratingB) {
            return (ratingA - ratingB) * effectiveSortDirection;
          }
        } else if (effectiveSortField === "soldier_tier") {
          const ratingA = a.soldier_rating;
          const ratingB = b.soldier_rating;
          if (ratingA !== ratingB) {
            return (ratingA - ratingB) * effectiveSortDirection;
          }
        }

        const otherFields = [
          "soldier_tier",
          "soldier_rating",
          "demoman_tier",
          "demoman_rating",
        ].filter((field) => {
          const sortFieldClass = effectiveSortField.includes("soldier")
            ? "soldier"
            : "demoman";
          return field.includes(sortFieldClass) && field !== effectiveSortField;
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
    switchLayout(layout) {
      if (this.currentLayout === layout) return;

      this.currentLayout = layout;
      this.updateUrl();
    },
    async fetchAvailableTags() {
      try {
        const response = await axios.get(`${API_BASE_URL}/maps/tags`);
        this.availableTags = response.data;
      } catch (error) {
        console.error("Error fetching tags:", error);
      }
    },
    openTagFilterModal() {
      this.showTagFilterModal = true;
    },
    closeTagFilterModal() {
      this.showTagFilterModal = false;
    },
    toggleTag(tagId) {
      if (this.selectedTags.includes(tagId)) {
        this.selectedTags = this.selectedTags.filter((id) => id !== tagId);
      } else {
        this.selectedTags.push(tagId);
      }
      this.onFilterChange();
    },
    isTagSelected(tagId) {
      return this.selectedTags.includes(tagId);
    },
    getTagsByClass(className) {
      return this.availableTags.filter((tag) => tag.class === className);
    },
    parseUrlFilters() {
      const query = this.$route.query;

      // Parse view
      if (query.view && ["maps", "courses", "bonuses"].includes(query.view)) {
        this.currentView = query.view;
      }

      // Parse tiers and ratings
      this.selectedSoldierTiers = this.parseArrayParam(
        query.st,
        this.availableTiers,
      );
      this.selectedSoldierRatings = this.parseArrayParam(
        query.sr,
        this.availableRatings,
      );
      this.selectedDemomanTiers = this.parseArrayParam(
        query.dt,
        this.availableTiers,
      );
      this.selectedDemomanRatings = this.parseArrayParam(
        query.dr,
        this.availableRatings,
      );

      // Parse intended classes
      this.selectedIntendedClasses = this.parseArrayParam(query.ic, [3, 4]);

      // Parse selected tags
      const availableTagIds = this.availableTags.map((tag) => tag.id);
      this.selectedTags = this.parseArrayParam(query.tags, availableTagIds);

      // Parse layout
      if (query.layout && ["table", "grid"].includes(query.layout)) {
        this.currentLayout = query.layout;
      }

      // Parse column visibility
      this.showSoldierColumns = query.sc !== "0";
      this.showDemomanColumns = query.dc !== "0";
      this.showCompletionColumns = query.cc === "1";
    },
    parseTagsParam(param) {
      if (!param) return [];
      const values = Array.isArray(param) ? param : param.split(",");
      return values.map((v) => parseInt(v)).filter((v) => !isNaN(v));
    },
    updateUrl() {
      const query = {};

      // Add view if not default
      if (this.currentView !== "maps") {
        query.view = this.currentView;
      }

      // Add filters only if they have values
      if (this.selectedSoldierTiers.length > 0) {
        query.st = this.selectedSoldierTiers.join(",");
      }
      if (this.selectedSoldierRatings.length > 0) {
        query.sr = this.selectedSoldierRatings.join(",");
      }
      if (this.selectedDemomanTiers.length > 0) {
        query.dt = this.selectedDemomanTiers.join(",");
      }
      if (this.selectedDemomanRatings.length > 0) {
        query.dr = this.selectedDemomanRatings.join(",");
      }
      if (this.selectedIntendedClasses.length > 0) {
        query.ic = this.selectedIntendedClasses.join(",");
      }

      // Add column visibility if not default
      if (!this.showSoldierColumns) {
        query.sc = "0";
      }
      if (!this.showDemomanColumns) {
        query.dc = "0";
      }
      if (this.showCompletionColumns) {
        query.cc = "1";
      }

      // Add selected tags to URL
      if (this.selectedTags.length > 0) {
        query.tags = this.selectedTags.join(",");
      }

      // Add layout if not default
      if (this.currentLayout !== "grid") {
        query.layout = this.currentLayout;
      }

      if (Object.keys(query).length || Object.keys(this.$route.query).length) {
        this.$router.replace({ query });
      }
    },
    parseArrayParam(param, availableValues) {
      if (!param) return [];
      const values = Array.isArray(param) ? param : param.split(",");
      return values
        .map((v) => parseInt(v))
        .filter((v) => !isNaN(v) && availableValues.includes(v));
    },
    toggleColumns(type) {
      if (type === "soldier") {
        this.showSoldierColumns = !this.showSoldierColumns;
      } else if (type === "demoman") {
        this.showDemomanColumns = !this.showDemomanColumns;
      } else if (type === "completions") {
        this.showCompletionColumns = !this.showCompletionColumns;
      }
      this.updateUrl();
    },
    toggleIntendedClass(clsId) {
      if (this.selectedIntendedClasses.includes(clsId)) {
        this.selectedIntendedClasses = this.selectedIntendedClasses.filter(
          (id) => id !== clsId,
        );
      } else {
        this.selectedIntendedClasses.push(clsId);
      }
      this.onFilterChange();
    },
    getMapRoute(item) {
      const itemId = this.currentView === "maps" ? item.id : item.map_id;
      return { name: "MapPage", params: { mapId: itemId } };
    },
    async switchView(view) {
      if (this.currentView === view) return;

      this.currentView = view;
      this.sortField = "soldier_tier";
      this.sortDirection = 1;
      this.resetPicker();

      this.updateUrl();
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

        const mapsWithTags = await Promise.all(
          response.data.map(async (map) => {
            try {
              return {
                ...map,
                class:
                  map.intended_class === 3
                    ? "soldier"
                    : map.intended_class === 4
                    ? "demoman"
                    : "soldier",
                tags: map.tags || [],
              };
            } catch (error) {
              console.error(`Error loading map info for ${map.id}:`, error);
              return {
                ...map,
                class:
                  map.intended_class === 3
                    ? "soldier"
                    : map.intended_class === 4
                    ? "demoman"
                    : "soldier",
                tags: [],
              };
            }
          }),
        );

        this.maps = mapsWithTags;
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
        this.courses = response.data.map((map) => ({
          ...map,
          class:
            map.intended_class === 3
              ? "soldier"
              : map.intended_class === 4
              ? "demoman"
              : "soldier",
        }));
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
        this.bonuses = response.data.map((map) => ({
          ...map,
          class:
            map.intended_class === 3
              ? "soldier"
              : map.intended_class === 4
              ? "demoman"
              : "soldier",
        }));
      } catch (error) {
        this.error = "Error fetching bonuses data.";
        console.error("Error fetching bonuses data:", error);
      } finally {
        this.loading = false;
      }
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
      if (this.effectiveSortField !== field) return "";
      return this.sortDirection === 1 ? "↑" : "↓";
    },
    clearAllFilters() {
      this.selectedSoldierTiers = [];
      this.selectedSoldierRatings = [];
      this.selectedDemomanTiers = [];
      this.selectedDemomanRatings = [];
      this.selectedIntendedClasses = [];
      this.selectedTags = [];
      this.showSoldierColumns = true;
      this.showDemomanColumns = true;
      this.showCompletionColumns = false;
      this.resetPicker();
      this.$router.replace({ query: {} });
    },
    onFilterChange() {
      this.resetPicker();
      this.updateUrl();
    },
    getRowId(item) {
      return `${this.currentView}-${item.id || item.map_id}`;
    },
    async startRandomPicker() {
      if (this.isPickerActive || this.filteredAndSortedItems.length <= 1)
        return;

      this.isPickerActive = true;
      this.pickerComplete = false;
      this.shouldContinuePicker = true;

      const totalItems = this.filteredAndSortedItems.length;
      const maxEliminationTime = 5000;
      const targetSteps = Math.min(30, totalItems - 1);
      const stepInterval = maxEliminationTime / targetSteps;
      const itemsPerStep = Math.ceil((totalItems - 1) / targetSteps);

      await this.eliminateRandomly(stepInterval, itemsPerStep);
    },
    async eliminateRandomly(stepInterval, itemsPerStep) {
      if (!this.shouldContinuePicker) return;

      const availableRows = this.filteredAndSortedItems
        .map((item, index) => ({ item, index, id: this.getRowId(item) }))
        .filter((row) => !this.eliminatedRows.has(row.id));

      if (availableRows.length <= 1) {
        this.isPickerActive = false;
        this.pickerComplete = true;

        if (availableRows.length === 1) {
          await this.$nextTick();
          const rowIndex = availableRows[0].index;

          if (this.currentLayout === "table" && this.$refs.tableRows) {
            this.$refs.tableRows[rowIndex]?.scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
          } else if (this.currentLayout === "grid" && this.$refs.gridCards) {
            this.$refs.gridCards[rowIndex]?.scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
          }
        }
        return;
      }

      const remainingToEliminate = availableRows.length - 1;
      const itemsToEliminateThisStep = Math.min(
        itemsPerStep,
        remainingToEliminate,
      );

      const shuffledRows = [...availableRows].sort(() => Math.random() - 0.5);
      const rowsToEliminate = shuffledRows.slice(0, itemsToEliminateThisStep);

      rowsToEliminate.forEach((row) => {
        this.eliminatedRows.add(row.id);
      });

      await new Promise((resolve) => setTimeout(resolve, stepInterval));

      await this.eliminateRandomly(stepInterval, itemsPerStep);
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
  border: 1px solid var(--color-border-soft);
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
  border: 1px solid var(--color-border-soft);
  border-radius: 12px;
  padding: 10px;
  margin-top: 20px;
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
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
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

.row-eliminated,
.map-card.card-eliminated {
  opacity: 0.3;
  transform: scale(0.95);
  filter: grayscale(100%);
  background: #4a4a4a !important;
}

.row-eliminated td {
  background: #4a4a4a !important;
  text-decoration: line-through;
}

.row-eliminating,
.map-card.card-eliminating {
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

.row-winner,
.map-card.card-winner {
  background: linear-gradient(45deg, #ffd700, #ffed4e, #ffd700);
  background-size: 200% 100%;
  position: relative;
  overflow: hidden;
}

.row-winner::before,
.map-card.card-winner::before {
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
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  padding: 20px;
  margin: 20px;
  display: flex;
  justify-content: center;
  width: fit-content;
  max-width: 100%;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.filter-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
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
  font-weight: bold;
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

.column-toggles-section {
  display: flex;
  justify-content: center;
  margin: 25px 0 5px;
}

.column-toggles-section.grid-mode {
  margin: 0;
}

.column-toggles {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.filter-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  justify-content: center;
}

.filter-count {
  color: white;
  font-weight: bold;
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.164);
  border: 1px solid var(--color-border-soft);
  font-weight: bold;
}

.btn-secondary:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
}

.table-responsive {
  width: 100%;
  overflow: hidden;
  border-left: 1px solid var(--color-border-soft);
  border-right: 1px solid var(--color-border-soft);
  border-bottom: 1px solid var(--color-border-soft);
  border-radius: 0 0 8px 8px;
}

.table-container {
  border-radius: 8px !important;
  border-top: none;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  margin-top: 20px;
}

.button-group {
  border: none;
  border-radius: 0;
}

.toggle-btn,
.toggle-btn:active,
.toggle-btn:hover,
.toggle-btn:first-child,
.toggle-btn:last-child {
  border-radius: 0 !important;
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
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
}

.table td {
  padding: 12px;
  border-bottom: 1px solid var(--color-border-soft);
}

.table-dark thead th {
  background: rgba(74, 111, 165, 0.3) !important;
  border-top: 1px solid var(--color-border-soft);
}

.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  border: 1px solid var(--color-border-soft);
  font-weight: bold;
  padding: 6px 8px;
}

.table-dark tr:nth-child(odd) .map-name:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
}

.table-dark tr:nth-child(odd) .map-name {
  background: rgba(119, 119, 119, 0.05);
}

.table-dark {
  margin: 0;
}

.map-name:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
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
  width: 120px;
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

.intended-class-buttons {
  display: flex;
  gap: 15px;
  justify-content: center;
}

.intended-class-btn {
  background: none;
  border: 1px solid transparent;
  border-radius: 4px;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
}

.intended-class-btn img {
  width: 28px;
  height: 28px;
  display: block;
}

.intended-class-btn.active {
  background: rgba(165, 165, 165, 0.5);
  border-color: var(--color-border);
  color: white;
}

.intended-class-btn:hover {
  background: rgba(165, 165, 165, 0.3);
  border-color: var(--color-border);
}

.spinner-border {
  color: white;
  font-weight: bold;
  width: 3rem;
  height: 3rem;
}

.alert-danger {
  background: rgba(220, 53, 69, 0.1);
  border: 1px solid rgba(220, 53, 69, 0.3);
  border-radius: 8px;
  color: #ff6b6b;
}

.layout-divider {
  width: 1px;
  background: var(--color-border-soft);
  margin: 0 10px;
}

.layout-toggle {
  font-size: 18px !important;
  min-width: 45px;
  padding: 8px 12px !important;
}

.grid-container {
  width: 100%;
  border-left: 1px solid var(--color-border-soft);
  border-right: 1px solid var(--color-border-soft);
  border-bottom: 1px solid var(--color-border-soft);
  border-radius: 0 0 8px 8px;
  background: rgba(255, 255, 255, 0.02);
  padding: 20px;
}

.maps-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(300px, 1fr));
  gap: 20px;
  width: 100%;
  justify-items: center;
}

@media (max-width: 1400px) {
  .maps-grid {
    grid-template-columns: repeat(3, minmax(300px, 1fr));
  }
}

@media (max-width: 1024px) {
  .maps-grid {
    grid-template-columns: repeat(2, minmax(300px, 1fr));
  }
}

@media (max-width: 708px) {
  .maps-grid {
    grid-template-columns: repeat(1, minmax(300px, 1fr));
  }
}

.map-card {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  height: 275px;
  width: 100%;
  transition: all 0.5s ease;
  border: 1px solid var(--color-border-soft);
}

.map-card:hover {
  border: 1px solid var(--color-primary);
  box-shadow: 0 0 40px rgba(102, 126, 234, 0.6);
  cursor: pointer;
}

.card-link {
  display: block;
  width: 100%;
  height: 100%;
  text-decoration: none;
  color: inherit;
}

.tag-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(5px);
}

.tag-modal {
  background: var(--color-box);
  border-radius: 16px;
  border: 1px solid var(--color-border-soft);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
  max-width: 800px;
  max-height: 80vh;
  width: 90%;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.tag-modal-header {
  padding: 20px;
  border-bottom: 1px solid var(--color-border-soft);
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--color-primary-dark);
}

.tag-modal-header h3 {
  margin: 0;
  color: var(--color-text);
  font-weight: 700;
}

.close-btn {
  background: none;
  border: none;
  color: var(--color-text);
  font-size: 1.2rem;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}

.tag-modal-body {
  padding: 20px;
  overflow-y: auto;
  flex: 1;
}

.tag-section {
  margin-bottom: 24px;
}

.tag-section h4 {
  color: var(--color-text);
  font-weight: 600;
  margin-bottom: 12px;
  font-size: 1.1rem;
}

.tag-class-group {
  margin-bottom: 20px;
  padding: 16px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.class-icon {
  width: 20px;
  height: 20px;
  object-fit: contain;
  vertical-align: middle;
}

.tag-class-group h5 {
  color: var(--color-text);
  font-weight: 600;
  margin-bottom: 12px;
  font-size: 1rem;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.course-tag-section,
.bonus-tag-section {
}

.available-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.tag-option {
  display: inline-flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 16px;
  border: 2px solid;
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--color-text);
  cursor: pointer;
  transition: all 0.3s ease;
  background: transparent;
}

.tag-option:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.tag-option.active {
  font-weight: 600;
  transform: scale(1.05);
}

.tag-modal-footer {
  padding: 20px;
  border-top: 1px solid var(--color-border-soft);
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  background: var(--color-row);
}

.cancel-btn {
  background: transparent;
  color: var(--color-text);
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateY(-1px);
}

.tag-option.active .vote-count {
  color: inherit;
  font-weight: 500;
}

.tag-description {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.description-text {
  margin: 0;
  color: #495057;
  font-size: 0.95rem;
  line-height: 1.5;
}

.description-text strong {
  color: #212529;
  font-weight: 600;
}

.description-text i {
  color: #6c757d;
}

@media (max-width: 768px) {
  .tag-modal {
    width: 95%;
    max-height: 90vh;
  }

  .tag-btn {
    right: 48px;
  }

  .tags-block {
    gap: 6px;
  }

  .tag-chip {
    font-size: 0.8rem;
    padding: 4px 10px;
  }
}

@media (max-width: 768px) {
  .column-toggles {
    flex-wrap: wrap;
    justify-content: center;
  }

  .filter-actions {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
}

.tag-modal-description {
  padding: 1.5rem 1rem;
}

.description-text {
  margin: 0;
  padding: 0.75rem 1rem;
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 0.5rem;
  font-size: 0.9rem;
  color: #6c757d;
  line-height: 1.4;
}

.description-text i {
  color: #17a2b8;
  opacity: 0.8;
}

.skeleton-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border-soft);
  overflow: hidden;
  pointer-events: none;
}

.shimmer {
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.04) 25%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.04) 75%
  );
  background-size: 800px 100%;
  animation: shimmer 1.6s infinite linear;
}

@keyframes shimmer {
  0% {
    background-position: -800px 0;
  }
  100% {
    background-position: 800px 0;
  }
}

.compact-controls {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
  margin-top: 25px;
}

.compact-search .search-input {
  width: 200px;
  padding: 8px 8px 8px 38px;
  font-size: 14px;
}

.compact-picker {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-sm {
  padding: 6px 12px !important;
  font-size: 14px !important;
}

.filter-count {
  font-size: 14px;
  opacity: 0.8;
}

.filter-btn {
  padding: 0.4rem 0.8rem;
  border: 2px solid var(--color-border-soft);
  background: rgba(255, 255, 255, 0.08);
  color: var(--color-text);
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 500;
  font-size: 0.85rem;
}

.filter-btn:hover {
  background: rgba(74, 111, 165, 0.25);
  border-color: var(--color-primary);
}

.filter-btn.active {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  border-color: var(--color-primary);
  color: white;
}

.filter-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
