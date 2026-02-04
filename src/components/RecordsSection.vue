<template>
  <div class="records-card">
    <div
      class="card-header tabs-header"
      style="background: rgba(255, 255, 255, 0.05)"
    >
      <div class="button-group">
        <button
          @click="switchRecordsTab('recent-runs')"
          class="toggle-btn btn btn-dark update-button"
          :class="{ active: activeRecordsTab === 'recent-runs' }"
        >
          Recent Runs
        </button>
        <button
          @click="switchRecordsTab('changed-placements')"
          class="toggle-btn btn btn-dark update-button"
          :class="{
            active: activeRecordsTab === 'changed-placements',
          }"
        >
          Point Changes
        </button>
      </div>
      <div
        class="d-flex justify-content-between align-items-center button-container-wrapper mt-3"
      >
        <div class="allruns-button-container">
          <SmartLink
            :to="{
              name: 'LookupPlayer',
              params: { playerId: playerId },
            }"
            tag="button"
            class="btn 'btn-dark' global-btn"
            :style="{
              color: 'var(--color-text)',
              'font-weight': 'bold',
            }"
          >
            All Runs
          </SmartLink>
        </div>
        <div class="filter-button-container">
          <button
            @click="toggleFilterSection"
            class="btn 'btn-dark' global-btn"
            :style="{
              color: 'var(--color-text)',
              'font-weight': 'bold',
            }"
          >
            <i class="bi bi-filter"></i> Filters
            <i class="bi bi-chevron-down"></i>
          </button>
        </div>
      </div>
    </div>
    <RecordsFilter
      :show-filter-section="showFilterSection"
      :filter-options="filterOptions"
      :show-change-filters="activeRecordsTab === 'changed-placements'"
      @update:filter-options="filterOptions = $event"
      @clear-filters="clearAllFilters"
    />
    <div
      class="card-body tabs-content"
      style="background: rgba(255, 255, 255, 0.05)"
    >
      <div class="records-section">
        <div v-if="currentLoadingState" class="text-center">
          <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
        <div v-else>
          <div v-if="activeRecordsTab === 'recent-runs'">
            <div
              v-for="(group, date) in filteredAndPaginatedRecords"
              :key="date"
              class="date-group fade-in"
            >
              <div class="date-header">
                <h5>{{ precomputedHeaders[date] }}</h5>
              </div>
              <ul class="list-group">
                <SmartLink
                  v-for="record in group"
                  :key="record.id"
                  :to="{
                    name: 'MapPage',
                    params: { mapId: record.map_id },
                  }"
                  tag="li"
                  class="list-group-item record-item"
                  style="background: rgba(255, 255, 255, 0.05)"
                >
                  <div class="d-flex align-items-center record-class-map">
                    <img
                      :src="`/icons/${record.class}.png`"
                      :alt="record.class"
                      class="class-icon"
                      loading="lazy"
                    />
                    <span class="ms-2 record-map">
                      <HoverPreview :map-name="record.map_name">
                        {{ record.map_name }}
                      </HoverPreview>
                      <span v-if="record.type !== 'map'">
                        |
                        <template v-if="record.type === 'course'">🚩</template>
                        <template v-if="record.type === 'bonus'">⭐</template>
                        {{ record.type.slice(0, 1).toUpperCase()
                        }}{{ record.index }}
                      </span>
                    </span>
                  </div>
                  <div class="text-end align-items-center record-time-detail">
                    <div class="d-flex flex-column align-items-end">
                      <div class="d-flex align-items-center gap-2">
                        <span
                          class="record-detail record-duration"
                          :class="record.rankClass"
                        >
                          {{ record.durationFormatted }}
                        </span>
                        <span
                          class="record-rank"
                          :class="record.placementClass"
                        >
                          {{ record.medal }} #{{ record.rank }}
                        </span>
                      </div>

                      <span class="record-detail record-date">
                        {{ record.formattedDate }}
                      </span>
                    </div>
                  </div>
                </SmartLink>
              </ul>
            </div>
          </div>
          <div v-else-if="activeRecordsTab === 'changed-placements'">
            <div
              v-for="(group, date) in processedChangedPlacements"
              :key="date"
              class="date-group fade-in"
            >
              <div class="date-header">
                <h5>{{ precomputedHeaders[date] }}</h5>
              </div>
              <ul class="list-group">
                <SmartLink
                  v-for="placement in group"
                  :key="placement.id"
                  :to="{
                    name: 'MapPage',
                    params: { mapId: placement.map_id },
                  }"
                  tag="li"
                  class="list-group-item record-item"
                  :class="placement.placementClass"
                  style="background: rgba(255, 255, 255, 0.05)"
                >
                  <div class="d-flex align-items-center record-class-map">
                    <img
                      :src="`/icons/${placement.class}.png`"
                      :alt="placement.class"
                      class="class-icon"
                      loading="lazy"
                    />
                    <span class="ms-2 record-map">
                      <HoverPreview :map-name="placement.map_name">
                        {{ placement.map_name }}
                      </HoverPreview>
                      <span v-if="placement.type !== 'map'">
                        |
                        <template v-if="placement.type === 'course'"
                          >🚩</template
                        >
                        <template v-if="placement.type === 'bonus'"
                          >⭐</template
                        >
                        {{ placement.type.slice(0, 1).toUpperCase()
                        }}{{ placement.index }}
                      </span>
                    </span>
                  </div>
                  <div
                    class="text-end align-items-center record-time-detail"
                    :class="placement.placementClass"
                  >
                    <div class="d-flex flex-column align-items-end">
                      <div class="placement-change-indicator">
                        <span class="placement-arrows">
                          <span
                            class="old-rank"
                            :class="placement.oldRankClass"
                          >
                            {{ placement.oldRankDisplay }}
                          </span>
                          <i class="bi bi-arrow-right placement-arrow"></i>
                          <span
                            class="new-rank"
                            :class="placement.newRankClass"
                          >
                            {{ placement.newRankDisplay }}
                          </span>
                        </span>

                        <span
                          class="points-change"
                          :class="placement.pointsChangeClass"
                        >
                          {{ placement.pointsChangeText }}
                        </span>
                      </div>

                      <span class="record-detail record-date">
                        {{ placement.formattedDate }}
                      </span>
                    </div>
                  </div>
                </SmartLink>
              </ul>
            </div>
          </div>
          <div class="pagination-controls">
            <div class="pagination-side">
              <button
                v-if="currentPage > 1"
                @click="prevPage"
                class="btn btn-dark global-btn"
              >
                Previous
              </button>
            </div>

            <div
              class="pagination-info"
              v-if="activeRecordsTab === 'changed-placements'"
            >
              Next update in {{ nextUpdateCountdown }}
            </div>

            <div class="pagination-side">
              <button
                v-if="shouldShowNextButton"
                @click="nextPage"
                class="btn btn-dark global-btn"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import RecordsFilter from "./RecordsFilter.vue";
import { formatDuration, formatDate } from "@/utils/calculations.js";

export default {
  name: "RecordsSection",
  components: {
    RecordsFilter,
  },
  props: {
    playerId: {
      type: Number,
      required: true,
    },
    recentRecords: {
      type: Array,
      required: true,
    },
    changedPlacements: {
      type: Array,
      required: true,
    },
    loading: {
      type: Object,
      required: true,
    },
    nextUpdateCountdown: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      activeRecordsTab: "recent-runs",
      showFilterSection: false,
      filterOptions: {
        selectedClasses: [],
        selectedTypes: [],
        selectedPlacements: [],
        selectedGainLoss: [],
        selectedUnchanged: false,
      },
      currentPage: 1,
      pageSize: 8,
    };
  },
  computed: {
    precomputedHeaders() {
      const headers = {};

      Object.keys(this.filteredAndPaginatedRecords || {}).forEach((date) => {
        headers[date] = this.formatDateHeader(date);
      });

      Object.keys(this.filteredAndPaginatedChangedPlacements || {}).forEach(
        (date) => {
          headers[date] = this.formatDateHeader(date);
        },
      );

      return headers;
    },
    processedChangedPlacements() {
      const processed = {};

      for (const [date, placements] of Object.entries(
        this.filteredAndPaginatedChangedPlacements,
      )) {
        processed[date] = placements.map((placement) => {
          let placementClass;

          if (placement.points_change > 0) {
            placementClass = "gained-placement";
          } else if (placement.points_change < 0) {
            placementClass = "lost-placement";
          } else {
            placementClass = "tied-placement";
          }

          return {
            ...placement,
            placementClass: placementClass,
          };
        });
      }

      return processed;
    },
    filteredAndPaginatedRecords() {
      const grouped = this.groupRecords(this.filteredRecords);
      return this.paginateGroupedData(grouped);
    },
    filteredAndPaginatedChangedPlacements() {
      return this.paginateGroupedData(this.groupedChangedPlacements);
    },
    shouldShowNextButton() {
      if (this.activeRecordsTab === "recent-runs") {
        return this.currentPage * this.pageSize < this.filteredRecords.length;
      } else {
        const totalItems = this.filteredChangedPlacements.length;
        return this.currentPage * this.pageSize < totalItems;
      }
    },
    filteredRecords() {
      let recordsToFilter = this.recentRecords;
      return recordsToFilter.filter((record) => {
        if (
          this.filterOptions.selectedClasses.length > 0 &&
          !this.filterOptions.selectedClasses.includes(record.class)
        ) {
          return false;
        }
        if (
          this.filterOptions.selectedTypes.length > 0 &&
          !this.filterOptions.selectedTypes.includes(record.type)
        ) {
          return false;
        }
        if (
          this.filterOptions.selectedPlacements.length > 0 &&
          !this.filterOptions.selectedPlacements.some((placement) => {
            if (placement === 1) return record.placement === 1;
            if (placement === 2)
              return record.placement >= 2 && record.placement <= 10;
            return record.placement === placement;
          })
        ) {
          return false;
        }
        return true;
      });
    },
    filteredChangedPlacements() {
      if (!this.changedPlacements || !this.changedPlacements.length) return [];

      let filtered = this.changedPlacements.filter((placement) => {
        if (
          this.filterOptions.selectedClasses.length > 0 &&
          !this.filterOptions.selectedClasses.includes(placement.class)
        ) {
          return false;
        }
        if (
          this.filterOptions.selectedTypes.length > 0 &&
          !this.filterOptions.selectedTypes.includes(placement.type)
        ) {
          return false;
        }
        if (this.filterOptions.selectedPlacements.length > 0) {
          const matchesPlacement = this.filterOptions.selectedPlacements.some(
            (selectedPlacement) => {
              const oldRankMatches = this.checkPlacementMatch(
                placement.old_rank,
                selectedPlacement,
              );
              const newRankMatches = this.checkPlacementMatch(
                placement.new_rank,
                selectedPlacement,
              );

              return oldRankMatches || newRankMatches;
            },
          );

          if (!matchesPlacement) {
            return false;
          }
        }
        if (
          this.filterOptions.selectedGainLoss.length > 0 ||
          this.filterOptions.selectedUnchanged
        ) {
          const isGain = placement.points_change > 0;
          const isLoss = placement.points_change < 0;
          const isUnchanged = placement.points_change === 0;

          let matches = false;

          if (this.filterOptions.selectedUnchanged && isUnchanged) {
            matches = true;
          }

          if (this.filterOptions.selectedGainLoss.length > 0) {
            const matchesGainLoss = this.filterOptions.selectedGainLoss.some(
              (filter) => {
                if (filter === "lost" && isGain) return true;
                if (filter === "gained" && isLoss) return true;
                return false;
              },
            );
            if (matchesGainLoss) {
              matches = true;
            }
          }

          if (!matches) {
            return false;
          }
        }

        return true;
      });

      return filtered;
    },
    groupedChangedPlacements() {
      if (
        !this.filteredChangedPlacements ||
        !Array.isArray(this.filteredChangedPlacements) ||
        this.filteredChangedPlacements.length === 0
      ) {
        return {};
      }
      return this.groupRecords(this.filteredChangedPlacements);
    },
    currentLoadingState() {
      return this.activeRecordsTab === "recent-runs"
        ? this.loading["Latest runs"]
        : this.loading["Lost placements"];
    },
  },
  methods: {
    switchRecordsTab(tab) {
      this.activeRecordsTab = tab;
      this.currentPage = 1;
    },
    toggleFilterSection() {
      this.showFilterSection = !this.showFilterSection;
    },
    clearAllFilters() {
      this.filterOptions = {
        selectedClasses: [],
        selectedTypes: [],
        selectedPlacements: [],
        selectedGainLoss: [],
        selectedUnchanged: false,
      };
      this.currentPage = 1;
    },
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
    paginateGroupedData(groupedData) {
      if (!groupedData || typeof groupedData !== "object") {
        return {};
      }

      const start = (this.currentPage - 1) * this.pageSize;
      const end = start + this.pageSize;
      const dates = Object.keys(groupedData);
      const paginated = {};
      let count = 0;

      for (const date of dates) {
        if (!Array.isArray(groupedData[date])) {
          continue;
        }

        for (const item of groupedData[date]) {
          if (count >= start && count < end) {
            if (!paginated[date]) {
              paginated[date] = [];
            }
            paginated[date].push(item);
          }
          count++;
        }
      }

      return paginated;
    },
    checkPlacementMatch(rank, selectedPlacement) {
      if (selectedPlacement === 1) return rank === 1;
      if (selectedPlacement === 2) return rank >= 2 && rank <= 10;
      return rank === selectedPlacement;
    },
    groupRecords(records) {
      const grouped = {};
      records.forEach((record) => {
        const date = new Date(record.date * 1000).toDateString();
        if (!grouped[date]) {
          grouped[date] = [];
        }
        grouped[date].push(record);
      });
      return grouped;
    },
  },
};
</script>

<style scoped>
.record-item {
  transition: transform 0.25s ease;
}
.record-item:hover {
  transform: scale(1.01);
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.3),
    rgba(74, 111, 165, 0.1)
  );
}
.button-container-wrapper {
  display: flex;
  width: 100%;
}
.allruns-button-container,
.filter-button-container {
  display: flex;
  justify-content: center;
  width: 50%;
}
.global-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  border-radius: 8px;
  max-width: 150px;
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
.button-group {
  border: none;
}
.card-header.tabs-header .btn {
  flex: 1;
  margin: 0;
  padding: 10px;
  text-align: center;
}
.card-body,
.tabs-content,
.records-section {
  width: 100%;
  padding: 0;
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}
.card-body {
  position: relative;
  padding: 20px;
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
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.3),
    rgba(74, 111, 165, 0.1)
  ) !important;
  cursor: pointer;
}
.card-body,
.card-header {
  padding: 10px;
  padding-bottom: 0;
}
.records-card {
  border-radius: 10px !important;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}
.record-class-map {
  display: flex;
  align-items: center;
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
}
.pagination-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  padding-bottom: 10px;
}
.pagination-info {
  color: var(--color-text-soft);
  font-weight: bold;
  text-align: center;
  white-space: nowrap;
  flex: 1;
}
.pagination-side {
  flex: 1;
  display: flex;
  justify-content: flex-start;
}
.pagination-side:last-child {
  justify-content: flex-end;
}
.records-section {
  position: relative;
  min-height: 630px !important;
  padding-bottom: 80px !important;
}
.records-section .pagination-controls {
  position: absolute;
  bottom: 10px;
  left: 0;
  width: 100%;
  padding: 10px;
  box-sizing: border-box;
}
.date-header {
  color: var(--color-text);
  padding-top: 5px;
}
.placement-change-indicator {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  font-size: 0.9rem;
}
.points-change {
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.85rem;
}
.points-gain {
  color: #51cf66;
  background: rgba(81, 207, 102, 0.1);
}
.points-loss {
  color: #ff6b6b;
  background: rgba(255, 107, 107, 0.1);
}
.points-tied {
  color: #6c757d;
  background: rgba(108, 117, 125, 0.1);
}
.placement-arrows {
  display: flex;
  align-items: center;
  gap: 4px;
  color: var(--color-text-soft);
}
.placement-arrow {
  font-size: 0.8rem;
}
.old-rank {
  color: var(--color-text-soft);
  text-decoration: line-through;
}
.new-rank {
  font-weight: bold;
}
.record-item.gained-placement {
  border-left: 3px solid #51cf66;
}
.record-item.lost-placement {
  border-left: 3px solid #ff6b6b;
}
.record-item.tied-placement {
  border-left: 3px solid #6c757d;
}
.record-time-detail.gained-placement,
.record-time-detail.lost-placement {
  flex-direction: column;
  gap: 4px;
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
.record-item {
  font-size: 0.9rem;
  padding: 8px;
}

@media (max-width: 1400px) {
  .record-time-detail {
    min-width: 0;
    flex-shrink: 1;
  }
  .placement-change-indicator {
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    min-width: 0;
  }
  .placement-arrows {
    font-size: 0.8rem;
    white-space: nowrap;
  }
  .points-change {
    font-size: 0.75rem;
    padding: 1px 4px;
  }
  .record-detail.record-date {
    font-size: 0.75rem;
    margin-top: 2px;
  }
  .record-item .d-flex {
    min-width: 0;
  }
  .record-class-map {
    flex: 1;
    min-width: 0;
    overflow: hidden;
  }
  .record-map {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .record-time-detail .d-flex.flex-column {
    align-items: flex-end;
    min-width: 0;
  }
}

@media (max-width: 567px) {
  :deep(.global-btn),
  :deep(.global-btn.active) {
    font-size: 0.8rem !important;
    padding: 10px !important;
  }
}
</style>
