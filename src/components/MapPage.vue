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
        class="card map-banner mb-4 shadow"
        :style="{
          background: `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('/maps/${map.name}.jpg') center/cover no-repeat`,
          backgroundBlendMode: 'multiply',
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          border: '1px solid rgba(42, 42, 42, 0.99)',
        }"
      >
        <div
          class="row g-0"
          :style="{
            background:
              'linear-gradient(135deg, var(--color-primary), var(--color-box))',
          }"
        >
          <div
            class="col-md-12 d-flex flex-column align-items-center map-left p-4"
          >
            <button @click="goBack" class="btn btn-dark back-button">
              Back
            </button>
            <div class="map-info text-center w-100">
              <h1 v-if="map.name" class="map-name mb-2">{{ map.name }}</h1>
              <div class="row p-3 map-overview w-100">
                <div class="col-md-3 mb-3">
                  <div class="card stat-block h-100">
                    <div class="card-body text-center">
                      <h3 class="card-title">Soldier tier</h3>
                      <p
                        class="card-text main-tier-color"
                        :class="'tier-' + map.soldier_tier"
                      >
                        T{{ map.soldier_tier }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="card stat-block h-100">
                    <div class="card-body text-center">
                      <h3 class="card-title">Soldier rating</h3>
                      <p
                        class="card-text main-rating-color"
                        :class="'rating-' + map.soldier_rating"
                      >
                        R{{ map.soldier_rating }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="card stat-block h-100">
                    <div class="card-body text-center">
                      <h3 class="card-title">Demoman tier</h3>
                      <p
                        class="card-text main-tier-color"
                        :class="'tier-' + map.demoman_tier"
                      >
                        T{{ map.demoman_tier }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="card stat-block h-100">
                    <div class="card-body text-center">
                      <h3 class="card-title">Demoman rating</h3>
                      <p
                        class="card-text main-rating-color"
                        :class="'rating-' + map.demoman_rating"
                      >
                        R{{ map.demoman_rating }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <hr class="divider" />

              <!-- Courses Section -->
              <div class="row w-100" v-if="courses.length > 0">
                <div class="col-md-12">
                  <h2 class="section-header">Courses</h2>
                  <div class="courses-block">
                    <div
                      v-for="course in courses"
                      :key="course.id"
                      class="course-item"
                    >
                      <div class="card stat-block h-100">
                        <div class="card-body text-center">
                          <h3 class="card-title">
                            Course {{ course.course_index }}
                          </h3>
                          <div class="ratings-grid">
                            <div class="rating-label">Soldier</div>
                            <div
                              class="rating-box tier-color"
                              :class="'tier-' + course.soldier_tier"
                            >
                              T{{ course.soldier_tier }}
                            </div>
                            <div
                              class="rating-box rating-color"
                              :class="'rating-' + course.soldier_rating"
                            >
                              R{{ course.soldier_rating }}
                            </div>
                            <div class="rating-label">Demoman</div>
                            <div
                              class="rating-box tier-color"
                              :class="'tier-' + course.demoman_tier"
                            >
                              T{{ course.demoman_tier }}
                            </div>
                            <div
                              class="rating-box rating-color"
                              :class="'rating-' + course.demoman_rating"
                            >
                              R{{ course.demoman_rating }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <hr class="divider" v-if="courses.length > 0" />

              <!-- Bonuses Section -->
              <div class="row w-100" v-if="bonuses.length > 0">
                <div class="col-md-12">
                  <h2 class="section-header">Bonuses</h2>
                  <div class="bonuses-block">
                    <div
                      v-for="bonus in bonuses"
                      :key="bonus.id"
                      class="bonus-item"
                    >
                      <div class="card stat-block h-100">
                        <div class="card-body text-center">
                          <h3 class="card-title">
                            Bonus {{ bonus.bonus_index }}
                          </h3>
                          <div class="ratings-grid">
                            <div class="rating-label">Soldier</div>
                            <div
                              class="rating-box tier-color"
                              :class="'tier-' + bonus.soldier_tier"
                            >
                              T{{ bonus.soldier_tier }}
                            </div>
                            <div
                              class="rating-box rating-color"
                              :class="'rating-' + bonus.soldier_rating"
                            >
                              R{{ bonus.soldier_rating }}
                            </div>
                            <div class="rating-label">Demoman</div>
                            <div
                              class="rating-box tier-color"
                              :class="'tier-' + bonus.demoman_tier"
                            >
                              T{{ bonus.demoman_tier }}
                            </div>
                            <div
                              class="rating-box rating-color"
                              :class="'rating-' + bonus.demoman_rating"
                            >
                              R{{ bonus.demoman_rating }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <hr class="divider" v-if="bonuses.length > 0" />

              <!-- Authors Section -->
              <div class="row w-100">
                <div class="col-md-12">
                  <h2 class="section-header">Authors</h2>
                  <div class="row authors-block">
                    <div
                      v-for="author in authors"
                      :key="author.author_id"
                      class="col-md-4 text-center"
                    >
                      <img
                        :src="
                          author && author.player && author.player.steam_avatar
                            ? `${author.player.steam_avatar}`
                            : '/avatars/default-avatar.jpg'
                        "
                        alt="Author Avatar"
                        class="rounded-circle author-avatar mb-3"
                      />
                      <h5 class="author-name">
                        {{
                          author.author_name
                            ? author.author_name
                            : "Unknown Author"
                        }}
                      </h5>
                    </div>
                  </div>
                </div>
              </div>
              <p class="map-date-added text-right">
                Added on: {{ formatDate(map.date_added) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Videos Section -->
      <div class="row main-content-wrapper">
        <div class="col-md-12 map-details">
          <div
            class="card map-videos mb-4"
            style="background: var(--color-box)"
          >
            <div class="card-body">
              <h2 class="section-header">Videos</h2>
              <div class="row">
                <div class="col-md-6 text-center">
                  <h4 class="card-title">Soldier video</h4>
                  <div class="video-container">
                    <div
                      v-if="
                        map && map.soldier_video && map.soldier_video !== 'null'
                      "
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' + map.soldier_video
                        "
                        frameborder="0"
                        allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                      >
                      </iframe>
                    </div>
                    <div v-else>
                      <i class="bi bi-camera-video-off"></i>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 text-center">
                  <h4 class="card-title">Demoman video</h4>
                  <div class="video-container">
                    <div
                      v-if="
                        map && map.demoman_video && map.demoman_video !== 'null'
                      "
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' + map.demoman_video
                        "
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                      >
                      </iframe>
                    </div>
                    <div v-else>
                      <i class="bi bi-camera-video-off"></i>
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
      console.log(date);
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const year = date.getFullYear();

      return `${day}/${month}/${year}`;
    },
  },
};
</script>

<style scoped>
.return-button {
  background: var(--color-box);
  color: var(--color-text);
}

.return-button:hover {
  background: var(--color-primary);
  color: var(--color-text);
}
.map-banner {
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
  position: relative;
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
}

.map-videos {
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
}

.back-button {
  background: var(--color-row);
  font-weight: bold;
  position: absolute;
  top: 10px;
  left: 10px;
}

.back-button:hover {
  background: var(--color-primary);
}

.stat-block {
  background: var(--color-dark) !important;
  transition: transform 0.2s ease;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.stat-block .card-title {
  color: var(--color-text);
  font-weight: bold;
}

.stat-block .card-text {
  font-size: 1.4rem;
  font-weight: bold;
  color: var(--color-dark);
}

.section-header {
  color: var(--color-text);
  font-weight: bold;
  text-align: center;
}

.card-title {
  color: var(--color-text) !important;
  font-weight: bold;
}

.authors-block {
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
}

.author-name {
  color: var(--color-text);
}

.author-avatar {
  width: 48px;
  height: 48px;
  border: 3px solid #000e25;
}

.map-date-added {
  position: absolute;
  bottom: 10px;
  right: 30px;
  font-size: 1rem;
  color: var(--color-text);
  font-weight: bold;
}

.video-container {
  position: relative;
  overflow: hidden;
  width: 100%;
  padding-top: 56.25%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.video-container iframe,
.video-container .bi-camera-video-off {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.bi-camera-video-off {
  font-size: 3rem;
  color: var(--color-text);
  display: flex;
  align-items: center;
  justify-content: center;
}

.tier-color {
  color: white;
  padding: 5px 10px;
  border-radius: 5px;
}

.rating-color {
  color: white;
  padding: 5px 10px;
  border-radius: 5px;
}

.ratings-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
  margin-top: 10px;
}

.rating-box {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  border-radius: 5px;
  font-weight: bold;
  color: var(--color-dark);
}

.rating-label {
  padding-bottom: 5px;
}

.courses-block,
.bonuses-block {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
}

.course-item,
.bonus-item {
  flex: 0 0 auto;
}

.rating-label {
  grid-column: span 2;
  text-align: center;
  font-size: 0.8rem;
  color: var(--color-text);
  margin-bottom: -10px;
}

.divider {
  width: 100%;
  border: none;
  border-top: 1px solid var(--color-text);
  margin: 20px 0;
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
</style>
