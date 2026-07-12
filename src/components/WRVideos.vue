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
                  <div class="video-container">
                    <div
                      v-if="
                        map &&
                        map.soldier_video &&
                        map.soldier_video !== 'null' &&
                        !isVideoLoaded('map-soldier')
                      "
                      class="custom-thumbnail"
                      :style="getThumbnailStyle(map.name)"
                      @click.stop="playVideo('map-soldier')"
                    >
                      <img
                        src="/icons/soldier.png"
                        class="class-icon"
                        alt="Soldier"
                      />
                      <img
                        :src="getYoutubeThumbnail(map.soldier_video)"
                        class="youtube-preview"
                        alt="YouTube thumbnail"
                      />
                      <div class="thumbnail-overlay">
                        <div class="thumbnail-info">
                          <span class="thumbnail-player">{{
                            parseVideoTitle(videoTitles[map.soldier_video])
                              .playerName || "Unknown"
                          }}</span>
                          <span
                            v-if="
                              parseVideoTitle(videoTitles[map.soldier_video])
                                .duration
                            "
                            class="thumbnail-duration"
                            >{{
                              parseVideoTitle(videoTitles[map.soldier_video])
                                .duration
                            }}</span
                          >
                        </div>
                        <i class="bi bi-play-circle-fill play-icon"></i>
                      </div>
                    </div>
                    <div
                      v-else-if="
                        map && map.soldier_video && map.soldier_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('map-soldier')"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' +
                          map.soldier_video +
                          '?autoplay=1'
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
                  :class="{ active: activeVideo === 'map-demoman' }"
                  @click.stop="toggleCard('map-demoman')"
                >
                  <div class="video-container">
                    <div
                      v-if="
                        map &&
                        map.demoman_video &&
                        map.demoman_video !== 'null' &&
                        !isVideoLoaded('map-demoman')
                      "
                      class="custom-thumbnail"
                      :style="getThumbnailStyle(map.name)"
                      @click.stop="playVideo('map-demoman')"
                    >
                      <img
                        src="/icons/demoman.png"
                        class="class-icon"
                        alt="Demoman"
                      />
                      <img
                        :src="getYoutubeThumbnail(map.demoman_video)"
                        class="youtube-preview"
                        alt="YouTube thumbnail"
                      />
                      <div class="thumbnail-overlay">
                        <div class="thumbnail-info">
                          <span class="thumbnail-player">{{
                            parseVideoTitle(videoTitles[map.demoman_video])
                              .playerName || "Unknown"
                          }}</span>
                          <span
                            v-if="
                              parseVideoTitle(videoTitles[map.demoman_video])
                                .duration
                            "
                            class="thumbnail-duration"
                            >{{
                              parseVideoTitle(videoTitles[map.demoman_video])
                                .duration
                            }}</span
                          >
                        </div>
                        <i class="bi bi-play-circle-fill play-icon"></i>
                      </div>
                    </div>
                    <div
                      v-else-if="
                        map && map.demoman_video && map.demoman_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('map-demoman')"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' +
                          map.demoman_video +
                          '?autoplay=1'
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
                  <div class="video-container">
                    <div
                      v-if="
                        course.soldier_video &&
                        course.soldier_video !== 'null' &&
                        !isVideoLoaded('course-soldier-' + course.id)
                      "
                      class="custom-thumbnail"
                      :style="getThumbnailStyle(map && map.name)"
                      @click.stop="playVideo('course-soldier-' + course.id)"
                    >
                      <img
                        src="/icons/soldier.png"
                        class="class-icon"
                        alt="Soldier"
                      />
                      <img
                        :src="getYoutubeThumbnail(course.soldier_video)"
                        class="youtube-preview"
                        alt="YouTube thumbnail"
                      />
                      <div class="thumbnail-overlay">
                        <div class="thumbnail-info">
                          <span class="thumbnail-player">{{
                            parseVideoTitle(videoTitles[course.soldier_video])
                              .playerName || "Unknown"
                          }}</span>
                          <span
                            v-if="
                              parseVideoTitle(videoTitles[course.soldier_video])
                                .duration
                            "
                            class="thumbnail-duration"
                            >{{
                              parseVideoTitle(videoTitles[course.soldier_video])
                                .duration
                            }}</span
                          >
                        </div>
                        <i class="bi bi-play-circle-fill play-icon"></i>
                      </div>
                    </div>
                    <div
                      v-else-if="
                        course.soldier_video && course.soldier_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('course-soldier-' + course.id)"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' +
                          course.soldier_video +
                          '?autoplay=1'
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
                  <div class="video-container">
                    <div
                      v-if="
                        course.demoman_video &&
                        course.demoman_video !== 'null' &&
                        !isVideoLoaded('course-demoman-' + course.id)
                      "
                      class="custom-thumbnail"
                      :style="getThumbnailStyle(map && map.name)"
                      @click.stop="playVideo('course-demoman-' + course.id)"
                    >
                      <img
                        src="/icons/demoman.png"
                        class="class-icon"
                        alt="Demoman"
                      />
                      <img
                        :src="getYoutubeThumbnail(course.demoman_video)"
                        class="youtube-preview"
                        alt="YouTube thumbnail"
                      />
                      <div class="thumbnail-overlay">
                        <div class="thumbnail-info">
                          <span class="thumbnail-player">{{
                            parseVideoTitle(videoTitles[course.demoman_video])
                              .playerName || "Unknown"
                          }}</span>
                          <span
                            v-if="
                              parseVideoTitle(videoTitles[course.demoman_video])
                                .duration
                            "
                            class="thumbnail-duration"
                            >{{
                              parseVideoTitle(videoTitles[course.demoman_video])
                                .duration
                            }}</span
                          >
                        </div>
                        <i class="bi bi-play-circle-fill play-icon"></i>
                      </div>
                    </div>
                    <div
                      v-else-if="
                        course.demoman_video && course.demoman_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('course-demoman-' + course.id)"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' +
                          course.demoman_video +
                          '?autoplay=1'
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
                  <div class="video-container">
                    <div
                      v-if="
                        bonus.soldier_video &&
                        bonus.soldier_video !== 'null' &&
                        !isVideoLoaded('bonus-soldier-' + bonus.id)
                      "
                      class="custom-thumbnail"
                      :style="getThumbnailStyle(map && map.name)"
                      @click.stop="playVideo('bonus-soldier-' + bonus.id)"
                    >
                      <img
                        src="/icons/soldier.png"
                        class="class-icon"
                        alt="Soldier"
                      />
                      <img
                        :src="getYoutubeThumbnail(bonus.soldier_video)"
                        class="youtube-preview"
                        alt="YouTube thumbnail"
                      />
                      <div class="thumbnail-overlay">
                        <div class="thumbnail-info">
                          <span class="thumbnail-player">{{
                            parseVideoTitle(videoTitles[bonus.soldier_video])
                              .playerName || "Unknown"
                          }}</span>
                          <span
                            v-if="
                              parseVideoTitle(videoTitles[bonus.soldier_video])
                                .duration
                            "
                            class="thumbnail-duration"
                            >{{
                              parseVideoTitle(videoTitles[bonus.soldier_video])
                                .duration
                            }}</span
                          >
                        </div>
                        <i class="bi bi-play-circle-fill play-icon"></i>
                      </div>
                    </div>
                    <div
                      v-else-if="
                        bonus.soldier_video && bonus.soldier_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('bonus-soldier-' + bonus.id)"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' +
                          bonus.soldier_video +
                          '?autoplay=1'
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
                  <div class="video-container">
                    <div
                      v-if="
                        bonus.demoman_video &&
                        bonus.demoman_video !== 'null' &&
                        !isVideoLoaded('bonus-demoman-' + bonus.id)
                      "
                      class="custom-thumbnail"
                      :style="getThumbnailStyle(map && map.name)"
                      @click.stop="playVideo('bonus-demoman-' + bonus.id)"
                    >
                      <img
                        src="/icons/demoman.png"
                        class="class-icon"
                        alt="Demoman"
                      />
                      <img
                        :src="getYoutubeThumbnail(bonus.demoman_video)"
                        class="youtube-preview"
                        alt="YouTube thumbnail"
                      />
                      <div class="thumbnail-overlay">
                        <div class="thumbnail-info">
                          <span class="thumbnail-player">{{
                            parseVideoTitle(videoTitles[bonus.demoman_video])
                              .playerName || "Unknown"
                          }}</span>
                          <span
                            v-if="
                              parseVideoTitle(videoTitles[bonus.demoman_video])
                                .duration
                            "
                            class="thumbnail-duration"
                            >{{
                              parseVideoTitle(videoTitles[bonus.demoman_video])
                                .duration
                            }}</span
                          >
                        </div>
                        <i class="bi bi-play-circle-fill play-icon"></i>
                      </div>
                    </div>
                    <div
                      v-else-if="
                        bonus.demoman_video && bonus.demoman_video !== 'null'
                      "
                      class="video-scale-wrapper"
                      @click.stop="toggleCard('bonus-demoman-' + bonus.id)"
                    >
                      <iframe
                        :src="
                          'https://www.youtube.com/embed/' +
                          bonus.demoman_video +
                          '?autoplay=1'
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
      loadedVideos: new Set(),
      failedThumbnails: new Set(),
      videoTitles: {},
      videoTitlesFetching: new Set(),
    };
  },
  watch: {
    map() {
      this.fetchAllVideoTitles();
    },
    courses() {
      this.fetchAllVideoTitles();
    },
    bonuses() {
      this.fetchAllVideoTitles();
    },
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
    getThumbnailUrl(mapName) {
      if (!mapName) return null;
      return `/map-backgrounds/${encodeURIComponent(mapName)}.webp`;
    },
    getThumbnailStyle(mapName) {
      const url = this.getThumbnailUrl(mapName);
      const hasImage = url && !this.failedThumbnails.has(mapName);
      if (hasImage) {
        return {
          backgroundImage:
            "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url(" +
            url +
            ")",
        };
      }
      return {
        backgroundImage:
          "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6))",
      };
    },
    markThumbnailFailed(mapName) {
      if (mapName) this.failedThumbnails.add(mapName);
    },
    preloadThumbnail(mapName) {
      const url = this.getThumbnailUrl(mapName);
      if (!url) return;
      const img = new Image();
      img.onerror = () => this.markThumbnailFailed(mapName);
      img.src = url;
    },
    isVideoLoaded(id) {
      return this.loadedVideos.has(id);
    },
    playVideo(id) {
      this.loadedVideos.add(id);
      this.activeVideo = id;
    },
    parseVideoTitle(title) {
      if (!title || typeof title !== "string") {
        return { playerName: null, duration: null };
      }

      const lastDashIndex = title.lastIndexOf(" - ");
      if (lastDashIndex === -1) {
        return { playerName: null, duration: null };
      }

      const meta = title.slice(0, lastDashIndex).trim();
      const duration = title.slice(lastDashIndex + 3).trim() || null;

      const nameMatch = meta.match(/^\[.+?\]\s*(.+?)\s+on\s+/i);
      const playerName = nameMatch ? nameMatch[1].trim() : null;

      return { playerName, duration };
    },
    setVideoTitle(videoId, title) {
      if (this.$set) {
        this.$set(this.videoTitles, videoId, title);
      } else {
        this.videoTitles[videoId] = title;
      }
    },
    async fetchVideoTitle(videoId) {
      if (!videoId || videoId === "null") return;
      if (this.videoTitles[videoId] !== undefined) return; // already fetched
      if (this.videoTitlesFetching.has(videoId)) return; // already in flight
      this.videoTitlesFetching.add(videoId);

      try {
        const oEmbedUrl =
          "https://www.youtube.com/oembed?url=" +
          encodeURIComponent("https://www.youtube.com/watch?v=" + videoId) +
          "&format=json";
        const res = await fetch(oEmbedUrl);
        if (!res.ok) throw new Error("oEmbed request failed");
        const data = await res.json();
        this.setVideoTitle(videoId, data.title || null);
      } catch (err) {
        this.setVideoTitle(videoId, null);
      } finally {
        this.videoTitlesFetching.delete(videoId);
      }
    },
    fetchAllVideoTitles() {
      const ids = [];
      if (this.map) {
        ids.push(this.map.soldier_video, this.map.demoman_video);
      }
      (this.courses || []).forEach((course) => {
        ids.push(course.soldier_video, course.demoman_video);
      });
      (this.bonuses || []).forEach((bonus) => {
        ids.push(bonus.soldier_video, bonus.demoman_video);
      });
      ids
        .filter((id) => id && id !== "null")
        .forEach((id) => this.fetchVideoTitle(id));
    },
    getYoutubeThumbnail(videoId) {
      if (!videoId || videoId === "null") return null;
      return `https://img.youtube.com/vi/${videoId}/mqdefault.jpg`;
    },
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
    if (this.map && this.map.name) this.preloadThumbnail(this.map.name);
    this.fetchAllVideoTitles();
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

.custom-thumbnail {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  border-radius: 8px;
  cursor: pointer;
}

.thumbnail-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  color: #fff;
  text-align: center;
  padding: 12px;
}

.class-icon {
  position: absolute;
  top: 16px;
  left: 16px;
  width: 40px;
  height: 40px;
  object-fit: contain;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.6));
  z-index: 2;
}

.youtube-preview {
  position: absolute;
  bottom: 8px;
  right: 8px;
  width: 144px;
  height: 81px;
  object-fit: cover;
  border-radius: 4px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
  z-index: 2;
}

.play-icon {
  font-size: 2rem;
  opacity: 0.9;
  transition: transform 0.2s ease;
}

.custom-thumbnail:hover .play-icon {
  transform: scale(1.15);
}

.thumbnail-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.thumbnail-player {
  font-weight: 700;
  font-size: 1.6rem;
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);
}

.thumbnail-duration {
  font-weight: 600;
  font-size: 1.3rem;
  font-variant-numeric: tabular-nums;
  color: rgba(255, 255, 255, 0.9);
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);
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
  .youtube-preview {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 96px;
    height: 54px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.6);
    z-index: 2;
  }
  .class-icon {
    width: 32px;
    height: 32px;
  }
}
</style>
