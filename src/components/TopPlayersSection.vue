<template>
  <div>
    <div class="divider-container">
      <div class="divider-line"></div>
      <div class="divider-icon">▲</div>
      <div class="divider-line"></div>
    </div>
    <div class="section">
      <div class="container">
        <h2 class="section-title">
          <span class="title-accent">MOST</span> POINTS
          <span class="title-accent">GAINED</span>
        </h2>
        <div class="period-selector">
          <button
            v-for="period in periods"
            :key="period.value"
            @click="selectedPeriod = period.value"
            :class="['period-btn', { active: selectedPeriod === period.value }]"
          >
            {{ period.label }}
          </button>
        </div>
        <div class="top-players-grid">
          <div class="class-section">
            <h5 class="section-subtitle">Soldier</h5>
            <div class="players-list">
              <template v-if="loading">
                <div
                  class="player-card skeleton-card"
                  v-for="n in 3"
                  :key="'sol-skel-' + n"
                >
                  <div class="skeleton-rank"></div>
                  <div class="skeleton-avatar"></div>
                  <div class="skeleton-info">
                    <div class="skeleton-name"></div>
                    <div class="skeleton-country"></div>
                    <div class="skeleton-points"></div>
                  </div>
                </div>
              </template>
              <template v-else>
                <SmartLink
                  v-for="(player, index) in filteredTopSoldiers"
                  :key="player.id"
                  :to="{
                    name: 'PlayerPage',
                    params: { playerId: player.player_id },
                  }"
                  class="player-card"
                >
                  <div class="player-rank">#{{ index + 1 }}</div>
                  <img
                    :src="player.steam_avatar"
                    alt="Avatar"
                    class="player-avatar"
                  />
                  <div class="player-info">
                    <div class="player-name">{{ player.player_name }}</div>
                    <div class="country">
                      <img
                        :src="getFlagImageUrl(player.country_code)"
                        alt="flag"
                        class="flag-icon"
                        @error="handleImageError"
                      />
                      {{ player.country }}
                    </div>
                    <div class="points-gained">
                      <span class="points-value"
                        >+{{ player.points_gained }}</span
                      >
                      <span class="points-label">points gained</span>
                    </div>
                  </div>
                </SmartLink>
              </template>
            </div>
          </div>
          <div class="class-section">
            <h5 class="section-subtitle">Demoman</h5>
            <template v-if="loading">
              <div
                class="player-card skeleton-card"
                v-for="n in 3"
                :key="'sol-skel-' + n"
              >
                <div class="skeleton-rank"></div>
                <div class="skeleton-avatar"></div>
                <div class="skeleton-info">
                  <div class="skeleton-name"></div>
                  <div class="skeleton-country"></div>
                  <div class="skeleton-points"></div>
                </div>
              </div>
            </template>
            <template v-else>
              <div class="players-list">
                <SmartLink
                  v-for="(player, index) in filteredTopDemomen"
                  :key="player.id"
                  :to="{
                    name: 'PlayerPage',
                    params: { playerId: player.player_id },
                  }"
                  class="player-card"
                >
                  <div class="player-rank">#{{ index + 1 }}</div>
                  <img
                    :src="player.steam_avatar"
                    alt="Avatar"
                    class="player-avatar"
                  />
                  <div class="player-info">
                    <div class="player-name">{{ player.player_name }}</div>
                    <div class="country">
                      <img
                        :src="getFlagImageUrl(player.country_code)"
                        alt="flag"
                        class="flag-icon"
                        @error="handleImageError"
                      />
                      {{ player.country }}
                    </div>
                    <div class="points-gained">
                      <span class="points-value"
                        >+{{ player.points_gained }}</span
                      >
                      <span class="points-label">points gained</span>
                    </div>
                  </div>
                </SmartLink>
              </div></template
            >
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
  name: "TopPlayersSection",
  data() {
    return {
      loading: true,
      topSoldiers: [],
      topDemomen: [],
      selectedPeriod: "3day",
      periods: [
        { value: "3day", label: "Past 3 Days" },
        { value: "7day", label: "Past 7 Days" },
        { value: "30day", label: "Past 30 Days" },
      ],
    };
  },
  computed: {
    filteredTopSoldiers() {
      return this.topSoldiers
        .filter((player) => player.period_type === this.selectedPeriod)
        .sort((a, b) => b.points_gained - a.points_gained)
        .slice(0, 3);
    },
    filteredTopDemomen() {
      return this.topDemomen
        .filter((player) => player.period_type === this.selectedPeriod)
        .sort((a, b) => b.points_gained - a.points_gained)
        .slice(0, 3);
    },
  },
  methods: {
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    handleImageError(e) {
      e.target.style.display = "none";
    },
    async fetchTopPlayers() {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/get-top-gainers`,
        );
        const data = response.data;
        this.topSoldiers = data.filter((p) => p.class_type === "soldier");
        this.topDemomen = data.filter((p) => p.class_type === "demoman");
      } catch (error) {
        console.error("Error fetching top players:", error);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.fetchTopPlayers();
  },
};
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.divider-container {
  display: flex;
  align-items: center;
  gap: 16px;
  margin: 48px 0 20px;
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

.section-subtitle {
  font-size: 1.5rem;
  font-weight: 700;
  text-align: center;
  color: #ffffff;
  background: linear-gradient(135deg, #ffffff 0%, var(--color-primary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
  z-index: 10;
}

.top-players-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  max-width: 1000px;
  margin: 0 auto;
}

.class-section {
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.05) 0%,
    rgba(255, 255, 255, 0.02) 100%
  );
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.section-subtitle {
  font-size: 1.8rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.players-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.player-card {
  display: flex;
  align-items: center;
  padding: 24px;
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.1) 0%,
    rgba(255, 255, 255, 0.05) 100%
  );
  border-radius: 20px;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  text-decoration: none;
  border: 1px solid rgba(255, 255, 255, 0.08);
  position: relative;
  overflow: hidden;
  max-width: 418px;
}

.player-card::after {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.1),
    transparent
  );
  transition: left 0.6s ease;
}

.player-card:hover::after {
  left: 100%;
}

.player-card:hover {
  box-shadow: 0 10px 40px rgba(102, 126, 234, 0.5),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
  border-color: rgba(102, 126, 234, 0.4);
  cursor: pointer;
}

.player-card:nth-child(1) {
  background: linear-gradient(
    135deg,
    rgba(255, 215, 0, 0.15) 0%,
    rgba(255, 215, 0, 0.05) 100%
  );
}
.player-card:nth-child(2) {
  background: linear-gradient(
    135deg,
    rgba(192, 192, 192, 0.15) 0%,
    rgba(192, 192, 192, 0.05) 100%
  );
}
.player-card:nth-child(3) {
  background: linear-gradient(
    135deg,
    rgba(205, 127, 50, 0.15) 0%,
    rgba(205, 127, 50, 0.05) 100%
  );
}

.player-rank {
  font-size: 2rem;
  font-weight: 800;
  color: var(--color-primary);
  margin-right: 15px;
  min-width: 50px;
  text-align: center;
}

.player-card:nth-child(1) .player-rank {
  color: #ffd700;
  text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
}
.player-card:nth-child(2) .player-rank {
  color: #c0c0c0;
  text-shadow: 0 0 10px rgba(192, 192, 192, 0.5);
}
.player-card:nth-child(3) .player-rank {
  color: #cd7f32;
  text-shadow: 0 0 10px rgba(205, 127, 50, 0.5);
}

.player-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  align-items: flex-start;
}

.player-avatar {
  width: 75px;
  height: 75px;
  border-radius: 50%;
  margin-right: 25px;
  border: 3px solid var(--color-primary);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.4);
  transition: all 0.3s ease;
}

.player-card:hover .player-avatar {
  transform: scale(1.1);
  box-shadow: 0 0 30px rgba(102, 126, 234, 0.8);
}

.player-name {
  font-size: 1.3rem;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 5px;
  align-self: flex-start;
  max-width: 210px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.country {
  display: flex;
  align-items: center;
  font-weight: bold;
  color: #d5d5d5;
  margin-bottom: 10px;
  align-self: flex-start;
}

.flag-icon {
  margin-right: 8px;
  width: 20px;
  height: auto;
}

.points-gained {
  display: flex;
  align-items: center;
  gap: 8px;
  align-self: flex-start;
}

.points-value {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-primary);
}

.points-label {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.6);
}

.period-selector {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin: 25px 0 35px 0;
}

.period-btn {
  padding: 12px 24px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 25px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.period-btn:hover {
  background: rgba(255, 255, 255, 0.12);
  border-color: rgba(102, 126, 234, 0.5);
  color: #ffffff;
}

.period-btn.active {
  background: linear-gradient(
    135deg,
    var(--color-primary) 0%,
    var(--color-primary-dark) 100%
  );
  border-color: var(--color-primary);
  color: #ffffff;
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
}

@media (max-width: 768px) {
  .top-players-grid {
    grid-template-columns: 1fr;
    gap: 30px;
  }

  .class-section {
    padding: 20px;
  }

  .player-card {
    max-width: none;
    padding: 15px;
  }

  .player-rank {
    font-size: 1.5rem;
    margin-right: 15px;
    min-width: 40px;
  }

  .player-name {
    font-size: 1.1rem;
  }

  .points-value {
    font-size: 1rem;
  }

  .period-selector {
    flex-direction: column;
    gap: 10px;
    padding: 0 20px;
  }

  .period-btn {
    width: 100%;
  }

  .section-title {
    font-size: 2rem;
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
  display: flex;
  align-items: center;
  padding: 24px;
}

.skeleton-rank,
.skeleton-avatar,
.skeleton-name,
.skeleton-country,
.skeleton-points {
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

.skeleton-rank {
  width: 40px;
  height: 36px;
  border-radius: 6px;
  margin-right: 15px;
  flex-shrink: 0;
}
.skeleton-avatar {
  width: 75px;
  height: 75px;
  border-radius: 50%;
  margin-right: 25px;
  flex-shrink: 0;
}
.skeleton-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
}
.skeleton-name {
  width: 55%;
  height: 18px;
}
.skeleton-country {
  width: 40%;
  height: 14px;
}
.skeleton-points {
  width: 70%;
  height: 16px;
}
</style>
