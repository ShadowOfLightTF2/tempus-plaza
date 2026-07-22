<template>
  <div>
    <div
      class="sidebar-overlay"
      :class="{ 'sidebar-overlay-open': navShared.sidebarOpen }"
      @click="closeSidebar"
    ></div>
    <div
      class="sidebar-drawer"
      :class="{ 'sidebar-open': navShared.sidebarOpen }"
      @click.stop
    >
      <div class="sidebar-header">
        <span class="sidebar-brand">Tempus Plaza</span>
        <button
          class="sidebar-close-btn"
          @click="closeSidebar"
          aria-label="Close menu"
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
      </div>

      <div class="sidebar-search-wrap" v-if="!isHomePage" @click.stop>
        <div
          class="sidebar-search-box"
          :class="{ 'is-focused': searchFocused }"
        >
          <div class="sidebar-search-icon-container">
            <svg
              width="14"
              height="14"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              class="sidebar-search-icon-svg"
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
            placeholder="Search maps & players..."
            class="sidebar-search-input"
            autocomplete="off"
          />
          <button
            v-if="navShared.searchQuery"
            class="sidebar-search-clear"
            @mousedown.prevent="navShared.clearSearch()"
            aria-label="Clear"
          >
            <svg
              width="11"
              height="11"
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
        <div
          class="sidebar-search-results"
          ref="sidebarDropdownRef"
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
              <span class="nav-skeleton nav-skeleton-icon"></span>
              <span class="nav-skeleton nav-skeleton-text"></span>
            </div>
            <ul
              v-else-if="
                navShared.searchResults && navShared.searchResults.maps.length
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
                    'is-highlighted': navShared.navHighlightedIndex === i,
                  }"
                  :data-nav-result-index="i"
                  @mouseenter="navShared.navHighlightedIndex = i"
                  @mouseleave="navShared.navHighlightedIndex = -1"
                  @click="
                    handleSearchResultClick();
                    closeSidebar();
                  "
                >
                  <span class="nav-item-name">{{
                    map.name || `Map ID: ${map.id}`
                  }}</span>
                </SmartLink>
              </HoverPreview>
            </ul>
            <div
              v-else-if="!navShared.loadingMaps && navShared.searchResults"
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
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              Players
            </div>
            <div v-if="navShared.loadingPlayers" class="nav-loading-rows">
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
                :to="{ name: 'PlayerPage', params: { playerId: player.id } }"
                tag="li"
                class="nav-result-item"
                :class="{
                  'is-highlighted':
                    navShared.navHighlightedIndex === navMapCount + i,
                }"
                :data-nav-result-index="navMapCount + i"
                @mouseenter="navShared.navHighlightedIndex = navMapCount + i"
                @mouseleave="navShared.navHighlightedIndex = -1"
                @click="
                  handleSearchResultClick();
                  closeSidebar();
                "
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
              v-else-if="!navShared.loadingPlayers && navShared.searchResults"
              class="nav-empty-state"
            >
              No players found
            </div>
          </div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <router-link
          to="/"
          class="sidebar-nav-link"
          :class="{ active: isNavItemActive('Home') }"
          @click="closeSidebar"
          ><i class="bi bi-house"></i><span>Home</span></router-link
        >
        <router-link
          to="/servers"
          class="sidebar-nav-link"
          :class="{ active: isNavItemActive('Servers') }"
          @click="closeSidebar"
          ><i class="bi bi-globe"></i><span>Servers</span></router-link
        >
        <router-link
          to="/activity"
          class="sidebar-nav-link"
          :class="{ active: isNavItemActive('Activity') }"
          @click="closeSidebar"
          ><i class="bi bi-graph-up"></i><span>Activity</span></router-link
        >
        <router-link
          to="/maps"
          class="sidebar-nav-link"
          :class="{ active: isNavItemActive('Maps') }"
          @click="closeSidebar"
          ><i class="bi bi-map"></i><span>Maps</span></router-link
        >
        <router-link
          to="/players"
          class="sidebar-nav-link"
          :class="{ active: isNavItemActive('Players') }"
          @click="closeSidebar"
          ><i class="bi bi-trophy"></i><span>Leaderboards</span></router-link
        >
        <router-link
          to="/lookup"
          class="sidebar-nav-link"
          :class="{ active: isNavItemActive('Lookup') }"
          @click="closeSidebar"
          ><i class="bi bi-search"></i><span>Lookup</span></router-link
        >
        <router-link
          to="/history"
          class="sidebar-nav-link"
          :class="{ active: isNavItemActive('History') }"
          @click="closeSidebar"
          ><i class="bi bi-clock-history"></i><span>History</span></router-link
        >
        <router-link
          to="/donate"
          class="sidebar-nav-link"
          :class="{ active: isNavItemActive('Donate') }"
          @click="closeSidebar"
          ><i class="bi bi-heart"></i><span>Donate</span></router-link
        >
      </nav>

      <div class="sidebar-footer">
        <div
          v-if="navShared.authChecking"
          class="login-checking login-checking-sidebar"
        >
          <span class="login-spinner"></span>
        </div>
        <button
          v-else-if="!navShared.user || !navShared.user.steamid"
          class="btn sidebar-login-btn"
          @click="navShared.loginWithSteam"
        >
          <i class="bi bi-steam"></i> Login with Steam
        </button>
        <div
          v-if="navShared.user && navShared.user.steamid"
          class="sidebar-user-section"
        >
          <button
            class="sidebar-user-toggle"
            @click.stop="sidebarSettingsOpen = !sidebarSettingsOpen"
          >
            <img
              :src="navShared.user.avatar"
              alt="avatar"
              class="sidebar-avatar"
              v-if="navShared.user.avatar"
            />
            <span class="sidebar-username">{{ navShared.user.name }}</span>
            <svg
              class="sidebar-chevron"
              :class="{ rotated: sidebarSettingsOpen }"
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
          </button>
          <div
            v-if="sidebarSettingsOpen"
            class="sidebar-settings-panel"
            @click.stop
          >
            <SmartLink
              tag="div"
              :to="navShared.getPlayerRoute()"
              class="sidebar-settings-item sidebar-settings-link"
              @click="closeSidebar"
            >
              <i class="bi bi-person-circle"></i> My profile
            </SmartLink>
            <div class="sidebar-settings-divider"></div>
            <div class="sidebar-settings-item">
              <h6>Rank preference</h6>
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  id="sb-preferenceOverall"
                  name="sbRankPreference"
                  v-model="navShared.rankPreference"
                  value="overall"
                  @change="navShared.updateUserPreferences"
                />
                <label class="form-check-label" for="sb-preferenceOverall"
                  >Overall</label
                >
              </div>
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  id="sb-preferenceSoldier"
                  name="sbRankPreference"
                  v-model="navShared.rankPreference"
                  value="soldier"
                  @change="navShared.updateUserPreferences"
                />
                <label class="form-check-label" for="sb-preferenceSoldier"
                  >Soldier</label
                >
              </div>
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  id="sb-preferenceDemoman"
                  name="sbRankPreference"
                  v-model="navShared.rankPreference"
                  value="demoman"
                  @change="navShared.updateUserPreferences"
                />
                <label class="form-check-label" for="sb-preferenceDemoman"
                  >Demoman</label
                >
              </div>
            </div>
            <div class="sidebar-settings-divider"></div>
            <div class="sidebar-settings-item">
              <h6>Rank gender</h6>
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  id="sb-genderMale"
                  name="sbGender"
                  v-model="navShared.gender"
                  value="male"
                  @change="navShared.updateUserPreferences"
                />
                <label class="form-check-label" for="sb-genderMale">Male</label>
              </div>
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  id="sb-genderFemale"
                  name="sbGender"
                  v-model="navShared.gender"
                  value="female"
                  @change="navShared.updateUserPreferences"
                />
                <label class="form-check-label" for="sb-genderFemale"
                  >Female</label
                >
              </div>
            </div>
            <div class="sidebar-settings-divider"></div>
            <div class="sidebar-settings-item" @click.stop>
              <h6>Banner colour</h6>
              <div class="color-picker-container position-relative">
                <div
                  v-for="(color, index) in navShared.colorOptions"
                  :key="index"
                  class="color-option-wrapper"
                  @click="navShared.selectColor(color.value)"
                >
                  <input
                    class="d-none"
                    type="radio"
                    :id="'sb-color-' + index"
                    name="sbColorPreference"
                    v-model="navShared.colorPreference"
                    :value="color.value"
                    @change="navShared.updateUserPreferences"
                    :disabled="!navShared.unlockedColors.includes(color.value)"
                  />
                  <label
                    class="color-swatch"
                    :for="'sb-color-' + index"
                    :class="{
                      selected: navShared.colorPreference === color.value,
                      locked: !navShared.unlockedColors.includes(color.value),
                    }"
                    :style="{ backgroundColor: color.color }"
                    :title="
                      navShared.unlockedColors.includes(color.value)
                        ? ''
                        : 'Supporter only'
                    "
                  >
                    <svg
                      v-if="!navShared.unlockedColors.includes(color.value)"
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
                </div>
                <div
                  v-if="navShared.showTooltip"
                  class="supporter-tooltip"
                  @click.stop
                >
                  <div class="tooltip-content">
                    <p>You need to become a supporter to select colors</p>
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
            <div class="sidebar-settings-divider"></div>
            <div class="sidebar-settings-item" @click.stop>
              <h6>Banner background</h6>
              <div
                class="pattern-picker-container position-relative"
                @click.stop
              >
                <div
                  v-for="pattern in navShared.patternOptions"
                  :key="pattern.value"
                  class="pattern-option-wrapper"
                  @click="
                    !navShared.unlockedPatterns.includes(pattern.value)
                      ? navShared.selectPattern(pattern)
                      : null
                  "
                >
                  <input
                    class="d-none"
                    type="radio"
                    :id="'sb-pattern-' + pattern.value"
                    name="sbPatternPreference"
                    v-model="navShared.bannerPattern"
                    :value="pattern.value"
                    @change="navShared.updateUserPreferences"
                    :disabled="
                      !navShared.unlockedPatterns.includes(pattern.value)
                    "
                  />
                  <label
                    class="pattern-block"
                    :for="'sb-pattern-' + pattern.value"
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
                      v-if="!navShared.unlockedPatterns.includes(pattern.value)"
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
                </div>
              </div>
            </div>
            <div class="sidebar-settings-divider"></div>
            <div class="sidebar-settings-item">
              <a class="sidebar-logout-link" @click="navShared.logout" href="#"
                ><i class="bi bi-box-arrow-right"></i> Logout</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import HoverPreview from "./utils/HoverPreview.vue";

export default {
  name: "Sidebar",
  components: {
    HoverPreview,
  },
  inject: ["navShared"],
  data() {
    return {
      searchFocused: false,
      sidebarSettingsOpen: false,
    };
  },
  computed: {
    isHomePage() {
      return this.$route.name === "Home";
    },
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
    $route() {
      this.closeSidebar();
    },
  },
  methods: {
    isNavItemActive(routeName) {
      return this.$route.name === routeName;
    },
    closeSidebar() {
      this.navShared.sidebarOpen = false;
      this.sidebarSettingsOpen = false;
      document.body.style.overflow = "";
    },
    handleSearchResultClick() {
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
        const ref = this.$refs.sidebarDropdownRef;
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
          this.closeSidebar();
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
          this.closeSidebar();
        }
      }
    },
  },
};
</script>

<style scoped>
.sidebar-search-wrap {
  padding: 0.75rem 1.25rem 0.5rem;
  position: relative;
}

.sidebar-search-box {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.06);
  border: 1.5px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  transition:
    border-color 0.2s,
    box-shadow 0.2s,
    background 0.2s;
}
.sidebar-search-box.is-focused,
.sidebar-search-box:hover {
  border-color: rgba(102, 126, 234, 0.6);
  background: rgba(255, 255, 255, 0.09);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}

.sidebar-search-icon-container {
  padding: 0 9px 0 12px;
  display: flex;
  align-items: center;
  flex-shrink: 0;
}
.sidebar-search-icon-svg {
  color: rgba(255, 255, 255, 0.35);
}

.sidebar-search-input {
  flex: 1;
  padding: 10px 0;
  background: transparent;
  border: none;
  color: #fff;
  font-size: 14px;
  outline: none;
  min-width: 0;
}
.sidebar-search-input::placeholder {
  color: rgba(255, 255, 255, 0.35);
}

.sidebar-search-clear {
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
.sidebar-search-clear:hover {
  color: rgba(255, 255, 255, 0.75);
}

.sidebar-search-results {
  margin-top: 6px;
  background: rgba(18, 20, 30, 0.97);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.5);
  overflow: hidden;
  max-height: 320px;
  overflow-y: auto;
}

.sidebar-search-results :deep(.hover-preview-wrapper) {
  display: block;
  width: 100%;
}

.sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(3px);
  z-index: 10000;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}
.sidebar-overlay.sidebar-overlay-open {
  opacity: 1;
  pointer-events: all;
}

.sidebar-drawer {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  width: 280px;
  z-index: 10001;
  background: linear-gradient(
    160deg,
    rgba(17, 22, 30, 0.98) 0%,
    rgba(25, 38, 60, 0.98) 100%
  );
  border-left: 1px solid rgba(74, 111, 165, 0.3);
  box-shadow: 8px 0 40px rgba(0, 0, 0, 0.5);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  overflow-x: hidden;
  transform: translateX(100%);
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform;
}
.sidebar-drawer.sidebar-open {
  transform: translateX(0);
}

.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.25rem 1rem;
  border-bottom: 1px solid rgba(74, 111, 165, 0.2);
  min-height: 64px;
}
.sidebar-brand {
  font-size: 1.1rem;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: 0.03em;
}
.sidebar-close-btn {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  cursor: pointer;
  padding: 6px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}
.sidebar-close-btn:hover {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff;
}

.sidebar-nav {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: 0.5rem 0.75rem;
  gap: 2px;
}

.sidebar-nav-link {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  color: rgba(255, 255, 255, 0.8) !important;
  font-weight: 600;
  font-size: 0.95rem;
  text-decoration: none !important;
  transition: all 0.2s ease;
  border: 1.5px solid transparent;
  background: none;
  cursor: pointer;
  width: 100%;
  text-align: left;
  font-family: inherit;
}
.sidebar-nav-link i {
  font-size: 1rem;
  width: 20px;
  text-align: center;
  opacity: 0.85;
  flex-shrink: 0;
}
.sidebar-nav-link:hover {
  background: rgba(255, 255, 255, 0.07);
  color: #ffffff !important;
  border-color: rgba(74, 111, 165, 0.25);
  transform: translateX(3px);
}
.sidebar-nav-link.active {
  background: linear-gradient(
    to right,
    rgba(74, 111, 165, 0.35),
    rgba(74, 111, 165, 0.15)
  );
  color: #ffffff !important;
  border-color: var(--color-primary, #4a7fc0);
}

.sidebar-footer {
  padding: 1rem 1.25rem 1.5rem;
  border-top: 1px solid rgba(74, 111, 165, 0.2);
}
.sidebar-login-btn {
  width: 100%;
  background: #1b2838;
  color: #ffffff !important;
  border: 1px solid rgba(74, 111, 165, 0.4) !important;
  border-radius: 10px !important;
  padding: 0.7rem 1rem !important;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
}
.sidebar-login-btn:hover {
  background: #2a3f5f;
  border-color: var(--color-primary) !important;
}
.sidebar-avatar {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: 2px solid var(--color-primary, #4a7fc0);
  flex-shrink: 0;
}
.sidebar-username {
  color: #ffffff;
  font-weight: 600;
  font-size: 0.9rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
  text-align: left;
}
.sidebar-user-section {
  width: 100%;
}
.sidebar-user-toggle {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1.5px solid rgba(74, 111, 165, 0.25);
  border-radius: 10px;
  padding: 0.6rem 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
}
.sidebar-user-toggle:hover {
  background: rgba(74, 111, 165, 0.15);
  border-color: rgba(74, 111, 165, 0.5);
}
.sidebar-chevron {
  color: rgba(255, 255, 255, 0.5);
  flex-shrink: 0;
  transition: transform 0.2s ease;
}
.sidebar-chevron.rotated {
  transform: rotate(180deg);
}
.sidebar-settings-panel {
  margin-top: 6px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(74, 111, 165, 0.2);
  border-radius: 10px;
  overflow: hidden;
}
.sidebar-settings-item {
  padding: 0.65rem 1rem;
  color: #ffffff;
  font-size: 0.9rem;
}
.sidebar-settings-item h6 {
  font-weight: 700;
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.5);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.4rem;
}
.sidebar-settings-item .form-check {
  margin-left: 4px;
  margin-bottom: 2px;
}
.sidebar-settings-item .form-check-label {
  color: rgba(255, 255, 255, 0.85);
  font-size: 0.88rem;
}
.sidebar-settings-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.15s ease;
}
.sidebar-settings-link:hover {
  background: rgba(74, 111, 165, 0.2);
}
.sidebar-settings-divider {
  height: 1px;
  background: rgba(74, 111, 165, 0.15);
}
.sidebar-logout-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: rgba(255, 100, 100, 0.85) !important;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.15s ease;
}
.sidebar-logout-link:hover {
  color: #ff6b6b !important;
}
</style>
