<template>
  <div id="app" @click="closeDropdown">
    <div
      v-if="showLoginPopup"
      class="login-popup-overlay"
      @click="closeLoginPopup"
    >
      <div class="login-popup" @click.stop>
        <div class="login-popup-content">
          <button
            class="popup-close"
            @click="closeLoginPopup"
            aria-label="Close"
          >
            <svg
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>

          <div class="popup-header">
            <h3>Welcome to Tempus Plaza!</h3>
            <p>Login for enhanced features and personalized experience</p>
            <small class="popup-disclaimer"
              >This popup will not appear again</small
            >
          </div>

          <div class="popup-features">
            <div class="feature-item">
              <i class="bi bi-person-circle"></i>
              <span>Easy navigation to your profile</span>
            </div>
            <div class="feature-item">
              <i class="bi bi-person-fill-gear"></i>
              <span>Personalized player profiles</span>
            </div>
            <div class="feature-item">
              <i class="bi bi-graph-up"></i>
              <span>See your own run on map leaderboards</span>
            </div>
            <div class="feature-item">
              <i class="bi bi-trophy"></i>
              <span>Automatically loads your player lookup</span>
            </div>
          </div>
          <div class="popup-actions">
            <button class="btn login-popup-btn" @click="loginWithSteam">
              <i class="bi bi-steam"></i> Login with Steam
            </button>
            <button class="btn continue-btn" @click="closeLoginPopup">
              Continue without login
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isUpdating" class="update-banner">
      <div class="update-banner-content">
        <div class="update-icon">
          <svg
            class="spinning"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M21 12a9 9 0 11-6.219-8.56" />
          </svg>
        </div>
        <span class="update-text">
          <span class="d-none d-md-inline"
            >Database is updating - Performance may be temporarily affected and
            some data may be missing</span
          >
          <span class="d-md-none">Database updating...</span>
        </span>
        <button
          class="update-close"
          @click="dismissUpdate"
          aria-label="Dismiss"
        >
          <svg
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <!-- <div class="progress-bar-container">
        <div
          class="progress-bar"
          :style="{ width: updatingPercentage + '%' }"
        ></div>
      </div> -->
    </div>
    <nav class="navbar navbar-expand-lg bg-custom">
      <div class="container-fluid">
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item me-3">
              <router-link
                to="/"
                class="nav-link"
                :class="{ active: isNavItemActive('Home') }"
                @click="closeNavbar"
              >
                <i class="bi bi-house"></i> Home
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/servers"
                class="nav-link"
                :class="{ active: isNavItemActive('Servers') }"
                @click="closeNavbar"
              >
                <i class="bi bi-globe"></i> Servers
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/activity"
                class="nav-link"
                :class="{ active: isNavItemActive('Activity') }"
                @click="closeNavbar"
              >
                <i class="bi bi-graph-up"></i> Activity
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/maps"
                class="nav-link"
                :class="{ active: isNavItemActive('Maps') }"
                @click="closeNavbar"
              >
                <i class="bi bi-map"></i> Maps
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/players"
                class="nav-link"
                :class="{ active: isNavItemActive('Players') }"
                @click="closeNavbar"
              >
                <i class="bi bi-trophy"></i> Players
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/compare"
                class="nav-link"
                :class="{ active: isNavItemActive('Compare') }"
                @click="closeNavbar"
              >
                <i class="bi bi-bar-chart"></i> Compare
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/lookup"
                class="nav-link"
                :class="{ active: isNavItemActive('Lookup') }"
                @click="closeNavbar"
              >
                <i class="bi bi-search"></i> Lookup
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/donate"
                class="nav-link"
                :class="{ active: isNavItemActive('Donate') }"
                @click="closeNavbar"
              >
                <i class="bi bi-heart"></i> Donate
              </router-link>
            </li>
            <div class="navbar-right" v-if="!isHomePage">
              <div class="search-container me-3" @click.stop>
                <div class="search-input-wrapper">
                  <svg
                    class="search-icon"
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                  </svg>
                  <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search..."
                    class="search-input"
                  />
                </div>
                <div
                  class="search-results-dropdown"
                  v-if="
                    searchQuery.trim() &&
                    (loadingMaps || loadingPlayers || searchResults)
                  "
                >
                  <div class="search-section">
                    <h6>Maps</h6>
                    <div v-if="loadingMaps" class="loading-container">
                      <div class="loading-spinner"></div>
                      <span>Loading maps...</span>
                    </div>
                    <ul v-else-if="searchResults && searchResults.maps.length">
                      <SmartLink
                        v-for="map in searchResults.maps"
                        :key="map.id"
                        :to="{ name: 'MapPage', params: { mapId: map.id } }"
                        tag="li"
                        class="search-result-item"
                        @click.native="handleSearchResultClick"
                      >
                        {{ map.name || `Map ID: ${map.id}` }}
                      </SmartLink>
                    </ul>
                    <div
                      v-else-if="!loadingMaps && searchResults"
                      class="no-results"
                    >
                      No maps found
                    </div>
                  </div>
                  <div class="search-section">
                    <h6>Players</h6>
                    <div v-if="loadingPlayers" class="loading-container">
                      <div class="loading-spinner"></div>
                      <span>Loading players...</span>
                    </div>
                    <ul
                      v-else-if="searchResults && searchResults.players.length"
                    >
                      <SmartLink
                        v-for="player in searchResults.players"
                        :key="player.id"
                        :to="{
                          name: 'PlayerPage',
                          params: { playerId: player.id },
                        }"
                        tag="li"
                        class="search-result-item"
                        @click.native="handleSearchResultClick"
                      >
                        {{ player.name || `Player ID: ${player.id}` }}
                      </SmartLink>
                    </ul>
                    <div
                      v-else-if="!loadingPlayers && searchResults"
                      class="no-results"
                    >
                      No players found
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="user-section">
              <button
                v-if="!user || !user.steamid"
                class="btn login-button"
                @click="loginWithSteam"
              >
                <i class="bi bi-steam"></i> Login with Steam
              </button>
              <div v-if="user && user.steamid" class="dropdown">
                <button
                  class="btn dropdown-toggle d-flex align-items-center"
                  type="button"
                  id="playerDropdown"
                  data-bs-toggle="dropdown"
                  data-bs-display="static"
                  aria-expanded="false"
                  style="color: var(--color-text); font-weight: bold"
                >
                  <img
                    :src="user.avatar"
                    alt="avatar"
                    class="me-2 avatar"
                    v-if="user.avatar"
                  />
                  <span>{{ user.name }}</span>
                </button>

                <ul
                  class="dropdown-menu dropdown-menu-end"
                  aria-labelledby="playerDropdown"
                >
                  <li>
                    <SmartLink
                      tag="a"
                      :to="getPlayerRoute()"
                      class="dropdown-item clickable"
                    >
                      My profile
                    </SmartLink>
                  </li>
                  <li>
                    <div class="dropdown-item non-clickable">
                      <h6>Rank preference</h6>
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="radio"
                          id="preferenceOverall"
                          name="rankPreference"
                          v-model="rankPreference"
                          value="overall"
                          @change="updateUserPreferences"
                        />
                        <label class="form-check-label" for="preferenceOverall">
                          Overall
                        </label>
                      </div>
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="radio"
                          id="preferenceSoldier"
                          name="rankPreference"
                          v-model="rankPreference"
                          value="soldier"
                          @change="updateUserPreferences"
                        />
                        <label class="form-check-label" for="preferenceSoldier">
                          Soldier
                        </label>
                      </div>
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="radio"
                          id="preferenceDemoman"
                          name="rankPreference"
                          v-model="rankPreference"
                          value="demoman"
                          @change="updateUserPreferences"
                        />
                        <label class="form-check-label" for="preferenceDemoman">
                          Demoman
                        </label>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="dropdown-item non-clickable">
                      <h6>Rank gender</h6>
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="radio"
                          id="genderMale"
                          name="gender"
                          v-model="gender"
                          value="male"
                          @change="updateUserPreferences"
                        />
                        <label class="form-check-label" for="genderMale">
                          Male
                        </label>
                      </div>
                      <div class="form-check">
                        <input
                          class="form-check-input"
                          type="radio"
                          id="genderFemale"
                          name="gender"
                          v-model="gender"
                          value="female"
                          @change="updateUserPreferences"
                        />
                        <label class="form-check-label" for="genderFemale">
                          Female
                        </label>
                      </div>
                    </div>
                  </li>
                  <li @click.stop>
                    <div class="dropdown-item non-clickable">
                      <h6>Banner colour</h6>
                      <div
                        class="color-picker-container"
                        :class="{ 'position-relative': true }"
                      >
                        <div
                          v-for="(color, index) in colorOptions"
                          :key="index"
                          class="color-option-wrapper"
                          @click="selectColor(color.value)"
                        >
                          <input
                            class="d-none"
                            type="radio"
                            :id="'color-' + index"
                            name="colorPreference"
                            v-model="colorPreference"
                            :value="color.value"
                            @change="updateUserPreferences"
                            :disabled="donator === 0"
                          />
                          <label
                            class="color-swatch"
                            :for="'color-' + index"
                            :class="{
                              selected: colorPreference === color.value,
                            }"
                            :style="{ backgroundColor: color.color }"
                          ></label>
                        </div>
                        <div
                          v-if="showTooltip"
                          class="supporter-tooltip"
                          @click.stop
                        >
                          <div class="tooltip-content">
                            <p>
                              You need to become a supporter to select colors
                            </p>
                            <button @click="goToDonate" class="tooltip-button">
                              Become a Supporter
                            </button>
                          </div>
                          <div class="tooltip-arrow"></div>
                        </div>
                      </div>
                    </div>
                  </li>
                  <li>
                    <a class="dropdown-item clickable" @click="logout" href="#">
                      Logout
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </ul>
        </div>
      </div>
    </nav>
    <router-view />
    <footer class="footer">
      <div class="container-fluid">
        <div class="footer-content">
          <a
            href="https://tempus2.xyz"
            class="footer-link"
            target="_blank"
            rel="noopener noreferrer"
          >
            <i class="bi bi-globe"></i> Tempus
          </a>
          <a
            href="https://discord.gg/U48JYd9h99"
            class="footer-link"
            target="_blank"
            rel="noopener noreferrer"
          >
            <i class="bi bi-discord"></i> Discord
          </a>
          <a
            href="https://docs.google.com/spreadsheets/d/1kL76rEPL2AtMSu5RWI2VtW5ZJ2QIQx61BNowdfN-09M/edit?usp=sharing"
            class="footer-link"
            target="_blank"
            rel="noopener noreferrer"
          >
            <i class="bi bi-journals"></i> Encyclopedia
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import DOMPurify from "dompurify";
import debounce from "debounce";
import { provide, reactive } from "vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "App",
  data() {
    return {
      showLoginPopup: false,
      hasVisitedBefore: false,
      searchQuery: "",
      searchResults: { maps: [], players: [] },
      loadingMaps: false,
      loadingPlayers: false,
      debounceTimer: null,
      rankPreference: "overall",
      gender: "male",
      donator: 0,
      currentUser: null,
      isUpdating: false,
      isAnnouncement: false,
      updatingPercentage: 0,
      updateDismissed: false,
      showTooltip: false,
      tooltipTimeout: null,
      colorPreference: "blue",
      colorOptions: [
        { value: "blue", color: "var(--color-banner-blue-1)" },
        { value: "red", color: "var(--color-banner-red-1)" },
        { value: "green", color: "var(--color-banner-green-1)" },
        { value: "purple", color: "var(--color-banner-purple-1)" },
        { value: "yellow", color: "var(--color-banner-yellow-1)" },
        { value: "cyan", color: "var(--color-banner-cyan-1)" },
        { value: "orange", color: "var(--color-banner-orange-1)" },
        { value: "teal", color: "var(--color-banner-teal-1)" },
        { value: "indigo", color: "var(--color-banner-indigo-1)" },
        { value: "pink", color: "var(--color-banner-pink-1)" },
      ],
      profileUpdateTracker: reactive({
        rank: "overall",
        color: "blue",
        gender: "male",
        updateCount: 0,
      }),
    };
  },
  computed: {
    user() {
      return this.currentUser;
    },
    isHomePage() {
      return this.$route.name === "Home";
    },
  },
  provide() {
    return {
      profileUpdateTracker: this.profileUpdateTracker,
    };
  },
  methods: {
    closeNavbar() {
      const navbarCollapse = document.getElementById("navbarNav");
      const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
        toggle: false,
      });
      bsCollapse.hide();
    },
    handleSearchResultClick() {
      this.closeNavbar();
      this.searchQuery = "";
      this.searchResults = { maps: [], players: [] };
    },
    closeLoginPopup() {
      this.showLoginPopup = false;
      localStorage.setItem("tempus_popup_shown", "true");
    },

    checkFirstVisit() {
      const hasSeenPopup = localStorage.getItem("tempus_popup_shown");
      return !hasSeenPopup;
    },
    isNavItemActive(routeName) {
      return this.$route.name === routeName;
    },
    async checkUpdateStatus() {
      try {
        const response = await fetch(`${API_BASE_URL}/is-updating`);
        const data = await response.json();
        this.isUpdating = data.isUpdating && !this.updateDismissed;
        this.updatingPercentage = data.progress;
      } catch (error) {
        console.error("Error fetching update status:", error);
      }
    },
    dismissUpdate() {
      this.updateDismissed = true;
      this.isUpdating = false;
    },
    dismissAnnouncement() {
      this.showAnnouncement = false;
    },
    closeDropdown() {
      this.searchResults = null;
      this.loadingMaps = false;
      this.loadingPlayers = false;
    },
    loginWithSteam() {
      window.location.href = `${API_BASE_URL}/auth/steam`;
    },
    async logout() {
      try {
        const response = await fetch(`${API_BASE_URL}/auth/logout`, {
          method: "POST",
          credentials: "include",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (response.ok) {
          window.location.reload();
        } else {
          console.error("Logout failed:", response.status);
        }
      } catch (error) {
        console.error("Logout failed:", error);
      }
    },
    async selectColor(colorValue) {
      const user = await this.fetchUserData();
      if (user.donator === 0) {
        this.showTooltip = true;
        if (this.tooltipTimeout) {
          clearTimeout(this.tooltipTimeout);
        }
        this.tooltipTimeout = setTimeout(() => {
          this.showTooltip = false;
        }, 3000);
        return;
      }
      this.colorPreference = colorValue;
      await this.updateUserPreferences();
    },
    async checkDonatorStatus() {
      const user = await this.fetchUserData();
      return user.donator > 0;
    },
    goToDonate() {
      this.$router.push({ name: "Donate" });
    },
    async checkAuthStatus() {
      try {
        const response = await fetch(`${API_BASE_URL}/auth/status`, {
          credentials: "include",
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        //console.log("Auth status response:", data);
        return data.isAuthenticated;
      } catch (error) {
        console.error("Error checking auth status:", error);
        return false;
      }
    },
    async fetchUserData() {
      try {
        const response = await fetch(`${API_BASE_URL}/api/get-user`, {
          credentials: "include",
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        });

        if (!response.ok) {
          console.log("Response not ok:", response.status, response.statusText);
          return null;
        }

        const result = await response.json();
        //console.log("User data response:", result);
        return result.data;
      } catch (error) {
        console.error("Error fetching user data:", error);
        return null;
      }
    },
    async updateUserPreferences() {
      const currentUser = await this.fetchUserData();
      if (currentUser.donator === 0) {
        this.colorPreference = "blue";
      }
      try {
        const response = await fetch(
          `${API_BASE_URL}/users/update-user/${this.currentUser.playerid}`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            credentials: "include",
            body: JSON.stringify({
              rankPref: this.rankPreference,
              gender: this.gender,
              color: this.colorPreference,
            }),
          }
        );

        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);

        console.log("User preferences updated successfully");

        this.profileUpdateTracker.rank = this.rankPreference;
        this.profileUpdateTracker.color = this.colorPreference;
        this.profileUpdateTracker.gender = this.gender;
        this.profileUpdateTracker.updateCount++;
      } catch (error) {
        console.error("Failed to update user preferences:", error);
      }
    },
    getPlayerRoute() {
      const playerId = this.currentUser.playerid;
      return { name: "PlayerPage", params: { playerId } };
    },
    async fetchMaps(query) {
      const response = await fetch(`${API_BASE_URL}/search/maps`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ query }),
      });

      if (!response.ok) throw new Error("Failed to fetch maps");
      return await response.json();
    },

    async fetchPlayers(query) {
      const response = await fetch(`${API_BASE_URL}/search/players`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ query }),
      });

      if (!response.ok) throw new Error("Failed to fetch players");
      return await response.json();
    },

    async fetchSearchResults() {
      const query = this.searchQuery.trim();

      this.searchResults = { maps: [], players: [] };
      this.loadingMaps = true;
      this.loadingPlayers = true;

      this.fetchMaps(query)
        .then((results) => {
          this.searchResults.maps = (results.maps || []).slice(0, 5);
        })
        .catch((error) => {
          console.error("Error fetching maps:", error);
          this.searchResults.maps = [];
        })
        .finally(() => {
          this.loadingMaps = false;
        });

      this.fetchPlayers(query)
        .then((results) => {
          this.searchResults.players = (results.players || []).slice(0, 20);
        })
        .catch((error) => {
          console.error("Error fetching players:", error);
          this.searchResults.players = [];
        })
        .finally(() => {
          this.loadingPlayers = false;
        });
    },

    debouncedSearch() {
      clearTimeout(this.debounceTimer);

      if (!this.searchQuery.trim()) {
        this.searchResults = null;
        this.loadingMaps = false;
        this.loadingPlayers = false;
        return;
      }

      this.loadingMaps = true;
      this.loadingPlayers = true;

      this.debounceTimer = setTimeout(() => {
        this.fetchSearchResults();
      }, 500);
    },
    sanitize(text) {
      return DOMPurify.sanitize(text);
    },
    async updateRankPreference(newRank) {
      this.rankPreference = newRank;
      await this.updateUserPreferences();
    },
  },
  created() {
    this.checkUpdateStatus();
    this.updateInterval = setInterval(this.checkUpdateStatus, 30000);
  },
  beforeDestroy() {
    clearInterval(this.updateInterval);
    clearTimeout(this.debounceTimer);
  },
  watch: {
    searchQuery() {
      this.debouncedSearch();
    },
  },
  async mounted() {
    try {
      const isAuthenticated = await this.checkAuthStatus();
      //console.log("Auth check result:", isAuthenticated);

      if (isAuthenticated) {
        const userData = await this.fetchUserData();

        if (userData) {
          this.currentUser = userData;
          this.rankPreference = userData.rankpref || "overall";
          this.gender = userData.gender || "male";
          this.donator = userData.donator || 0;
          this.colorPreference = userData.color || "blue";

          this.profileUpdateTracker.rank = this.rankPreference;
          this.profileUpdateTracker.color = this.colorPreference;
          this.profileUpdateTracker.gender = this.gender;
        }
      } else {
        console.log("User is not authenticated");
      }
    } catch (error) {
      console.error("Error during authentication check:", error);
      this.currentUser = null;
    }
    if (!this.currentUser && this.checkFirstVisit()) {
      this.debounceTimer = setTimeout(() => {
        this.showLoginPopup = true;
      }, 1000);
    }
  },
  beforeDestroy() {
    if (this.debounceTimer) {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = null;
    }
  },
};
</script>

<style>
body,
html {
  margin: 0;
  padding: 0;
  height: 100%;
  background: var(--color-background-new) !important;
}

body {
  padding-top: 73px !important;
}

#app {
  background: var(--color-background-new);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.navbar {
  position: fixed !important;
  top: 0;
  left: 0;
  right: 0;
  height: 75px;
  z-index: 9999;
  background: linear-gradient(
    90deg,
    rgba(17, 20, 24, 0.95) 0%,
    rgba(37, 55, 82, 0.95) 50%,
    rgba(17, 20, 24, 0.95) 100%
  ) !important;
}

.background-container {
  background-image: url("data:image/svg+xml, %3Csvg xmlns='http://www.w3.org/2000/svg' width='1728' height='1180' viewBox='0 0 1728 1180' fill='currentColor' class='position-absolute w-100 top-40 pointer-events-none' style='opacity: 0.3; z-index: 0' %3E%3Cg filter='url(%23filter0_f_226_149)'%3E%3Cpath d='M2123.75 293.105C1333.23 688.128 703.641 515.327 508.724 374.099C452.243 349.269 313.169 228.867 73.6286 197.922C-225.797 159.242 110.578 663.582 380.092 782.356C649.606 901.131 1580.73 925.224 2029.92 784.99C2601.98 606.399 3111.92 -200.674 2123.75 293.105Z' fill='url(%23paint0_radial_226_149)' fill-opacity='0.45' %3E%3C/path%3E%3C/g%3E%3Cdefs%3E%3Cfilter id='filter0_f_226_149' x='-342.14' y='-163' width='3323.17' height='1342.42' filterUnits='userSpaceOnUse' color-interpolation-filters='sRGB' %3E%3CfeFlood flood-opacity='0' result='BackgroundImageFix'%3E%3C/feFlood%3E%3CfeBlend mode='normal' in='SourceGraphic' in2='BackgroundImageFix' result='shape' %3E%3C/feBlend%3E%3CfeGaussianBlur stdDeviation='149.181' result='effect1_foregroundBlur_226_149' %3E%3C/feGaussianBlur%3E%3C/filter%3E%3CradialGradient id='paint0_radial_226_149' cx='0' cy='0' r='1' gradientUnits='userSpaceOnUse' gradientTransform='translate(2522.36 771.207) rotate(-178.79) scale(2230.36 879.25)' %3E%3Cstop stop-color='%236248FF'%3E%3C/stop%3E%3Cstop offset='0.369278' stop-color='%23E5FF48'%3E%3C/stop%3E%3Cstop offset='0.588842' stop-color='%23FF48ED'%3E%3C/stop%3E%3Cstop offset='0.708333' stop-color='%2348BDFF'%3E%3C/stop%3E%3Cstop offset='0.932292' stop-color='%236248FF'%3E%3C/stop%3E%3C/radialGradient%3E%3C/defs%3E%3C/svg%3E");
  background-repeat: repeat-y;
  background-size: 100% auto;
  min-height: 100vh;
}

::-webkit-scrollbar {
  width: 10px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #888;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(37, 55, 82, 0.8);
}

.nav-link {
  color: #ffffff !important;
  font-weight: bold !important;
  border-radius: 30px;
  padding: 10px 15px !important;
  border: 2px solid transparent !important;
}

.nav-link:hover {
  color: var(--color-text);
  border-color: #363a3d !important;
  background: rgba(255, 255, 255, 0.15);
}

.nav-link.active {
  color: var(--color-text);
  border-color: var(--color-primary, #007bff) !important;
  background: rgba(255, 255, 255, 0.1);
}
.bg-custom {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 0%,
    rgba(255, 255, 255, 0.02) 100%
  );
  border: none !important;
  border-bottom: 1px solid rgba(148, 163, 184, 0.1) !important;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
  position: relative;
}

.bg-custom::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  z-index: -1;
}

.bg-dark-custom {
  background: var(--color-background-new);
}

.user {
  color: var(--color-text);
  font-weight: bold;
}

.form-check-input:checked {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
  border-color: var(--color-primary);
}

.login-button {
  color: #ffffff !important;
  font-weight: bold !important;
  border-radius: 30px !important;
  padding: 10px 15px !important;
  border: 2px solid transparent !important;
}

.login-button:hover {
  color: var(--color-text);
  border-color: #363a3d !important;
  background: rgba(255, 255, 255, 0.05);
}
.login-button.active {
  color: var(--color-text);
  border-color: var(--color-primary, #007bff) !important;
  background: rgba(255, 255, 255, 0.1);
}

.bi-steam {
  color: var(--color-text);
}

.page-header {
  text-align: center;
  padding: 1.5rem 0;
}

.page-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: var(--color-text);
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
}

.title-icon {
  font-size: 2rem;
  filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
}

.page-subtitle {
  color: var(--color-text);
  opacity: 0.8;
  font-size: 1.1rem;
  margin: 0;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.fade-in {
  animation: fadeIn 0.5s ease-in forwards;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

.footer {
  background: var(--color-dark);
  border-top: 1px solid var(--color-border-soft);
  padding: 0.4rem 0;
  margin-top: auto;
}

.footer .container-fluid {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-content {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
}

.footer-link {
  color: var(--color-text);
  text-decoration: none;
  font-weight: bold;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: transform 0.3s ease;
}

.footer-link:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  color: var(--color-text);
  text-decoration: none;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.login-popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  backdrop-filter: blur(4px);
}

.login-popup {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  border-radius: 12px;
  max-width: 450px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  border: 1px solid var(--color-border);
}

.login-popup-content {
  padding: 2rem;
  position: relative;
}

.popup-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  color: var(--color-text);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.popup-close:hover {
  background: var(--color-dark);
  color: var(--color-text);
}

.popup-header {
  text-align: center;
  margin-bottom: 1rem;
}

.popup-header h3 {
  color: var(--color-text);
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.popup-header p {
  color: var(--color-text);
  margin: 0;
  font-size: 0.95rem;
}

.popup-disclaimer {
  color: var(--color-text-soft);
  font-size: 0.85rem;
  opacity: 0.8;
  display: block;
  margin: 0;
}

.popup-features {
  margin-bottom: 2rem;
}

.feature-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 0;
  color: var(--color-text);
  font-size: 0.9rem;
}

.feature-item i {
  margin-right: 0.75rem;
  color: var(--color-text);
  font-size: 1.1rem;
  width: 20px;
  text-align: center;
}

.popup-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.login-popup-btn {
  background: #1b2838;
  color: var(--color-text) !important;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.login-popup-btn:hover {
  background: #2a3f5f;
  transform: translateY(-1px);
}

.continue-btn {
  background: transparent;
  color: var(--color-text) !important;
  border: 1px solid var(--color-border) !important;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.continue-btn:hover {
  background: var(--color-dark) !important;
  border-color: var(--color-border) !important;
}

@media (max-width: 768px) {
  .navbar-collapse {
    background: var(--color-dark);
    padding: 1rem;
    border-radius: 8px;
    margin-top: 0.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .navbar-nav {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .navbar-nav .nav-item {
    margin-bottom: 0.5rem;
    width: 100%;
    text-align: center;
  }

  .navbar-nav .nav-item:last-child {
    margin-bottom: 0;
  }

  .navbar {
    font-size: medium;
  }

  .navbar-right {
    flex-direction: column;
    align-items: center;
    width: 100%;
  }

  .user-section {
    align-self: center;
    width: 100%;
    display: flex;
    justify-content: center;
  }

  .footer {
    padding: 0.75rem 0;
  }

  .footer .container-fluid {
    padding: 0 0.5rem;
  }

  .footer-content {
    gap: 1rem;
    justify-content: center;
  }

  .footer-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    min-width: auto;
  }
}
</style>

<style scoped>
@media (max-width: 768px) {
  .search-container {
    width: 100%;
    margin: 1rem 0;
    display: flex;
    justify-content: center;
    margin-right: 0 !important;
    margin-bottom: 1rem;
    width: 100%;
  }

  .search-input {
    width: 100%;
    max-width: 300px;
  }

  .search-results-dropdown {
    width: 90vw;
    max-width: 300px;
    left: 50%;
    transform: translateX(-50%);
    margin-top: 50px !important;
  }
}
.update-banner {
  background: linear-gradient(135deg, #ff6b35, #f7931e);
  color: white;
  position: relative;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  animation: slideDown 0.3s ease-out;
}

.announcement-banner {
  background: linear-gradient(135deg, #b85b5b, #c24949);
  color: white;
  position: relative;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  animation: slideDown 0.3s ease-out;
}

.update-banner-content,
.announcement-banner-content {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px 20px;
  gap: 12px;
  max-width: 1200px;
  margin: 0 auto;
}

.update-icon,
.announcement-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
}

.spinning {
  animation: spin 2s linear infinite;
}

.update-text,
.announcement-text {
  font-weight: 600;
  font-size: 16px;
  flex: 1;
  text-align: center;
}

.update-close,
.announcement-close {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.update-close:hover,
.announcement-close:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.progress-bar-container {
  height: 4px;
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 2px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background-color: white;
  border-radius: 2px;
  transition: width 0.3s ease;
}

@keyframes slideDown {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.navbar-nav {
  display: flex;
  align-items: center;
  justify-content: center;
}

.navbar-right {
  display: flex;
  align-items: center;
  margin-left: auto;
}

.login-button {
  border-radius: 8px;
  margin-top: 1%;
}

.avatar {
  width: 32px;
  height: 32px;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
}

.dropdown-toggle {
  color: #ffffff !important;
  font-weight: bold !important;
  border-radius: 30px !important;
  padding: 10px 15px !important;
  border: 2px solid transparent !important;
  background: transparent !important;
}

.dropdown-toggle:hover,
.dropdown-toggle:focus {
  color: var(--color-text) !important;
  border-color: #363a3d !important;
  background: rgba(255, 255, 255, 0.05) !important;
}

.dropdown-toggle:active {
  border-color: var(--color-primary, #007bff) !important;
}

.dropdown-menu {
  background: rgba(255, 255, 255, 0.05) !important;
  border: 2px solid #363a3d !important;
  border-radius: 12px !important;
  padding: 0.25rem 0;
  backdrop-filter: blur(6px);
}

.dropdown-menu[data-bs-popper] {
  margin-top: 8px !important;
}

@media (max-width: 991px) {
  .dropdown-menu {
    max-height: 60vh !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
  }
}

@media (max-width: 991px) and (max-height: 1115px) {
  .dropdown-menu {
    position: absolute !important;
    bottom: 100% !important;
    top: auto !important;
    transform: translateY(-8px) !important;
    margin: 0 !important;
    width: 100% !important;
    min-width: max-content !important;
  }

  .dropdown-menu[data-bs-popper] {
    bottom: 100% !important;
    top: auto !important;
    transform: translateY(-8px) !important;
    margin-top: 0 !important;
  }
}

.dropdown-item {
  color: #ffffff !important;
  font-weight: bold !important;
  padding: 10px 15px !important;
  border-radius: 8px !important;
}

.dropdown-item:hover,
.dropdown-item:focus {
  background: rgba(255, 255, 255, 0.05) !important;
}

.dropdown-item.clickable {
  cursor: pointer;
  transform: none !important;
}

.dropdown-item.clickable:hover {
  background: rgba(74, 111, 165, 0.4) !important;
  color: var(--color-text);
  transform: none !important;
}

.dropdown-item.non-clickable {
  cursor: default;
}

.dropdown-item.non-clickable:hover {
  background: transparent !important;
}

.dropdown-item .form-check {
  margin-left: 10px;
}

.dropdown-item h6 {
  font-weight: bold !important;
}
.color-picker-container {
  display: flex;
  flex-wrap: wrap;
  max-width: 140px;
}

.color-option-wrapper {
  margin: 2px;
}

.color-swatch {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  display: inline-block;
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
  border: 2px solid transparent;
}

.color-swatch:hover {
  border: 1px solid var(--color-border);
}

.color-swatch.selected {
  border: 2px solid var(--color-text);
}

.supporter-tooltip {
  position: absolute;
  top: -80px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--color-dark);
  border: 2px solid var(--color-primary);
  border-radius: 12px;
  padding: 12px 16px;
  min-width: 200px;
  z-index: 1000;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
  animation: tooltipSlideIn 0.3s ease-out;
}

.tooltip-content {
  text-align: center;
}

.tooltip-content p {
  margin: 0 0 8px 0;
  font-size: 13px;
  color: var(--color-text);
  font-weight: 500;
}

.tooltip-button {
  background: var(--color-primary);
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tooltip-button:hover {
  background: rgba(74, 111, 165, 0.9);
  transform: translateY(-1px);
}

.tooltip-arrow {
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-top: 8px solid var(--color-primary);
}

@keyframes tooltipSlideIn {
  from {
    opacity: 0;
    transform: translateX(-50%) translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
  }
}

.user-section {
  display: flex;
  align-items: center;
}

.search-container {
  margin-right: 15px;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  left: 16px;
  color: #888;
  z-index: 2;
}

.search-input {
  width: 200px;
  padding: 8px 8px 8px 40px;
  background: rgba(255, 255, 255, 0.1);
  border: 2px solid transparent !important;
  border-radius: 12px;
  color: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.212);
  border-color: var(--color-primary, #007bff) !important;
}

.search-input::placeholder {
  color: #888;
}

.search-results-dropdown {
  position: absolute;
  background: var(--color-box);
  border: 1px solid rgba(68, 68, 68, 0.3);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  width: 300px;
  max-height: 400px;
  overflow-y: auto;
  z-index: 1000;
  margin-top: 8px;
}

.search-results-dropdown ul {
  list-style: none;
  padding: 8px;
  margin: 0;
}

.search-results-dropdown li {
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 4px;
  background: var(--color-box);
  font-weight: bold;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.search-results-dropdown li:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  transform: translateX(4px);
}

.search-results-dropdown li:last-child {
  margin-bottom: 0;
}

.search-results-dropdown h6 {
  margin: 12px 16px 8px;
  font-size: 12px;
  font-weight: 700;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.loading-container {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 16px;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 10px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.no-results {
  padding: 8px 12px;
  color: #999;
  font-size: 0.9rem;
  font-style: italic;
}

.navbar-toggler {
  border: none;
  padding: 0.25rem 0.5rem;
}

.navbar-toggler:focus {
  box-shadow: none;
}

.navbar-toggler-icon {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}
</style>
