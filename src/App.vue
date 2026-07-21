<template>
  <div id="app" @click="closeDropdown">
    <LoginPopup v-model="showLoginPopup" @login-with-steam="loginWithSteam" />
    <AccountNotFoundPopup v-model="showErrorPopup" />
    <PointsCalculator v-model="showCalculator" />
    <UpdateBanner v-if="isUpdating" @dismiss="dismissUpdate" />
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
                @click="showCalculator = true"
              >
                <i class="bi bi-calculator"></i>
              </button>
            </li>
            <div class="navbar-right">
              <div class="navbar-search-container me-3" @click.stop>
                <div
                  class="navbar-search-box"
                  :class="{ 'is-focused': navbarSearchFocused }"
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
                    v-model="searchQuery"
                    @focus="navbarSearchFocused = true"
                    @blur="navbarSearchFocused = false"
                    @keydown="onNavSearchKeydown"
                    placeholder="Search..."
                    class="navbar-search-input"
                    autocomplete="off"
                  />
                  <button
                    v-if="searchQuery"
                    class="navbar-clear-btn"
                    @mousedown.prevent="
                      searchQuery = '';
                      searchResults = null;
                      navHighlightedIndex = -1;
                    "
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
                      searchQuery.trim() &&
                      (loadingMaps || loadingPlayers || searchResults)
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
                      <div v-if="loadingMaps" class="nav-loading-row">
                        <span class="nav-skeleton nav-skeleton-text"></span>
                      </div>
                      <ul
                        v-else-if="searchResults && searchResults.maps.length"
                      >
                        <HoverPreview
                          v-for="(map, i) in searchResults.maps"
                          :key="map.id"
                          :mapName="map.name"
                          style="display: block"
                        >
                          <SmartLink
                            :to="{ name: 'MapPage', params: { mapId: map.id } }"
                            tag="li"
                            class="nav-result-item"
                            :class="{
                              'is-highlighted': navHighlightedIndex === i,
                            }"
                            :data-nav-result-index="i"
                            @mouseenter="navHighlightedIndex = i"
                            @mouseleave="navHighlightedIndex = -1"
                            @click="handleSearchResultClick"
                          >
                            <span class="nav-item-name">{{
                              map.name || `Map ID: ${map.id}`
                            }}</span>
                          </SmartLink>
                        </HoverPreview>
                      </ul>
                      <div
                        v-else-if="!loadingMaps && searchResults"
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
                      <div v-if="loadingPlayers" class="nav-loading-rows">
                        <div v-for="n in 3" :key="n" class="nav-loading-row">
                          <span class="nav-skeleton nav-skeleton-avatar"></span>
                          <span class="nav-skeleton nav-skeleton-text"></span>
                        </div>
                      </div>
                      <ul
                        v-else-if="
                          searchResults && searchResults.players.length
                        "
                      >
                        <SmartLink
                          v-for="(player, i) in searchResults.players"
                          :key="player.id"
                          :to="{
                            name: 'PlayerPage',
                            params: { playerId: player.id },
                          }"
                          tag="li"
                          class="nav-result-item"
                          :class="{
                            'is-highlighted':
                              navHighlightedIndex === navMapCount + i,
                          }"
                          :data-nav-result-index="navMapCount + i"
                          @mouseenter="navHighlightedIndex = navMapCount + i"
                          @mouseleave="navHighlightedIndex = -1"
                        >
                          <div class="nav-player-avatar-wrapper">
                            <img
                              v-if="player.steam_avatar"
                              :src="player.steam_avatar"
                              :alt="player.name"
                              class="nav-player-avatar"
                              @error="handleAvatarError"
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
                        v-else-if="!loadingPlayers && searchResults"
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
              <div v-if="authChecking" class="login-checking">
                <span class="login-spinner"></span>
              </div>
              <button
                v-else-if="!user || !user.steamid"
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
                          v-model="rankPreference"
                          value="overall"
                          @change="updateUserPreferences"
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
                          v-model="rankPreference"
                          value="soldier"
                          @change="updateUserPreferences"
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
                          v-model="rankPreference"
                          value="demoman"
                          @change="updateUserPreferences"
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
                          v-model="gender"
                          value="male"
                          @change="updateUserPreferences"
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
                          v-model="gender"
                          value="female"
                          @change="updateUserPreferences"
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
                          <label
                            class="color-swatch"
                            :for="'color-' + index"
                            :class="{
                              selected: colorPreference === color.value,
                              locked: !unlockedColors.includes(color.value),
                            }"
                            :style="{ backgroundColor: color.color }"
                            :title="
                              unlockedColors.includes(color.value)
                                ? ''
                                : 'Donator only'
                            "
                          >
                            <svg
                              v-if="!unlockedColors.includes(color.value)"
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
                              showTooltip && activeTooltipColor === color.value
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
                                @click="goToDonate"
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
                          v-for="pattern in patternOptions"
                          :key="pattern.value"
                          class="pattern-option-wrapper"
                          @click="selectPattern(pattern)"
                        >
                          <label
                            class="pattern-block"
                            :for="'pattern-' + (pattern.value ?? 'none')"
                            :class="{
                              selected: bannerPattern === pattern.value,
                              'pattern-none': pattern.value === null,
                              locked: !unlockedPatterns.includes(pattern.value),
                            }"
                            :style="
                              pattern.thumb
                                ? { backgroundImage: `url(${pattern.thumb})` }
                                : {}
                            "
                            :title="
                              unlockedPatterns.includes(pattern.value)
                                ? pattern.label
                                : pattern.label + ' (Donator only)'
                            "
                          >
                            <svg
                              v-if="!unlockedPatterns.includes(pattern.value)"
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
                              showPatternTooltip &&
                              activeTooltipPattern === pattern.value
                            "
                            class="supporter-tooltip pattern-tooltip"
                            @click.stop
                          >
                            <div class="tooltip-content">
                              <p>{{ patternTooltipMessage }}</p>
                              <button
                                v-if="patternTooltipShowDonateBtn"
                                @click="goToDonate"
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
                    <a class="dropdown-item clickable" @click="logout" href="#"
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
          @click="showCalculator = true"
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
    <div
      class="sidebar-overlay"
      :class="{ 'sidebar-overlay-open': sidebarOpen }"
      @click="closeSidebar"
    ></div>
    <div
      class="sidebar-drawer"
      :class="{ 'sidebar-open': sidebarOpen }"
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
          :class="{ 'is-focused': sidebarSearchFocused }"
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
            v-model="searchQuery"
            @focus="sidebarSearchFocused = true"
            @blur="sidebarSearchFocused = false"
            @keydown="onNavSearchKeydown"
            placeholder="Search maps & players..."
            class="sidebar-search-input"
            autocomplete="off"
          />
          <button
            v-if="searchQuery"
            class="sidebar-search-clear"
            @mousedown.prevent="
              searchQuery = '';
              searchResults = null;
              navHighlightedIndex = -1;
            "
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
            searchQuery.trim() &&
            (loadingMaps || loadingPlayers || searchResults)
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
            <div v-if="loadingMaps" class="nav-loading-row">
              <span class="nav-skeleton nav-skeleton-icon"></span>
              <span class="nav-skeleton nav-skeleton-text"></span>
            </div>
            <ul v-else-if="searchResults && searchResults.maps.length">
              <HoverPreview
                v-for="(map, i) in searchResults.maps"
                :key="map.id"
                :mapName="map.name"
                style="display: block"
              >
                <SmartLink
                  :to="{ name: 'MapPage', params: { mapId: map.id } }"
                  tag="li"
                  class="nav-result-item"
                  :class="{ 'is-highlighted': navHighlightedIndex === i }"
                  :data-nav-result-index="i"
                  @mouseenter="navHighlightedIndex = i"
                  @mouseleave="navHighlightedIndex = -1"
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
              v-else-if="!loadingMaps && searchResults"
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
            <div v-if="loadingPlayers" class="nav-loading-rows">
              <div v-for="n in 3" :key="n" class="nav-loading-row">
                <span class="nav-skeleton nav-skeleton-avatar"></span>
                <span class="nav-skeleton nav-skeleton-text"></span>
              </div>
            </div>
            <ul v-else-if="searchResults && searchResults.players.length">
              <SmartLink
                v-for="(player, i) in searchResults.players"
                :key="player.id"
                :to="{ name: 'PlayerPage', params: { playerId: player.id } }"
                tag="li"
                class="nav-result-item"
                :class="{
                  'is-highlighted': navHighlightedIndex === navMapCount + i,
                }"
                :data-nav-result-index="navMapCount + i"
                @mouseenter="navHighlightedIndex = navMapCount + i"
                @mouseleave="navHighlightedIndex = -1"
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
                    @error="handleAvatarError"
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
              v-else-if="!loadingPlayers && searchResults"
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
        <div v-if="authChecking" class="login-checking login-checking-sidebar">
          <span class="login-spinner"></span>
        </div>
        <button
          v-else-if="!user || !user.steamid"
          class="btn sidebar-login-btn"
          @click="loginWithSteam"
        >
          <i class="bi bi-steam"></i> Login with Steam
        </button>
        <div v-if="user && user.steamid" class="sidebar-user-section">
          <button
            class="sidebar-user-toggle"
            @click.stop="sidebarSettingsOpen = !sidebarSettingsOpen"
          >
            <img
              :src="user.avatar"
              alt="avatar"
              class="sidebar-avatar"
              v-if="user.avatar"
            />
            <span class="sidebar-username">{{ user.name }}</span>
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
              :to="getPlayerRoute()"
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
                  v-model="rankPreference"
                  value="overall"
                  @change="updateUserPreferences"
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
                  v-model="rankPreference"
                  value="soldier"
                  @change="updateUserPreferences"
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
                  v-model="rankPreference"
                  value="demoman"
                  @change="updateUserPreferences"
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
                  v-model="gender"
                  value="male"
                  @change="updateUserPreferences"
                />
                <label class="form-check-label" for="sb-genderMale">Male</label>
              </div>
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="radio"
                  id="sb-genderFemale"
                  name="sbGender"
                  v-model="gender"
                  value="female"
                  @change="updateUserPreferences"
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
                  v-for="(color, index) in colorOptions"
                  :key="index"
                  class="color-option-wrapper"
                  @click="selectColor(color.value)"
                >
                  <input
                    class="d-none"
                    type="radio"
                    :id="'sb-color-' + index"
                    name="sbColorPreference"
                    v-model="colorPreference"
                    :value="color.value"
                    @change="updateUserPreferences"
                    :disabled="!unlockedColors.includes(color.value)"
                  />
                  <label
                    class="color-swatch"
                    :for="'sb-color-' + index"
                    :class="{
                      selected: colorPreference === color.value,
                      locked: !unlockedColors.includes(color.value),
                    }"
                    :style="{ backgroundColor: color.color }"
                    :title="
                      unlockedColors.includes(color.value)
                        ? ''
                        : 'Supporter only'
                    "
                  >
                    <svg
                      v-if="!unlockedColors.includes(color.value)"
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
                <div v-if="showTooltip" class="supporter-tooltip" @click.stop>
                  <div class="tooltip-content">
                    <p>You need to become a supporter to select colors</p>
                    <button @click="goToDonate" class="tooltip-button">
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
                  v-for="pattern in patternOptions"
                  :key="pattern.value"
                  class="pattern-option-wrapper"
                  @click="
                    !unlockedPatterns.includes(pattern.value)
                      ? selectPattern(pattern)
                      : null
                  "
                >
                  <input
                    class="d-none"
                    type="radio"
                    :id="'sb-pattern-' + pattern.value"
                    name="sbPatternPreference"
                    v-model="bannerPattern"
                    :value="pattern.value"
                    @change="updateUserPreferences"
                    :disabled="!unlockedPatterns.includes(pattern.value)"
                  />
                  <label
                    class="pattern-block"
                    :for="'sb-pattern-' + pattern.value"
                    :class="{
                      selected: bannerPattern === pattern.value,
                      'pattern-none': pattern.value === null,
                      locked: !unlockedPatterns.includes(pattern.value),
                    }"
                    :style="
                      pattern.thumb
                        ? { backgroundImage: `url(${pattern.thumb})` }
                        : {}
                    "
                    :title="
                      unlockedPatterns.includes(pattern.value)
                        ? pattern.label
                        : pattern.label + ' (Donator only)'
                    "
                  >
                    <svg
                      v-if="!unlockedPatterns.includes(pattern.value)"
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
              <a class="sidebar-logout-link" @click="logout" href="#"
                ><i class="bi bi-box-arrow-right"></i> Logout</a
              >
            </div>
          </div>
        </div>
      </div>
    </div>
    <router-view />
    <AppFooter />
  </div>
</template>

<script>
import HoverPreview from "./components/utils/HoverPreview.vue";
import PointsCalculator from "./components/utils/PointsCalculator.vue";
import LoginPopup from "./components/popups/LoginPopup.vue";
import AccountNotFoundPopup from "./components/popups/AccountNotFoundPopup.vue";
import UpdateBanner from "./components/UpdateBanner.vue";
import AppFooter from "./components/AppFooter.vue";
import DOMPurify from "dompurify";
import { provide, reactive } from "vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "App",
  components: {
    HoverPreview,
    PointsCalculator,
    LoginPopup,
    AccountNotFoundPopup,
    UpdateBanner,
    AppFooter,
  },
  data() {
    return {
      showErrorPopup: false,
      showLoginPopup: false,
      showCalculator: false,
      hasVisitedBefore: false,
      authChecking: true,
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
      activeTooltipColor: null,
      showPatternTooltip: false,
      patternTooltipTimeout: null,
      patternTooltipMessage: "",
      patternTooltipShowDonateBtn: false,
      activeTooltipPattern: null,
      sidebarOpen: false,
      sidebarSettingsOpen: false,
      isDesktop: true,
      navbarSearchFocused: false,
      sidebarSearchFocused: false,
      navHighlightedIndex: -1,
      bannerPattern: null,
      colorPreference: "blue",
      unlockedColors: [],
      unlockedPatterns: [],
      patternOptions: [
        { value: null, label: "None", thumb: null },
        { value: "soldier", label: "Soldier", thumb: "/icons/soldier.png" },
        { value: "demoman", label: "Demoman", thumb: "/icons/demoman.png" },
        {
          value: "golly",
          label: "Golly",
          thumb: "/images/golly.jpg",
          unlockMessage:
            "You need to become a supporter to select this pattern",
          donatable: true,
        },
        {
          value: "jf",
          label: "JF",
          thumb: "/images/jf-logo.png",
          unlockMessage: "Unlocked by playing in special Jump Fortress events",
          donatable: false,
        },
      ],
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
      profileUpdateTracker: reactive({
        rank: "overall",
        color: "blue",
        gender: "male",
        pattern: null,
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
    navMapCount() {
      return this.searchResults?.maps?.length ?? 0;
    },
    navPlayerCount() {
      return this.searchResults?.players?.length ?? 0;
    },
    navTotalResults() {
      return this.navMapCount + this.navPlayerCount;
    },
  },
  provide() {
    return { profileUpdateTracker: this.profileUpdateTracker };
  },
  methods: {
    onNavSearchKeydown(e) {
      if (!this.searchResults || this.navTotalResults === 0) return;
      if (e.key === "ArrowDown") {
        e.preventDefault();
        this.navHighlightedIndex =
          this.navHighlightedIndex < this.navTotalResults - 1
            ? this.navHighlightedIndex + 1
            : 0;
        this.scrollNavHighlightedIntoView();
      } else if (e.key === "ArrowUp") {
        e.preventDefault();
        this.navHighlightedIndex =
          this.navHighlightedIndex > 0
            ? this.navHighlightedIndex - 1
            : this.navTotalResults - 1;
        this.scrollNavHighlightedIntoView();
      } else if (e.key === "Enter" && this.navHighlightedIndex >= 0) {
        e.preventDefault();
        this.navigateNavHighlighted();
      } else if (e.key === "Escape") {
        this.searchResults = null;
        this.navHighlightedIndex = -1;
      }
    },
    scrollNavHighlightedIntoView() {
      this.$nextTick(() => {
        const ref = this.sidebarOpen
          ? this.$refs.sidebarDropdownRef
          : this.$refs.navDropdownRef;
        if (!ref) return;
        const el = ref.querySelector(
          `[data-nav-result-index="${this.navHighlightedIndex}"]`,
        );
        if (el) el.scrollIntoView({ block: "nearest" });
      });
    },
    navigateNavHighlighted() {
      if (!this.searchResults) return;
      if (this.navHighlightedIndex < this.navMapCount) {
        const map = this.searchResults.maps[this.navHighlightedIndex];
        if (map) {
          this.$router.push({ name: "MapPage", params: { mapId: map.id } });
          this.handleSearchResultClick();
          if (this.sidebarOpen) this.closeSidebar();
        }
      } else {
        const player =
          this.searchResults.players[
            this.navHighlightedIndex - this.navMapCount
          ];
        if (player) {
          this.$router.push({
            name: "PlayerPage",
            params: { playerId: player.id },
          });
          this.handleSearchResultClick();
          if (this.sidebarOpen) this.closeSidebar();
        }
      }
    },
    handleAvatarError(e) {
      e.target.style.display = "none";
      if (e.target.nextElementSibling)
        e.target.nextElementSibling.style.display = "flex";
    },
    checkNavbarOverflow() {
      this.isDesktop = window.innerWidth >= 1200;
      if (this.isDesktop) this.sidebarOpen = false;
      const nav = document.querySelector(".navbar-nav");
      if (!nav || !this.isDesktop) return;

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
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
      document.body.style.overflow = this.sidebarOpen ? "hidden" : "";
    },
    closeSidebar() {
      this.sidebarOpen = false;
      this.sidebarSettingsOpen = false;
      document.body.style.overflow = "";
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
    handleSearchResultClick() {
      this.closeNavbar();
      this.searchQuery = "";
      this.searchResults = { maps: [], players: [] };
      this.navHighlightedIndex = -1;
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
      this.navHighlightedIndex = -1;
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
      if (!this.unlockedColors.includes(colorValue)) {
        this.activeTooltipColor = colorValue;
        this.showTooltip = true;
        if (this.tooltipTimeout) clearTimeout(this.tooltipTimeout);
        this.tooltipTimeout = setTimeout(() => {
          this.showTooltip = false;
          this.activeTooltipColor = null;
        }, 3000);
        return;
      }
      this.colorPreference = colorValue;
      await this.updateUserPreferences();
    },
    async selectPattern(pattern) {
      if (!this.unlockedPatterns.includes(pattern.value)) {
        this.activeTooltipPattern = pattern.value;
        this.patternTooltipMessage =
          pattern.unlockMessage || "This pattern is locked";
        this.patternTooltipShowDonateBtn = !!pattern.donatable;
        this.showPatternTooltip = true;
        if (this.patternTooltipTimeout)
          clearTimeout(this.patternTooltipTimeout);
        this.patternTooltipTimeout = setTimeout(() => {
          this.showPatternTooltip = false;
          this.activeTooltipPattern = null;
        }, 3000);
        return;
      }
      this.bannerPattern = pattern.value;
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
      if (!this.unlockedColors.includes(this.colorPreference)) {
        this.colorPreference = "blue";
      }
      if (!this.unlockedPatterns.includes(this.bannerPattern)) {
        this.bannerPattern = null;
      }
      try {
        const response = await fetch(
          `${API_BASE_URL}/users/update-user/${this.currentUser.playerid}`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({
              rankPref: this.rankPreference,
              gender: this.gender,
              color: this.colorPreference,
              pattern: this.bannerPattern ?? null,
            }),
          },
        );
        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);
        this.profileUpdateTracker.rank = this.rankPreference;
        this.profileUpdateTracker.color = this.colorPreference;
        this.profileUpdateTracker.gender = this.gender;
        this.profileUpdateTracker.pattern = this.bannerPattern;
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
      this.navHighlightedIndex = -1;
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
        this.navHighlightedIndex = -1;
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
  created() {},
  beforeDestroy() {
    window.removeEventListener("resize", this._navResizeHandler);
    if (this._navRAF) cancelAnimationFrame(this._navRAF);
    this._navObserver?.disconnect();
    clearInterval(this.updateInterval);
    clearTimeout(this.debounceTimer);
    document.body.style.overflow = "";
  },
  watch: {
    currentUser() {
      this.$nextTick(() => this.checkNavbarOverflow());
    },
    searchQuery() {
      this.debouncedSearch();
    },
    $route() {
      this.closeSidebar();
    },
  },
  async mounted() {
    this.isDesktop = window.innerWidth >= 1200;

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("error") === "player_not_found") {
      this.showErrorPopup = true;
      window.history.replaceState({}, "", window.location.pathname);
    }

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

    try {
      const isAuthenticated = await this.checkAuthStatus();
      if (isAuthenticated) {
        const userData = await this.fetchUserData();
        if (userData) {
          this.currentUser = userData;
          this.rankPreference = userData.rankpref || "overall";
          this.gender = userData.gender || "male";
          this.donator = userData.donator || 0;
          this.colorPreference = userData.color || "blue";
          this.bannerPattern = userData.pattern ?? null;
          this.unlockedColors = userData.unlockedColors || [];
          this.unlockedPatterns = userData.unlockedPatterns || [];
          this.profileUpdateTracker.rank = this.rankPreference;
          this.profileUpdateTracker.color = this.colorPreference;
          this.profileUpdateTracker.gender = this.gender;
          this.profileUpdateTracker.pattern = this.bannerPattern;
        }
      }
    } catch (error) {
      console.error("Error during authentication check:", error);
      this.currentUser = null;
    } finally {
      this.authChecking = false;
    }

    if (!this.currentUser && this.checkFirstVisit()) {
      this.debounceTimer = setTimeout(() => {
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
  border-radius: 10px !important;
  padding: 10px 15px !important;
  border: 2px solid transparent !important;
}
.login-button:hover {
  color: var(--color-text);
  border-color: #363a3d !important;
  background: rgba(255, 255, 255, 0.05);
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
</style>

<style scoped>
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

.navbar-search-results-dropdown :deep(.hover-preview-wrapper),
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
