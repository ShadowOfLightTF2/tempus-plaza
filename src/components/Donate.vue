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
                running. If you still would like to support me that is still
                appreciated. Tempus Plaza is not associated or affiliated with
                Tempus.
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
              <h3 class="instructions-title">📝 How to Get Your Perks</h3>
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
                      >tempusplaza.xyz/players/{{ playerId }}</code
                    >
                    <code v-else>tempusplaza.xyz/players/[YOUR_ID]</code>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="perks-panel">
            <h2 class="perks-title">
              <span class="perks-icon"></span>
              Donator Perks
            </h2>

            <div class="perks-grid">
              <div class="perk-card">
                <div class="perk-icon">🌈</div>
                <h3 class="perk-title">Custom Banner Colors</h3>
                <p class="perk-description">
                  Choose from 10+ unique gradient combinations for your profile
                  banner
                </p>
              </div>

              <div class="perk-card">
                <div class="perk-icon">👑</div>
                <h3 class="perk-title">Golden Border & Badge</h3>
                <p class="perk-description">
                  Stand out with an animated golden border and exclusive
                  "Donator" badge
                </p>
              </div>

              <div class="perk-card">
                <div class="perk-icon">💬</div>
                <h3 class="perk-title">Donator Discord Role</h3>
                <p class="perk-description">
                  Special role in the Discord server
                </p>
              </div>

              <div class="perk-card">
                <div class="perk-icon">🌟</div>
                <h3 class="perk-title">More to come</h3>
                <p class="perk-description">More perks are on the way</p>
              </div>
            </div>

            <div class="perks-note">
              <p>
                <span class="note-icon">ℹ️</span>
                All cosmetic perks are permanent and when new features are
                released, they will be available to all donators. You get all
                perks with the minimum donation amount of €3,-.
              </p>
            </div>
          </div> -->
        </div>
        <div class="preview-section">
          <h2 class="preview-title">{{ previewTitle }}</h2>
          <div v-if="loading" class="loading-message">
            Loading your profile data...
          </div>
          <div v-else class="banner-container">
            <div
              v-for="index in 4"
              :key="index"
              class="profile-banner"
              :style="{
                background: `linear-gradient(135deg, ${
                  bannerColors[(index - 1) % bannerColors.length][0]
                }, ${bannerColors[(index - 1) % bannerColors.length][1]})`,
                transform: index % 2 === 0 ? 'rotate(-1deg)' : 'rotate(1deg)',
                opacity: 0.8 + (index - 1) * 0.05,
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
                    :src="player.avatar || '/avatars/default-avatar.jpg'"
                    alt="Avatar"
                    class="rounded-circle avatar mb-3"
                  />
                  <div class="profile-info text-center">
                    <h1 class="player-name">{{ player.name }}</h1>
                    <p class="rank-name mb-2">
                      <span :class="playerRankInfo.color">
                        {{ playerRankInfo.title }}
                      </span>
                    </p>
                    <p
                      class="country mb-3"
                      style="font-size: 10px; font-weight: bold; color: #d5d5d5"
                    >
                      <img
                        :src="getFlagUrl(player.country_code)"
                        alt="flag"
                        class="flag-icon"
                      />
                      {{ player.country || "United States (US)" }}
                    </p>
                  </div>
                </div>
                <div class="col-md-8 d-flex align-items-center profile-right">
                  <div class="row p-3 profile-overview">
                    <div
                      class="col-md-4 mb-3"
                      v-for="(stat, statIndex) in playerStats"
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
        <!-- <div class="thank-you-section">
          <h2 class="thank-you-title">Thank You!</h2>
          <p class="thank-you-text">
            Every donation, no matter the size, makes a real difference in
            keeping Tempus Plaza running smoothly and continuously improving.
          </p>
        </div> -->
      </div>
    </div>
  </div>
</template>

<script>
import { useHead } from "@vueuse/head";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

const cache = new Map();
const cacheTimeout = 5 * 60 * 1000;

async function cachedFetch(url, options = {}) {
  const cacheKey = `${url}-${JSON.stringify(options)}`;
  const cached = cache.get(cacheKey);

  if (cached && Date.now() - cached.timestamp < cacheTimeout) {
    return cached.data;
  }

  const response = await fetch(url, {
    credentials: "include",
    ...options,
    headers: { "Content-Type": "application/json", ...options.headers },
  });

  if (!response.ok) throw new Error(`HTTP ${response.status}`);

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
    { range: [6, 10], male: "Prince V", female: "Princess V" },
    { range: [6, 10], male: "Prince IV", female: "Princess IV" },
    { range: [6, 10], male: "Prince III", female: "Princess III" },
    { range: [6, 10], male: "Prince II", female: "Princess II" },
    { range: [6, 10], male: "Prince I", female: "Princess I" },
    { range: [11, 11], male: "Earl V", female: "Gearl V" },
    { range: [12, 12], male: "Earl IV", female: "Gearl IV" },
    { range: [13, 13], male: "Earl III", female: "Gearl III" },
    { range: [14, 14], male: "Earl II", female: "Gearl II" },
    { range: [15, 15], male: "Earl I", female: "Gearl I" },
    { range: [16, 16], male: "Sir V", female: "Madam V" },
    { range: [17, 17], male: "Sir IV", female: "Madam IV" },
    { range: [18, 18], male: "Sir III", female: "Madam III" },
    { range: [19, 19], male: "Sir II", female: "Madam II" },
    { range: [20, 20], male: "Sir I", female: "Madam I" },
    { range: [21, 21], male: "Count V", female: "Countess" },
    { range: [22, 22], male: "Count IV", female: "Countess" },
    { range: [23, 23], male: "Count III", female: "Countess" },
    { range: [24, 24], male: "Count II", female: "Countess" },
    { range: [25, 25], male: "Count I", female: "Countess" },
    { range: [26, 30], male: "Baron V", female: "Baroness V" },
    { range: [31, 35], male: "Baron IV", female: "Baroness IV" },
    { range: [36, 40], male: "Baron III", female: "Baroness III" },
    { range: [41, 45], male: "Baron II", female: "Baroness II" },
    { range: [46, 50], male: "Baron I", female: "Baroness I" },
    { range: [51, 60], male: "Knight V", female: "Dame V" },
    { range: [61, 70], male: "Knight IV", female: "Dame IV" },
    { range: [71, 80], male: "Knight III", female: "Dame III" },
    { range: [81, 90], male: "Knight II", female: "Dame II" },
    { range: [91, 100], male: "Knight I", female: "Dame I" },
    { range: [101, 120], male: "Noble V", female: "Noblewoman V" },
    { range: [121, 140], male: "Noble IV", female: "Noblewoman IV" },
    { range: [141, 160], male: "Noble III", female: "Noblewoman III" },
    { range: [161, 180], male: "Noble II", female: "Noblewoman II" },
    { range: [181, 200], male: "Noble I", female: "Noblewoman I" },
    { range: [201, 260], male: "Esquire V", female: "Esquire V" },
    { range: [261, 320], male: "Esquire IV", female: "Esquire IV" },
    { range: [321, 380], male: "Esquire III", female: "Esquire III" },
    { range: [381, 440], male: "Esquire II", female: "Esquire II" },
    { range: [441, 500], male: "Esquire I", female: "Esquire I" },
    { range: [501, 600], male: "Jester V", female: "Jester V" },
    { range: [601, 700], male: "Jester IV", female: "Jester IV" },
    { range: [701, 800], male: "Jester III", female: "Jester III" },
    { range: [801, 900], male: "Jester II", female: "Jester II" },
    { range: [901, 1000], male: "Jester I", female: "Jester I" },
    { range: [1001, 1300], male: "Plebeian V", female: "Plebeian V" },
    { range: [1301, 1600], male: "Plebeian IV", female: "Plebeian IV" },
    { range: [1601, 1900], male: "Plebeian III", female: "Plebeian III" },
    { range: [1901, 2200], male: "Plebeian II", female: "Plebeian II" },
    { range: [2201, 2500], male: "Plebeian I", female: "Plebeian I" },
    { range: [2501, 2700], male: "Peasant V", female: "Peasant V" },
    { range: [2701, 2900], male: "Peasant IV", female: "Peasant IV" },
    { range: [2901, 3100], male: "Peasant III", female: "Peasant III" },
    { range: [3101, 3300], male: "Peasant II", female: "Peasant II" },
    { range: [3301, 3500], male: "Peasant I", female: "Peasant I" },
    { range: [3501, 4000], male: "Peasant I", female: "Peasant I" },
    { range: [4001, 5000], male: "Peasant I", female: "Peasant I" },
    { range: [5001, 999999], male: "Peon", female: "Peon" },
  ];

  return titles.map((title) => ({
    ...title,
    maleColor: `rank-color-${title.male
      .replace(/\s[IVX]+$/, "")
      .toLowerCase()}`,
    femaleColor: `rank-color-${(title.female === "Queen" ||
    title.female === "Empress"
      ? title.female
      : title.female.replace(/\s[IVX]+$/, "")
    ).toLowerCase()}`,
  }));
})();
function getRankData(rank) {
  for (const title of RANK_TITLES) {
    if (rank >= title.range[0] && rank <= title.range[1]) {
      return title;
    }
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
  setup() {
    useHead({
      title: "Tempus Plaza | Support",
      meta: [
        {
          name: "description",
          content:
            "Support Tempus Plaza and unlock exclusive donator perks including custom banner colors, priority support, and beta access.",
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
        rank: "King",
        country: "United States (US)",
        country_code: "us",
        avatar: null,
        gender: "male",
        donator: false,
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
      animationId: null,
    };
  },
  async mounted() {
    const [userResult, donatorsResult] = await Promise.allSettled([
      this.initializeUserData(),
      this.fetchDonators(),
    ]);

    if (userResult.status === "rejected") {
      console.warn("Failed to load user data:", userResult.reason);
    }
    if (donatorsResult.status === "rejected") {
      console.warn("Failed to load donators:", donatorsResult.reason);
    }

    this.$nextTick(() => {
      this.startCarousel();
    });
  },
  beforeUnmount() {
    this.stopCarousel();
  },
  computed: {
    formatNumber() {
      return (num) => {
        return num?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") || "0";
      };
    },
    getFlagUrl() {
      return (countryCode) => {
        return `https://flagcdn.com/24x18/${(
          countryCode || "us"
        ).toLowerCase()}.png`;
      };
    },
    previewTitle() {
      return this.isLoggedIn
        ? "Your Profile with Donator Perks"
        : "Preview: Profile with Donator Perks";
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
          ...[overall_rank, soldier_rank, demoman_rank].filter(Boolean)
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

    playerStats() {
      const stats = [
        {
          title: "Overall Rank",
          key: "overall_rank",
          default: 10,
          prefix: "#",
        },
        {
          title: "Soldier Rank",
          key: "soldier_rank",
          default: 12,
          prefix: "#",
        },
        {
          title: "Demoman Rank",
          key: "demoman_rank",
          default: 15,
          prefix: "#",
        },
        {
          title: "Overall Points",
          key: "overall_points",
          default: 1200,
          format: true,
        },
        {
          title: "Soldier Points",
          key: "soldier_points",
          default: 600,
          format: true,
        },
        {
          title: "Demoman Points",
          key: "demoman_points",
          default: 600,
          format: true,
        },
      ];

      return stats.map(
        ({ title, key, default: defaultVal, prefix = "", format = false }) => {
          let value = this.isLoggedIn
            ? this.player[key] || defaultVal
            : defaultVal;
          if (format) value = this.formatNumber(value);
          return { title, value: `${prefix}${value}` };
        }
      );
    },

    bannerColors() {
      return [
        ["var(--color-banner-red-1)", "var(--color-banner-red-2)"],
        ["var(--color-banner-purple-1)", "var(--color-banner-purple-2)"],
        ["var(--color-banner-teal-1)", "var(--color-banner-teal-2)"],
        ["var(--color-banner-cyan-1)", "var(--color-banner-cyan-2)"],
        ["var(--color-banner-orange-1)", "var(--color-banner-orange-2)"],
      ];
    },

    duplicatedDonators() {
      if (!this.donators || !this.donators.length) return [];

      const result = [];
      const maxCopies = 3;

      for (let i = 0; i < maxCopies; i++) {
        for (let j = 0; j < this.donators.length; j++) {
          const donator = this.donators[j];
          result.push({
            ...donator,
            duplicateIndex: i,
            id: `${donator.id || j}-${i}-${j}`,
            playerId: donator.id || donator.playerId || j,
          });
        }
      }
      return result;
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
        const result = await cachedFetch(`${API_BASE_URL}/api/get-user`);
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
        const data = await cachedFetch(`${API_BASE_URL}/users/${playerId}`);
        if (data?.length) {
          Object.assign(this.player, {
            gender: data.gender || "male",
            rank_pref: data.rank_pref || "overall",
            donator: Boolean(data.donator),
            color: data.color || "blue",
          });
        }
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    },

    async fetchPlayerData(playerId) {
      if (!playerId) return;
      try {
        const data = await cachedFetch(`${API_BASE_URL}/players/${playerId}`);
        if (data?.length) {
          const playerData = data[0];
          Object.assign(this.player, {
            ...playerData,
            avatar: playerData.steam_avatar || "golly.jpg",
            country: playerData.country || "United States (US)",
            country_code: playerData.country_code || "us",
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
          `${API_BASE_URL}/players/${playerId}/ranks`
        );
        if (data?.length) {
          const ranks = data[0];
          Object.assign(this.player, {
            overall_rank: ranks.overall_rank || null,
            soldier_rank: ranks.soldier_rank || null,
            demoman_rank: ranks.demoman_rank || null,
            overall_points:
              (ranks.soldier_points || 0) + (ranks.demoman_points || 0),
            soldier_points: ranks.soldier_points || null,
            demoman_points: ranks.demoman_points || null,
          });
        }
      } catch (error) {
        console.error("Error fetching player ranks:", error);
      }
    },
    async fetchDonators() {
      try {
        const data = await cachedFetch(`${API_BASE_URL}/users/get-donators`);
        this.donators = data || [];
      } catch (error) {
        console.error("Error fetching donators:", error);
      }
    },
    startCarousel() {
      if (!this.$refs.carouselTrack || !this.donators.length) return;

      const track = this.$refs.carouselTrack;
      const cardWidth = 280;
      const resetPosition = -(cardWidth * this.donators.length);
      const speed = 50 / 60;

      let carouselPosition = 0;

      const animate = () => {
        carouselPosition -= speed;
        if (carouselPosition <= resetPosition) {
          carouselPosition = 0;
        }
        track.style.transform = `translateX(${carouselPosition}px)`;
        this.animationId = requestAnimationFrame(animate);
      };

      this.animationId = requestAnimationFrame(animate);
    },
    stopCarousel() {
      if (this.animationId) {
        cancelAnimationFrame(this.animationId);
        this.animationId = null;
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
.page-subtitle {
  color: var(--color-text-muted, #aaa);
  font-size: 18px;
  margin-top: 10px;
  text-align: center;
}

.content-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 1000px;
}

.donation-section {
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
  gap: 10px;
}

.help-icon {
  font-size: 16px;
}

.perks-panel {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  text-align: center;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.perks-title {
  color: var(--color-text, #fff);
  font-size: 28px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
}

.perks-icon {
  font-size: 32px;
}

.perks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 25px;
  margin-bottom: 30px;
}

.perk-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 25px;
  text-align: center;
  transition: all 0.3s ease;
  border: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
  overflow: hidden;
}

.perk-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  border-color: rgba(255, 255, 255, 0.2);
}

.perk-icon {
  font-size: 36px;
  margin-bottom: 15px;
  display: block;
}

.perk-title {
  color: var(--color-text, #fff);
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 12px;
}

.perk-description {
  color: var(--color-text-muted, #bbb);
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 15px;
}

.perk-tier {
  background: linear-gradient(135deg, #ffd700, #ffed4a);
  color: #000;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  display: inline-block;
}

.perks-note {
  text-align: center;
  padding: 20px;
  background: rgba(0, 123, 255, 0.1);
  border-radius: 12px;
  border: 1px solid rgba(0, 123, 255, 0.2);
}

.perks-note p {
  color: var(--color-text-muted, #ddd);
  font-size: 14px;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.note-icon {
  font-size: 16px;
}

.preview-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 40px;
}

.preview-title {
  color: var(--color-text, #fff);
  font-size: 24px;
  font-weight: 600;
  text-align: center;
  margin-bottom: 30px;
}

.banner-container {
  margin: 20px 0;
  perspective: 1000px;
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
  transition: all 0.3s ease;
}

.profile-banner:hover {
  transform: scale(1.02) !important;
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

code {
  background: rgba(255, 255, 255, 0.1);
  padding: 4px 8px;
  border-radius: 6px;
  font-family: "Courier New", monospace;
  color: #ffd700;
}

.avatar {
  width: 96px;
  height: 96px;
  border: 3px solid gold;
}

.player-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  color: var(--color-text, #fff);
  font-size: 1.25rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.rank-name {
  color: var(--color-text, #fff);
  font-weight: bold;
  font-size: small;
  margin-bottom: 10px;
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
  font-weight: 600;
  font-size: 14px;
}

.banner-block .card-text {
  font-size: 1rem;
  font-weight: 700;
}

.rank-card-body {
  padding: 15px;
}

.player-stats {
  color: var(--color-text, #fff) !important;
}

.thank-you-section {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 0px 20px rgb(0, 0, 0, 0.5);
  text-align: center;
  border: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 100px;
}

.thank-you-title {
  color: var(--color-text, #fff);
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 20px;
}

.thank-you-text {
  color: var(--color-text-muted, #bbb);
  font-size: 16px;
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto;
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
  gap: 20px;
  width: max-content;
  animation: none;
}

.donator-card {
  flex-shrink: 0;
  width: 260px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 24px;
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
  width: 64px;
  height: 64px;
  border-radius: 50%;
  border: 2px solid gold;
  margin-bottom: 16px;
  object-fit: cover;
}

.donator-info {
  margin-bottom: 12px;
}

.donator-name {
  color: var(--color-text, #fff);
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 8px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.donator-country {
  font-size: 14px;
  font-weight: 500;
  margin: 0;
  color: var(--color-text-muted, #bbb);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.country-flag {
  width: 20px;
  height: 15px;
  border-radius: 2px;
  object-fit: cover;
}

.badge-icon {
  font-size: 16px;
}

@media (max-width: 768px) {
  .donator-card {
    width: 220px;
    padding: 20px;
  }

  .donator-avatar {
    width: 56px;
    height: 56px;
  }

  .donator-name {
    font-size: 16px;
  }

  .donation-panel,
  .perks-panel {
    margin: 20px;
    padding: 30px 20px;
  }

  .perks-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .donation-title,
  .perks-title {
    font-size: 24px;
  }

  .profile-banner {
    margin: 10px;
  }
}
</style>
