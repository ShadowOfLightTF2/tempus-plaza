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

    <div class="banner-inner">
      <div class="profile-left">
        <div v-if="player.donator" class="donator-badge">Donator</div>

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
            onerror="this.src='/avatars/golly.jpg'"
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
  },
  computed: {
    stats() {
      return [
        { label: "Overall Rank", value: `#${this.player.overall_rank}` },
        { label: "Soldier Rank", value: `#${this.player.soldier_rank}` },
        { label: "Demoman Rank", value: `#${this.player.demoman_rank}` },
        { label: "Overall Points", value: this.player.overall_points },
        { label: "Soldier Points", value: this.player.soldier_points },
        { label: "Demoman Points", value: this.player.demoman_points },
      ];
    },
  },
  methods: {
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    handleImageError(event) {
      event.target.src = "/icons/default-flag.jpg";
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
  padding: 28px 24px;
  flex: 0 0 240px;

  text-align: center;
}

.donator-badge {
  position: absolute;
  top: 12px;
  left: 12px;
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
  padding: 24px 20px;
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
  transition: background 0.2s ease, transform 0.15s ease;
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

@media (min-width: 992px) {
  .player-nameN {
    min-width: 250px;
  }
}

@media (max-width: 900px) {
  .profile-left {
    flex: 0 0 200px;
    padding: 24px 16px;
  }

  .stats-grid {
    grid-template-columns: repeat(3, 1fr);
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
    padding: 20px 16px;
    text-align: left;
  }

  .profile-left .donator-badge {
    top: 8px;
    left: 8px;
    font-size: 0.6rem;
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
    padding: 16px 14px 20px;
  }

  .stats-grid {
    grid-template-columns: repeat(3, 1fr);
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
}

@media (max-width: 400px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
