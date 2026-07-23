<template>
  <nav class="navbar navbar-expand-xl bg-custom">
    <div class="container-fluid">
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
              <i class="bi bi-trophy"></i> Leaderboards
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
          <li class="nav-item me-2">
            <router-link
              to="/history"
              class="nav-link"
              :class="{ active: isNavItemActive('History') }"
              @click="closeNavbar"
            >
              <i class="bi bi-clock-history"></i> History
            </router-link>
          </li>
          <li class="nav-item">
            <router-link
              to="/donate"
              class="nav-link"
              :class="{ active: isNavItemActive('Donate') }"
              @click="closeNavbar"
            >
              <i class="bi bi-heart"></i>
            </router-link>
          </li>
          <li class="nav-item me-3">
            <button
              type="button"
              class="nav-link calculator-btn"
              @click="$emit('open-calculator')"
            >
              <i class="bi bi-calculator"></i>
            </button>
          </li>

          <div class="navbar-right">
            <div class="navbar-search-container me-3" @click.stop>
              <div
                class="navbar-search-box"
                :class="{ 'is-focused': searchFocused }"
              >
                <div class="navbar-search-icon-container">
                  <svg
                    width="15"
                    height="15"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    class="navbar-search-icon"
                  >
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                  </svg>
                </div>
                <input
                  type="text"
                  v-model="navShared.searchQuery"
                  @focus="searchFocused = true"
                  @blur="searchFocused = false"
                  @keydown="onNavSearchKeydown"
                  placeholder="Search..."
                  class="navbar-search-input"
                  autocomplete="off"
                />
                <button
                  v-if="navShared.searchQuery"
                  class="navbar-clear-btn"
                  @mousedown.prevent="navShared.clearSearch()"
                  aria-label="Clear search"
                >
                  <svg
                    width="12"
                    height="12"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
              </div>
              <Transition name="nav-dropdown">
                <div
                  class="navbar-search-results-dropdown"
                  ref="navDropdownRef"
                  v-if="
                    navShared.searchQuery.trim() &&
                    (navShared.loadingMaps ||
                      navShared.loadingPlayers ||
                      navShared.searchResults)
                  "
                >
                  <div class="nav-search-section">
                    <div class="nav-section-label">
                      <svg
                        width="11"
                        height="11"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                      >
                        <polygon
                          points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"
                        ></polygon>
                      </svg>
                      Maps
                    </div>
                    <div v-if="navShared.loadingMaps" class="nav-loading-row">
                      <span class="nav-skeleton nav-skeleton-text"></span>
                    </div>
                    <ul
                      v-else-if="
                        navShared.searchResults &&
                        navShared.searchResults.maps.length
                      "
                    >
                      <HoverPreview
                        v-for="(map, i) in navShared.searchResults.maps"
                        :key="map.id"
                        :mapName="map.name"
                        style="display: block"
                      >
                        <SmartLink
                          :to="{ name: 'MapPage', params: { mapId: map.id } }"
                          tag="li"
                          class="nav-result-item"
                          :class="{
                            'is-highlighted':
                              navShared.navHighlightedIndex === i,
                          }"
                          :data-nav-result-index="i"
                          @mouseenter="navShared.navHighlightedIndex = i"
                          @mouseleave="navShared.navHighlightedIndex = -1"
                          @click="handleSearchResultClick"
                        >
                          <span class="nav-item-name">{{
                            map.name || `Map ID: ${map.id}`
                          }}</span>
                        </SmartLink>
                      </HoverPreview>
                    </ul>
                    <div
                      v-else-if="
                        !navShared.loadingMaps && navShared.searchResults
                      "
                      class="nav-empty-state"
                    >
                      No maps found
                    </div>
                  </div>
                  <div class="nav-section-divider"></div>
                  <div class="nav-search-section">
                    <div class="nav-section-label">
                      <svg
                        width="11"
                        height="11"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                      >
                        <path
                          d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                        ></path>
                        <circle cx="12" cy="7" r="4"></circle>
                      </svg>
                      Players
                    </div>
                    <div
                      v-if="navShared.loadingPlayers"
                      class="nav-loading-rows"
                    >
                      <div v-for="n in 3" :key="n" class="nav-loading-row">
                        <span class="nav-skeleton nav-skeleton-avatar"></span>
                        <span class="nav-skeleton nav-skeleton-text"></span>
                      </div>
                    </div>
                    <ul
                      v-else-if="
                        navShared.searchResults &&
                        navShared.searchResults.players.length
                      "
                    >
                      <SmartLink
                        v-for="(player, i) in navShared.searchResults.players"
                        :key="player.id"
                        :to="{
                          name: 'PlayerPage',
                          params: { playerId: player.id },
                        }"
                        tag="li"
                        class="nav-result-item"
                        :class="{
                          'is-highlighted':
                            navShared.navHighlightedIndex === navMapCount + i,
                        }"
                        :data-nav-result-index="navMapCount + i"
                        @mouseenter="
                          navShared.navHighlightedIndex = navMapCount + i
                        "
                        @mouseleave="navShared.navHighlightedIndex = -1"
                      >
                        <div class="nav-player-avatar-wrapper">
                          <img
                            v-if="player.steam_avatar"
                            :src="player.steam_avatar"
                            :alt="player.name"
                            class="nav-player-avatar"
                            @error="navShared.handleAvatarError"
                          />
                          <div v-else class="nav-player-avatar-fallback">
                            {{ (player.name || "?")[0].toUpperCase() }}
                          </div>
                        </div>
                        <span class="nav-item-name">{{
                          player.name || `Player ID: ${player.id}`
                        }}</span>
                      </SmartLink>
                    </ul>
                    <div
                      v-else-if="
                        !navShared.loadingPlayers && navShared.searchResults
                      "
                      class="nav-empty-state"
                    >
                      No players found
                    </div>
                  </div>
                </div>
              </Transition>
            </div>
          </div>

          <div class="user-section">
            <div v-if="navShared.authChecking" class="login-checking">
              <span class="login-spinner"></span>
            </div>
            <button
              v-else-if="!navShared.user || !navShared.user.steamid"
              class="btn login-button"
              @click="navShared.loginWithSteam"
            >
              <i class="bi bi-steam"></i> Login with Steam
            </button>
            <div
              v-if="navShared.user && navShared.user.steamid"
              class="dropdown"
            >
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
                  :src="navShared.user.avatar"
                  alt="avatar"
                  class="me-2 avatar"
                  v-if="navShared.user.avatar"
                />
                <span>{{ navShared.user.name }}</span>
              </button>
              <ul
                class="dropdown-menu dropdown-menu-end"
                aria-labelledby="playerDropdown"
              >
                <li>
                  <SmartLink
                    tag="a"
                    :to="navShared.getPlayerRoute()"
                    class="dropdown-item clickable"
                    >My profile</SmartLink
                  >
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
                        v-model="navShared.rankPreference"
                        value="overall"
                        @change="navShared.updateUserPreferences"
                      />
                      <label class="form-check-label" for="preferenceOverall"
                        >Overall</label
                      >
                    </div>
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="radio"
                        id="preferenceSoldier"
                        name="rankPreference"
                        v-model="navShared.rankPreference"
                        value="soldier"
                        @change="navShared.updateUserPreferences"
                      />
                      <label class="form-check-label" for="preferenceSoldier"
                        >Soldier</label
                      >
                    </div>
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="radio"
                        id="preferenceDemoman"
                        name="rankPreference"
                        v-model="navShared.rankPreference"
                        value="demoman"
                        @change="navShared.updateUserPreferences"
                      />
                      <label class="form-check-label" for="preferenceDemoman"
                        >Demoman</label
                      >
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
                        v-model="navShared.gender"
                        value="male"
                        @change="navShared.updateUserPreferences"
                      />
                      <label class="form-check-label" for="genderMale"
                        >Male</label
                      >
                    </div>
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="radio"
                        id="genderFemale"
                        name="gender"
                        v-model="navShared.gender"
                        value="female"
                        @change="navShared.updateUserPreferences"
                      />
                      <label class="form-check-label" for="genderFemale"
                        >Female</label
                      >
                    </div>
                  </div>
                </li>
                <li @click.stop>
                  <div class="dropdown-item non-clickable">
                    <h6>Banner colour</h6>
                    <div class="color-picker-container position-relative">
                      <div
                        v-for="(color, index) in navShared.colorOptions"
                        :key="index"
                        class="color-option-wrapper"
                        @click="navShared.selectColor(color.value)"
                      >
                        <label
                          class="color-swatch"
                          :for="'color-' + index"
                          :class="{
                            selected: navShared.colorPreference === color.value,
                            locked: !navShared.unlockedColors.includes(
                              color.value,
                            ),
                          }"
                          :style="{ backgroundColor: color.color }"
                          :title="
                            navShared.unlockedColors.includes(color.value)
                              ? ''
                              : 'Donator only'
                          "
                        >
                          <svg
                            v-if="
                              !navShared.unlockedColors.includes(color.value)
                            "
                            class="lock-icon"
                            width="10"
                            height="10"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                          >
                            <path
                              d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5zm-3 8V6a3 3 0 0 1 6 0v3z"
                            />
                          </svg>
                        </label>
                        <div
                          v-if="
                            navShared.showTooltip &&
                            navShared.activeTooltipColor === color.value
                          "
                          class="supporter-tooltip"
                          @click.stop
                        >
                          <div class="tooltip-content">
                            <p>
                              You need to become a supporter to select this
                              colors
                            </p>
                            <button
                              @click="navShared.goToDonate"
                              class="tooltip-button"
                            >
                              Become a Supporter
                            </button>
                          </div>
                          <div class="tooltip-arrow"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li @click.stop>
                  <div class="dropdown-item non-clickable">
                    <h6>Banner background</h6>
                    <div class="pattern-picker-container">
                      <div
                        v-for="pattern in navShared.patternOptions"
                        :key="pattern.value"
                        class="pattern-option-wrapper"
                        @click="navShared.selectPattern(pattern)"
                      >
                        <label
                          class="pattern-block"
                          :for="'pattern-' + (pattern.value ?? 'none')"
                          :class="{
                            selected: navShared.bannerPattern === pattern.value,
                            'pattern-none': pattern.value === null,
                            locked: !navShared.unlockedPatterns.includes(
                              pattern.value,
                            ),
                          }"
                          :style="
                            pattern.thumb
                              ? { backgroundImage: `url(${pattern.thumb})` }
                              : {}
                          "
                          :title="
                            navShared.unlockedPatterns.includes(pattern.value)
                              ? pattern.label
                              : pattern.label + ' (Donator only)'
                          "
                        >
                          <svg
                            v-if="
                              !navShared.unlockedPatterns.includes(
                                pattern.value,
                              )
                            "
                            class="lock-icon pattern-lock-icon"
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                          >
                            <path
                              d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5zm-3 8V6a3 3 0 0 1 6 0v3z"
                            />
                          </svg>
                        </label>
                        <div
                          v-if="
                            navShared.showPatternTooltip &&
                            navShared.activeTooltipPattern === pattern.value
                          "
                          class="supporter-tooltip pattern-tooltip"
                          @click.stop
                        >
                          <div class="tooltip-content">
                            <p>{{ navShared.patternTooltipMessage }}</p>
                            <button
                              v-if="navShared.patternTooltipShowDonateBtn"
                              @click="navShared.goToDonate"
                              class="tooltip-button"
                            >
                              Become a Supporter
                            </button>
                          </div>
                          <div class="tooltip-arrow"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <li>
                  <a
                    class="dropdown-item clickable"
                    @click.prevent="confirmLogoutOpen = true"
                    href="#"
                    >Logout</a
                  >
                </li>
              </ul>
            </div>
          </div>
        </ul>
      </div>
      <button
        class="navbar-toggler mobile-calc-btn"
        type="button"
        @click="$emit('open-calculator')"
        aria-label="Points calculator"
      >
        <i class="bi bi-calculator"></i>
      </button>
      <button
        class="navbar-toggler"
        type="button"
        @click.stop="toggleSidebar"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </nav>
  <ConfirmPopup
    v-model="confirmLogoutOpen"
    title="Log out?"
    message="Are you sure you want to log out?"
    confirm-text="Log out"
    cancel-text="Cancel"
    @confirm="navShared.logout"
  />
</template>

<script>
import HoverPreview from "@/components/utils/HoverPreview.vue";
import ConfirmPopup from "@/components/popups/ConfirmPopup.vue";

export default {
  name: "Navbar",
  components: {
    HoverPreview,
    ConfirmPopup,
  },
  inject: ["navShared"],
  emits: ["open-calculator"],
  data() {
    return {
      searchFocused: false,
      confirmLogoutOpen: false,
    };
  },
  computed: {
    navMapCount() {
      return this.navShared.searchResults?.maps?.length ?? 0;
    },
    navPlayerCount() {
      return this.navShared.searchResults?.players?.length ?? 0;
    },
    navTotalResults() {
      return this.navMapCount + this.navPlayerCount;
    },
  },
  watch: {
    "navShared.user"() {
      this.$nextTick(() => this.checkNavbarOverflow());
    },
  },
  methods: {
    isNavItemActive(routeName) {
      return this.$route.name === routeName;
    },
    closeNavbar() {
      const navbarCollapse = document.getElementById("navbarNav");
      if (navbarCollapse) {
        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
          toggle: false,
        });
        bsCollapse.hide();
      }
    },
    toggleSidebar() {
      this.navShared.sidebarOpen = !this.navShared.sidebarOpen;
      document.body.style.overflow = this.navShared.sidebarOpen ? "hidden" : "";
    },
    handleSearchResultClick() {
      this.closeNavbar();
      this.navShared.clearSearch();
    },
    onNavSearchKeydown(e) {
      if (!this.navShared.searchResults || this.navTotalResults === 0) return;
      if (e.key === "ArrowDown") {
        e.preventDefault();
        this.navShared.navHighlightedIndex =
          this.navShared.navHighlightedIndex < this.navTotalResults - 1
            ? this.navShared.navHighlightedIndex + 1
            : 0;
        this.scrollNavHighlightedIntoView();
      } else if (e.key === "ArrowUp") {
        e.preventDefault();
        this.navShared.navHighlightedIndex =
          this.navShared.navHighlightedIndex > 0
            ? this.navShared.navHighlightedIndex - 1
            : this.navTotalResults - 1;
        this.scrollNavHighlightedIntoView();
      } else if (e.key === "Enter" && this.navShared.navHighlightedIndex >= 0) {
        e.preventDefault();
        this.navigateNavHighlighted();
      } else if (e.key === "Escape") {
        this.navShared.searchResults = null;
        this.navShared.navHighlightedIndex = -1;
      }
    },
    scrollNavHighlightedIntoView() {
      this.$nextTick(() => {
        const ref = this.$refs.navDropdownRef;
        if (!ref) return;
        const el = ref.querySelector(
          `[data-nav-result-index="${this.navShared.navHighlightedIndex}"]`,
        );
        if (el) el.scrollIntoView({ block: "nearest" });
      });
    },
    navigateNavHighlighted() {
      if (!this.navShared.searchResults) return;
      if (this.navShared.navHighlightedIndex < this.navMapCount) {
        const map =
          this.navShared.searchResults.maps[this.navShared.navHighlightedIndex];
        if (map) {
          this.$router.push({ name: "MapPage", params: { mapId: map.id } });
          this.handleSearchResultClick();
        }
      } else {
        const player =
          this.navShared.searchResults.players[
            this.navShared.navHighlightedIndex - this.navMapCount
          ];
        if (player) {
          this.$router.push({
            name: "PlayerPage",
            params: { playerId: player.id },
          });
          this.handleSearchResultClick();
        }
      }
    },
    checkNavbarOverflow() {
      const isDesktop = window.innerWidth >= 1200;
      if (isDesktop) this.navShared.sidebarOpen = false;
      const nav = document.querySelector(".navbar-nav");
      if (!nav || !isDesktop) return;

      const wasCompact = nav.classList.contains("nav-compact");
      if (wasCompact) nav.classList.remove("nav-compact");

      const items = nav.querySelectorAll(
        ".nav-item, .navbar-right, .user-section",
      );
      let maxBottom = 0;
      let minBottom = Infinity;
      items.forEach((item) => {
        const rect = item.getBoundingClientRect();
        if (rect.bottom > maxBottom) maxBottom = rect.bottom;
        if (rect.bottom < minBottom) minBottom = rect.bottom;
      });
      const hasOverflow = maxBottom - minBottom > 10;

      nav.classList.toggle("nav-compact", hasOverflow);
    },
  },
  mounted() {
    this.$nextTick(() => this.checkNavbarOverflow());

    this._navRAF = null;
    this._navResizeHandler = () => {
      if (this._navRAF) return;
      this._navRAF = requestAnimationFrame(() => {
        this._navRAF = null;
        this.checkNavbarOverflow();
      });
    };
    window.addEventListener("resize", this._navResizeHandler);

    const nav = document.querySelector(".navbar-nav");
    if (nav && "ResizeObserver" in window) {
      this._navObserver = new ResizeObserver(() => {
        if (this._navRAF) return;
        this._navRAF = requestAnimationFrame(() => {
          this._navRAF = null;
          this.checkNavbarOverflow();
        });
      });
      this._navObserver.observe(nav);
    }

    if (document.fonts?.ready) {
      document.fonts.ready.then(() => this.checkNavbarOverflow());
    }
  },
  beforeDestroy() {
    window.removeEventListener("resize", this._navResizeHandler);
    if (this._navRAF) cancelAnimationFrame(this._navRAF);
    this._navObserver?.disconnect();
  },
};
</script>

<style scoped>
.nav-link {
  color: #ffffff !important;
  font-weight: bold !important;
  border-radius: 10px;
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
  border-color: var(--color-primary, #4a7fc0) !important;
  background: linear-gradient(
    to right,
    rgba(74, 111, 165, 0.35),
    rgba(74, 111, 165, 0.15)
  );
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

.login-button {
  color: #ffffff !important;
  font-weight: bold !important;
  border-radius: 10px !important;
  padding: 10px 15px !important;
  border: 2px solid transparent !important;
}
.login-button:hover {
  color: var(--color-text);
  border-color: #363a3d !important;
  background: rgba(255, 255, 255, 0.05);
}

.navbar-search-container {
  position: relative;
}

.navbar-search-box {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.06);
  border: 1.5px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  transition:
    border-color 0.2s,
    box-shadow 0.2s,
    background 0.2s;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.25);
  width: 240px;
}
.navbar-search-box.is-focused,
.navbar-search-box:hover {
  border-color: rgba(102, 126, 234, 0.6);
  background: rgba(255, 255, 255, 0.09);
  box-shadow:
    0 0 0 3px rgba(102, 126, 234, 0.15),
    0 2px 12px rgba(0, 0, 0, 0.3);
}

.navbar-search-icon-container {
  padding: 0 10px 0 12px;
  display: flex;
  align-items: center;
  flex-shrink: 0;
}
.navbar-search-icon {
  color: rgba(255, 255, 255, 0.35);
}

.navbar-search-input {
  flex: 1;
  padding: 9px 0;
  background: transparent;
  border: none;
  color: #fff;
  font-size: 14px;
  outline: none;
  min-width: 0;
}
.navbar-search-input::placeholder {
  color: rgba(255, 255, 255, 0.35);
}

.navbar-clear-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0 10px;
  color: rgba(255, 255, 255, 0.35);
  display: flex;
  align-items: center;
  transition: color 0.15s;
  flex-shrink: 0;
}
.navbar-clear-btn:hover {
  color: rgba(255, 255, 255, 0.75);
}

.navbar-search-results-dropdown {
  position: absolute;
  top: calc(100% + 5px);
  right: 0;
  width: 240px;
  background: rgba(18, 20, 30, 0.97);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  box-shadow:
    0 16px 48px rgba(0, 0, 0, 0.6),
    0 0 0 1px rgba(255, 255, 255, 0.04);
  overflow: hidden;
  z-index: 1000;
}

.navbar-search-results-dropdown :deep(.hover-preview-wrapper) {
  display: block;
  width: 100%;
}

.calculator-btn {
  background: transparent;
  cursor: pointer;
  font-family: inherit;
  font-size: inherit;
}

.calculator-btn,
.donate-link {
  padding: 10px 8px !important;
}

.user-section {
  display: flex;
  align-items: center;
}

.navbar-nav.nav-compact .nav-link {
  padding: 8px 9px !important;
  font-size: 0.85rem;
}
.navbar-nav.nav-compact .nav-item.me-3 {
  margin-right: 0.2rem !important;
}
.navbar-nav.nav-compact .navbar-search-box {
  width: 170px;
}
.navbar-nav.nav-compact .navbar-search-container {
  margin-right: 6px !important;
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
.avatar {
  width: 32px;
  height: 32px;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
}
.dropdown-toggle {
  color: #ffffff !important;
  font-weight: bold !important;
  border-radius: 10px !important;
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
}
.dropdown-item.clickable:hover {
  background: rgba(74, 111, 165, 0.4) !important;
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

.mobile-calc-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, 0.85);
  font-size: 1.1rem;
  margin-right: 0.5rem;
  margin-left: auto;
  padding: 0.25rem 0.5rem;
}
.mobile-calc-btn:focus {
  box-shadow: none;
}
</style>
