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
                <i class="bi bi-globe"></i> Maps
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
                  searchResults &&
                  (searchResults.maps.length || searchResults.players.length)
                "
              >
                <div v-if="searchResults.maps.length">
                  <h6>Maps</h6>
                  <ul>
                    <li
                      v-for="map in searchResults.maps"
                      :key="map.id"
                      @click="goToMap(map.id)"
                      v-html="sanitize(map.name || `Map ID: ${map.id}`)"
                    ></li>
                  </ul>
                </div>
                <div v-if="searchResults.players.length">
                  <h6>Players</h6>
                  <ul>
                    <li
                      v-for="player in searchResults.players"
                      :key="player.id"
                      @click="goToPlayer(player.id)"
                      v-html="
                        sanitize(player.name || `Player ID: ${player.id}`)
                      "
                    ></li>
                  </ul>
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
                    <a
                      class="dropdown-item clickable"
                      @click="goToProfile"
                      href="#"
                    >
                      My profile
                    </a>
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
import Cookies from "js-cookie";
import debounce from "debounce";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "App",
  data() {
    return {
      searchQuery: "",
      searchResults: null,
      rankPreference: "overall",
      gender: "male",
      currentUser: null,
      isUpdating: false,
      updateDismissed: false,
    };
  },
  computed: {
    user() {
      return this.currentUser;
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
        console.log(data);
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
    },

    loginWithSteam() {
      window.location.href = `${API_BASE_URL}/auth/steam`;
    },
    logout() {
      console.log(Cookies.get("user"), "user");
      console.log(Cookies.get("session"), "session");
      Cookies.remove("user", {
        path: "/",
        secure: false,
        sameSite: "lax",
      });
      Cookies.remove("session", {
        path: "/",
        secure: false,
        sameSite: "lax",
      });
      this.currentUser = null;
      console.log(Cookies.get("user"));
      this.$router.push({ name: "Home" });
    },
    async updateUserPreferences() {
      if (this.currentUser && this.currentUser.steamid) {
        try {
          const response = await fetch(
            `${API_BASE_URL}/users/update-user/${this.currentUser.playerid}`,
            {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                rankPref: this.rankPreference,
                gender: this.gender,
              }),
            }
          );
          if (!response.ok)
            throw new Error("Failed to update user preferences");
          const data = await response.json();
          console.log("User preferences updated:", data);

          const updatedUser = {
            ...this.currentUser,
            rankpref: this.rankPreference,
            gender: this.gender,
          };

          this.currentUser = updatedUser;
          Cookies.set("user", JSON.stringify(updatedUser), {
            expires: 3650,
            sameSite: "Lax",
          });
        } catch (error) {
          console.error("Error updating user preferences:", error);
        }
      }
    },

    async goToProfile() {
      if (!this.currentUser || !this.currentUser.steamid) {
        console.log("No user logged in or invalid user data.");
        return;
      }
      try {
        const response = await fetch(
          `${API_BASE_URL}/players/login/${this.currentUser.steamid}`,
          {
            method: "GET",
            credentials: "include",
          }
        );
        if (!response.ok) throw new Error("Failed to fetch playerId");
        const data = await response.json();
        const playerId = data[0].id;
        if (playerId) {
          this.$router.push({ name: "PlayerPage", params: { playerId } });
        } else {
          throw new Error("playerId missing in response");
        }
      } catch (error) {
        console.error("Failed to login to backend:", error);
      }
    },
    goToMap(mapId) {
      this.$router.push({ name: "Records", params: { mapId } });
      this.searchResults = null;
    },
    goToPlayer(playerId) {
      this.$router.push({ name: "PlayerPage", params: { playerId } });
      this.searchResults = null;
    },
    async fetchSearchResults() {
      if (this.searchQuery.trim()) {
        try {
          const response = await fetch(`${API_BASE_URL}/search`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ query: this.searchQuery }),
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
        }
      } else {
        this.searchResults = null;
      }
    },
    sanitize(text) {
      return DOMPurify.sanitize(text);
    },
  },
  created() {
    this.debouncedSearch = debounce(this.fetchSearchResults, 500);
    this.checkUpdateStatus();
    this.updateInterval = setInterval(this.checkUpdateStatus, 30000);
  },
  beforeDestroy() {
    clearInterval(this.updateInterval);
  },
  watch: {
    searchQuery() {
      this.debouncedSearch();
    },
  },
  mounted() {
    const userCookie = Cookies.get("user");
    if (userCookie) {
      try {
        const user = JSON.parse(userCookie);
        this.currentUser = user;
        this.rankPreference = user.rankpref || "overall";
        this.gender = user.gender || "male";
        this.donator = user.donator || 0;
      } catch (error) {
        console.error("Error parsing user cookie:", error);
        this.logout();
      }
    } else {
      this.currentUser = null;
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
  font-family: "Segoe UI";
  background: var(--color-background) !important;
}

#app {
  font-family: "Segoe UI";
  background: var(--color-background);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
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
  background: var(--color-primary);
}

.navbar-dark .navbar-nav .nav-link:focus,
.navbar-dark .navbar-nav .nav-link:hover {
  background: var(--color-primary);
}

.navbar-dark .navbar-nav .router-link-exact-active {
  background: var(--color-primary);
  font-weight: bold;
}

.nav-link-bold.active {
  background: var(--color-primary);
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
  background: var(--color-background);
}

.user {
  color: var(--color-text);
  font-weight: bold;
}

.form-check-input:checked {
  background: var(--color-primary) !important;
  border-color: var(--color-primary);
}

.login-button {
  color: var(--color-text) !important;
  font-weight: bold !important;
  border-radius: 0;
}

.login-button:hover {
  background: var(--color-primary) !important;
  border-radius: 0;
}

.bi-steam {
  color: var(--color-text);
}

.nav-link-bold {
  color: #ffffff !important;
  font-weight: bold;
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
}

.footer-link:hover {
  background: var(--color-primary);
  color: var(--color-text);
  text-decoration: none;
}

.footer-link i {
  font-size: 1rem;
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

.row-divider {
  width: 100%;
  border: none;
  border-top: 1px solid var(--color-primary) !important;
  margin: 20px 0;
}

.dropdown-toggle {
  border-radius: 8px;
  border: 0;
  background: var(--color-dark);
  color: var(--color-text);
}

.login-button {
  border-radius: 8px;
}

.avatar {
  width: 32px;
  height: 32px;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
}

.dropdown-toggle:hover {
  background: var(--color-primary);
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
  background: var(--color-primary);
}

.dropdown-menu .dropdown-item:hover,
.dropdown-menu .dropdown-item:focus {
  background: var(--color-primary);
  color: var(--color-text);
}

.dropdown-item.clickable {
  cursor: pointer;
  color: var(--color-text-clickable);
}

.dropdown-item.clickable:hover {
  background: var(--color-primary);
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
  border: 2px solid rgba(68, 68, 68, 0.3);
  border-radius: 12px;
  color: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.1);
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
  color: #ffffff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.search-results-dropdown li:hover {
  background: var(--color-primary);
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
