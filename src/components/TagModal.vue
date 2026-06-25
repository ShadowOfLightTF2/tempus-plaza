<template>
  <div v-if="showTagModal" class="tag-modal-backdrop" @click="closeModal">
    <div class="tag-modal" @click.stop>
      <div class="tag-modal-header">
        <h3><i class="bi bi-tags-fill me-2"></i>Vote for Map Tags</h3>
        <button @click="closeModal" class="close-btn">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <div class="tag-modal-body">
        <div class="tag-description">
          <div class="beta-notice">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>This is a beta test for the tagging system</strong>
          </div>
          <p class="description-text">
            Vote for tags that describe the majority of this map's gameplay. A
            tag needs at least
            <strong>2 votes (eventually it will be more)</strong> to be applied
            to the map. You can vote for multiple tags, but only the
            <strong>top most voted</strong> tags will be displayed on the map.
            Currently only maps can have tags (courses/bonuses might come later)
          </p>
          <div class="warning-notice">
            <i class="bi bi-eye me-2"></i>
            <em
              >Note: All votes are tracked and monitored. Please vote
              responsibly and don't abuse the system.</em
            >
          </div>
        </div>

        <div class="tag-section">
          <h4><i class="bi bi-map me-2"></i>Map Tags</h4>

          <div class="tag-class-group" v-if="tagsByClass('soldier').length > 0">
            <h5>
              <img
                src="/icons/soldier.png"
                alt="Soldier"
                class="class-icon me-1"
              />Soldier
            </h5>
            <div class="tag-selector">
              <div class="available-tags">
                <div
                  v-for="tag in tagsByClass('soldier')"
                  :key="'map-soldier-' + tag.id"
                  class="tag-option"
                  :class="{ active: isSelected(tag.id) }"
                  @click="toggleTag(tag.id)"
                  :style="{
                    backgroundColor: isSelected(tag.id)
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
                  <span class="vote-count" v-if="tag.votes > 0"
                    >({{ tag.votes }})</span
                  >
                </div>
              </div>
            </div>
          </div>

          <div class="tag-class-group" v-if="tagsByClass('demoman').length > 0">
            <h5>
              <img
                src="/icons/demoman.png"
                alt="Demoman"
                class="class-icon me-1"
              />Demoman
            </h5>
            <div class="tag-selector">
              <div class="available-tags">
                <div
                  v-for="tag in tagsByClass('demoman')"
                  :key="'map-demoman-' + tag.id"
                  class="tag-option"
                  :class="{ active: isSelected(tag.id) }"
                  @click="toggleTag(tag.id)"
                  :style="{
                    backgroundColor: isSelected(tag.id)
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
                  <span class="vote-count" v-if="tag.votes > 0"
                    >({{ tag.votes }})</span
                  >
                </div>
              </div>
            </div>
          </div>

          <div class="tag-class-group" v-if="tagsByClass('both').length > 0">
            <h5><i class="bi bi-people me-1"></i>Both Classes</h5>
            <div class="tag-selector">
              <div class="available-tags">
                <div
                  v-for="tag in tagsByClass('both')"
                  :key="'map-both-' + tag.id"
                  class="tag-option"
                  :class="{ active: isSelected(tag.id) }"
                  @click="toggleTag(tag.id)"
                  :style="{
                    backgroundColor: isSelected(tag.id)
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
                  <span class="vote-count" v-if="tag.votes > 0"
                    >({{ tag.votes }})</span
                  >
                </div>
              </div>
            </div>
          </div>

          <div class="tag-class-group" v-if="tagsByClass('extra').length > 0">
            <h5><i class="bi bi-gear me-1"></i>Extra</h5>
            <div class="tag-selector">
              <div class="available-tags">
                <div
                  v-for="tag in tagsByClass('extra')"
                  :key="'map-extra-' + tag.id"
                  class="tag-option"
                  :class="{ active: isSelected(tag.id) }"
                  @click="toggleTag(tag.id)"
                  :style="{
                    backgroundColor: isSelected(tag.id)
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
                  <span class="vote-count" v-if="tag.votes > 0"
                    >({{ tag.votes }})</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="tag-modal-footer">
        <button @click="saveTags" class="save-tags-btn">
          <i class="bi bi-check-lg me-2"></i>Save Changes
        </button>
        <button @click="closeModal" class="cancel-btn">Cancel</button>
      </div>
    </div>
  </div>

  <div
    v-if="showLoginRequired"
    class="login-popup-backdrop"
    @click="showLoginRequired = false"
  >
    <div class="login-popup" @click.stop>
      <div class="login-popup-content">
        <h4>Login Required</h4>
        <p>You need to be logged in to vote on map tags</p>
        <div class="login-popup-actions">
          <button @click="loginWithSteam" class="btn-login">
            <i class="bi bi-steam"></i> Login with Steam
          </button>
          <button @click="showLoginRequired = false" class="btn-cancel">
            Maybe Later
          </button>
        </div>
      </div>
      <button @click="showLoginRequired = false" class="login-popup-close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  </div>
</template>

<script>
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "TagModal",
  props: {
    showTagModal: { type: Boolean, default: false },
    availableTags: { type: Array, default: () => [] },
    selectedMapTags: { type: Array, default: () => [] },
    mapId: { type: Number, required: true },
    playerId: { type: [String, Number], default: null },
  },
  emits: ["update:showTagModal", "save-tags", "close"],
  data() {
    return {
      localSelected: [...this.selectedMapTags],
      showLoginRequired: false,
    };
  },
  watch: {
    selectedMapTags(newVal) {
      this.localSelected = [...newVal];
    },
    showTagModal(isVisible) {
      if (isVisible) {
        this.localSelected = [...this.selectedMapTags];
      }
    },
  },
  methods: {
    closeModal() {
      this.$emit("update:showTagModal", false);
      this.$emit("close");
    },
    loginWithSteam() {
      window.location.href = `${API_BASE_URL}/auth/steam`;
    },
    async saveTags() {
      if (!this.playerId) {
        this.showLoginRequired = true;
        return;
      }

      try {
        const response = await fetch(
          `${API_BASE_URL}/maps/${this.mapId}/${this.playerId}/vote-tags`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ mapTags: this.localSelected }),
          },
        );

        if (response.ok) {
          this.$emit("save-tags", this.localSelected);
          this.closeModal();
        } else {
          const errorText = await response.text();
          console.error("Failed to save tags:", response.status, errorText);
        }
      } catch (error) {
        console.error("Error saving tags:", error);
      }
    },
    tagsByClass(className) {
      return this.availableTags.filter((tag) => tag.class === className);
    },
    isSelected(tagId) {
      return this.localSelected.includes(tagId);
    },
    toggleTag(tagId) {
      const index = this.localSelected.indexOf(tagId);
      if (index > -1) {
        this.localSelected.splice(index, 1);
      } else {
        this.localSelected.push(tagId);
      }
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
.save-tags-btn {
  background: var(--color-primary);
  color: var(--color-text);
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}
.save-tags-btn:hover {
  background: var(--color-primary-dark);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
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
.vote-count {
  color: #666;
  font-size: 0.9em;
  margin-left: 0.25rem;
  opacity: 0.8;
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
.beta-notice {
  background-color: #fff3cd;
  border: 1px solid #ffeaa7;
  border-radius: 6px;
  padding: 0.75rem;
  margin-bottom: 1rem;
  color: #856404;
  font-size: 0.9rem;
}
.beta-notice i {
  color: #f39c12;
}
.warning-notice {
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 6px;
  padding: 0.75rem;
  margin-top: 1rem;
  color: #721c24;
  font-size: 0.85rem;
}
.warning-notice i {
  color: #dc3545;
}
.login-popup-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}
.login-popup {
  background: #343a40;
  border-radius: 1rem;
  padding: 2rem;
  max-width: 400px;
  width: 90%;
  margin: 1rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
  position: relative;
  text-align: center;
  animation: popupSlideIn 0.3s ease-out;
  border: 1px solid #495057;
}
.login-popup-content h4 {
  color: #f8f9fa;
  margin-bottom: 0.5rem;
  font-size: 1.4rem;
  font-weight: 600;
}
.login-popup-content p {
  color: #adb5bd;
  margin-bottom: 2rem;
  line-height: 1.5;
  font-size: 1rem;
}
.login-popup-actions {
  display: flex;
  gap: 1rem;
  flex-direction: column;
}
.btn-login {
  background: var(--color-primary);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}
.btn-login:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
.btn-cancel {
  background: transparent;
  color: #adb5bd;
  border: 1px solid #495057;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 1rem;
}
.btn-cancel:hover {
  background: #495057;
  color: #f8f9fa;
  border-color: #6c757d;
}
.login-popup-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #adb5bd;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}
.login-popup-close:hover {
  background: #495057;
  color: #f8f9fa;
}
@keyframes popupSlideIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
@media (max-width: 768px) {
  .tag-modal {
    width: 95%;
    max-height: 90vh;
  }
}
@media (max-width: 576px) {
  .login-popup {
    padding: 1.5rem;
    margin: 0.5rem;
  }
  .login-popup-actions {
    gap: 0.75rem;
  }
  .btn-login,
  .btn-cancel {
    padding: 0.875rem 1.25rem;
    font-size: 0.95rem;
  }
}
</style>
