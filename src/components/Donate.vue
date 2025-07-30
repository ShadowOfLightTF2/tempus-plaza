<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div
      class="container mx-auto py-4 d-flex flex-column align-items-center"
      style="z-index: 1"
    >
      <div class="content-container" style="z-index: 1">
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">❤️</span>
            Support the Website
          </h1>
        </div>
        <hr class="divider" style="width: 100%" />
        <div class="donation-panel">
          <div class="donation-options">
            <a
              href="https://ko-fi.com/shadowoflight"
              target="_blank"
              rel="noopener noreferrer"
              class="kofi-button"
            >
              <span class="kofi-text">Donate through ko-fi</span>
            </a>
          </div>
          <p class="panel-description">
            Your support helps me pay for server uptime and upgrade the
            website's services. Donator perks will be added to the website in
            the future. (cosmetic changes only). Currently you will get this
            golden border around your player page banner + ability to change the
            colour of it (with minimum donation of 3€). Please include one of
            the following in your message so I can give you the donator tag:
            name, id, steamid
          </p>
          <hr class="divider" />

          <div class="panel-footer">
            <p class="thank-you">Thank you for your generosity!</p>
          </div>
        </div>
        <div class="banner-container">
          <div
            v-for="(_, index) in count"
            :key="index"
            class="profile-banner"
            :style="{
              background: `linear-gradient(135deg, ${
                getBannerColors(index)[0]
              }, ${getBannerColors(index)[1]})`,
              transform: index % 2 === 0 ? 'rotate(-2deg)' : 'rotate(2deg)',
            }"
          >
            <div
              class="row g-0"
              style="height: 100%; display: flex; align-items: center"
            >
              <div
                class="col-md-4 d-flex flex-column align-items-center profile-left p-4"
              >
                <div class="donator-badge">
                  <span class="badge-text">Donator</span>
                </div>
                <img
                  src="/avatars/default-avatar.jpg"
                  alt="Avatar"
                  class="rounded-circle avatar mb-3"
                />
                <div class="profile-info text-center">
                  <h1 class="player-name">{{ player.name }}</h1>
                  <p class="rank-name mb-2">
                    <span style="color: var(--color-text-soft)">[</span>
                    <span class="rank-color-king">{{ player.rank }}</span>
                    <span style="color: var(--color-text-soft)">]</span>
                  </p>
                  <p
                    class="country mb-3"
                    style="font-size: 10px; font-weight: bold; color: #d5d5d5"
                  >
                    <img :src="player.flag" alt="flag" class="flag-icon" />
                    {{ player.country }}
                  </p>
                </div>
              </div>
              <div class="col-md-8 d-flex align-items-center profile-right">
                <div class="row p-3 profile-overview">
                  <div
                    class="col-md-4 mb-3"
                    v-for="(stat, statIndex) in player.stats"
                    :key="statIndex"
                  >
                    <div class="card banner-block h-100">
                      <div class="rank-card-body text-center">
                        <h5 class="card-title">{{ stat.title }}</h5>
                        <p class="card-text player-stats">{{ stat.value }}</p>
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
import { useHead } from "@vueuse/head";

export default {
  name: "DonatePage",
  setup() {
    useHead({
      title: "Tempus Plaza | Donate",
    });
  },
  data() {
    return {
      count: 5,
      player: {
        name: "Your name",
        rank: "King",
        flag: "https://flagcdn.com/24x18/us.png",
        country: "United States (US)",
        stats: [
          { title: "Overall Rank", value: "#10" },
          { title: "Soldier Rank", value: "#12" },
          { title: "Demoman Rank", value: "#15" },
          { title: "Overall Points", value: "1200" },
          { title: "Soldier Points", value: "600" },
          { title: "Demoman Points", value: "600" },
        ],
      },
    };
  },
  methods: {
    openKofi() {
      window.open(
        "https://ko-fi.com/shadowoflight",
        "_blank",
        "noopener,noreferrer"
      );
    },
    getBannerColors(index) {
      const colors = [
        ["var(--color-banner-red-1)", "var(--color-banner-red-2)"],
        ["var(--color-banner-purple-1)", "var(--color-banner-purple-2)"],
        ["var(--color-banner-teal-1)", "var(--color-banner-teal-2)"],
        ["var(--color-banner-cyan-1)", "var(--color-banner-cyan-2)"],
        ["var(--color-banner-orange-1)", "var(--color-banner-orange-2)"],
      ];
      return colors[index % colors.length];
    },
  },
};
</script>

<style scoped>
.donation-panel {
  max-width: 500px;
  margin: 40px auto;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  padding: 40px;
  text-align: center;
}

.panel-description {
  color: var(--color-text);
  font-size: 16px;
  line-height: 1.6;
  margin-bottom: 30px;
}

.kofi-button {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: linear-gradient(
    135deg,
    var(--color-primary),
    var(--color-primary-dark)
  );
  color: white;
  text-decoration: none;
  padding: 16px 32px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px var(--color-primary);
  margin-bottom: 25px;
}

.kofi-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px var(--color-primary);
  background: linear-gradient(
    225deg,
    var(--color-primary-dark),
    var(--color-primary)
  );
}

.divider {
  border: none;
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent,
    var(--color-primary),
    transparent
  );
  margin: 30px 0;
  opacity: 0.6;
}

.thank-you {
  color: var(--color-text);
  font-style: italic;
  margin: 0;
  font-size: 14px;
}

.banner-container {
  margin: 20px 0;
}

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
  margin-bottom: 20px;
  border-radius: 12px;
  border: 2px solid gold;
  animation: goldenGlow 3s infinite;
  max-width: 1000px;
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

.banner-left {
  transform: rotate(-2deg);
}

.banner-right {
  transform: rotate(2deg);
}

.avatar {
  width: 96px;
  height: 96px;
  border: 3px solid #000e25;
}

.rank-name {
  color: var(--color-text);
  font-weight: bold;
  font-size: small;
  margin-bottom: 10px;
}

.player-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  color: var(--color-text);
  font-size: 1.25rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.flag-icon {
  width: 18px;
  height: 12px;
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
  font-size: 1rem;
  font-weight: bold;
}

.rank-card-body {
  padding: 15px;
}

.player-stats {
  color: var(--color-text) !important;
}

@media (max-width: 768px) {
  .donation-panel {
    margin: 20px;
    padding: 30px 20px;
  }
}
</style>
