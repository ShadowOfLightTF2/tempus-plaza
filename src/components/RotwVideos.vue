<template>
  <div v-if="visibleRotwVideos.length > 0" class="rotw-section">
    <div class="rotw-container">
      <h4 class="rotw-section-title">
        Runs of the Week
        <span class="rotw-count">({{ rotwVideos.length }})</span>
      </h4>
      <div class="rotw-grid">
        <div
          v-for="(video, index) in visibleRotwVideos"
          :key="index"
          class="rotw-card"
          :class="{
            'rotw-card-active': activeRotwVideo === video.video_id,
            'rotw-card-left': index % 2 === 0,
            'rotw-card-right': index % 2 !== 0,
          }"
          @click.stop="
            toggleRotwVideo(video.video_id, index % 2 === 0 ? 'left' : 'right')
          "
        >
          <div class="rotw-video-embed">
            <div class="video-scale-wrapper">
              <iframe
                :src="`https://www.youtube.com/embed/${video.video_id}`"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                :style="{
                  pointerEvents:
                    activeRotwVideo === video.video_id ? 'auto' : 'none',
                }"
              ></iframe>
            </div>
          </div>
          <div class="rotw-video-info">
            <h5>{{ video.map_name }}</h5>
            <p>{{ video.formatted_upload_date }}</p>
          </div>
        </div>
      </div>
      <div class="load-more-container">
        <button
          v-if="visibleRotwVideos.length < rotwVideos.length"
          @click="loadMoreRotwVideos"
          class="global-btn"
        >
          Load More
        </button>
        <button
          v-if="visibleRotwVideos.length < rotwVideos.length"
          @click="showAllRotwVideos"
          class="global-btn"
        >
          Show All
        </button>
        <button
          v-if="visibleRotwVideos.length > 2"
          @click="closeAllRotwVideos"
          class="global-btn"
        >
          Close All
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "RotwVideos",
  props: {
    playerId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      rotwVideos: [],
      visibleRotwVideos: [],
      activeRotwVideo: null,
      activeRotwSide: null,
      rotwVideosPage: 1,
      rotwVideosPerPage: 2,
    };
  },
  mounted() {
    this.fetchRotwVideos(this.playerId);
    document.addEventListener("click", this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
  },
  watch: {
    playerId: {
      immediate: false,
      handler(newId) {
        this.closeAllRotwVideos();
        this.fetchRotwVideos(newId);
      },
    },
  },
  methods: {
    formatUploadDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },
    async fetchRotwVideos(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/rotw-videos`,
        );

        const uniqueVideos = response.data.filter(
          (video, index, self) =>
            index === self.findIndex((v) => v.video_id === video.video_id),
        );

        this.rotwVideos = uniqueVideos.map((video) => ({
          ...video,
          formatted_upload_date: this.formatUploadDate(video.uploaded_at),
        }));

        this.visibleRotwVideos = this.rotwVideos.slice(
          0,
          this.rotwVideosPerPage,
        );
      } catch (error) {
        console.error("Error fetching ROTW videos:", error);
      }
    },
    loadMoreRotwVideos() {
      const start = this.rotwVideosPage * this.rotwVideosPerPage;
      const end = start + this.rotwVideosPerPage;
      this.visibleRotwVideos = [
        ...this.visibleRotwVideos,
        ...this.rotwVideos.slice(start, end),
      ];
      this.rotwVideosPage++;
    },
    showAllRotwVideos() {
      this.visibleRotwVideos = [...this.rotwVideos];
    },
    closeAllRotwVideos() {
      this.visibleRotwVideos = this.rotwVideos.slice(0, 2);
      this.rotwVideosPage = 1;
      this.activeRotwVideo = null;
      this.activeRotwSide = null;
    },
    toggleRotwVideo(videoId, side) {
      this.activeRotwVideo = this.activeRotwVideo === videoId ? null : videoId;
      this.activeRotwSide = side;
    },
    handleClickOutside(e) {
      if (!e.target.closest(".rotw-card")) {
        this.activeRotwVideo = null;
      }
    },
  },
};
</script>

<style scoped>
.rotw-section {
  padding: 15px 0;
  display: flex;
  justify-content: center;
}
.rotw-container {
  width: fit-content;
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}
.rotw-section-title {
  font-size: 1.75rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 30px;
  color: var(--color-text);
}
.rotw-count {
  font-size: 1.5rem;
  color: var(--color-text-soft);
  font-weight: normal;
}
.rotw-grid {
  display: grid;
  gap: 30px;
  margin-top: 20px;
  margin-bottom: 20px;
  justify-content: center;
  max-width: 1250px;
  width: 100%;
  grid-template-columns: repeat(2, minmax(590px, 1fr));
}
.rotw-grid:has(.rotw-card:nth-child(1):last-child) {
  grid-template-columns: minmax(750px, 1fr);
}
.rotw-grid:has(.rotw-card:nth-child(1):last-child) .rotw-card-active {
  transform: scale(1.416);
}
.rotw-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  padding: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  transition: all 0.3s ease;
  min-width: 300px;
  max-width: 750px;
  cursor: pointer;
  position: relative;
  z-index: 1;
}
.rotw-card:hover {
  background: var(--color-primary-dark);
}
.rotw-card-active {
  z-index: 100;
  position: relative;
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
  background: var(--color-primary-dark);
}
.rotw-card-active.rotw-card-left {
  transform: translateX(52%) scale(1.8);
}
.rotw-card-active.rotw-card-right {
  transform: translateX(-52%) scale(1.8);
}
.rotw-card-active .video-scale-wrapper iframe {
  pointer-events: auto;
}
.rotw-video-embed {
  position: relative;
  width: 100%;
  height: 0;
  padding-bottom: 56.25%;
  overflow: hidden;
  border-radius: 8px;
}
.rotw-video-embed iframe {
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
  transform: scale(0.75);
  transform-origin: top left;
}
.video-scale-wrapper iframe {
  width: calc(100% / 0.75);
  height: calc(100% / 0.75);
  border-radius: 8px;
}
.rotw-video-info {
  padding-top: 10px;
  text-align: center;
}
.rotw-video-info h5 {
  margin: 0;
  color: var(--color-text);
  font-size: 1.1rem;
  font-weight: 700;
}
.rotw-video-info p {
  margin: 5px 0 0;
  color: #aaa;
  font-size: 0.9rem;
}
.load-more-container {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 20px;
}
.global-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  border-radius: 8px;
  max-width: 150px;
}

@media (max-width: 1400px) {
  .rotw-grid {
    grid-template-columns: 1fr !important;
    width: 100% !important;
    max-width: none !important;
    overflow-x: hidden;
    gap: 15px;
  }
  .rotw-section-title {
    font-size: 1.5rem;
  }
  .rotw-count {
    font-size: 1rem;
  }
  .rotw-card-active {
    transform: none !important;
    width: 100%;
    max-width: 100%;
  }
  .rotw-container {
    padding: 10px;
    width: 100%;
    box-sizing: border-box;
  }
  .rotw-card {
    max-width: 100%;
  }
}
</style>
