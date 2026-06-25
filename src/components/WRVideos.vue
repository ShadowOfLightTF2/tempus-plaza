<template>
  <div class="card map-videos">
    <div class="card-body">
      <div class="collapsible-header" @click="toggleMapVideos">
        <h5 class="video-section-title mb-0">
          🌍 Map WR Videos
          <i
            class="bi"
            :class="showMapVideos ? 'bi-chevron-up' : 'bi-chevron-down'"
            style="margin-left: auto"
          ></i>
        </h5>
      </div>
      <div
        v-if="showMapVideos"
        class="collapsible-content"
        @click.self="clearActive"
      >
        <div class="video-section mb-3">
          <div class="section-content">
            <div class="row g-4">
              <div
                class="col-lg-6"
                :class="{
                  'order-mobile-last':
                    isIntendedClass('demoman') && !isIntendedClass('soldier'),
                }"
              >
                <div
                  class="video-card video-card-soldier"
                  :class="{ active: activeVideo === 'map-soldier' }"
                  @click.stop="toggleCard('map-soldier')"
                >
                  <h5 class="video-title">
                    <i class="bi bi-person-fill me-2"></i>Soldier record
                  </h5>
                  <div class="video-container">
                    <div
                      v-if="
                        map && map.soldier_video && map.soldier_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('map-soldier')"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' + map.soldier_video
                        "
                        frameborder="0"
                        allow="
                          accelerometer;
                          clipboard-write;
                          encrypted-media;
                          gyroscope;
                          picture-in-picture;
                        "
                        allowfullscreen
                      ></iframe>
                    </div>
                    <div v-else class="no-video-placeholder">
                      <i class="bi bi-camera-video-off"></i>
                      <span>No video available</span>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="col-lg-6"
                :class="{
                  'order-mobile-last':
                    isIntendedClass('soldier') && !isIntendedClass('demoman'),
                }"
              >
                <div
                  class="video-card video-card-demoman"
                  :class="{ active: activeVideo === 'map-demoman' }"
                  @click.stop="toggleCard('map-demoman')"
                >
                  <h5 class="video-title">
                    <i class="bi bi-person-fill me-2"></i>Demoman record
                  </h5>
                  <div class="video-container">
                    <div
                      v-if="
                        map && map.demoman_video && map.demoman_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('map-demoman')"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' + map.demoman_video
                        "
                        frameborder="0"
                        allow="
                          accelerometer;
                          autoplay;
                          clipboard-write;
                          encrypted-media;
                          gyroscope;
                          picture-in-picture;
                        "
                        allowfullscreen
                      ></iframe>
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
      <div v-if="courses.length > 0" class="video-section mb-3">
        <div class="collapsible-header" @click="toggleCourseVideos">
          <h5 class="video-section-title mb-0">
            🚩 Course WR Videos
            <i
              class="bi"
              :class="showCourseVideos ? 'bi-chevron-up' : 'bi-chevron-down'"
              style="margin-left: auto"
            ></i>
          </h5>
        </div>
        <div
          v-if="showCourseVideos"
          class="collapsible-content"
          @click.self="clearActive"
        >
          <div
            v-for="course in courses"
            :key="course.id"
            class="course-videos mb-4"
          >
            <h5 class="course-video-title mb-3">Course {{ course.index }}</h5>
            <div class="row g-4">
              <div
                class="col-lg-6"
                :class="{
                  'order-mobile-last':
                    isIntendedClass('demoman') && !isIntendedClass('soldier'),
                }"
              >
                <div
                  class="video-card video-card-soldier"
                  :class="{
                    active: activeVideo === 'course-soldier-' + course.id,
                  }"
                  @click.stop="toggleCard('course-soldier-' + course.id)"
                >
                  <h6 class="video-title">
                    <i class="bi bi-person-fill me-2"></i>Soldier record
                  </h6>
                  <div class="video-container">
                    <div
                      v-if="
                        course.soldier_video && course.soldier_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('course-soldier-' + course.id)"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' +
                          course.soldier_video
                        "
                        frameborder="0"
                        allow="
                          accelerometer;
                          autoplay;
                          clipboard-write;
                          encrypted-media;
                          gyroscope;
                          picture-in-picture;
                        "
                        allowfullscreen
                      ></iframe>
                    </div>
                    <div v-else class="no-video-placeholder">
                      <i class="bi bi-camera-video-off"></i>
                      <span>No video available</span>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="col-lg-6"
                :class="{
                  'order-mobile-last':
                    isIntendedClass('soldier') && !isIntendedClass('demoman'),
                }"
              >
                <div
                  class="video-card video-card-demoman"
                  :class="{
                    active: activeVideo === 'course-demoman-' + course.id,
                  }"
                  @click.stop="toggleCard('course-demoman-' + course.id)"
                >
                  <h6 class="video-title">
                    <i class="bi bi-person-fill me-2"></i>Demoman record
                  </h6>
                  <div class="video-container">
                    <div
                      v-if="
                        course.demoman_video && course.demoman_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('course-demoman-' + course.id)"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' +
                          course.demoman_video
                        "
                        frameborder="0"
                        allow="
                          accelerometer;
                          autoplay;
                          clipboard-write;
                          encrypted-media;
                          gyroscope;
                          picture-in-picture;
                        "
                        allowfullscreen
                      ></iframe>
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
      <div v-if="bonuses.length > 0" class="video-section">
        <div class="collapsible-header" @click="toggleBonusVideos">
          <h5 class="video-section-title mb-0">
            ⭐ Bonus WR Videos
            <i
              class="bi"
              :class="showBonusVideos ? 'bi-chevron-up' : 'bi-chevron-down'"
              style="margin-left: auto"
            ></i>
          </h5>
        </div>
        <div
          v-if="showBonusVideos"
          class="collapsible-content"
          @click.self="clearActive"
        >
          <div v-for="bonus in bonuses" :key="bonus.id" class="bonus-videos">
            <h5 class="bonus-video-title mb-3">Bonus {{ bonus.index }}</h5>
            <div class="row g-4">
              <div
                class="col-lg-6"
                :class="{
                  'order-mobile-last':
                    isIntendedClass('demoman') && !isIntendedClass('soldier'),
                }"
              >
                <div
                  class="video-card video-card-soldier"
                  :class="{
                    active: activeVideo === 'bonus-soldier-' + bonus.id,
                  }"
                  @click.stop="toggleCard('bonus-soldier-' + bonus.id)"
                >
                  <h6 class="video-title">
                    <i class="bi bi-person-fill me-2"></i>Soldier record
                  </h6>
                  <div class="video-container">
                    <div
                      v-if="
                        bonus.soldier_video && bonus.soldier_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('bonus-soldier-' + bonus.id)"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' + bonus.soldier_video
                        "
                        frameborder="0"
                        allow="
                          accelerometer;
                          autoplay;
                          clipboard-write;
                          encrypted-media;
                          gyroscope;
                          picture-in-picture;
                        "
                        allowfullscreen
                      ></iframe>
                    </div>
                    <div v-else class="no-video-placeholder">
                      <i class="bi bi-camera-video-off"></i>
                      <span>No video available</span>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="col-lg-6"
                :class="{
                  'order-mobile-last':
                    isIntendedClass('soldier') && !isIntendedClass('demoman'),
                }"
              >
                <div
                  class="video-card video-card-demoman"
                  :class="{
                    active: activeVideo === 'bonus-demoman-' + bonus.id,
                  }"
                  @click.stop="toggleCard('bonus-demoman-' + bonus.id)"
                >
                  <h6 class="video-title">
                    <i class="bi bi-person-fill me-2"></i>Demoman record
                  </h6>
                  <div class="video-container">
                    <div
                      v-if="
                        bonus.demoman_video && bonus.demoman_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('bonus-demoman-' + bonus.id)"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' + bonus.demoman_video
                        "
                        frameborder="0"
                        allow="
                          accelerometer;
                          autoplay;
                          clipboard-write;
                          encrypted-media;
                          gyroscope;
                          picture-in-picture;
                        "
                        allowfullscreen
                      ></iframe>
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
      <div v-if="rotwVideos.length > 0" class="video-section">
        <div class="collapsible-header" @click="toggleRotwVideos">
          <h5 class="video-section-title mb-0">
            🏆 ROTW Video(s)
            <i
              class="bi"
              :class="showRotwVideos ? 'bi-chevron-up' : 'bi-chevron-down'"
              style="margin-left: auto"
            ></i>
          </h5>
        </div>
        <div
          v-if="showRotwVideos"
          class="collapsible-content"
          @click.self="clearActive"
        >
          <div class="row g-4 justify-content-center align-items-center">
            <div
              v-for="rotwVideo in displayedRotwVideos"
              :key="rotwVideo.video_id"
              class="col-lg-8 col-md-10 col-12"
            >
              <div
                class="video-card video-card-rotw"
                :class="{
                  active: activeVideo === 'rotw-' + rotwVideo.video_id,
                }"
                @click.stop="toggleCard('rotw-' + rotwVideo.video_id)"
              >
                <h6 class="video-title">
                  {{ rotwVideo.player_name || "Unknown Player" }}
                </h6>
                <div class="video-container">
                  <div
                    v-if="rotwVideo.video_id"
                    class="video-scale-wrapper-rotw"
                    @click.stop="toggleCard('rotw-' + rotwVideo.video_id)"
                  >
                    <iframe
                      :src="
                        'https://www.youtube.com/embed/' + rotwVideo.video_id
                      "
                      frameborder="0"
                      allow="
                        accelerometer;
                        autoplay;
                        clipboard-write;
                        encrypted-media;
                        gyroscope;
                        picture-in-picture;
                      "
                      allowfullscreen
                    ></iframe>
                  </div>
                  <div v-else class="no-video-placeholder">
                    <i class="bi bi-camera-video-off"></i>
                    <span>No video available</span>
                  </div>
                </div>
                <div class="rotw-info mt-2">
                  <small class="rotw-text">
                    <i class="bi bi-calendar me-1"></i>
                    {{
                      formatDate(
                        new Date(rotwVideo.uploaded_at).getTime() / 1000,
                      )
                    }}
                  </small>
                </div>
              </div>
            </div>
            <div
              v-if="hasMoreRotwVideos && !showAllRotwVideos"
              class="text-center mt-3"
            >
              <button
                @click="toggleShowAllRotwVideos"
                class="btn load-more-btn"
              >
                <i class="bi bi-chevron-down me-2"></i>
                Load {{ rotwVideos.length - 1 }} older ROTW video{{
                  rotwVideos.length - 1 > 1 ? "s" : ""
                }}
              </button>
            </div>
            <div
              v-if="showAllRotwVideos && hasMoreRotwVideos"
              class="text-center mt-3"
            >
              <button
                @click="toggleShowAllRotwVideos"
                class="btn load-more-btn"
              >
                <i class="bi bi-chevron-up me-2"></i>
                Show only most recent
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "WRVideos",
  props: {
    map: {
      type: Object,
      required: true,
    },
    courses: {
      type: Array,
      default: () => [],
    },
    bonuses: {
      type: Array,
      default: () => [],
    },
    rotwVideos: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      showMapVideos: false,
      showCourseVideos: false,
      showBonusVideos: false,
      showRotwVideos: false,
      showAllRotwVideos: false,
      activeVideo: null,
    };
  },
  computed: {
    displayedRotwVideos() {
      if (this.showAllRotwVideos || this.rotwVideos.length <= 1) {
        return this.rotwVideos;
      }
      return this.rotwVideos.slice(0, 1);
    },
    hasMoreRotwVideos() {
      return this.rotwVideos.length > 1;
    },
  },
  methods: {
    toggleCard(cardId) {
      this.activeVideo = this.activeVideo === cardId ? null : cardId;
    },
    clearActive() {
      this.activeVideo = null;
    },
    handleClickOutside(e) {
      if (!e.target.closest(".video-card")) {
        this.activeVideo = null;
      }
    },
    toggleShowAllRotwVideos() {
      const wasShowingAll = this.showAllRotwVideos;
      this.showAllRotwVideos = !this.showAllRotwVideos;

      if (wasShowingAll && !this.showAllRotwVideos) {
        this.$nextTick(() => {
          const rotwSection = document.querySelector(
            ".video-section:has(.video-card-rotw)",
          );
          if (rotwSection) {
            rotwSection.scrollIntoView({
              behavior: "smooth",
              block: "start",
            });
          }
        });
      }
    },
    toggleRotwVideos() {
      this.showRotwVideos = !this.showRotwVideos;
    },
    toggleCourseVideos() {
      this.showCourseVideos = !this.showCourseVideos;
    },
    toggleBonusVideos() {
      this.showBonusVideos = !this.showBonusVideos;
    },
    toggleMapVideos() {
      this.showMapVideos = !this.showMapVideos;
    },
    isIntendedClass(className) {
      if (!this.map || !this.map.intended_class) return false;

      const intendedClass = this.map.intended_class;

      if (intendedClass === 5) return true;
      if (intendedClass === 3 && className === "soldier") return true;
      if (intendedClass === 4 && className === "demoman") return true;

      return false;
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
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
  },
};
</script>

<style scoped>
.map-videos {
  background-color: transparent;
  border: none;
}

.card-body {
  padding-bottom: 5px !important;
}

.video-section-title {
  color: var(--color-text);
  font-weight: 600;
  display: flex;
  align-items: center;
}

.course-video-title,
.bonus-video-title {
  color: var(--color-text);
  font-weight: bold;
  text-align: center;
  background: rgba(255, 255, 255, 0.05);
  padding: 4px 12px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 20px rgba(0, 0, 0, 0.4);
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
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px !important;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
  transition:
    transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
    box-shadow 0.35s ease,
    border-color 0.35s ease;
  will-change: transform;
}

.video-card:hover {
  cursor: pointer;
  background: var(--color-primary-dark);
}

.video-card-soldier.active {
  transform: scale(2) translateX(26%);
  z-index: 100;
  background: var(--color-primary-dark);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
}

.video-card-demoman.active {
  transform: scale(2) translateX(-26%);
  z-index: 100;
  background: var(--color-primary-dark);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
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
  background: rgba(255, 255, 255, 0.05);
}

.video-container iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 8px;
}

.video-scale-wrapper {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transform: scale(0.5);
  transform-origin: top left;
}

.video-scale-wrapper iframe {
  width: 200%;
  height: 200%;
  border-radius: 8px;
}

.video-scale-wrapper-rotw {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transform: scale(1);
  transform-origin: top left;
}

.video-scale-wrapper-rotw iframe {
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
  background: rgba(255, 255, 255, 0.05);
  border: 2px dashed rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.9rem;
}

.no-video-placeholder i {
  font-size: 2rem;
  margin-bottom: 8px;
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
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  border-bottom: none;
  padding-bottom: 0;
  margin-bottom: 0;
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

.video-card-rotw {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.video-card-rotw.active {
  background: var(--color-primary-dark);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
  transform: scale(1.5);
  z-index: 999;
}

.rotw-info {
  text-align: center;
  padding: 8px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 6px;
}

.rotw-info small {
  color: var(--color-text);
  font-size: 0.75rem;
}
.load-more-btn {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  margin-bottom: 10px;
}

.load-more-btn:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  color: var(--color-text);
  border-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.video-scale-wrapper iframe,
.video-scale-wrapper-rotw iframe {
  pointer-events: none;
}

.video-card.active .video-scale-wrapper iframe,
.video-card.active .video-scale-wrapper-rotw iframe {
  pointer-events: auto;
}

@media (max-width: 768px) {
  .order-mobile-last {
    order: 1;
  }

  .video-card.active {
    transform: none;
  }
  .video-card-rotw.active {
    transform: none !important;
  }
}
</style>
