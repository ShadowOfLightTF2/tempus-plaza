<template>
  <div class="youtube-section-wrapper">
    <div class="divider-container">
      <div class="divider-line"></div>
      <div class="divider-icon">▶</div>
      <div class="divider-line"></div>
    </div>
    <div class="section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">
            TF2RJ<span class="title-accent">weekly</span>
          </h2>
        </div>
        <div class="grid">
          <template v-if="loading">
            <div
              class="video-card skeleton-card"
              v-for="n in 6"
              :key="'yt-skel-' + n"
            >
              <div class="skeleton-thumbnail"></div>
              <div class="card-body">
                <div class="skeleton-title-line"></div>
                <div class="skeleton-title-line short"></div>
                <div class="skeleton-date-line"></div>
              </div>
            </div>
          </template>
          <template v-else>
            <div
              class="video-card"
              v-for="(video, index) in tf2rjweeklyVideos"
              :key="video.id"
              :style="{ '--delay': index * 60 + 'ms' }"
              @click="openYoutubeVideo(video.youtube_id)"
            >
              <div class="thumbnail-wrapper">
                <img
                  :src="video.thumbnail_url"
                  alt="Video Thumbnail"
                  class="thumbnail-img"
                />
                <div class="play-overlay">
                  <div class="play-btn">
                    <svg
                      viewBox="0 0 24 24"
                      fill="currentColor"
                      width="22"
                      height="22"
                    >
                      <path d="M8 5.14v14l11-7-11-7z" />
                    </svg>
                  </div>
                </div>
                <div class="thumbnail-shimmer"></div>
              </div>

              <div class="card-body">
                <div class="video-title">{{ video.title }}</div>
                <div class="video-footer">
                  <div class="video-date">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      width="12"
                      height="12"
                    >
                      <rect x="3" y="4" width="18" height="18" rx="2" />
                      <line x1="16" y1="2" x2="16" y2="6" />
                      <line x1="8" y1="2" x2="8" y2="6" />
                      <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    {{ formatDate(video.published_on) }}
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "YoutubeSection",
  emits: ["data-loaded"],
  data() {
    return {
      tf2rjweeklyVideos: [],
      loading: true,
    };
  },
  methods: {
    formatDate(dateString) {
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    openYoutubeVideo(youtubeId) {
      if (youtubeId) {
        window.open(`https://www.youtube.com/watch?v=${youtubeId}`, "_blank");
      }
    },
    async fetchTF2RJWeeklyVideos() {
      try {
        const response = await axios.get(`${API_BASE_URL}/news/get-videos`);
        this.tf2rjweeklyVideos = response.data;
        if (this.tf2rjweeklyVideos.length > 0) {
          this.$emit("data-loaded", this.tf2rjweeklyVideos[0]);
        }
      } catch (error) {
        console.error("Error fetching TF2RJWeekly videos:", error);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.fetchTF2RJWeeklyVideos();
  },
};
</script>

<style scoped>
.divider-container {
  display: flex;
  align-items: center;
  gap: 16px;
  margin: 48px 0 30px;
  padding: 0 20px;
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
}

.divider-line {
  flex: 1;
  height: 1px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(102, 126, 234, 0.5),
    transparent
  );
}

.divider-icon {
  font-size: 0.65rem;
  color: rgba(102, 126, 234, 0.7);
  letter-spacing: 0.1em;
  border: 1px solid rgba(102, 126, 234, 0.3);
  padding: 5px 9px;
  border-radius: 4px;
  background: rgba(102, 126, 234, 0.08);
  backdrop-filter: blur(4px);
}

.section {
  padding: 0 0 60px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.section-header {
  text-align: center;
  margin-bottom: 48px;
}

.section-title {
  font-family: "Barlow Condensed", sans-serif;
  font-size: 3rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  line-height: 1;
  text-transform: uppercase;
  color: #ffffff;
  margin: 0 0 10px;
}

.title-accent {
  background: linear-gradient(135deg, #4a6fa5, #253752);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.video-card {
  display: flex;
  flex-direction: column;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1),
    box-shadow 0.25s ease, border-color 0.25s ease;
  animation: fadeSlideUp 0.4s ease both;
  animation-delay: var(--delay, 0ms);
  will-change: transform;
}

@keyframes fadeSlideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.video-card:hover {
  transform: translateY(-6px) scale(1.015);
  box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2),
    0 0 0 1px rgba(102, 126, 234, 0.3);
  border-color: rgba(102, 126, 234, 0.35);
}

.video-card:hover .play-overlay {
  opacity: 1;
}

.video-card:hover .thumbnail-shimmer {
  opacity: 1;
}

.thumbnail-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  overflow: hidden;
}

.thumbnail-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}

.video-card:hover .thumbnail-img {
  transform: scale(1.04);
}

.play-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.play-btn {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: rgba(102, 126, 234, 0.9);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  box-shadow: 0 4px 20px rgba(102, 126, 234, 0.5);
  padding-left: 2px;
  transition: transform 0.15s ease;
}

.play-btn:hover {
  transform: scale(1.1);
}

.thumbnail-shimmer {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    rgba(102, 126, 234, 0.08) 0%,
    transparent 60%
  );
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
}

.card-body {
  padding: 16px 18px 18px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.video-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.9);
  line-height: 1.45;
  margin-bottom: 14px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.video-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: auto;
}

.video-date {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.78rem;
  color: rgba(255, 255, 255, 0.35);
  font-weight: 500;
}

@media (max-width: 768px) {
  .grid {
    grid-template-columns: 1fr;
  }

  .section-title {
    font-size: 2.6rem;
  }
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

.skeleton-card {
  cursor: default;
  pointer-events: none;
}

.skeleton-thumbnail,
.skeleton-title-line,
.skeleton-date-line {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 25%,
    rgba(255, 255, 255, 0.12) 50%,
    rgba(255, 255, 255, 0.05) 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.6s infinite linear;
  border-radius: 6px;
}

.skeleton-thumbnail {
  width: 100%;
  aspect-ratio: 16 / 9;
  border-radius: 0;
}

.skeleton-title-line {
  height: 15px;
  width: 90%;
  margin-bottom: 8px;
}
.skeleton-title-line.short {
  width: 60%;
}
.skeleton-date-line {
  height: 12px;
  width: 80px;
  margin-top: 14px;
}
</style>
