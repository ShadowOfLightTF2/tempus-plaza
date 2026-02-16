<template>
  <div
    class="profile-banner mb-4"
    :class="{ 'golden-border': player.donator }"
    :style="{
      background: `linear-gradient(135deg, ${bannerColors.color1}, ${bannerColors.color2})`,
    }"
  >
    <div v-if="loading" class="text-center">
      <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading ranks...</span>
      </div>
    </div>
    <div
      class="row g-0"
      style="height: 100%; display: flex; align-items: center"
    >
      <div
        class="col-md-4 d-flex flex-column align-items-center profile-left p-4"
        style="height: 100%"
      >
        <div v-if="player.donator" class="donator-badge">
          <span class="badge-text">Donator</span>
        </div>
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
        >
          <img
            :src="`${player.steam_avatar}`"
            alt="Avatar"
            class="rounded-circle avatar mb-3"
            :class="{ 'golden-avatar': player.donator }"
            onerror="this.src='/avatars/golly.jpg'"
          />
        </a>
        <div v-if="player.donator" class="donator-badge">
          <span class="badge-text">Donator</span>
        </div>
        <div class="profile-info text-center">
          <h1 v-if="player.name" class="player-name" :title="player.name">
            {{ player.name }}
          </h1>
          <p class="rank-name mb-2">
            <span :class="playerRankInfo.color">
              {{ playerRankInfo.title }}
            </span>
          </p>
          <p class="country mb-3" style="font-weight: bold; color: #d5d5d5">
            <img
              :src="getFlagImageUrl(player.country_code)"
              alt="flag"
              class="flag-icon"
              @error="handleImageError"
            />
            {{ player.country }} ({{ player.country_code }})
          </p>
        </div>
      </div>
      <div class="col-md-8 d-flex align-items-center profile-right">
        <div class="row p-3 profile-overview">
          <div class="col-md-4 mb-3">
            <div class="card banner-block h-100">
              <div class="rank-card-body text-center">
                <h3 class="card-title">Overall rank</h3>
                <p class="card-text player-stats">#{{ player.overall_rank }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="card banner-block h-100">
              <div class="rank-card-body text-center">
                <h3 class="card-title">Soldier rank</h3>
                <p class="card-text player-stats">#{{ player.soldier_rank }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="card banner-block h-100">
              <div class="rank-card-body text-center">
                <h3 class="card-title">Demoman rank</h3>
                <p class="card-text player-stats">#{{ player.demoman_rank }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="card banner-block h-100">
              <div class="rank-card-body text-center">
                <h3 class="card-title">Overall points</h3>
                <p class="card-text player-stats">
                  {{ player.overall_points }}
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="card banner-block h-100">
              <div class="rank-card-body text-center">
                <h3 class="card-title">Soldier points</h3>
                <p class="card-text player-stats">
                  {{ player.soldier_points }}
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="card banner-block h-100">
              <div class="rank-card-body text-center">
                <h3 class="card-title">Demoman points</h3>
                <p class="card-text player-stats">
                  {{ player.demoman_points }}
                </p>
              </div>
            </div>
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
  background: linear-gradient(
    135deg,
    var(--color-banner-blue-1),
    var(--color-banner-blue-2)
  );
  border: 1px solid rgba(42, 42, 42, 0.99);
  position: relative;
  border-radius: 12px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.golden-border {
  border-radius: 12px;
  border: 2px solid gold;
  animation: goldenGlow 3s infinite;
}

@keyframes goldenGlow {
  0% {
    box-shadow: 0 0 5px gold;
  }
  50% {
    box-shadow: 0 0 20px gold;
  }
  100% {
    box-shadow: 0 0 5px gold;
  }
}

.avatar {
  width: 96px;
  height: 96px;
  border: 3px solid var(--color-primary);
}

.avatar:hover {
  box-shadow: 0 0px 20px rgba(0, 0, 0, 0.75);
}

.golden-avatar {
  width: 96px;
  height: 96px;
  border: 3px solid gold;
}

.profile-info {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  max-width: 100%;
}

.player-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  color: var(--color-text);
  font-size: 2.5rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.flag-icon {
  width: 24px;
  height: 18px;
  vertical-align: middle;
  border-radius: 2px;
}

.banner-block {
  background: rgba(255, 255, 255, 0.05);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  border-radius: 10px;
}

.banner-block .card-title {
  color: #aaa;
  font-weight: bold;
}

.banner-block .card-text {
  font-size: 1.4rem;
  font-weight: bold;
}

.player-stats {
  color: var(--color-text) !important;
}

.rank-name {
  color: var(--color-text);
  font-weight: bold;
  font-size: large;
  margin-bottom: 10px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
}

.donator-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: gold;
  color: black;
  padding: 5px 10px;
  border-radius: 20px;
  font-weight: bold;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 215, 0, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(255, 215, 0, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(255, 215, 0, 0);
  }
}

.rank-card-body {
  padding: 15px;
}

@media (max-width: 768px) {
  .profile-banner {
    margin: 0 10px;
  }

  .row.g-0 {
    flex-direction: column;
  }

  .profile-left,
  .profile-right {
    width: 100% !important;
    max-width: 100%;
    justify-content: center;
  }

  .profile-overview {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
  }

  .player-name {
    font-size: 2rem;
  }
}

@media (max-width: 510px) {
  .banner-block {
    min-width: 0;
  }

  .col-md-4 {
    padding: 5px !important;
  }
}
</style>
