<template>
  <div class="newest-section">
    <div class="divider-container">
      <div class="divider-line"></div>
      <div class="divider-icon">⊛</div>
      <div class="divider-line"></div>
    </div>
    <div class="section">
      <div class="container">
        <h2 class="section-title">
          <span class="title-accent">LATEST </span>NEWS
        </h2>
        <div class="news-grid">
          <SmartLink
            v-if="currentMap"
            class="news-card map-card"
            :to="{ name: 'MapPage', params: { mapId: currentMap.id } }"
          >
            <div
              class="card-bg"
              :style="{
                backgroundImage: `url('/map-backgrounds/medium/${currentMap.name}.jpg')`,
              }"
            ></div>
            <div class="card-inner">
              <div class="card-badge new-badge">
                <span class="badge-dot"></span>
                {{ newestMaps.length > 1 ? "New Maps" : "New Map" }}
              </div>
              <div class="class-icon-container">
                <img
                  v-for="icon in getClassIcons(currentMap.intended_class)"
                  :key="icon.alt"
                  :src="icon.src"
                  :class="icon.class"
                  :alt="icon.alt"
                />
              </div>
              <button
                v-if="newestMaps.length > 1"
                class="map-nav map-nav-prev"
                @click.prevent.stop="prevMap"
                :disabled="mapIndex === 0"
              >
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                >
                  <path d="m15 18-6-6 6-6" />
                </svg>
              </button>

              <button
                v-if="newestMaps.length > 1"
                class="map-nav map-nav-next"
                @click.prevent.stop="nextMap"
                :disabled="mapIndex === newestMaps.length - 1"
              >
                <svg
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                >
                  <path d="m9 18 6-6-6-6" />
                </svg>
              </button>
              <div class="card-content">
                <div class="card-spacer"></div>
                <div class="map-counter" v-if="newestMaps.length > 1">
                  {{ mapIndex + 1 }} / {{ newestMaps.length }}
                </div>
                <h3 class="card-map-name">{{ currentMap.name }}</h3>
                <div class="authors-row" v-if="currentAuthors.length">
                  <span class="by-label">by</span>
                  <div class="author-avatars">
                    <img
                      v-for="author in currentAuthors.slice(0, 3)"
                      :key="author.author_id"
                      :src="author.player?.steam_avatar"
                      :title="author.author_name"
                      class="author-avatar"
                      @error="(e) => (e.target.style.display = 'none')"
                    />
                  </div>
                  <span class="author-names">{{
                    currentAuthors.map((a) => a.author_name).join(", ")
                  }}</span>
                </div>
                <div class="ratings-row">
                  <div class="class-rating">
                    <span class="class-label">Soldier</span>
                    <div class="pills">
                      <span
                        class="pill"
                        :class="'tier-' + currentMap.soldier_tier"
                        >T{{ currentMap.soldier_tier }}</span
                      >
                      <span
                        class="pill"
                        :class="'rating-' + currentMap.soldier_rating"
                        >R{{ currentMap.soldier_rating }}</span
                      >
                    </div>
                  </div>
                  <div class="class-divider"></div>
                  <div class="class-rating">
                    <span class="class-label">Demoman</span>
                    <div class="pills">
                      <span
                        class="pill"
                        :class="'tier-' + currentMap.demoman_tier"
                        >T{{ currentMap.demoman_tier }}</span
                      >
                      <span
                        class="pill"
                        :class="'rating-' + currentMap.demoman_rating"
                        >R{{ currentMap.demoman_rating }}</span
                      >
                    </div>
                  </div>
                </div>
                <div class="map-meta-row">
                  <div class="meta-item" v-if="currentMap.bonus_count > 0">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <polygon
                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                      />
                    </svg>
                    {{ currentMap.bonus_count }} bonus{{
                      currentMap.bonus_count !== 1 ? "es" : ""
                    }}
                  </div>
                  <div class="meta-item" v-if="currentMap.course_count > 0">
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path
                        d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"
                      />
                      <line x1="4" y1="22" x2="4" y2="15" />
                    </svg>
                    {{ currentMap.course_count }} course{{
                      currentMap.course_count !== 1 ? "s" : ""
                    }}
                  </div>
                  <div
                    class="meta-item"
                    v-if="currentMap.tags && currentMap.tags.length"
                  >
                    <svg
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path
                        d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"
                      />
                      <line x1="7" y1="7" x2="7.01" y2="7" />
                    </svg>
                    {{ currentMap.tags.map((t) => t.name).join(", ") }}
                  </div>
                </div>
              </div>
            </div>
          </SmartLink>
          <div
            v-if="latestVideo"
            class="news-card video-card"
            @click="openYoutubeVideo(latestVideo.youtube_id)"
          >
            <div class="card-badge youtube-badge">
              <svg class="yt-icon" viewBox="0 0 24 24" fill="currentColor">
                <path
                  d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z"
                />
                <polygon
                  fill="white"
                  points="9.545 15.568 15.818 12 9.545 8.432 9.545 15.568"
                />
              </svg>
              New Video
            </div>

            <div class="video-thumbnail-wrapper">
              <img
                :src="latestVideo.thumbnail_url"
                :alt="latestVideo.title"
                class="video-thumbnail"
              />
              <div class="play-overlay">
                <div class="play-button">
                  <svg viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="5 3 19 12 5 21 5 3" />
                  </svg>
                </div>
              </div>
            </div>
            <div class="card-content video-card-content">
              <h3 class="card-video-title">{{ latestVideo.title }}</h3>
              <div class="video-meta-row">
                <span class="channel-name">TF2RJweekly</span>
                <span class="video-date">{{
                  formatDate(latestVideo.published_on)
                }}</span>
              </div>
            </div>
          </div>
          <div v-if="loadingMap" class="news-card skeleton-card">
            <div class="skeleton-badge"></div>
            <div class="skeleton-content">
              <div class="skeleton-line wide"></div>
              <div class="skeleton-line medium"></div>
              <div class="skeleton-line narrow"></div>
            </div>
          </div>
          <div v-if="loadingVideo" class="news-card skeleton-card">
            <div class="skeleton-thumb"></div>
            <div class="skeleton-content">
              <div class="skeleton-line wide"></div>
              <div class="skeleton-line medium"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "NewestSection",
  data() {
    return {
      newestMaps: [],
      mapIndex: 0,
      latestVideo: null,
      loadingMap: true,
      loadingVideo: true,
    };
  },
  computed: {
    currentMap() {
      return this.newestMaps[this.mapIndex]?.map || null;
    },
    currentAuthors() {
      return this.newestMaps[this.mapIndex]?.authors || [];
    },
  },
  methods: {
    getClassIcons(intendedClass) {
      if (intendedClass === 3)
        return [
          {
            src: "/icons/soldier.png",
            alt: "Soldier class icon",
            class: "author-class-icon",
          },
        ];
      if (intendedClass === 4)
        return [
          {
            src: "/icons/demoman.png",
            alt: "Demoman class icon",
            class: "author-class-icon",
          },
        ];
      if (intendedClass === 5)
        return [
          {
            src: "/icons/soldier.png",
            alt: "Soldier class icon",
            class: "author-class-icon dual-icon",
          },
          {
            src: "/icons/demoman.png",
            alt: "Demoman class icon",
            class: "author-class-icon dual-icon",
          },
        ];
      return [];
    },
    prevMap() {
      if (this.mapIndex > 0) this.mapIndex--;
    },
    nextMap() {
      if (this.mapIndex < this.newestMaps.length - 1) this.mapIndex++;
    },
    formatDate(dateString) {
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    openYoutubeVideo(youtubeId) {
      if (youtubeId) {
        window.open(`https://www.youtube.com/watch?v=${youtubeId}`, "_blank");
      }
    },
    async fetchNewestMaps() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/get-newest-maps`,
        );
        const data = response.data;
        const maps = Array.isArray(data) ? data : [data];
        this.newestMaps = maps.map((entry) => {
          if (entry.map) {
            entry.map.tags = entry.tags || [];
            return entry;
          }
          entry.tags = entry.tags || [];
          return { map: entry, authors: [], tags: entry.tags };
        });
      } catch (error) {
        console.error("Error fetching newest maps:", error);
      } finally {
        this.loadingMap = false;
      }
    },
    async fetchLatestVideo() {
      try {
        const response = await axios.get(`${API_BASE_URL}/news/get-videos`);
        const videos = response.data;
        if (videos && videos.length > 0) {
          this.latestVideo = videos[0];
        }
      } catch (error) {
        console.error("Error fetching latest video:", error);
      } finally {
        this.loadingVideo = false;
      }
    },
  },
  mounted() {
    this.fetchNewestMaps();
    this.fetchLatestVideo();
  },
};
</script>

<style scoped>
.newest-section {
  width: 100%;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.divider-container {
  display: flex;
  align-items: center;
  gap: 16px;
  margin: 48px auto 20px;
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
}

.section-title {
  font-family: "Barlow Condensed", sans-serif;
  font-size: 3rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  line-height: 1;
  text-transform: uppercase;
  color: #ffffff;
  margin: 0 0 30px;
}

.title-accent {
  background: linear-gradient(135deg, #4a6fa5, #253752);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.news-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
  margin-top: 20px;
  margin-bottom: 20px;
}

.card-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  transition: transform 0.4s ease;
  z-index: 0;
  background-image: linear-gradient(
    135deg,
    rgba(0, 0, 0, 0.75) 0%,
    rgba(0, 0, 0, 0.35) 50%,
    rgba(0, 0, 0, 0.85) 100%
  );
}

.map-card:hover .card-bg {
  transform: scale(1.04);
}

.card-inner {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.card-bg::after {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.3);
}

.news-card {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  background: linear-gradient(
      to bottom,
      rgba(255, 255, 255, 0.12) 0%,
      rgba(255, 255, 255, 0.06) 40%,
      rgba(255, 255, 255, 0) 100%
    ),
    rgba(255, 255, 255, 0.04);
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.6);
  display: flex;
  flex-direction: column;
  cursor: pointer;
  text-decoration: none;
  color: #fff;
  transition: all 0.35s ease;
  transform: translateZ(0);
  will-change: transform;
  min-height: 340px;
}

.news-card:hover {
  box-shadow: 0 0 50px rgba(102, 126, 234, 0.5), 0 20px 40px rgba(0, 0, 0, 0.5);
}

.card-badge {
  position: absolute;
  top: 18px;
  left: 18px;
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  z-index: 10;
}

.class-icon-container {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 2;
  display: flex;
  gap: 8px;
}
.author-class-icon {
  width: 45px;
  height: 45px;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 50%;
  padding: 8px;
  border: 2px solid rgba(74, 111, 165, 0.3);
}
.author-class-icon.dual-icon {
  width: 35px;
  height: 35x;
  padding: 6px;
}

.new-badge {
  background: rgba(102, 126, 234, 0.25);
  border: 1px solid rgba(102, 126, 234, 0.6);
  color: #a8b8ff;
}

.badge-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #a8b8ff;
  box-shadow: 0 0 8px #a8b8ff;
  animation: pulse-dot 2s ease-in-out infinite;
}

@keyframes pulse-dot {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.5;
    transform: scale(0.7);
  }
}

.youtube-badge {
  background: rgba(255, 0, 0, 0.2);
  border: 1px solid rgba(255, 60, 60, 0.5);
  color: #ff8080;
}

.yt-icon {
  width: 16px;
  height: 16px;
  color: #ff4444;
}

.map-nav {
  position: absolute;
  top: 40%;
  transform: translateY(-50%);
  z-index: 20;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.25);
  background: rgba(0, 0, 0, 0.45);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
  padding: 0;
}

.map-nav:hover:not(:disabled) {
  background: rgba(102, 126, 234, 0.5);
  border-color: rgba(102, 126, 234, 0.8);
  transform: translateY(-50%) scale(1.1);
}

.map-nav:disabled {
  opacity: 0.25;
  cursor: default;
}

.map-nav svg {
  width: 18px;
  height: 18px;
}

.map-nav-prev {
  left: 14px;
}

.map-nav-next {
  right: 14px;
}

.card-content {
  flex: 1;
  padding: 20px 22px;
  padding-top: 56px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.card-spacer {
  flex: 1;
}

.map-counter {
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 1px;
  color: rgba(255, 255, 255, 0.65);
  text-transform: uppercase;
}

.card-map-name {
  font-size: 1.6rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.5px;
  margin-bottom: 50px;
  text-shadow: 0 2px 12px rgba(0, 0, 0, 0.8);
  word-break: break-word;
}

.authors-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.by-label {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
  font-style: italic;
}

.author-avatars {
  display: flex;
}

.author-avatar {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.3);
  margin-left: -6px;
  object-fit: cover;
  transition: transform 0.2s;
}

.author-avatar:first-child {
  margin-left: 0;
}

.author-avatar:hover {
  transform: scale(1.15);
  z-index: 2;
}

.author-names {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.75);
  font-weight: 600;
}

.ratings-row {
  display: flex;
  align-items: center;
  gap: 20px;
}

.class-rating {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.class-label {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  opacity: 0.7;
}

.class-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
}

.pills {
  display: flex;
  gap: 6px;
}

.pill {
  padding: 5px 12px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.82rem;
  color: var(--color-text, #eee);
  text-align: center;
  min-width: 42px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.tier-0 {
  background: rgba(51, 51, 51, 0.5);
}
.tier-1 {
  background: rgba(110, 208, 246, 0.5);
}
.tier-2 {
  background: rgba(86, 179, 233, 0.5);
}
.tier-3 {
  background: rgba(69, 184, 173, 0.5);
}
.tier-4 {
  background: rgba(101, 193, 139, 0.5);
}
.tier-5 {
  background: rgba(163, 217, 119, 0.5);
}
.tier-6 {
  background: rgba(243, 230, 131, 0.5);
}
.tier-7 {
  background: rgba(246, 194, 103, 0.5);
}
.tier-8 {
  background: rgba(240, 141, 91, 0.5);
}
.tier-9 {
  background: rgba(230, 105, 94, 0.5);
}
.tier-10 {
  background: rgba(214, 69, 69, 0.5);
}

.rating-1 {
  background: rgba(148, 196, 125, 0.5);
}
.rating-2 {
  background: rgba(171, 208, 153, 0.5);
}
.rating-3 {
  background: rgba(195, 178, 147, 0.5);
}
.rating-4 {
  background: rgba(224, 102, 102, 0.5);
}

.map-meta-row {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.6);
}

.meta-item svg {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
}

.video-thumbnail-wrapper {
  position: relative;
  width: 100%;
  height: 250px;
  overflow: hidden;
  flex-shrink: 0;
}

.video-thumbnail {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.news-card:hover .video-thumbnail {
  transform: scale(1.04);
}

.play-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.3);
  transition: background 0.3s ease;
}

.news-card:hover .play-overlay {
  background: rgba(0, 0, 0, 0.15);
}

.play-button {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: rgba(255, 0, 0, 0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(255, 0, 0, 0.5);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.news-card:hover .play-button {
  transform: scale(1.1);
  box-shadow: 0 6px 30px rgba(255, 0, 0, 0.7);
}

.play-button svg {
  width: 24px;
  height: 24px;
  color: #fff;
  margin-left: 3px;
}

.video-card-content {
  padding-top: 18px;
  gap: 10px;
}

.card-video-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #fff;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.video-meta-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.channel-name {
  font-size: 0.82rem;
  font-weight: 600;
  color: rgba(255, 60, 60, 0.9);
  background: rgba(255, 60, 60, 0.1);
  padding: 3px 10px;
  border-radius: 12px;
  border: 1px solid rgba(255, 60, 60, 0.25);
}

.video-date {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
}

.skeleton-card {
  cursor: default;
  pointer-events: none;
}

.skeleton-badge {
  margin: 18px 18px 0;
  height: 28px;
  width: 110px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.07);
  animation: shimmer 1.5s infinite;
}

.skeleton-thumb {
  width: 100%;
  height: 250px;
  background: rgba(255, 255, 255, 0.07);
  animation: shimmer 1.5s infinite;
}

.skeleton-content {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  flex: 1;
}

.skeleton-line {
  height: 16px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.07);
  animation: shimmer 1.5s infinite;
}

.skeleton-line.wide {
  width: 90%;
}
.skeleton-line.medium {
  width: 65%;
}
.skeleton-line.narrow {
  width: 40%;
}

@keyframes shimmer {
  0% {
    opacity: 0.5;
  }
  50% {
    opacity: 1;
  }
  100% {
    opacity: 0.5;
  }
}

@media (max-width: 900px) {
  .news-grid {
    grid-template-columns: 1fr;
  }

  .section-title {
    font-size: 2rem;
  }

  .card-map-name {
    font-size: 1.3rem;
  }
}
</style>
