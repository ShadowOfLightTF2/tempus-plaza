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
            Support Tempus Plaza
          </h1>
        </div>
        <hr class="row-divider" style="width: 75%" />
        <div class="donation-section">
          <div class="donation-panel">
            <div class="donation-header">
              <h2 class="donation-title">Make a Donation</h2>
              <p class="donation-description">
                Donations are not necessary or needed in order to keep plaza
                running. If you still would like to support me that is very much
                appreciated! Tempus Plaza is not associated or affiliated with
                Tempus and donations will not go towards Tempus itself.
              </p>
            </div>
            <div class="donation-options">
              <a
                href="https://ko-fi.com/shadowoflight"
                target="_blank"
                rel="noopener noreferrer"
                class="kofi-button"
              >
                <span class="kofi-text">Donate via Ko-fi</span>
                <span class="kofi-icon">☕</span>
              </a>
            </div>
            <div class="donation-instructions">
              <h3 class="instructions-title">How to Get Your Perks</h3>
              <div class="instructions-content">
                <p class="instructions-text">
                  While donating, simply include your
                  <strong>Player ID</strong> in the Ko-fi message and your perks
                  will activate within 24 hours! Any donation amount will unlock
                  the perks
                </p>
                <div class="player-id-help">
                  <p class="help-text">
                    <span class="help-icon">💡</span>
                    Find your Player ID on your profile page URL:
                    <code v-if="isLoggedIn"
                      >tempusplaza.com/players/{{ playerId }}</code
                    >
                    <code v-else>tempusplaza.com/players/[YOUR_ID]</code>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="preview-section">
          <h2 class="preview-title">
            Donating unlocks different profile banner colours
          </h2>
          <p class="preview-subtitle">
            This is what your profile could look like with donator perks!
          </p>
          <div v-if="loading" class="loading-message">
            Loading your profile data...
          </div>
          <div v-else class="banner-container">
            <ProfileBanner
              v-for="index in 3"
              :key="index"
              :player="previewPlayer"
              :playerRankInfo="playerRankInfo"
              :bannerColors="
                bannerColorPairs[(index - 1) % bannerColorPairs.length]
              "
              :loading="false"
            />
          </div>
        </div>
        <div class="donators-carousel-section">
          <h2 class="donators-title">Our Amazing Donators</h2>
          <div v-if="donators.length" class="carousel-container">
            <div class="carousel-track" ref="carouselTrack">
              <SmartLink
                v-for="donator in duplicatedDonators"
                :key="donator.id"
                :to="{
                  name: 'PlayerPage',
                  params: { playerId: donator.playerId },
                }"
                class="donator-card"
              >
                <img
                  :src="donator.avatar || '/avatars/default-avatar.jpg'"
                  :alt="donator.name"
                  class="donator-avatar"
                  loading="lazy"
                />
                <div class="donator-info">
                  <h4 class="donator-name">{{ donator.name }}</h4>
                  <p class="donator-country">
                    <img
                      :src="getFlagUrl(donator.country_code)"
                      :alt="donator.country_code"
                      class="country-flag"
                      loading="lazy"
                    />
                    {{ donator.country || "Unknown" }}
                  </p>
                </div>
              </SmartLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useHead } from "@vueuse/head";
import ProfileBanner from "@/components/ProfileBanner.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;
const cache = new Map();
const cacheTimeout = 5 * 60 * 1000;

async function cachedFetch(url, options = {}) {
  const cacheKey = `${url}-${JSON.stringify(options)}`;
  const cached = cache.get(cacheKey);
  if (cached && Date.now() - cached.timestamp < cacheTimeout)
    return cached.data;
  const response = await fetch(url, {
    credentials: "include",
    ...options,
    headers: { "Content-Type": "application/json", ...options.headers },
  });
  if (!response.ok) throw new Error("HTTP " + response.status);
  const data = await response.json();
  cache.set(cacheKey, { data, timestamp: Date.now() });
  return data;
}

const RANK_TITLES = (() => {
  const titles = [
    { range: [1, 1], male: "Emperor", female: "Empress" },
    { range: [2, 2], male: "King", female: "Queen" },
    { range: [3, 3], male: "Archduke", female: "Archduchess" },
    { range: [4, 4], male: "Lord", female: "Lady" },
    { range: [5, 5], male: "Duke", female: "Duchess" },
    { range: [6, 10], male: "Prince I", female: "Princess I" },
    { range: [11, 15], male: "Earl I", female: "Gearl I" },
    { range: [16, 20], male: "Sir I", female: "Madam I" },
    { range: [21, 25], male: "Count I", female: "Countess" },
    { range: [26, 50], male: "Baron I", female: "Baroness I" },
    { range: [51, 100], male: "Knight I", female: "Dame I" },
    { range: [101, 200], male: "Noble I", female: "Noblewoman I" },
    { range: [201, 500], male: "Esquire I", female: "Esquire I" },
    { range: [501, 1000], male: "Jester I", female: "Jester I" },
    { range: [1001, 2500], male: "Plebeian I", female: "Plebeian I" },
    { range: [2501, 3500], male: "Peasant I", female: "Peasant I" },
    { range: [3501, 999999], male: "Peon", female: "Peon" },
  ];
  return titles.map((title) => ({
    ...title,
    maleColor:
      "rank-color-" + title.male.replace(/\s[IVX]+$/, "").toLowerCase(),
    femaleColor:
      "rank-color-" +
      (title.female === "Queen" || title.female === "Empress"
        ? title.female
        : title.female.replace(/\s[IVX]+$/, "")
      ).toLowerCase(),
  }));
})();

function getRankData(rank) {
  for (const title of RANK_TITLES) {
    if (rank >= title.range[0] && rank <= title.range[1]) return title;
  }
  return {
    male: "Unranked",
    female: "Unranked",
    maleColor: "--color-peon",
    femaleColor: "--color-peon",
  };
}
export default {
  name: "DonatePage",
  components: { ProfileBanner },
  setup() {
    useHead({
      title: "Tempus Plaza | Support",
      meta: [
        {
          name: "description",
          content: "Support Tempus Plaza",
        },
      ],
    });
  },
  data() {
    return {
      loading: false,
      isLoggedIn: false,
      playerId: null,
      player: {
        name: "Your Name Here",
        country: "United States",
        country_code: "us",
        steam_avatar: null,
        gender: "male",
        donator: true,
        color: "blue",
        overall_rank: 10,
        soldier_rank: 12,
        demoman_rank: 15,
        overall_points: 1200,
        soldier_points: 600,
        demoman_points: 600,
        rank_pref: "overall",
      },
      donators: [],
      duplicatedDonators: [],
    };
  },
  async mounted() {
    const [userResult, donatorsResult] = await Promise.allSettled([
      this.initializeUserData(),
      this.fetchDonators(),
    ]);
    if (userResult.status === "rejected")
      console.warn("Failed to load user data:", userResult.reason);
    if (donatorsResult.status === "rejected")
      console.warn("Failed to load donators:", donatorsResult.reason);
    this.$nextTick(() => {
      this.startCarousel();
    });
  },
  beforeUnmount() {
    this.stopCarousel();
  },
  computed: {
    getFlagUrl() {
      return (countryCode) =>
        "https://flagcdn.com/24x18/" +
        (countryCode || "us").toLowerCase() +
        ".png";
    },
    // Player object passed to ProfileBanner — always show donator=true for preview
    previewPlayer() {
      return { ...this.player, donator: true };
    },
    highestRank() {
      if (!this.isLoggedIn || !this.player.overall_rank) return 10;
      const { rank_pref, overall_rank, soldier_rank, demoman_rank } =
        this.player;
      if (rank_pref === "overall" && overall_rank) return overall_rank;
      if (rank_pref === "soldier" && soldier_rank) return soldier_rank;
      if (rank_pref === "demoman" && demoman_rank) return demoman_rank;
      return (
        Math.min(
          ...[overall_rank, soldier_rank, demoman_rank].filter(Boolean),
        ) || 10
      );
    },
    playerRankInfo() {
      const rankData = getRankData(this.highestRank);
      const isMale = this.player.gender === "male";
      return {
        title: isMale ? rankData.male : rankData.female,
        color: isMale ? rankData.maleColor : rankData.femaleColor,
      };
    },
    bannerColorPairs() {
      return [
        {
          color1: "var(--color-banner-red-1)",
          color2: "var(--color-banner-red-2)",
        },
        {
          color1: "var(--color-banner-purple-1)",
          color2: "var(--color-banner-purple-2)",
        },
        {
          color1: "var(--color-banner-teal-1)",
          color2: "var(--color-banner-teal-2)",
        },
        {
          color1: "var(--color-banner-cyan-1)",
          color2: "var(--color-banner-cyan-2)",
        },
      ];
    },
  },
  methods: {
    async initializeUserData() {
      this.loading = true;
      try {
        const userId = await this.fetchUser();
        if (userId) {
          this.isLoggedIn = true;
          await Promise.all([
            this.fetchUserData(userId),
            this.fetchPlayerData(userId),
            this.fetchPlayerRanks(userId),
          ]);
        }
      } catch (error) {
        console.error("Error initializing user data:", error);
        this.isLoggedIn = false;
      } finally {
        this.loading = false;
      }
    },
    async fetchUser() {
      try {
        const result = await cachedFetch(API_BASE_URL + "/api/get-user");
        this.playerId = result.data?.playerid || null;
        return this.playerId;
      } catch (error) {
        console.error("Error fetching user:", error);
        return null;
      }
    },
    async fetchUserData(playerId) {
      if (!playerId) return;
      try {
        const data = await cachedFetch(API_BASE_URL + "/users/" + playerId);
        if (data?.length)
          Object.assign(this.player, {
            gender: data.gender || "male",
            rank_pref: data.rank_pref || "overall",
            donator: Boolean(data.donator),
            color: data.color || "blue",
          });
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    },
    async fetchPlayerData(playerId) {
      if (!playerId) return;
      try {
        const data = await cachedFetch(API_BASE_URL + "/players/" + playerId);
        if (data?.length) {
          const p = data[0];
          Object.assign(this.player, {
            ...p,
            steam_avatar: p.steam_avatar || "golly.jpg",
            country: p.country || "United States",
            country_code: p.country_code || "us",
          });
        }
      } catch (error) {
        console.error("Error fetching player data:", error);
      }
    },
    async fetchPlayerRanks(playerId) {
      if (!playerId) return;
      try {
        const data = await cachedFetch(
          API_BASE_URL + "/players/" + playerId + "/ranks",
        );
        if (data?.length) {
          const r = data[0];
          Object.assign(this.player, {
            overall_rank: r.overall_rank || null,
            soldier_rank: r.soldier_rank || null,
            demoman_rank: r.demoman_rank || null,
            overall_points: (r.soldier_points || 0) + (r.demoman_points || 0),
            soldier_points: r.soldier_points || null,
            demoman_points: r.demoman_points || null,
          });
        }
      } catch (error) {
        console.error("Error fetching player ranks:", error);
      }
    },
    async fetchDonators() {
      try {
        const data = await cachedFetch(API_BASE_URL + "/users/get-donators");
        this.donators = data || [];
        const shuffle = (arr) => [...arr].sort(() => Math.random() - 0.5);
        for (let i = 0; i < 3; i++) {
          shuffle(this.donators).forEach((d, j) => {
            this.duplicatedDonators.push({
              ...d,
              id: `${d.id}-${i}-${j}`,
              playerId: d.id || j,
            });
          });
        }
      } catch (error) {
        console.error("Error fetching donators:", error);
      }
    },
    startCarousel() {
      if (!this.$refs.carouselTrack || !this.donators.length) return;
      const track = this.$refs.carouselTrack;
      const cardWidth = 216;
      const totalWidth = cardWidth * this.donators.length;
      track.style.setProperty("--scroll-width", `${totalWidth}px`);
      track.style.animationPlayState = "running";
    },
    stopCarousel() {
      if (this.$refs.carouselTrack) {
        this.$refs.carouselTrack.style.animationPlayState = "paused";
      }
    },
  },
};
</script>

<style scoped>
.loading-message {
  text-align: center;
  color: var(--color-text-muted, #aaa);
  font-size: 16px;
  padding: 40px;
}

.row-divider {
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

.content-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 1000px;
  width: 100%;
  padding: 0 16px;
  box-sizing: border-box;
}

.donation-section {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  gap: 40px;
}

.donation-panel {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  text-align: center;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.donation-title {
  color: var(--color-text, #fff);
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 15px;
}

.donation-description {
  color: var(--color-text-muted, #bbb);
  font-size: 16px;
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto;
}

.kofi-button {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  background: linear-gradient(135deg, #ff5f5f, #ff3838);
  color: white;
  text-decoration: none;
  padding: 18px 36px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 18px;
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(255, 95, 95, 0.4);
  margin: 20px 0;
  border: none;
  cursor: pointer;
}

.kofi-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 35px rgba(255, 95, 95, 0.6);
  background: linear-gradient(135deg, #ff3838, #ff1a1a);
  color: white;
  text-decoration: none;
}

.kofi-icon {
  font-size: 20px;
}

.donation-instructions {
  margin-top: 5px;
  padding: 30px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  border-left: 4px solid var(--color-primary, #007bff);
}

.instructions-title {
  color: var(--color-text, #fff);
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 20px;
}

.instructions-text {
  color: var(--color-text-muted, #ccc);
  font-size: 16px;
  line-height: 1.6;
  margin-bottom: 20px;
}

.player-id-help {
  background: rgba(0, 123, 255, 0.1);
  border-radius: 12px;
  padding: 20px;
  border: 1px solid rgba(0, 123, 255, 0.2);
}

.help-text {
  color: var(--color-text-muted, #ddd);
  font-size: 14px;
  margin: 0;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
}

.help-icon {
  font-size: 16px;
}

code {
  background: rgba(255, 255, 255, 0.1);
  padding: 4px 8px;
  border-radius: 6px;
  font-family: "Courier New", monospace;
  color: #ffd700;
  word-break: break-all;
}

.preview-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 40px;
  width: 100%;
}

.preview-title {
  color: var(--color-text, #fff);
  font-size: 30px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 30px;
}

.preview-subtitle {
  color: var(--color-text-muted, #bbb);
  font-size: 15px;
  text-align: center;
  margin-top: -20px;
  margin-bottom: 10px;
}

.banner-container {
  width: 100%;
  max-width: 100%;
  margin: 20px auto;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.banner-container :deep(.profile-banner):hover {
  transform: scale(1.01) !important;
}

.donators-carousel-section {
  width: 100%;
  margin-top: 20px;
  margin-bottom: 60px;
  overflow: hidden;
}

.donators-title {
  color: var(--color-text, #fff);
  font-size: 28px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 40px;
}

.carousel-container {
  width: 100%;
  overflow: hidden;
  mask: linear-gradient(
    to right,
    transparent 0%,
    black 10%,
    black 90%,
    transparent 100%
  );
  -webkit-mask: linear-gradient(
    to right,
    transparent 0%,
    black 10%,
    black 90%,
    transparent 100%
  );
}

.carousel-track {
  display: flex;
  gap: 16px;
  width: max-content;
  will-change: transform;
  animation: carousel-scroll 40s linear infinite paused;
}

@keyframes carousel-scroll {
  from {
    transform: translateX(0);
  }
  to {
    transform: translateX(calc(-1 * var(--scroll-width)));
  }
}

.donator-card {
  flex-shrink: 0;
  width: 200px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 20px;
  text-align: center;
  border: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
  transition: all 0.3s ease;
  text-decoration: none;
  cursor: pointer;
}

.donator-card:hover {
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  border-color: rgba(255, 215, 0, 0.5);
}

.donator-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  border: 2px solid gold;
  margin-bottom: 12px;
  object-fit: cover;
}

.donator-info {
  margin-bottom: 8px;
}

.donator-name {
  color: var(--color-text, #fff);
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.donator-country {
  font-size: 13px;
  font-weight: 500;
  margin: 0;
  color: var(--color-text-muted, #bbb);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.country-flag {
  width: 18px;
  height: 13px;
  border-radius: 2px;
  object-fit: cover;
}

@media (max-width: 768px) {
  .carousel-track {
    animation-duration: 60s;
  }
}

@media (max-width: 768px) {
  .content-container {
    padding: 0 10px;
  }
  .donation-panel {
    padding: 20px 16px;
  }
  .donation-title {
    font-size: 22px;
  }
  .donation-description {
    font-size: 14px;
  }
  .kofi-button {
    padding: 14px 24px;
    font-size: 15px;
  }
  .donation-instructions {
    padding: 16px;
  }
  .instructions-title {
    font-size: 16px;
  }
  .instructions-text {
    font-size: 14px;
  }
  .player-id-help {
    padding: 12px;
  }
  .banner-container {
    max-width: 100%;
  }
  .donators-title {
    font-size: 22px;
  }
  .donator-card {
    width: 160px;
    padding: 14px;
  }
  .donator-name {
    font-size: 14px;
  }
}

@media (max-width: 480px) {
  .donation-panel {
    padding: 16px 10px;
  }
  .donation-title {
    font-size: 20px;
  }
  .kofi-button {
    padding: 12px 20px;
    font-size: 14px;
    width: 100%;
    justify-content: center;
  }
  .donator-card {
    width: 140px;
    padding: 12px;
  }
  .donators-title {
    font-size: 20px;
  }
}
</style>
