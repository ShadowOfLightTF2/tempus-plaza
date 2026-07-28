<template>
  <div v-if="show" class="tag-modal-backdrop" @click="close">
    <div class="tag-modal" @click.stop>
      <div class="tag-modal-header">
        <h3><i class="bi bi-funnel-fill me-2"></i>Filter by Tags</h3>
        <button @click="close" class="close-btn">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <div class="tag-modal-description">
        <p class="description-text">
          <i class="bi bi-info-circle me-2"></i>
          Filter records by the gameplay characteristics and features of their
          map. Note that not every map has tags yet, you can help by voting for
          tags on the individual map pages to improve the filtering system for
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
          <div class="tag-class-group" v-if="getTagsByClass('both').length > 0">
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
      <div class="tag-modal-footer">
        <button
          type="button"
          @click="clearTags"
          class="btn btn-secondary clear-tags-btn"
          :disabled="selectedTags.length === 0"
        >
          <i class="bi bi-x-circle me-1"></i>Clear tags
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TagFilterModal",
  props: {
    show: { type: Boolean, default: false },
    availableTags: { type: Array, default: () => [] },
    selectedTags: { type: Array, default: () => [] },
  },
  emits: ["close", "toggle-tag", "clear-tags"],
  methods: {
    close() {
      this.$emit("close");
    },
    toggleTag(tagId) {
      this.$emit("toggle-tag", tagId);
    },
    clearTags() {
      this.$emit("clear-tags");
    },
    isTagSelected(tagId) {
      return this.selectedTags.includes(tagId);
    },
    getTagsByClass(className) {
      return this.availableTags.filter((tag) => tag.class === className);
    },
  },
};
</script>

<style scoped>
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

.tag-class-group {
  margin-bottom: 20px;
  padding: 16px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.tag-class-group .class-icon {
  width: 20px;
  height: 20px;
  margin: 0;
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

.tag-option:hover tag-modal-footer {
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

.clear-tags-btn {
  display: inline-flex;
  align-items: center;
}
.clear-tags-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
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

@media (max-width: 767.98px) {
  .tag-modal {
    width: 95%;
    max-height: 90vh;
  }
}
</style>
