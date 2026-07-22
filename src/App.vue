<template>
  <div id="app" @click="closeDropdown">
    <LoginPopup v-model="showLoginPopup" @login-with-steam="loginWithSteam" />
    <AccountNotFoundPopup v-model="showErrorPopup" />
    <PointsCalculator v-model="showCalculator" />
    <UpdateBanner v-if="isUpdating" @dismiss="dismissUpdate" />

    <Navbar @open-calculator="showCalculator = true" />
    <Sidebar />

    <router-view />
    <AppFooter />
  </div>
</template>

<script>
import PointsCalculator from "@/components/utils/PointsCalculator.vue";
import LoginPopup from "@/components/popups/LoginPopup.vue";
import AccountNotFoundPopup from "@/components/popups/AccountNotFoundPopup.vue";
import UpdateBanner from "@/components/UpdateBanner.vue";
import AppFooter from "@/components/AppFooter.vue";
import Navbar from "@/components/Navbar.vue";
import Sidebar from "@/components/Sidebar.vue";
import DOMPurify from "dompurify";
import { reactive } from "vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "App",
  components: {
    PointsCalculator,
    LoginPopup,
    AccountNotFoundPopup,
    UpdateBanner,
    AppFooter,
    Navbar,
    Sidebar,
  },
  data() {
    return {
      showErrorPopup: false,
      showLoginPopup: false,
      showCalculator: false,
      isUpdating: false,
      isAnnouncement: false,
      updatingPercentage: 0,
      updateDismissed: false,

      // Object shared with Navbar.vue and Sidebar.vue via provide/inject.
      // Both components read/write directly to this so the two navs
      // (desktop navbar + mobile sidebar) always stay in sync.
      navShared: reactive({
        // search
        searchQuery: "",
        searchResults: null,
        loadingMaps: false,
        loadingPlayers: false,
        navHighlightedIndex: -1,

        // auth / user
        user: null,
        authChecking: true,

        // preferences
        rankPreference: "overall",
        gender: "male",
        colorPreference: "blue",
        bannerPattern: null,
        unlockedColors: [],
        unlockedPatterns: [],
        donator: 0,
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
          { value: "sky", color: "var(--color-banner-sky-1)" },
          { value: "rust", color: "var(--color-banner-rust-1)" },
          { value: "forest", color: "var(--color-banner-forest-1)" },
          { value: "charcoal", color: "var(--color-banner-charcoal-1)" },
          { value: "burgundy", color: "var(--color-banner-burgundy-1)" },
        ],
        patternOptions: [
          { value: null, label: "None", thumb: null },
          { value: "soldier", label: "Soldier", thumb: "/icons/soldier.png" },
          { value: "demoman", label: "Demoman", thumb: "/icons/demoman.png" },
          { value: "golly", label: "Golly", thumb: "/images/golly.jpg" },
          {
            value: "goldengolly",
            label: "Golden Golly",
            thumb: "/images/goldengolly.jpg",
            unlockMessage:
              "You need to become a supporter to select this pattern",
            donatable: true,
          },
          {
            value: "jf",
            label: "JF",
            thumb: "/images/jf-logo.png",
            unlockMessage:
              "Unlocked by playing in special Jump Fortress events",
            donatable: false,
          },
        ],

        // ui state
        sidebarOpen: false,
        showTooltip: false,
        activeTooltipColor: null,
        showPatternTooltip: false,
        activeTooltipPattern: null,
        patternTooltipMessage: "",
        patternTooltipShowDonateBtn: false,
      }),

      profileUpdateTracker: reactive({
        rank: "overall",
        color: "blue",
        gender: "male",
        pattern: null,
      }),

      debounceTimer: null,
      tooltipTimeout: null,
      patternTooltipTimeout: null,
      firstVisitTimer: null,
    };
  },
  provide() {
    return {
      profileUpdateTracker: this.profileUpdateTracker,
      navShared: this.navShared,
    };
  },
  watch: {
    "navShared.searchQuery"() {
      this.debouncedSearch();
    },
  },
  methods: {
    checkFirstVisit() {
      const hasSeenPopup = localStorage.getItem("tempus_popup_shown");
      return !hasSeenPopup;
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
      this.isAnnouncement = false;
    },
    closeDropdown() {
      this.navShared.searchResults = null;
      this.navShared.loadingMaps = false;
      this.navShared.loadingPlayers = false;
      this.navShared.navHighlightedIndex = -1;
    },
    loginWithSteam() {
      window.location.href = `${API_BASE_URL}/auth/steam`;
    },
    async logout() {
      try {
        const response = await fetch(`${API_BASE_URL}/auth/logout`, {
          method: "POST",
          credentials: "include",
          headers: { "Content-Type": "application/json" },
        });
        if (response.ok) window.location.reload();
        else console.error("Logout failed:", response.status);
      } catch (error) {
        console.error("Logout failed:", error);
      }
    },
    async selectColor(colorValue) {
      if (!this.navShared.unlockedColors.includes(colorValue)) {
        this.navShared.activeTooltipColor = colorValue;
        this.navShared.showTooltip = true;
        if (this.tooltipTimeout) clearTimeout(this.tooltipTimeout);
        this.tooltipTimeout = setTimeout(() => {
          this.navShared.showTooltip = false;
          this.navShared.activeTooltipColor = null;
        }, 3000);
        return;
      }
      this.navShared.colorPreference = colorValue;
      await this.updateUserPreferences();
    },
    async selectPattern(pattern) {
      if (!this.navShared.unlockedPatterns.includes(pattern.value)) {
        this.navShared.activeTooltipPattern = pattern.value;
        this.navShared.patternTooltipMessage =
          pattern.unlockMessage || "This pattern is locked";
        this.navShared.patternTooltipShowDonateBtn = !!pattern.donatable;
        this.navShared.showPatternTooltip = true;
        if (this.patternTooltipTimeout)
          clearTimeout(this.patternTooltipTimeout);
        this.patternTooltipTimeout = setTimeout(() => {
          this.navShared.showPatternTooltip = false;
          this.navShared.activeTooltipPattern = null;
        }, 3000);
        return;
      }
      this.navShared.bannerPattern = pattern.value;
      await this.updateUserPreferences();
    },
    goToDonate() {
      this.$router.push({ name: "Donate" });
    },
    async checkAuthStatus() {
      try {
        const response = await fetch(`${API_BASE_URL}/auth/status`, {
          credentials: "include",
          method: "GET",
          headers: { "Content-Type": "application/json" },
        });
        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
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
          headers: { "Content-Type": "application/json" },
        });
        if (!response.ok) return null;
        const result = await response.json();
        return result.data;
      } catch (error) {
        console.error("Error fetching user data:", error);
        return null;
      }
    },
    async updateUserPreferences() {
      if (
        !this.navShared.unlockedColors.includes(this.navShared.colorPreference)
      ) {
        this.navShared.colorPreference = "blue";
      }
      if (
        !this.navShared.unlockedPatterns.includes(this.navShared.bannerPattern)
      ) {
        this.navShared.bannerPattern = null;
      }
      try {
        const response = await fetch(
          `${API_BASE_URL}/users/update-user/${this.navShared.user.playerid}`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({
              rankPref: this.navShared.rankPreference,
              gender: this.navShared.gender,
              color: this.navShared.colorPreference,
              pattern: this.navShared.bannerPattern ?? null,
            }),
          },
        );
        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);
        this.profileUpdateTracker.rank = this.navShared.rankPreference;
        this.profileUpdateTracker.color = this.navShared.colorPreference;
        this.profileUpdateTracker.gender = this.navShared.gender;
        this.profileUpdateTracker.pattern = this.navShared.bannerPattern;
      } catch (error) {
        console.error("Failed to update user preferences:", error);
      }
    },
    getPlayerRoute() {
      const playerId = this.navShared.user.playerid;
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
      const query = this.navShared.searchQuery.trim();
      this.navShared.searchResults = { maps: [], players: [] };
      this.navShared.navHighlightedIndex = -1;
      this.navShared.loadingMaps = true;
      this.navShared.loadingPlayers = true;
      this.fetchMaps(query)
        .then((results) => {
          this.navShared.searchResults.maps = (results.maps || []).slice(0, 5);
        })
        .catch((error) => {
          console.error("Error fetching maps:", error);
          this.navShared.searchResults.maps = [];
        })
        .finally(() => {
          this.navShared.loadingMaps = false;
        });
      this.fetchPlayers(query)
        .then((results) => {
          this.navShared.searchResults.players = (results.players || []).slice(
            0,
            20,
          );
        })
        .catch((error) => {
          console.error("Error fetching players:", error);
          this.navShared.searchResults.players = [];
        })
        .finally(() => {
          this.navShared.loadingPlayers = false;
        });
    },
    debouncedSearch() {
      clearTimeout(this.debounceTimer);
      if (!this.navShared.searchQuery.trim()) {
        this.navShared.searchResults = null;
        this.navShared.loadingMaps = false;
        this.navShared.loadingPlayers = false;
        this.navShared.navHighlightedIndex = -1;
        return;
      }
      this.navShared.loadingMaps = true;
      this.navShared.loadingPlayers = true;
      this.debounceTimer = setTimeout(() => {
        this.fetchSearchResults();
      }, 500);
    },
    clearSearch() {
      this.navShared.searchQuery = "";
      this.navShared.searchResults = { maps: [], players: [] };
      this.navShared.navHighlightedIndex = -1;
    },
    sanitize(text) {
      return DOMPurify.sanitize(text);
    },
    handleAvatarError(e) {
      e.target.style.display = "none";
      if (e.target.nextElementSibling)
        e.target.nextElementSibling.style.display = "flex";
    },
  },
  beforeDestroy() {
    clearTimeout(this.debounceTimer);
    clearTimeout(this.tooltipTimeout);
    clearTimeout(this.patternTooltipTimeout);
    clearTimeout(this.firstVisitTimer);
    document.body.style.overflow = "";
  },
  async mounted() {
    // Wire the shared methods onto navShared once they're bound to `this`,
    // so Navbar.vue / Sidebar.vue can call e.g. navShared.loginWithSteam().
    Object.assign(this.navShared, {
      loginWithSteam: this.loginWithSteam,
      logout: this.logout,
      selectColor: this.selectColor,
      selectPattern: this.selectPattern,
      updateUserPreferences: this.updateUserPreferences,
      goToDonate: this.goToDonate,
      getPlayerRoute: this.getPlayerRoute,
      fetchSearchResults: this.fetchSearchResults,
      debouncedSearch: this.debouncedSearch,
      clearSearch: this.clearSearch,
      sanitize: this.sanitize,
      handleAvatarError: this.handleAvatarError,
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("error") === "player_not_found") {
      this.showErrorPopup = true;
      window.history.replaceState({}, "", window.location.pathname);
    }

    try {
      const isAuthenticated = await this.checkAuthStatus();
      if (isAuthenticated) {
        const userData = await this.fetchUserData();
        if (userData) {
          this.navShared.user = userData;
          this.navShared.rankPreference = userData.rankpref || "overall";
          this.navShared.gender = userData.gender || "male";
          this.navShared.donator = userData.donator || 0;
          this.navShared.colorPreference = userData.color || "blue";
          this.navShared.bannerPattern = userData.pattern ?? null;
          this.navShared.unlockedColors = userData.unlockedColors || [];
          this.navShared.unlockedPatterns = userData.unlockedPatterns || [];
          this.profileUpdateTracker.rank = this.navShared.rankPreference;
          this.profileUpdateTracker.color = this.navShared.colorPreference;
          this.profileUpdateTracker.gender = this.navShared.gender;
          this.profileUpdateTracker.pattern = this.navShared.bannerPattern;
        }
      }
    } catch (error) {
      console.error("Error during authentication check:", error);
      this.navShared.user = null;
    } finally {
      this.navShared.authChecking = false;
    }

    if (!this.navShared.user && this.checkFirstVisit()) {
      this.firstVisitTimer = setTimeout(() => {
        this.showLoginPopup = true;
      }, 1000);
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

.w-95 {
  width: 90% !important;
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

@media (max-width: 1199px) {
  .navbar-collapse {
    display: none !important;
  }
}

/* --- Styles shared between Navbar.vue and Sidebar.vue --- */

.form-check-input:checked {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
  border-color: var(--color-primary);
}

.login-checking {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
}
.login-checking-sidebar {
  width: 100%;
  height: 32px;
}
.login-spinner {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.15);
  border-top-color: var(--color-primary, #4a7fc0);
  animation: login-spinner-spin 0.7s linear infinite;
}
@keyframes login-spinner-spin {
  to {
    transform: rotate(360deg);
  }
}

.bi-steam {
  color: var(--color-text);
}

/* search dropdown result rows (used by both navbar + sidebar search) */
.nav-search-section {
  padding: 6px 0;
}
.nav-section-label {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px 4px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.3);
}
.nav-section-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.06);
  margin: 0 12px;
}
.nav-search-section ul {
  list-style: none;
  margin: 0;
  padding: 2px 6px 6px;
}
.nav-result-item {
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 8px 8px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.15s;
  text-decoration: none;
  color: #fff;
}
.nav-result-item:hover {
  background: rgba(102, 126, 234, 0.15);
}
.nav-result-item:active {
  background: rgba(102, 126, 234, 0.25);
}
.nav-result-item.is-highlighted {
  background: rgba(102, 126, 234, 0.2);
  outline: 1px solid rgba(102, 126, 234, 0.35);
}
.nav-player-avatar-wrapper {
  position: relative;
  flex-shrink: 0;
}
.nav-player-avatar {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  object-fit: cover;
  display: block;
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.nav-player-avatar-fallback {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  background: linear-gradient(
    135deg,
    rgba(102, 126, 234, 0.5),
    rgba(154, 176, 255, 0.3)
  );
  border: 1px solid rgba(102, 126, 234, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.8);
}
.nav-item-name {
  font-size: 15px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.88);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.nav-empty-state {
  padding: 8px 12px 10px;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.25);
  font-style: italic;
}
.nav-loading-rows {
  padding: 2px 6px 6px;
}
.nav-loading-row {
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 8px 8px;
}
.nav-skeleton {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 0%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 100%
  );
  background-size: 200% 100%;
  animation: nav-shimmer 1.5s infinite;
  border-radius: 5px;
  display: block;
}
.nav-skeleton-avatar {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  flex-shrink: 0;
}
.nav-skeleton-text {
  flex: 1;
  height: 12px;
  max-width: 140px;
}
@keyframes nav-shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
.nav-dropdown-enter-active {
  transition:
    opacity 0.15s ease,
    transform 0.15s ease;
}
.nav-dropdown-leave-active {
  transition:
    opacity 0.1s ease,
    transform 0.1s ease;
}
.nav-dropdown-enter-from,
.nav-dropdown-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

/* color / pattern pickers + supporter tooltip (used in both navbar dropdown and sidebar settings) */
.pattern-picker-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  max-width: 180px;
  margin-top: 4px;
}
.pattern-option-wrapper {
  margin: 2px;
  position: relative;
}
.pattern-block {
  width: 42px;
  height: 42px;
  border-radius: 8px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  background-color: rgba(255, 255, 255, 0.05);
  background-size: cover;
  background-position: center;
  transition:
    transform 0.15s ease,
    border-color 0.15s ease;
}
.pattern-block.pattern-none {
  background-image: none !important;
  background: repeating-linear-gradient(
    45deg,
    rgba(255, 255, 255, 0.05),
    rgba(255, 255, 255, 0.05) 4px,
    rgba(255, 255, 255, 0.1) 4px,
    rgba(255, 255, 255, 0.1) 8px
  );
}
.pattern-block:hover {
  border-color: var(--color-border);
}
.pattern-block.selected {
  border: 2px solid var(--color-text);
}
.pattern-block.locked {
  opacity: 0.45;
}
.pattern-lock-icon {
  color: rgba(255, 255, 255, 0.95);
  filter: drop-shadow(0 1px 3px rgba(0, 0, 0, 0.7));
  pointer-events: none;
}
.color-picker-container {
  display: flex;
  flex-wrap: wrap;
  max-width: 140px;
}
.color-option-wrapper {
  margin: 2px;
  position: relative;
}
.color-swatch {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  cursor: pointer;
  transition: transform 0.15s ease;
  border: 2px solid transparent;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}
.color-swatch.locked {
  opacity: 0.45;
  cursor: pointer;
}
.lock-icon {
  color: rgba(255, 255, 255, 0.9);
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.6));
  pointer-events: none;
}
.color-swatch:hover {
  border: 1px solid var(--color-border);
}
.color-swatch.selected {
  border: 2px solid var(--color-text);
}
.supporter-tooltip {
  position: absolute;
  bottom: calc(100% + 8px);
  left: 50%;
  transform: translateX(-50%);
  background: var(--color-dark);
  border: 2px solid var(--color-primary);
  border-radius: 12px;
  padding: 12px 16px;
  width: 200px;
  box-sizing: border-box;
  z-index: 1000;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
  animation: tooltipSlideIn 0.3s ease-out;
}
.tooltip-content p {
  margin: 0 0 8px 0;
  font-size: 13px;
  color: var(--color-text);
  font-weight: 500;
  white-space: normal;
  word-wrap: break-word;
  overflow-wrap: break-word;
}
.tooltip-content {
  text-align: center;
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
</style>
