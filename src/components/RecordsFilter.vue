<template>
  <div
    v-if="showFilterSection"
    class="filter-section"
    style="background: rgba(255, 255, 255, 0.05)"
  >
    <div class="filter-content">
      <div class="filter-columns">
        <div class="filter-group">
          <h6 class="filter-title text-light mb-2">Class</h6>
          <div class="class-filter-container">
            <button
              @click="toggleFilter('class', 'soldier')"
              :class="{
                active: filterOptions.selectedClasses.includes('soldier'),
              }"
              class="global-btn"
            >
              Soldier
            </button>
            <button
              @click="toggleFilter('class', 'demoman')"
              :class="{
                active: filterOptions.selectedClasses.includes('demoman'),
              }"
              class="global-btn"
            >
              Demoman
            </button>
          </div>
        </div>

        <div class="filter-group">
          <h6 class="filter-title text-light mb-2">Type</h6>
          <div class="type-filter-container">
            <button
              @click="toggleFilter('type', 'map')"
              :class="{ active: filterOptions.selectedTypes.includes('map') }"
              class="global-btn"
            >
              Map
            </button>
            <button
              @click="toggleFilter('type', 'course')"
              :class="{
                active: filterOptions.selectedTypes.includes('course'),
              }"
              class="global-btn"
            >
              Course
            </button>
            <button
              @click="toggleFilter('type', 'bonus')"
              :class="{ active: filterOptions.selectedTypes.includes('bonus') }"
              class="global-btn"
            >
              Bonus
            </button>
          </div>
        </div>
      </div>

      <div class="filter-group" style="margin-bottom: 20px; margin-top: 10px">
        <h6 class="filter-title text-light mb-2">Placement</h6>
        <div class="group-filter-container">
          <div class="group-filters">
            <button
              @click="toggleFilter('placement', 1)"
              :class="{ active: filterOptions.selectedPlacements.includes(1) }"
              class="filter-button group-badge group-wr"
            >
              WR
            </button>
            <button
              @click="toggleFilter('placement', 2)"
              :class="{ active: filterOptions.selectedPlacements.includes(2) }"
              class="filter-button group-badge group-tt"
            >
              TT
            </button>
            <button
              v-for="group in [11, 12, 13, 14, 15]"
              :key="'group-' + group"
              @click="toggleFilter('placement', group)"
              class="filter-button group-badge"
              :class="{
                active: filterOptions.selectedPlacements.includes(group),
                'group-1': group - 10 === 1,
                'group-2': group - 10 === 2,
                'group-3': group - 10 === 3,
                'group-4': group - 10 === 4,
                'group-5': group - 10 === 5,
              }"
            >
              G{{ group - 10 }}
            </button>
          </div>
        </div>
      </div>

      <div
        class="filter-columns"
        :class="{ 'no-padding-bottom': !showChangeFilters }"
      >
        <div v-if="showChangeFilters" class="filter-group">
          <h6 class="filter-title text-light mb-2">Change</h6>
          <div class="gain-loss-filter-container">
            <button
              @click="toggleFilter('gainLoss', 'lost')"
              :class="{
                active: filterOptions.selectedGainLoss.includes('lost'),
              }"
              class="global-btn"
            >
              Gained
            </button>
            <button
              @click="toggleUnchangedFilter"
              :class="{ active: filterOptions.selectedUnchanged }"
              class="global-btn"
            >
              Unchanged
            </button>
            <button
              @click="toggleFilter('gainLoss', 'gained')"
              :class="{
                active: filterOptions.selectedGainLoss.includes('gained'),
              }"
              class="global-btn"
            >
              Lost
            </button>
          </div>
        </div>
        <div
          class="filter-actions"
          :class="{ 'no-margin-top': !showChangeFilters }"
        >
          <button
            type="button"
            @click="$emit('clear-filters')"
            class="btn btn-secondary"
          >
            Clear filters
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "RecordsFilter",
  props: {
    showFilterSection: {
      type: Boolean,
      required: true,
    },
    filterOptions: {
      type: Object,
      required: true,
    },
    showChangeFilters: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["update:filter-options", "clear-filters"],
  methods: {
    toggleFilter(filterType, value) {
      const mapping = {
        class: "selectedClasses",
        type: "selectedTypes",
        placement: "selectedPlacements",
        gainLoss: "selectedGainLoss",
      };
      const arrayKey = mapping[filterType];
      const updatedOptions = { ...this.filterOptions };
      const index = updatedOptions[arrayKey].indexOf(value);

      if (index === -1) {
        updatedOptions[arrayKey].push(value);
      } else {
        updatedOptions[arrayKey].splice(index, 1);
      }

      this.$emit("update:filter-options", updatedOptions);
    },
    toggleUnchangedFilter() {
      const updatedOptions = {
        ...this.filterOptions,
        selectedUnchanged: !this.filterOptions.selectedUnchanged,
      };
      this.$emit("update:filter-options", updatedOptions);
    },
    clearAllFilters() {
      this.$emit("clear-filters");
    },
  },
};
</script>

<style scoped>
.filter-section {
  padding: 20px;
  width: 100%;
  max-width: 100%;
}

.filter-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 0px 15px rgba(0, 0, 0, 0.75);
  border: 1px solid var(--color-border-soft);
  width: 100%;
  box-sizing: border-box;
}

.filter-columns {
  display: flex;
  gap: 48px;
  align-items: flex-start;
  justify-content: center;
  padding-bottom: 15px;
}

.no-padding-bottom {
  padding-bottom: 0;
}

.no-margin-top {
  margin-top: 0 !important;
}

.filter-group {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.filter-title {
  font-weight: bold;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-align: center;
}

.class-filter-container,
.type-filter-container {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
}

.filter-button {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 2px solid rgba(68, 68, 68, 0.3);
  border-radius: 8px;
  padding: 8px 16px;
  margin: 0 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: bold;
  font-size: 13px;
  text-transform: capitalize;
  box-shadow: 0 0px 15px rgb(0, 0, 0, 0.35);
}

.filter-button.active {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  border-color: var(--color-border, #444);
}

.filter-button:hover:not(.active) {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
}

.gain-loss-filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.group-filter-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
}

.group-filters {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.group-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 12px;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  min-width: 28px;
  text-align: center;
}

.group-badge.group-wr {
  background: #ffd700 !important;
  color: var(--color-dark) !important;
}

.group-badge.group-tt {
  background: #30869b !important;
  color: var(--color-dark) !important;
}

.group-badge.group-1 {
  background: #ff9797af !important;
  color: var(--color-dark) !important;
}

.group-badge.group-2 {
  background: #f7cf84 !important;
  color: var(--color-dark) !important;
}

.group-badge.group-3 {
  background: #d27d2dbb !important;
  color: var(--color-dark) !important;
}

.group-badge.group-4 {
  background: #b3b3b3ce !important;
  color: var(--color-dark) !important;
}

.group-badge.group-5 {
  background: #e4e4e4ce !important;
  color: var(--color-dark) !important;
}

.filter-actions {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-top: 25px;
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
</style>
