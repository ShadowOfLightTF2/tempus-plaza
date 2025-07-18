<template>
  <div id="app" @click="closeDropdown">
    <!-- Update Banner -->
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
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-custom">
      <div class="container-fluid">
        <!-- Mobile navbar toggler -->
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
        <!-- Collapsible navbar content -->
        <div class="collapse navbar-collapse" id="navbarNav">
          <!-- Main navigation links - centered -->
          <ul class="navbar-nav mx-auto">
            <li class="nav-item me-3">
              <router-link
                to="/"
                class="nav-link nav-link-bold"
                :class="{ active: isNavItemActive('Home') }"
              >
                <i class="bi bi-house"></i> Home
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/servers"
                class="nav-link nav-link-bold"
                :class="{ active: isNavItemActive('Servers') }"
              >
                <i class="bi bi-globe"></i> Servers
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/activity"
                class="nav-link nav-link-bold"
                :class="{ active: isNavItemActive('Activity') }"
              >
                <i class="bi bi-graph-up"></i> Activity
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/maps"
                class="nav-link nav-link-bold"
                :class="{ active: isNavItemActive('Maps') }"
              >
                <i class="bi bi-map"></i> Maps
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/players"
                class="nav-link nav-link-bold"
                :class="{ active: isNavItemActive('Players') }"
              >
                <i class="bi bi-trophy"></i> Players
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/lookup"
                class="nav-link nav-link-bold"
                :class="{ active: isNavItemActive('Lookup') }"
              >
                <i class="bi bi-search"></i> Lookup
              </router-link>
            </li>
            <li class="nav-item me-3">
              <router-link
                to="/donate"
                class="nav-link nav-link-bold"
                :class="{ active: isNavItemActive('Donate') }"
              >
                <i class="bi bi-heart"></i> Donate
              </router-link>
            </li>
            <!-- Search container -->
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
                    (showLoading ||
                      (searchResults &&
                        (searchResults.maps.length ||
                          searchResults.players.length)))
                  "
                >
                  <div v-if="showLoading" class="loading-container">
                    <div class="loading-spinner"></div>
                    <span>Searching...</span>
                  </div>
                  <div v-else>
                    <div v-if="searchResults.maps.length">
                      <h6>Maps</h6>
                      <ul>
                        <SmartLink
                          v-for="map in searchResults.maps"
                          :key="map.id"
                          :to="{ name: 'MapPage', params: { mapId: map.id } }"
                          tag="li"
                          class="search-result-item"
                        >
                          {{ map.name || `Map ID: ${map.id}` }}
                        </SmartLink>
                      </ul>
                    </div>
                    <div v-if="searchResults.players.length">
                      <h6>Players</h6>
                      <ul>
                        <SmartLink
                          v-for="player in searchResults.players"
                          :key="player.id"
                          :to="{
                            name: 'PlayerPage',
                            params: { playerId: player.id },
                          }"
                          tag="li"
                          class="search-result-item"
                        >
                          {{ player.name || `Player ID: ${player.id}` }}
                        </SmartLink>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- User authentication section -->
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
                        <label
                          class="form-check-label"
                          for="preferenceOverall"
                          style="
                            color: var(--color-text-clickable);
                            cursor: pointer;
                          "
                        >
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
                        <label
                          class="form-check-label"
                          for="preferenceSoldier"
                          style="
                            color: var(--color-text-clickable);
                            cursor: pointer;
                          "
                        >
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
                        <label
                          class="form-check-label"
                          for="preferenceDemoman"
                          style="
                            color: var(--color-text-clickable);
                            cursor: pointer;
                          "
                        >
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
                        <label
                          class="form-check-label"
                          for="genderMale"
                          style="
                            color: var(--color-text-clickable);
                            cursor: pointer;
                          "
                        >
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
                        <label
                          class="form-check-label"
                          for="genderFemale"
                          style="
                            color: var(--color-text-clickable);
                            cursor: pointer;
                          "
                        >
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
                    <hr class="dropdown-divider" />
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
          <a href="https://tempus2.xyz" class="footer-link">
            <i class="bi bi-globe"></i> Tempus
          </a>
          <a href="https://discord.gg/ed9s9H22" class="footer-link">
            <i class="bi bi-discord"></i> Discord
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import DOMPurify from "dompurify";
import debounce from "debounce";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "App",
  data() {
    return {
      searchQuery: "",
      searchResults: null,
      showLoading: false,
      debounceTimer: null,
      rankPreference: "overall",
      gender: "male",
      donator: 0,
      currentUser: null,
      isUpdating: false,
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
  methods: {
    isNavItemActive(routeName) {
      return this.$route.name === routeName;
    },
    async checkUpdateStatus() {
      try {
        const response = await fetch(`${API_BASE_URL}/is-updating`);
        const data = await response.json();
        this.isUpdating = data.isUpdating && !this.updateDismissed;
      } catch (error) {
        console.error("Error fetching update status:", error);
      }
    },
    dismissUpdate() {
      this.updateDismissed = true;
      this.isUpdating = false;
    },
    closeDropdown() {
      this.searchResults = null;
      this.showLoading = false;
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
      this.updateUserPreferences();
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
      } catch (error) {
        console.error("Failed to update user preferences:", error);
      }
    },
    getPlayerRoute() {
      const playerId = this.currentUser.playerid;
      return { name: "PlayerPage", params: { playerId } };
    },
    async fetchSearchResults() {
      if (!this.searchQuery.trim() || this.searchQuery.trim().length < 2) {
        this.searchResults = null;
        this.showLoading = false;
        return;
      }

      this.showLoading = true;

      try {
        const response = await fetch(`${API_BASE_URL}/search`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ query: this.searchQuery.trim() }),
        });

        if (!response.ok) throw new Error("Failed to fetch search results");
        const data = await response.json();

        if (data.players && data.players.length > 20)
          data.players = data.players.slice(0, 20);
        if (data.maps && data.maps.length > 5)
          data.maps = data.maps.slice(0, 5);

        this.searchResults = data;
      } catch (error) {
        console.error("Error fetching search results:", error);
        this.searchResults = null;
      } finally {
        this.showLoading = false;
      }
    },
    debouncedSearch() {
      clearTimeout(this.debounceTimer);

      if (!this.searchQuery.trim()) {
        this.searchResults = null;
        this.showLoading = false;
        return;
      }

      this.showLoading = true;
      this.debounceTimer = setTimeout(() => {
        this.fetchSearchResults();
      }, 500);
    },
    sanitize(text) {
      return DOMPurify.sanitize(text);
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
    console.log("Component mounted, checking auth...");

    try {
      //console.log("Document cookies:", document.cookie);

      const isAuthenticated = await this.checkAuthStatus();
      console.log("Auth check result:", isAuthenticated);

      if (isAuthenticated) {
        //console.log("User is authenticated, fetching user data...");
        const userData = await this.fetchUserData();

        if (userData) {
          this.currentUser = userData;
          this.rankPreference = userData.rankpref || "overall";
          this.gender = userData.gender || "male";
          this.donator = userData.donator || 0;
          this.colorPreference = userData.color || "blue";
        }
      } else {
        console.log("User is not authenticated");
      }
    } catch (error) {
      console.error("Error during authentication check:", error);
      this.currentUser = null;
    }
  },
  beforeDestroy() {
    clearTimeout(this.debounceTimer);
  },
};
</script>

<style>
body,
html {
  margin: 0;
  padding: 0;
  height: 100%;
  font-family: "Segoe UI";
  background: var(--color-background-new) !important;
}

#app {
  font-family: "Segoe UI";
  background: var(--color-background-new);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
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
  background: rgba(74, 111, 165, 0.8);
}

.navbar-dark .navbar-nav .nav-link:focus,
.navbar-dark .navbar-nav .nav-link:hover {
  background: rgba(74, 111, 165, 0.8);
}

.navbar-dark .navbar-nav .router-link-exact-active {
  background: rgba(74, 111, 165, 0.8);
  font-weight: bold;
}

.nav-link-bold.active {
  background: rgba(74, 111, 165, 0.8);
  font-weight: bold;
}

.navbar-dark {
  font-size: large;
}

.bg-custom {
  background: var(--color-dark);
  border: 1px solid var(--color-border-soft);
}

.bg-dark-custom {
  background: var(--color-background-new);
}

.user {
  color: var(--color-text);
  font-weight: bold;
}

.form-check-input:checked {
  background: rgba(74, 111, 165, 0.8) !important;
  border-color: var(--color-primary);
}

.login-button {
  color: var(--color-text) !important;
  font-weight: bold !important;
  border-radius: 0;
}

.login-button:hover {
  background: rgba(74, 111, 165, 0.8) !important;
  border-radius: 0;
}

.bi-steam {
  color: var(--color-text);
}

.nav-link-bold {
  color: #ffffff !important;
  font-weight: bold !important;
  border-radius: 8px;
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
  background: rgba(74, 111, 165, 0.8);
  color: var(--color-text);
  text-decoration: none;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

@media (max-width: 768px) {
  .navbar-collapse {
    background: var(--color-dark);
    padding: 1rem;
    border-radius: 8px;
    margin-top: 0.5rem;
  }

  .navbar-nav .nav-item {
    margin-bottom: 0.5rem;
  }

  .navbar-nav .nav-item:last-child {
    margin-bottom: 0;
  }

  .search-container {
    margin: 1rem 0;
  }

  .search-input {
    width: 100%;
  }

  .search-results-dropdown {
    width: 100%;
    left: 0;
    right: 0;
  }

  .navbar-dark {
    font-size: medium;
  }

  .navbar-right {
    flex-direction: column;
    align-items: stretch !important;
  }

  .search-container {
    margin-right: 0 !important;
    margin-bottom: 1rem;
  }

  .user-section {
    align-self: center;
  }
}
</style>

<style scoped>
.update-banner {
  background: linear-gradient(135deg, #ff6b35, #f7931e);
  color: white;
  position: relative;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  animation: slideDown 0.3s ease-out;
}

.update-banner-content {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px 20px;
  gap: 12px;
  max-width: 1200px;
  margin: 0 auto;
}

.update-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
}

.spinning {
  animation: spin 2s linear infinite;
}

.update-text {
  font-weight: 500;
  font-size: 14px;
  flex: 1;
  text-align: center;
}

.update-close {
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

.update-close:hover {
  background-color: rgba(255, 255, 255, 0.2);
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
  justify-content: center;
}

.navbar-right {
  display: flex;
  align-items: center;
}

.dropdown-toggle {
  border-radius: 8px;
  border: 0;
  background: var(--color-dark);
  color: var(--color-text);
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

.dropdown-toggle:hover {
  background: rgba(74, 111, 165, 0.8) !important;
  color: var(--color-text);
}

.dropdown-menu {
  background: var(--color-box);
  border: 1px solid var(--color-border);
}

.dropdown-item {
  color: var(--color-text);
  font-weight: bold;
}

.dropdown-item:hover,
.dropdown-item:focus {
  background: rgba(74, 111, 165, 0.8);
}

.dropdown-menu .dropdown-item:hover,
.dropdown-menu .dropdown-item:focus {
  background: rgba(74, 111, 165, 0.8);
  color: var(--color-text);
}

.dropdown-item.clickable {
  cursor: pointer;
  color: var(--color-text-clickable);
}

.dropdown-item.clickable:hover {
  background: rgba(74, 111, 165, 0.8);
  color: var(--color-text);
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

.search-container {
  position: relative;
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
  background: var(--color-box);
  border: 2px solid var(--color-border-soft);
  border-radius: 12px;
  color: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
}

.search-input:focus {
  background: rgba(74, 111, 165, 0.8);
  outline: none;
  box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.212);
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
  background: rgba(74, 111, 165, 0.8);
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
