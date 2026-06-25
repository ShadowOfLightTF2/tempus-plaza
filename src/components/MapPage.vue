<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="w-95 mx-auto d-flex flex-column align-items-center py-4">
      <div class="content-container">
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">🔍</span>
            Map Page
          </h1>
          <p class="page-subtitle">Records and information about the map</p>
        </div>
        <hr class="row-divider" style="width: 100%" />
        <div
          v-if="mapNotFound"
          class="alert alert-warning map-not-found text-center"
        >
          <p class="mb-3">No map found</p>
          <button @click="returnToAllMaps" class="btn return-button">
            Return to all maps
          </button>
        </div>
        <div v-else class="w-100">
          <div class="map-name-container">
            <SmartLink
              tag="div"
              :to="{ name: 'LookupMap', params: { mapId: mapId } }"
              class="lookup-map-banner fancy-hover"
              :style="{
                backgroundImage: `linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/map-backgrounds/${
                  map?.name || mapName
                }.webp')`,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
              }"
            >
              <div class="lookup-banner-content">
                <div class="lookup-map-main">
                  <h1
                    class="lookup-map-name"
                    v-html="mapName || 'Selected Map'"
                  ></h1>
                  <div v-if="map" class="map-primary-info">
                    <div class="map-tiers">
                      <div class="tier-group">
                        <img
                          src="/icons/soldier.png"
                          alt="Soldier"
                          class="tier-class-icon"
                        />
                        <span class="tier-text">T{{ map.soldier_tier }}</span>
                        <span class="rating-text"
                          >R{{ map.soldier_rating }}</span
                        >
                      </div>
                      <span class="tier-divider">•</span>
                      <div class="tier-group">
                        <img
                          src="/icons/demoman.png"
                          alt="Demoman"
                          class="tier-class-icon"
                        />
                        <span class="tier-text">T{{ map.demoman_tier }}</span>
                        <span class="rating-text"
                          >R{{ map.demoman_rating }}</span
                        >
                      </div>
                    </div>
                    <div
                      v-if="map.intended_class"
                      :class="[
                        'intended-class-display',
                        { 'no-circle': map.intended_class === 5 },
                      ]"
                    >
                      <div v-if="map.intended_class === 5" class="both-classes">
                        <div class="class-circle">
                          <img
                            src="/icons/soldier.png"
                            alt="Soldier"
                            class="intended-class-icon"
                          />
                        </div>
                        <div class="class-circle">
                          <img
                            src="/icons/demoman.png"
                            alt="Demoman"
                            class="intended-class-icon"
                          />
                        </div>
                      </div>
                      <img
                        v-else-if="isIntendedClass('soldier')"
                        src="/icons/soldier.png"
                        alt="Soldier"
                        class="intended-class-icon"
                      />
                      <img
                        v-else-if="isIntendedClass('demoman')"
                        src="/icons/demoman.png"
                        alt="Demoman"
                        class="intended-class-icon"
                      />
                    </div>
                  </div>
                </div>
                <div v-if="map" class="lookup-map-secondary">
                  <div class="secondary-left">
                    <template v-if="mapTags && mapTags.length">
                      <i class="bi bi-tags-fill tag-icon"></i>
                      <template
                        v-for="(tag, idx) in mapTags"
                        :key="tag.id || idx"
                      >
                        <span class="secondary-stat">{{
                          tag.name || tag
                        }}</span>
                        <span
                          v-if="idx < mapTags.length - 1"
                          class="stat-separator"
                          >•</span
                        >
                      </template>
                    </template>
                  </div>
                  <div class="secondary-center">
                    <template
                      v-for="(stat, idx) in secondaryStatsList"
                      :key="idx"
                    >
                      <span class="secondary-stat">{{ stat }}</span>
                      <span
                        v-if="idx < secondaryStatsList.length - 1"
                        class="stat-separator"
                        >•</span
                      >
                    </template>
                  </div>
                  <div class="secondary-right">
                    <span class="secondary-stat"
                      >Added {{ formatDate(map.date_added) }}</span
                    >
                  </div>
                </div>
                <div v-else class="loading-ranks">
                  <div class="loading-spinner"></div>
                  <span>Loading...</span>
                </div>
                <button
                  class="banner-tag-btn"
                  title="Vote for tag(s)"
                  @click.stop.prevent="showTagModal = true"
                >
                  <i class="bi bi-tag-fill"></i>
                </button>
                <a
                  :href="`https://static.tempus2.xyz/tempus/server/maps/${mapName}.bsp.bz2`"
                  class="download-btn"
                  :download="`${mapName}.bsp.bz2`"
                  title="Download Map"
                  @click.stop
                >
                  <i class="bi bi-download"></i>
                </a>
              </div>
            </SmartLink>
          </div>
          <hr class="row-divider" style="width: 100%" />
          <div class="collapsible-header" @click="toggleMapInformation">
            <h5 class="video-section-title mb-0">
              🗺️ More Map Information
              <i
                class="bi"
                :class="
                  showMapInformation ? 'bi-chevron-up' : 'bi-chevron-down'
                "
                style="margin-left: auto"
              ></i>
            </h5>
          </div>
          <div v-if="showMapInformation" class="collapsible-content">
            <MapInformation
              v-if="map"
              :map="map"
              :courses="courses"
              :bonuses="bonuses"
              :authors="authors"
              :map-tags="mapTags"
              @show-tag-modal="showTagModal = true"
            />
          </div>
          <div class="row main-content-wrapper">
            <div class="col-md-12 map-details">
              <WRVideos
                :map="map"
                :courses="courses"
                :bonuses="bonuses"
                :rotw-videos="rotwVideos"
              />
              <Leaderboard
                :map-id="mapId"
                :type="type"
                :index="index"
                :intended-class="map ? map.intended_class : null"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <TagModal
    v-model:showTagModal="showTagModal"
    :available-tags="availableTags"
    :selected-map-tags="selectedMapTags"
    :map-id="mapId"
    :player-id="playerId"
    @save-tags="handleSaveTags"
    @close="handleTagModalClose"
  />
</template>

<script>
import axios from "axios";
import Leaderboard from "./Leaderboard.vue";
import MapInformation from "./MapInformation.vue";
import WRVideos from "./WRVideos.vue";
import TagModal from "./TagModal.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "MapPage",
  components: {
    Leaderboard,
    MapInformation,
    WRVideos,
    TagModal,
  },
  props: {
    mapId: {
      type: Number,
      required: true,
    },
    type: {
      type: String,
      default: "map",
      validator: (val) => ["map", "course", "bonus"].includes(val),
    },
    index: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      map: null,
      mapName: "",
      authors: [],
      courses: [],
      bonuses: [],
      showTagModal: false,
      availableTags: [],
      mapTags: [],
      selectedMapTags: [],
      rotwVideos: [],
      showMapInformation: false,
      mapNotFound: false,
      playerId: null,
    };
  },
  async mounted() {
    await this.fetchUser();
    await this.fetchAllMapData(this.mapId);
    await this.loadTags(this.mapId);
    await this.loadMapTags(this.mapId);
  },
  watch: {
    mapId: {
      immediate: false,
      async handler(newMapId, oldMapId) {
        if (newMapId !== oldMapId) {
          this.resetComponentState();
          await this.loadTags(this.mapId);
          await this.loadMapTags(this.mapId);
          await this.fetchAllMapData(newMapId);
        }
      },
    },
  },
  computed: {
    secondaryStatsList() {
      if (!this.map) return [];
      const stats = [];
      if (this.map.course_count > 0) {
        stats.push(
          `${this.map.course_count} course${this.map.course_count > 1 ? "s" : ""}`,
        );
      }
      if (this.map.bonus_count > 0) {
        stats.push(
          `${this.map.bonus_count} bonus${this.map.bonus_count > 1 ? "es" : ""}`,
        );
      }
      stats.push(`${this.map.soldier_completion_count || 0} [S] completions`);
      stats.push(`${this.map.demoman_completion_count || 0} [D] completions`);
      return stats;
    },
  },
  methods: {
    handleSaveTags(newSelectedTags) {
      this.selectedMapTags = newSelectedTags;
    },
    handleTagModalClose() {
      this.loadMapTags(this.mapId);
    },
    async loadTags(mapId = null) {
      try {
        const tagsResponse = await fetch(`${API_BASE_URL}/maps/tags`);
        const tagsData = await tagsResponse.json();

        let voteCountsByName = {};
        let playerVotedTags = [];

        if (mapId) {
          try {
            const votesResponse = await fetch(
              `${API_BASE_URL}/maps/${mapId}/map-tag-votes`,
            );
            const votesData = await votesResponse.json();

            if (votesData && votesData[0]) {
              votesData[0].forEach((tagVote) => {
                voteCountsByName[tagVote.name] = tagVote.votes;

                if (this.playerId && tagVote.player_ids) {
                  const playerIds = JSON.parse(tagVote.player_ids);
                  if (playerIds.includes(this.playerId)) {
                    playerVotedTags.push(tagVote.name);
                  }
                }
              });
            }
          } catch (voteError) {
            console.log("Could not load vote counts:", voteError);
          }
        }

        if (Array.isArray(tagsData)) {
          this.availableTags = tagsData.map((tag) => ({
            ...tag,
            votes: voteCountsByName[tag.name] || 0,
            playerVoted: playerVotedTags.includes(tag.name),
          }));

          this.selectedMapTags = this.availableTags
            .filter((tag) => tag.playerVoted)
            .map((tag) => tag.id);
        } else {
          console.error("Expected tags data to be an array:", tagsData);
          this.availableTags = [];
        }
      } catch (error) {
        console.error("Error loading tags:", error);
      }
    },
    async loadMapTags(mapId) {
      try {
        const response = await fetch(`${API_BASE_URL}/maps/${mapId}/tags`);
        const data = await response.json();

        this.mapTags = [...data.tags].sort((a, b) => a.id - b.id);
      } catch (error) {
        console.error("Error loading map tags:", error);
      }
    },
    async fetchUser() {
      try {
        const res = await fetch(`${API_BASE_URL}/api/get-user`, {
          credentials: "include",
        });
        const data = await res.json();
        this.playerId = data.data?.playerid || null;
      } catch (err) {
        console.error("Error fetching user:", err);
        this.playerId = null;
      }
    },
    returnToAllMaps() {
      this.$router.push({ name: "Maps" });
    },
    goBack() {
      this.$router.go(-1);
    },
    async fetchAllMapData(mapId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/${mapId}/all-info`,
        );
        if (!response.data.map) {
          this.mapNotFound = true;
          return;
        }
        this.map = response.data.map;
        this.mapName = response.data.map.name;
        this.authors = response.data.authors;
        this.courses = (response.data.courses || []).sort(
          (a, b) => a.index - b.index,
        );
        this.bonuses = (response.data.bonuses || []).sort(
          (a, b) => a.index - b.index,
        );
        this.rotwVideos = response.data.rotw_videos || [];
      } catch (error) {
        this.error = "Failed to fetch map data. Please try again later.";
        console.error("Error fetching map data:", error);
        this.mapNotFound = true;
      }
    },
    formatDate(unixTimestamp) {
      const date = new Date(unixTimestamp * 1000);
      const day = String(date.getDate()).padStart(2, "0");
      const monthNames = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];
      const monthName = monthNames[date.getMonth()];
      const year = date.getFullYear();
      return `${day} ${monthName} ${year}`;
    },
    toggleMapInformation() {
      this.showMapInformation = !this.showMapInformation;
    },
    isIntendedClass(className) {
      if (!this.map || !this.map.intended_class) return false;

      const intendedClass = this.map.intended_class;

      if (intendedClass === 5) return true;
      if (intendedClass === 3 && className === "soldier") return true;
      if (intendedClass === 4 && className === "demoman") return true;

      return false;
    },
    resetComponentState() {
      this.map = null;
      this.mapName = "";
      this.authors = [];
      this.courses = [];
      this.bonuses = [];
      this.rotwVideos = [];
      this.showMapInformation = true;
      this.mapNotFound = false;
    },
  },
};
</script>

<style scoped>
.content-container {
  max-width: 1320px;
  width: 100%;
}
.return-button {
  background: var(--color-box);
  color: var(--color-text);
  border-radius: 8px;
  padding: 12px 24px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.return-button:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  color: var(--color-text);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.download-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 6px;
  padding: 6px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text);
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 1rem;
  pointer-events: auto;
  z-index: 10;
}

.download-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
  color: var(--color-text);
  text-decoration: none;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.download-btn:active {
  transform: translateY(0);
}

.lookup-map-banner {
  width: 100%;
  max-width: 1000px;
  margin: 20px auto;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  position: relative;
}

.lookup-map-banner::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.2),
    rgba(74, 111, 165, 0.1)
  );
  pointer-events: none;
}

.lookup-map-banner .lookup-banner-content {
  display: flex;
  flex-direction: column;
  padding: 24px 32px;
  gap: 12px;
  transition: all 0.3s ease;
  min-height: 188px;
}

.lookup-map-banner .lookup-banner-content:hover {
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
}

.banner-tag-btn {
  position: absolute;
  top: 16px;
  left: 16px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 6px;
  padding: 6px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1rem;
  pointer-events: auto;
  z-index: 10;
}

.banner-tag-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
  color: var(--color-text);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.banner-tag-btn:active {
  transform: translateY(0);
}

.lookup-map-main {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.lookup-map-name {
  font-size: 2.25rem;
  font-weight: 700;
  margin: auto;
  width: 80%;
  color: #ffffff;
  text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.8);
  text-align: center;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.map-primary-info {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 24px;
  flex-wrap: wrap;
}

.map-tiers {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 1.25rem;
  font-weight: 700;
  color: #ffffff;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
}

.tier-group {
  display: flex;
  align-items: center;
  gap: 8px;
}

.tier-class-icon {
  width: 28px;
  height: 28px;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
}

.tier-text {
  font-size: 1.25rem;
  letter-spacing: 0.5px;
}

.rating-text {
  font-size: 1.25rem;
  color: rgba(255, 255, 255, 0.9);
  letter-spacing: 0.5px;
}

.tier-divider {
  color: rgba(255, 255, 255, 0.5);
  font-size: 1rem;
}

.intended-class-display {
  display: flex;
  align-items: center;
  border-radius: 50%;
  padding: 8px;
  background: rgba(0, 0, 0, 0.3);
  border: 2px solid rgba(74, 111, 165, 0.3);
}

.intended-class-display.no-circle {
  background: none;
  border: none;
  padding: 0;
}

.intended-class-icon {
  width: 24px;
  height: 24px;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
}

.both-classes {
  display: flex;
  align-items: center;
  gap: 4px;
}

.class-circle {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  padding: 8px;
  background: rgba(0, 0, 0, 0.3);
  border: 2px solid rgba(74, 111, 165, 0.3);
  background: #000000b3;
}

.lookup-map-secondary {
  display: flex;
  align-items: center;
  padding-top: 8px;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.tag-icon {
  margin-right: 6px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
  flex-shrink: 0;
}

.secondary-left,
.secondary-right {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 4px;
  flex-wrap: wrap;
}

.secondary-left {
  justify-content: flex-start;
}

.secondary-right {
  justify-content: flex-end;
}

.secondary-center {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
}

.secondary-stat {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 500;
}

.stat-separator {
  color: rgba(255, 255, 255, 0.4);
  font-size: 0.75rem;
}

.collapsible-header {
  cursor: pointer;
  padding: 8px 12px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  margin-bottom: 16px;
  transition: all 0.3s ease;
}

.collapsible-header:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.collapsible-header .video-section-title {
  color: var(--color-text);
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  border-bottom: none;
  padding-bottom: 0;
}

.collapsible-content {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
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

.main-content-wrapper {
  margin-top: 20px;
}

@media (max-width: 767.98px) {
  .lookup-map-banner .lookup-banner-content {
    padding: 20px 16px;
  }

  .lookup-map-name {
    font-size: 1.5rem;
    width: 100%;
  }

  .map-tiers {
    font-size: 1rem;
    justify-content: center;
  }

  .tier-class-icon {
    width: 24px;
    height: 24px;
  }

  .tier-text,
  .rating-text {
    font-size: 1rem;
  }

  .intended-class-display {
    padding: 5px;
  }

  .intended-class-icon {
    width: 20px;
    height: 20px;
  }

  .lookup-map-secondary {
    flex-direction: column;
    align-items: center;
    gap: 6px;
  }

  .secondary-left,
  .secondary-center,
  .secondary-right {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    flex-wrap: wrap;
    width: 100%;
    max-width: 100%;
  }

  .secondary-left {
    justify-content: center;
    order: 1;
  }

  .secondary-center {
    justify-content: center;
    order: 2;
  }

  .secondary-right {
    justify-content: center;
    order: 3;
  }

  .secondary-stat {
    font-size: 0.75rem;
  }
}
</style>
