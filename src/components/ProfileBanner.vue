<template>
  <div
    class="profile-banner mb-4"
    :class="{ 'golden-border': player.donator }"
    :style="{
      background: `linear-gradient(135deg, ${bannerColors.color1}, ${bannerColors.color2})`,
    }"
  >
    <div v-if="loading" class="loading-overlay">
      <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading ranks...</span>
      </div>
    </div>

    <div class="profile-top">
      <div class="top-left">
        <div v-if="player.donator" class="donator-badge">Donor</div>
      </div>
      <div class="top-right">
        <div
          v-if="onlineStatus"
          class="online-indicator"
          @click="connectToServer(onlineStatus.ipaddr, onlineStatus.port)"
          title="Click to connect"
        >
          <span class="online-dot"></span>
          <span class="online-text">
            {{ onlineStatus.shortname || onlineStatus.server_name }}
            <span class="online-sep">·</span>
            {{ onlineStatus.currentMap }}
          </span>
        </div>
        <div v-if="player.tempus_first_seen" class="date-item">
          <span class="date-label">First joined</span>
          <span class="date-value">{{
            formatJoinDate(player.tempus_first_seen)
          }}</span>
        </div>
        <span
          v-if="player.tempus_last_seen && player.tempus_first_seen"
          class="date-sep"
          >·</span
        >
        <div v-if="player.tempus_last_seen" class="date-item">
          <span class="date-label">Last online</span>
          <span class="date-value">{{
            formatRelativeTime(player.tempus_last_seen)
          }}</span>
        </div>
      </div>
    </div>
    <div class="banner-inner">
      <div class="profile-left">
        <a
          :href="
            player.steamidconverted && player.steamidconverted !== '#'
              ? `https://steamcommunity.com/profiles/${player.steamidconverted}`
              : '#'
          "
          :class="{
            'pointer-events-none':
              !player.steamidconverted || player.steamidconverted === '#',
          }"
          target="_blank"
          rel="noopener noreferrer"
          class="avatar-link"
        >
          <img
            :src="`${player.steam_avatar}`"
            alt="Avatar"
            class="avatar"
            :class="{ 'golden-avatar': player.donator }"
            onerror="this.src = '/avatars/golly.jpg'"
          />
        </a>

        <div class="profile-info">
          <h1 v-if="player.name" class="player-name" :title="player.name">
            {{ player.name }}
          </h1>
          <p class="rank-name">
            <span :class="playerRankInfo.color">{{
              playerRankInfo.title
            }}</span>
          </p>
          <p class="country">
            <img
              :src="getFlagImageUrl(player.country_code)"
              alt="flag"
              class="flag-icon"
              @error="handleImageError"
            />
            {{ player.country }}
          </p>
        </div>
      </div>
      <div class="profile-right">
        <div class="stats-grid">
          <div class="stat-card" v-for="stat in stats" :key="stat.label">
            <span class="stat-label">{{ stat.label }}</span>
            <span class="stat-value">{{ stat.value }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ProfileBanner",
  props: {
    player: {
      type: Object,
      required: true,
    },
    playerRankInfo: {
      type: Object,
      required: true,
    },
    bannerColors: {
      type: Object,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    onlineStatus: {
      type: Object,
      default: null, // null = offline, { server_name, shortname, currentMap } = online
    },
  },
  computed: {
    stats() {
      const fmt = (val) => (val != null ? `#${val}` : "—");
      const fmtPts = (val) => (val != null ? val : "—");
      return [
        { label: "Overall Rank", value: fmt(this.player.overall_rank) },
        { label: "Soldier Rank", value: fmt(this.player.soldier_rank) },
        { label: "Demoman Rank", value: fmt(this.player.demoman_rank) },
        { label: "Overall Points", value: fmtPts(this.player.overall_points) },
        { label: "Soldier Points", value: fmtPts(this.player.soldier_points) },
        { label: "Demoman Points", value: fmtPts(this.player.demoman_points) },
      ];
    },
  },
  methods: {
    connectToServer(ip, port) {
      window.location.href = `steam://run/440//+connect ${ip}:${port}/`;
    },
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    handleImageError(event) {
      event.target.src = "/icons/default-flag.jpg";
    },
    formatRelativeTime(timestamp) {
      if (!timestamp) return null;
      const now = Date.now();
      const ts =
        typeof timestamp === "number"
          ? timestamp * 1000
          : new Date(timestamp).getTime();
      const diff = Math.floor((now - ts) / 1000);
      if (diff < 60) return "just now";
      if (diff < 3600) {
        const m = Math.floor(diff / 60);
        return `${m}m ago`;
      }
      if (diff < 86400) {
        const h = Math.floor(diff / 3600);
        return `${h}h ago`;
      }
      if (diff < 86400 * 30) {
        const d = Math.floor(diff / 86400);
        return `${d}d ago`;
      }
      return new Date(ts).toLocaleDateString(undefined, {
        month: "short",
        day: "numeric",
        year: "numeric",
      });
    },
    formatJoinDate(timestamp) {
      if (!timestamp) return null;
      const ts =
        typeof timestamp === "number"
          ? timestamp * 1000
          : new Date(timestamp).getTime();
      return new Date(ts).toLocaleDateString(undefined, {
        month: "short",
        year: "numeric",
      });
    },
  },
};
</script>

<style scoped>
.profile-banner {
  position: relative;
  border: 1px solid rgba(42, 42, 42, 0.99);
  border-radius: 14px;
  box-shadow: 0 4px 32px rgba(0, 0, 0, 0.6);
  overflow: hidden;
  width: 100%;
}

.golden-border {
  border: 2px solid gold;
  animation: goldenGlow 3s ease-in-out infinite;
}

@keyframes goldenGlow {
  0%,
  100% {
    box-shadow: 0 0 8px 2px rgba(255, 215, 0, 0.4);
  }
  50% {
    box-shadow: 0 0 28px 6px rgba(255, 215, 0, 0.75);
  }
}

.loading-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.35);
  z-index: 10;
  border-radius: inherit;
}

.profile-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 7px 14px 0;
  flex-wrap: wrap;
  gap: 8px 14px;
}

.top-left {
  display: flex;
  align-items: center;
  min-width: 70px;
}

.top-right {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.date-item {
  display: flex;
  align-items: baseline;
  gap: 5px;
}

.date-label {
  color: rgba(255, 255, 255, 0.42);
  font-size: 0.65rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  white-space: nowrap;
}

.date-value {
  color: rgba(255, 255, 255, 0.82);
  font-size: 0.78rem;
  font-weight: 500;
  white-space: nowrap;
}

.date-sep {
  color: rgba(255, 255, 255, 0.2);
  font-size: 0.85rem;
  line-height: 1;
}

.online-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(72, 199, 116, 0.12);
  border: 1px solid rgba(72, 199, 116, 0.3);
  border-radius: 20px;
  padding: 2px 10px 2px 7px;
  cursor: pointer;
  transition:
    background 0.2s ease,
    border-color 0.2s ease;
}

.online-indicator:hover {
  background: rgba(72, 199, 116, 0.22);
  border-color: rgba(72, 199, 116, 0.55);
}

.online-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #48c774;
  box-shadow: 0 0 6px rgba(72, 199, 116, 0.8);
  animation: onlinePulse 2s ease-in-out infinite;
  flex-shrink: 0;
}

@keyframes onlinePulse {
  0%,
  100% {
    box-shadow: 0 0 4px rgba(72, 199, 116, 0.6);
  }
  50% {
    box-shadow: 0 0 10px rgba(72, 199, 116, 1);
  }
}

.online-text {
  color: rgba(255, 255, 255, 0.85);
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
  max-width: 220px;
  overflow: hidden;
  text-overflow: ellipsis;
}

.online-sep {
  color: rgba(255, 255, 255, 0.35);
  margin: 0 1px;
}

.banner-inner {
  display: flex;
  align-items: stretch;
  gap: 0;
  min-height: 200px;
}

.profile-left {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 10px 28px 24px;
  flex: 0 0 240px;
  text-align: center;
}

.donator-badge {
  background: gold;
  color: #1a1200;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  box-shadow: 0 2px 8px rgba(255, 200, 0, 0.45);
  animation: pulse 2.2s ease-in-out infinite;
}

@keyframes pulse {
  0%,
  100% {
    box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.6);
  }
  60% {
    box-shadow: 0 0 0 8px rgba(255, 215, 0, 0);
  }
}

.avatar-link {
  display: block;
  border-radius: 50%;
  transition: transform 0.2s ease;
  z-index: 1;
}
.avatar-link:hover {
  transform: scale(1.06);
}

.avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 3px solid var(--color-primary, #5b9bd5);
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.55);
  object-fit: cover;
  display: block;
}

.golden-avatar {
  border-color: gold;
  box-shadow: 0 0 16px rgba(255, 215, 0, 0.5);
}

.profile-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  max-width: 100%;
}

.player-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: var(--color-text, #fff);
  font-size: 1.6rem;
  font-weight: 700;
  text-shadow: 1px 2px 6px rgba(0, 0, 0, 0.6);
  margin: 0;
  line-height: 1.2;
}

.rank-name {
  font-weight: 600;
  font-size: 0.95rem;
  margin: 0;
  text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.7);
}

.country {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #c8c8c8;
  margin: 0;
}

.flag-icon {
  width: 24px;
  height: 18px;
  border-radius: 2px;
  vertical-align: middle;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
}

.profile-right {
  flex: 1;
  display: flex;
  align-items: center;
  padding: 10px 24px 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  width: 100%;
}

.stat-card {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 14px 10px;
  gap: 6px;
  transition:
    background 0.2s ease,
    transform 0.15s ease;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.stat-label {
  color: #9aa3b0;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  text-align: center;
}

.stat-value {
  color: var(--color-text, #fff);
  font-size: 1.35rem;
  font-weight: 700;
  line-height: 1;
}

@media (max-width: 900px) {
  .profile-left {
    flex: 0 0 200px;
    padding: 10px 24px 16px;
  }
  .stats-grid {
    gap: 8px;
  }
  .stat-value {
    font-size: 1.15rem;
  }
  .player-name {
    font-size: 1.4rem;
    max-width: 160px;
  }
}

@media (max-width: 768px) {
  .online-indicator {
    display: none;
  }
}

@media (max-width: 640px) {
  .banner-inner {
    flex-direction: column;
  }

  .profile-left {
    flex: none;
    flex-direction: row;
    align-items: center;
    gap: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    padding: 10px 20px 16px;
    text-align: left;
  }

  .profile-info {
    align-items: flex-start;
  }
  .player-name {
    font-size: 1.4rem;
    max-width: 250px;
  }
  .country {
    font-size: 0.82rem;
  }
  .profile-right {
    padding: 10px 14px 20px;
  }
  .stats-grid {
    gap: 8px;
  }
  .stat-card {
    padding: 12px 6px;
  }
  .stat-value {
    font-size: 1rem;
  }
  .stat-label {
    font-size: 0.68rem;
  }

  .profile-top {
    padding: 10px 14px 0;
    gap: 8px;
  }

  .date-sep {
    display: none;
  }

  .online-text {
    max-width: 160px;
  }

  .date-label {
    font-size: 0.6rem;
  }
  .date-value {
    font-size: 0.72rem;
  }
}

@media (max-width: 400px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .profile-top {
    padding: 8px 12px 0;
  }

  .top-right {
    gap: 6px;
  }

  .online-indicator {
    padding: 2px 8px 2px 6px;
  }

  .online-text {
    max-width: 120px;
  }

  .date-item {
    gap: 4px;
  }

  .date-label {
    font-size: 0.55rem;
  }

  .date-value {
    font-size: 0.65rem;
  }
}
</style>
