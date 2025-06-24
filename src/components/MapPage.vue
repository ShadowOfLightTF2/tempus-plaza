<template>
  <div
    class="container map-page py-4"
    style="background: var(--color-background)"
  >
    <div
      v-if="mapNotFound"
      class="alert alert-warning map-not-found text-center"
    >
      <p class="mb-3">No map found</p>
      <button @click="returnToAllMaps" class="btn return-button">
        Return to all maps
      </button>
    </div>
    <div v-else>
      <div
        v-if="map"
        class="card map-banner mb-4"
        :style="{
          background: `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('/maps/${map.name}.jpg') center/cover no-repeat`,
          backgroundBlendMode: 'multiply',
          backgroundSize: 'cover',
          backgroundPosition: 'center',
        }"
      >
        <div
          class="row g-0 banner-content"
          :style="{
            background:
              'linear-gradient(135deg, var(--color-box), var(--color-row))',
          }"
        >
          <div class="col-md-12 d-flex flex-column align-items-center p-4">
            <button @click="goBack" class="btn btn-dark back-button">
              <i class="bi bi-arrow-left me-2"></i>Back
            </button>
            <div class="map-info text-center w-100">
              <h1 v-if="map.name" class="map-name mb-4">{{ map.name }}</h1>
              <div class="row p-3 map-overview w-100 g-3">
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="card stat-block h-100">
                    <div class="card-body text-center p-3">
                      <div class="stat-icon mb-2">
                        <i class="bi bi-bar-chart"></i>
                      </div>
                      <h5 class="card-title mb-2">Soldier Tier</h5>
                      <p
                        class="card-text main-tier-color stat-value"
                        :class="'tier-' + map.soldier_tier"
                      >
                        T{{ map.soldier_tier }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="card stat-block h-100">
                    <div class="card-body text-center p-3">
                      <div class="stat-icon mb-2">
                        <i class="bi bi-star-fill"></i>
                      </div>
                      <h5 class="card-title mb-2">Soldier Rating</h5>
                      <p
                        class="card-text main-rating-color stat-value"
                        :class="'rating-' + map.soldier_rating"
                      >
                        R{{ map.soldier_rating }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="card stat-block h-100">
                    <div class="card-body text-center p-3">
                      <div class="stat-icon mb-2">
                        <i class="bi bi-bar-chart"></i>
                      </div>
                      <h5 class="card-title mb-2">Demoman Tier</h5>
                      <p
                        class="card-text main-tier-color stat-value"
                        :class="'tier-' + map.demoman_tier"
                      >
                        T{{ map.demoman_tier }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="card stat-block h-100">
                    <div class="card-body text-center p-3">
                      <div class="stat-icon mb-2">
                        <i class="bi bi-star-fill"></i>
                      </div>
                      <h5 class="card-title mb-2">Demoman Rating</h5>
                      <p
                        class="card-text main-rating-color stat-value"
                        :class="'rating-' + map.demoman_rating"
                      >
                        R{{ map.demoman_rating }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <hr class="row-divider" style="width: 85%" />
              <div class="row w-100" v-if="courses.length > 0">
                <div class="col-md-12">
                  <h2 class="section-header mb-4">
                    <i class="me-2"></i>🚩 Courses
                  </h2>
                  <div class="courses-block">
                    <div
                      v-for="course in courses"
                      :key="course.id"
                      class="course-item"
                    >
                      <div class="card stat-block h-100">
                        <div class="card-body text-center p-3">
                          <h5 class="card-title mb-3">
                            Course {{ course.index }}
                          </h5>
                          <div class="compact-ratings-grid">
                            <div class="rating-section">
                              <div class="rating-label">Soldier</div>
                              <div class="rating-pills">
                                <span
                                  class="rating-pill tier-color"
                                  :class="'tier-' + course.soldier_tier"
                                >
                                  T{{ course.soldier_tier }}
                                </span>
                                <span
                                  class="rating-pill rating-color"
                                  :class="'rating-' + course.soldier_rating"
                                >
                                  R{{ course.soldier_rating }}
                                </span>
                              </div>
                            </div>
                            <div class="rating-section">
                              <div class="rating-label">Demoman</div>
                              <div class="rating-pills">
                                <span
                                  class="rating-pill tier-color"
                                  :class="'tier-' + course.demoman_tier"
                                >
                                  T{{ course.demoman_tier }}
                                </span>
                                <span
                                  class="rating-pill rating-color"
                                  :class="'rating-' + course.demoman_rating"
                                >
                                  R{{ course.demoman_rating }}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <hr
                class="row-divider"
                style="width: 85%"
                v-if="courses.length > 0"
              />
              <div class="row w-100" v-if="bonuses.length > 0">
                <div class="col-md-12">
                  <h2 class="section-header mb-4">⭐ Bonuses</h2>
                  <div class="bonuses-block">
                    <div
                      v-for="bonus in bonuses"
                      :key="bonus.id"
                      class="bonus-item"
                    >
                      <div class="card stat-block h-100">
                        <div class="card-body text-center p-3">
                          <h5 class="card-title mb-3">
                            Bonus {{ bonus.index }}
                          </h5>
                          <div class="compact-ratings-grid">
                            <div class="rating-section">
                              <div class="rating-label">Soldier</div>
                              <div class="rating-pills">
                                <span
                                  class="rating-pill tier-color"
                                  :class="'tier-' + bonus.soldier_tier"
                                >
                                  T{{ bonus.soldier_tier }}
                                </span>
                                <span
                                  class="rating-pill rating-color"
                                  :class="'rating-' + bonus.soldier_rating"
                                >
                                  R{{ bonus.soldier_rating }}
                                </span>
                              </div>
                            </div>
                            <div class="rating-section">
                              <div class="rating-label">Demoman</div>
                              <div class="rating-pills">
                                <span
                                  class="rating-pill tier-color"
                                  :class="'tier-' + bonus.demoman_tier"
                                >
                                  T{{ bonus.demoman_tier }}
                                </span>
                                <span
                                  class="rating-pill rating-color"
                                  :class="'rating-' + bonus.demoman_rating"
                                >
                                  R{{ bonus.demoman_rating }}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <hr
                class="row-divider"
                style="width: 85%"
                v-if="bonuses.length > 0"
              />
              <div class="row w-100">
                <div class="col-md-12">
                  <h2 class="section-header mb-4">
                    <i class="bi bi-people-fill me-2"></i>Authors
                  </h2>
                  <div class="authors-wrapper">
                    <div class="authors-block">
                      <div
                        v-for="author in authors"
                        :key="author.author_id"
                        class="author-card"
                      >
                        <div class="author-avatar-wrapper">
                          <img
                            :src="
                              author &&
                              author.player &&
                              author.player.steam_avatar
                                ? `${author.player.steam_avatar}`
                                : '/avatars/default-avatar.jpg'
                            "
                            alt="Author Avatar"
                            class="author-avatar"
                          />
                        </div>
                        <h6 class="author-name mt-2">
                          {{
                            author.author_name
                              ? author.author_name
                              : "Unknown Author"
                          }}
                        </h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="map-date-wrapper mt-4">
                <p class="map-date-added">
                  <i class="bi bi-calendar-plus me-2"></i>
                  Added on: {{ formatDate(map.date_added) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row main-content-wrapper">
        <div class="col-md-12 map-details">
          <div
            class="card map-videos mb-4"
            style="background: var(--color-box)"
          >
            <div class="card-body p-4">
              <h2 class="section-header mb-4">
                <i class="bi bi-play-circle me-2"></i>Record videos
              </h2>
              <div class="video-section mb-5">
                <h4 class="video-section-title mb-3">🌍Map records</h4>
                <div class="row g-4">
                  <div class="col-lg-6">
                    <div class="video-card">
                      <h5 class="video-title">
                        <i class="bi bi-person-fill me-2"></i>Soldier record
                      </h5>
                      <div class="video-container">
                        <div
                          v-if="
                            map &&
                            map.soldier_video &&
                            map.soldier_video !== 'null'
                          "
                        >
                          <iframe
                            :src="
                              'https://www.youtube.com/embed/' +
                              map.soldier_video
                            "
                            frameborder="0"
                            allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                          >
                          </iframe>
                        </div>
                        <div v-else class="no-video-placeholder">
                          <i class="bi bi-camera-video-off"></i>
                          <span>No video available</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="video-card">
                      <h5 class="video-title">
                        <i class="bi bi-person-fill me-2"></i>Demoman record
                      </h5>
                      <div class="video-container">
                        <div
                          v-if="
                            map &&
                            map.demoman_video &&
                            map.demoman_video !== 'null'
                          "
                        >
                          <iframe
                            :src="
                              'https://www.youtube.com/embed/' +
                              map.demoman_video
                            "
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                          >
                          </iframe>
                        </div>
                        <div v-else class="no-video-placeholder">
                          <i class="bi bi-camera-video-off"></i>
                          <span>No video available</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="courses.length > 0" class="video-section mb-5">
                <h4 class="video-section-title mb-3">🚩Course records</h4>
                <div
                  v-for="course in courses"
                  :key="course.id"
                  class="course-videos mb-4"
                >
                  <h5 class="course-video-title mb-3">
                    Course {{ course.index }}
                  </h5>
                  <div class="row g-4">
                    <div class="col-lg-6">
                      <div class="video-card">
                        <h6 class="video-title">
                          <i class="bi bi-person-fill me-2"></i>Soldier record
                        </h6>
                        <div class="video-container">
                          <div
                            v-if="
                              course.soldier_video &&
                              course.soldier_video !== 'null'
                            "
                          >
                            <iframe
                              :src="
                                'https://www.youtube.com/embed/' +
                                course.soldier_video
                              "
                              frameborder="0"
                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                              allowfullscreen
                            >
                            </iframe>
                          </div>
                          <div v-else class="no-video-placeholder">
                            <i class="bi bi-camera-video-off"></i>
                            <span>No video available</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="video-card">
                        <h6 class="video-title">
                          <i class="bi bi-person-fill me-2"></i>Demoman record
                        </h6>
                        <div class="video-container">
                          <div
                            v-if="
                              course.demoman_video &&
                              course.demoman_video !== 'null'
                            "
                          >
                            <iframe
                              :src="
                                'https://www.youtube.com/embed/' +
                                course.demoman_video
                              "
                              frameborder="0"
                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                              allowfullscreen
                            >
                            </iframe>
                          </div>
                          <div v-else class="no-video-placeholder">
                            <i class="bi bi-camera-video-off"></i>
                            <span>No video available</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="bonuses.length > 0" class="video-section">
                <h4 class="video-section-title mb-3">⭐Bonus records</h4>
                <div
                  v-for="bonus in bonuses"
                  :key="bonus.id"
                  class="bonus-videos mb-4"
                >
                  <h5 class="bonus-video-title mb-3">
                    Bonus {{ bonus.index }}
                  </h5>
                  <div class="row g-4">
                    <div class="col-lg-6">
                      <div class="video-card">
                        <h6 class="video-title">
                          <i class="bi bi-person-fill me-2"></i>Soldier record
                        </h6>
                        <div class="video-container">
                          <div
                            v-if="
                              bonus.soldier_video &&
                              bonus.soldier_video !== 'null'
                            "
                          >
                            <iframe
                              :src="
                                'https://www.youtube.com/embed/' +
                                bonus.soldier_video
                              "
                              frameborder="0"
                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                              allowfullscreen
                            >
                            </iframe>
                          </div>
                          <div v-else class="no-video-placeholder">
                            <i class="bi bi-camera-video-off"></i>
                            <span>No video available</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="video-card">
                        <h6 class="video-title">
                          <i class="bi bi-person-fill me-2"></i>Demoman record
                        </h6>
                        <div class="video-container">
                          <div
                            v-if="
                              bonus.demoman_video &&
                              bonus.demoman_video !== 'null'
                            "
                          >
                            <iframe
                              :src="
                                'https://www.youtube.com/embed/' +
                                bonus.demoman_video
                              "
                              frameborder="0"
                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                              allowfullscreen
                            >
                            </iframe>
                          </div>
                          <div v-else class="no-video-placeholder">
                            <i class="bi bi-camera-video-off"></i>
                            <span>No video available</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const API_BASE_URL = "http://localhost:3000";

export default {
  name: "MapPage",
  props: {
    mapId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      map: null,
      authors: [],
      courses: [],
      bonuses: [],
      mapNotFound: false,
    };
  },
  async mounted() {
    await this.fetchAllMapData(this.mapId);
    document.title = "Tempus plaza - " + (this.map.name || "Map");
  },
  methods: {
    returnToAllMaps() {
      this.$router.push("/maps");
    },
    goBack() {
      this.$router.go(-1);
    },
    async fetchAllMapData(mapId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/${mapId}/all-info`
        );
        if (!response.data.map) {
          this.mapNotFound = true;
          return;
        }
        this.map = response.data.map;
        this.authors = response.data.authors;
        this.courses = response.data.courses;
        this.bonuses = response.data.bonuses;
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
  },
};
</script>

<style scoped>
.return-button {
  background: var(--color-box);
  color: var(--color-text);
  border-radius: 8px;
  padding: 12px 24px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.return-button:hover {
  background: var(--color-primary);
  color: var(--color-text);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.map-banner {
  border-radius: 16px;
  position: relative;
  overflow: hidden;
}

.banner-content {
  border-radius: 16px;
  border: 1px solid var(--color-border-soft);
  min-height: 200px;
}

.map-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  max-width: 100%;
}

.map-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  color: var(--color-text);
  margin: 0 auto;
  font-size: 2.5rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.map-videos {
  border-radius: 16px;
  border: 1px solid var(--color-border-soft);
}

.back-button {
  background: var(--color-row);
  font-weight: 600;
  position: absolute;
  top: 20px;
  left: 20px;
  border-radius: 8px;
  padding: 10px 16px;
  border: none;
  transition: all 0.3s ease;
  z-index: 10;
}

.back-button:hover {
  background: var(--color-primary);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.stat-block {
  background: var(--color-dark) !important;
  transition: all 0.3s ease;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.1);
  overflow: hidden;
}

.hover-lift:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}

.stat-icon {
  font-size: 1.5rem;
  color: var(--color-primary);
  opacity: 0.8;
}

.stat-block .card-title {
  color: var(--color-text);
  font-weight: 600;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--color-dark);
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

.section-header {
  color: var(--color-text);
  font-weight: 700;
  text-align: center;
  font-size: 1.5rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.video-section-title {
  color: var(--color-text);
  font-weight: 600;
  display: flex;
  align-items: center;
  border-bottom: 2px solid var(--color-primary);
  padding-bottom: 8px;
}

.course-video-title,
.bonus-video-title {
  color: var(--color-text);
  font-weight: bold;
  text-align: center;
  background: var(--color-dark);
  padding: 8px 16px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.video-title {
  color: var(--color-text) !important;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
}

.video-card {
  background: var(--color-dark);
  border-radius: 12px;
  padding: 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.authors-wrapper {
  display: flex;
  justify-content: center;
  width: 100%;
}

.authors-block {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
  max-width: 600px;
}

.author-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: all 0.3s ease;
}

.author-avatar-wrapper {
  position: relative;
  border-radius: 50%;
  padding: 3px;
  border: 2px solid var(--color-primary);
}

.author-name {
  color: var(--color-text);
  font-weight: 600;
  font-size: 0.9rem;
}

.author-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  border: 2px solid var(--color-dark);
  transition: all 0.3s ease;
}

.map-date-wrapper {
  display: flex;
  justify-content: center;
  width: 100%;
}

.map-date-added {
  color: var(--color-text);
  font-weight: 600;
  font-size: 0.9rem;
  background: var(--color-dark);
  padding: 8px 16px;
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  display: inline-flex;
  align-items: center;
}

.video-container {
  position: relative;
  overflow: hidden;
  width: 100%;
  padding-top: 56.25%;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: var(--color-background);
}

.video-container iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 8px;
}

.no-video-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: var(--color-background);
  border: 2px dashed rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.9rem;
}

.no-video-placeholder i {
  font-size: 2rem;
  margin-bottom: 8px;
}

.compact-ratings-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.rating-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.rating-pills {
  display: flex;
  gap: 8px;
}

.rating-pill {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.8rem;
  color: var(--color-dark);
  text-shadow: none;
  min-width: 40px;
  text-align: center;
}

.rating-label {
  font-size: 0.75rem;
  color: var(--color-text);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.8;
}

.courses-block,
.bonuses-block {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 16px;
  max-width: 100%;
}

.course-item,
.bonus-item {
  flex: 0 0 auto;
  min-width: 150px;
  max-width: 250px;
}

.row-divider {
  border: none;
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent,
    var(--color-primary),
    transparent
  ) !important;
  margin: 30px 0;
  opacity: 0.6;
}

.main-tier-color.tier-0 {
  background: var(--color-dark);
  color: var(--color-text);
}
.main-tier-color.tier-1 {
  background: #6ecff6;
  color: var(--color-dark);
}
.main-tier-color.tier-2 {
  background: #56b4e9;
  color: var(--color-dark);
}
.main-tier-color.tier-3 {
  background: #45b8ac;
  color: var(--color-dark);
}
.main-tier-color.tier-4 {
  background: #65c18c;
  color: var(--color-dark);
}
.main-tier-color.tier-5 {
  background: #a3d977;
  color: var(--color-dark);
}
.main-tier-color.tier-6 {
  background: #f3e683;
  color: var(--color-dark);
}
.main-tier-color.tier-7 {
  background: #f6c267;
  color: var(--color-dark);
}
.main-tier-color.tier-8 {
  background: #f08c5b;
  color: var(--color-dark);
}
.main-tier-color.tier-9 {
  background: #e66a5e;
  color: var(--color-dark);
}
.main-tier-color.tier-10 {
  background: #d64545;
  color: var(--color-dark);
}

.main-rating-color.rating-1 {
  background: #93c47d;
  color: var(--color-dark);
}
.main-rating-color.rating-2 {
  background: #aad099;
  color: var(--color-dark);
}
.main-rating-color.rating-3 {
  background: #c3b293;
  color: var(--color-dark);
}
.main-rating-color.rating-4 {
  background: #e06666;
  color: var(--color-dark);
}

.tier-color.tier-0 {
  background: var(--color-dark);
  color: var(--color-text);
}
.tier-color.tier-1 {
  background: var(--color-tier-1);
  color: var(--color-text);
}
.tier-color.tier-2 {
  background: var(--color-tier-2);
  color: var(--color-text);
}
.tier-color.tier-3 {
  background: var(--color-tier-3);
  color: var(--color-text);
}
.tier-color.tier-4 {
  background: var(--color-tier-4);
  color: var(--color-text);
}
.tier-color.tier-5 {
  background: var(--color-tier-5);
  color: var(--color-text);
}
.tier-color.tier-6 {
  background: var(--color-tier-6);
  color: var(--color-text);
}
.tier-color.tier-7 {
  background: var(--color-tier-7);
  color: var(--color-text);
}
.tier-color.tier-8 {
  background: var(--color-tier-8);
  color: var(--color-text);
}
.tier-color.tier-9 {
  background: var(--color-tier-9);
  color: var(--color-text);
}
.tier-color.tier-10 {
  background: var(--color-tier-10);
  color: var(--color-text);
}

.rating-color.rating-1 {
  background: var(--color-rating-1);
  color: var(--color-text);
}
.rating-color.rating-2 {
  background: var(--color-rating-2);
  color: var(--color-text);
}
.rating-color.rating-3 {
  background: var(--color-rating-3);
  color: var(--color-text);
}
.rating-color.rating-4 {
  background: var(--color-rating-4);
  color: var(--color-text);
}

@media (max-width: 768px) {
  .map-name {
    font-size: 2rem;
  }

  .back-button {
    position: static;
    margin-bottom: 20px;
  }

  .courses-block,
  .bonuses-block {
    justify-content: center;
  }

  .course-item,
  .bonus-item {
    min-width: 180px;
    max-width: 100%;
  }

  .authors-block {
    gap: 16px;
  }

  .stat-value {
    font-size: 1.5rem;
  }
}
</style>
