<template>
  <div v-if="show" class="map-search-overlay">
    <div class="map-search-container">
      <div class="class-selection">
        <h3>Select a class:</h3>
        <div class="class-icons">
          <div
            class="class-option"
            :class="{ active: selectedClass === 'soldier' }"
            @click="selectClass('soldier')"
          >
            <img
              src="/icons/soldier.png"
              alt="Soldier"
              class="class-icon"
              loading="lazy"
            />
            <span>Soldier</span>
          </div>
          <div
            class="class-option"
            :class="{ active: selectedClass === 'demoman' }"
            @click="selectClass('demoman')"
          >
            <img
              src="/icons/demoman.png"
              alt="Demoman"
              class="class-icon"
              loading="lazy"
            />
            <span>Demoman</span>
          </div>
        </div>
      </div>

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
          placeholder="Search maps..."
          class="search-input"
          @input="$emit('search', searchQuery)"
        />
      </div>

      <div
        class="search-results-dropdown"
        v-if="searchResults && searchResults.length > 0"
      >
        <div>
          <ul>
            <li
              v-for="map in searchResults"
              :key="map.id"
              @click="handleMapClick(map)"
              :class="{ disabled: !selectedClass }"
            >
              {{ map.name || `Map ID: ${map.id}` }}
            </li>
          </ul>
        </div>
      </div>

      <div v-if="showClassWarning" class="class-warning-popup">
        Select a class first
      </div>

      <div class="search-buttons">
        <button
          v-if="hasMapAtCurrentIndex"
          @click="$emit('remove-map')"
          class="btn remove-button"
        >
          Remove Map
        </button>
        <button @click="cancel" class="btn cancel-button">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "MapSearchModal",
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    searchResults: {
      type: Array,
      default: () => [],
    },
    hasMapAtCurrentIndex: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["close", "search", "select-map", "remove-map"],
  data() {
    return {
      selectedClass: null,
      searchQuery: "",
      showClassWarning: false,
      classWarningTimeout: null,
    };
  },
  methods: {
    selectClass(className) {
      this.selectedClass = className;
      this.hideClassWarning();
    },
    handleMapClick(map) {
      if (!this.selectedClass) {
        this.showClassWarningPopup();
        return;
      }
      this.$emit("select-map", map, this.selectedClass);
      this.cancel();
    },
    showClassWarningPopup() {
      this.showClassWarning = true;
      if (this.classWarningTimeout) {
        clearTimeout(this.classWarningTimeout);
      }
      this.classWarningTimeout = setTimeout(() => {
        this.hideClassWarning();
      }, 2000);
    },
    hideClassWarning() {
      this.showClassWarning = false;
      if (this.classWarningTimeout) {
        clearTimeout(this.classWarningTimeout);
        this.classWarningTimeout = null;
      }
    },
    cancel() {
      this.selectedClass = null;
      this.searchQuery = "";
      this.hideClassWarning();
      this.$emit("close");
    },
  },
  beforeUnmount() {
    if (this.classWarningTimeout) {
      clearTimeout(this.classWarningTimeout);
    }
  },
};
</script>

<style scoped>
.map-search-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.map-search-container {
  background: var(--color-dark);
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  position: relative;
  min-width: 300px;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.search-icon {
  position: absolute;
  left: 16px;
  color: #888;
  z-index: 2;
}

.search-input {
  width: 100%;
  padding: 8px 8px 8px 40px;
  background: var(--color-box);
  border: 2px solid var(--color-border-soft);
  border-radius: 12px;
  color: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
}

.search-input:focus {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  outline: none;
  box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.212);
}

.search-input::placeholder {
  color: #888;
}

.search-results-dropdown {
  background: var(--color-box);
  border: 1px solid rgba(68, 68, 68, 0.3);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
  margin-bottom: 15px;
}

.search-results-dropdown ul {
  list-style: none;
  padding: 8px;
  margin: 0;
}

.search-results-dropdown li {
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 4px;
  background: var(--color-box);
  font-weight: bold;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.search-results-dropdown li:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
}

.search-results-dropdown li:last-child {
  margin-bottom: 0;
}

.search-results-dropdown li.disabled {
  cursor: not-allowed;
  opacity: 0.5;
  background: var(--color-box);
}

.search-results-dropdown li.disabled:hover {
  background: var(--color-box);
  transform: none;
}

.search-buttons {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.cancel-button,
.remove-button {
  background: var(--color-box);
  color: var(--color-text);
  border: 1px solid var(--color-border-soft);
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.cancel-button:hover {
  color: var(--color-text);
  background: var(--color-primary);
}

.remove-button:hover {
  color: var(--color-text);
  background: #a54a4a;
}

.class-selection {
  margin-bottom: 20px;
}

.class-selection h3 {
  margin: 0 0 10px 0;
  font-size: 16px;
  color: #ffffff;
}

.class-icons {
  display: flex;
  gap: 15px;
  justify-content: center;
}

.class-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 10px;
  border: 2px solid var(--color-border-soft);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 80px;
  background: var(--color-box);
}

.class-option:hover {
  border-color: var(--color-primary);
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
}

.class-option.active {
  border-color: var(--color-primary);
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
}

.class-icon {
  width: 32px;
  height: 32px;
  margin-bottom: 5px;
}

.class-option span {
  font-size: 12px;
  color: #ffffff;
  text-transform: capitalize;
}

.class-warning-popup {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #ff4444;
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  z-index: 1001;
  pointer-events: none;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}

@media (max-width: 1400px) {
  .map-search-overlay {
    padding: 20px;
  }

  .map-search-container {
    padding: 15px;
    margin: 10px;
    max-height: 85vh;
    width: 100%;
    max-width: 400px;
  }

  .search-icon {
    left: 10px;
  }

  .search-input {
    padding: 10px 10px 10px 35px;
    font-size: 16px;
  }

  .search-results-dropdown {
    max-height: 150px;
  }

  .search-results-dropdown li {
    padding: 8px 10px;
    font-size: 14px;
  }

  .cancel-button {
    padding: 8px 16px;
    min-width: 70px;
  }

  .class-icons {
    gap: 10px;
  }

  .class-option {
    min-width: 70px;
    padding: 8px;
  }

  .class-icon {
    width: 28px;
    height: 28px;
  }

  .class-option span {
    font-size: 11px;
  }
}
</style>
