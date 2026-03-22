<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container py-4 d-flex flex-column align-items-center">
      <div class="page-header">
        <h1 class="page-title">
          <span class="title-icon">🔍</span>
          Lookup Players and Maps
        </h1>
        <p class="page-subtitle">
          Search and filter through player or map records
        </p>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div
        v-if="playerId"
        class="lookup-player-banner"
        :style="{
          background: `linear-gradient(135deg, ${bannerColors.color1}, ${bannerColors.color2})`,
        }"
      >
        <SmartLink
          tag="div"
          :to="{ name: 'PlayerPage', params: { playerId: playerId } }"
          class="lookup-banner-content fancy-hover"
        >
          <div class="lookup-banner-left">
            <img
              :src="playerAvatar || '/avatars/golly.jpg'"
              alt="Avatar"
              class="lookup-avatar"
              onerror="this.src='/avatars/golly.jpg'"
            />
            <div class="lookup-player-info">
              <h2
                class="lookup-player-name"
                v-html="sanitize(selectedPlayerName) || 'Selected Player'"
              ></h2>
              <p v-if="playerCountry" class="lookup-country">
                <img
                  :src="getFlagImageUrl(playerCountryCode)"
                  alt="flag"
                  class="lookup-flag-icon"
                />
                {{ playerCountry }} ({{ playerCountryCode }})
              </p>
            </div>
          </div>
          <div v-if="playerRankInfo" class="lookup-banner-stats">
            <div class="lookup-stat-card">
              <span class="lookup-stat-label">Overall</span>
              <span class="lookup-stat-value"
                >#{{ playerRankInfo.overall_rank }}</span
              >
              <span class="lookup-stat-points">{{
                playerRankInfo.overall_points.toLocaleString()
              }}</span>
            </div>
            <div class="lookup-stat-card">
              <span class="lookup-stat-label">Soldier</span>
              <span class="lookup-stat-value"
                >#{{ playerRankInfo.soldier_rank }}</span
              >
              <span class="lookup-stat-points">{{
                playerRankInfo.soldier_points.toLocaleString()
              }}</span>
            </div>
            <div class="lookup-stat-card">
              <span class="lookup-stat-label">Demoman</span>
              <span class="lookup-stat-value"
                >#{{ playerRankInfo.demoman_rank }}</span
              >
              <span class="lookup-stat-points">{{
                playerRankInfo.demoman_points.toLocaleString()
              }}</span>
            </div>
          </div>
          <div v-else-if="loadingRankInfo" class="loading-ranks">
            <div class="loading-spinner"></div>
            <span>Loading...</span>
          </div>
        </SmartLink>
      </div>
      <div
        v-else-if="mapId"
        class="lookup-map-banner"
        :style="{
          backgroundImage: `linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/map-backgrounds/${mapInfo?.map.name}.webp')`,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
        }"
      >
        <SmartLink
          tag="div"
          :to="{ name: 'MapPage', params: { mapId: mapId } }"
          class="lookup-banner-content fancy-hover"
        >
          <div class="lookup-map-main">
            <h2
              class="lookup-map-name"
              v-html="sanitize(selectedMapName) || 'Selected Map'"
            ></h2>
            <div class="map-primary-info">
              <div class="map-tiers">
                <div class="tier-group">
                  <img
                    src="/icons/soldier.png"
                    alt="Soldier"
                    class="tier-class-icon"
                  />
                  <span class="tier-text"
                    >T{{ mapInfo?.map.soldier_tier }}</span
                  >
                  <span class="rating-text"
                    >R{{ mapInfo?.map.soldier_rating }}</span
                  >
                </div>
                <span class="tier-divider">•</span>
                <div class="tier-group">
                  <img
                    src="/icons/demoman.png"
                    alt="Demoman"
                    class="tier-class-icon"
                  />
                  <span class="tier-text"
                    >T{{ mapInfo?.map.demoman_tier }}</span
                  >
                  <span class="rating-text"
                    >R{{ mapInfo?.map.demoman_rating }}</span
                  >
                </div>
              </div>
              <div
                v-if="mapInfo?.map.intended_class"
                :class="[
                  'intended-class-display',
                  { 'no-circle': mapInfo.map.intended_class === 5 },
                ]"
              >
                <div
                  v-if="mapInfo.map.intended_class === 5"
                  class="both-classes"
                >
                  <div class="class-circle">
                    <img
                      src="/icons/soldier.png"
                      alt="Soldier"
                      class="intended-class-icon"
                    />
                  </div>
                  <div class="class-circle">
                    <img
                      src="/icons/demoman.png"
                      alt="Demoman"
                      class="intended-class-icon"
                    />
                  </div>
                </div>
                <img
                  v-else-if="mapInfo.map.intended_class === 3"
                  src="/icons/soldier.png"
                  alt="Soldier"
                  class="intended-class-icon"
                />
                <img
                  v-else-if="mapInfo.map.intended_class === 4"
                  src="/icons/demoman.png"
                  alt="Demoman"
                  class="intended-class-icon"
                />
              </div>
            </div>
          </div>
          <div v-if="mapInfo" class="lookup-map-secondary">
            <span class="secondary-stat"
              >{{ mapInfo.map.course_count }} courses</span
            >
            <span class="stat-separator">•</span>
            <span class="secondary-stat"
              >{{ mapInfo.map.bonus_count }} bonuses</span
            >
            <span class="stat-separator">•</span>
            <span class="secondary-stat"
              >{{ mapInfo.map.soldier_completion_count }} [S] completions</span
            >
            <span class="stat-separator">•</span>
            <span class="secondary-stat"
              >{{ mapInfo.map.demoman_completion_count }} [D] completions</span
            >
            <span class="stat-separator">•</span>
            <span class="secondary-stat"
              >Added {{ formatMapDate(mapInfo.map.date_added) }}</span
            >
          </div>
          <div v-else-if="loading" class="loading-ranks">
            <div class="loading-spinner"></div>
            <span>Loading...</span>
          </div>
        </SmartLink>
      </div>

      <div class="search-section">
        <div class="lookup-search-container" @click.stop>
          <div
            class="lookup-search-box"
            :class="{ 'is-focused': lookupSearchFocused }"
          >
            <div class="lookup-search-icon-wrap">
              <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                class="lookup-search-icon-svg"
              >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
              </svg>
            </div>
            <input
              type="text"
              v-model="searchQuery"
              @input="onSearch"
              @focus="lookupSearchFocused = true"
              @blur="lookupSearchFocused = false"
              placeholder="Search for players or maps..."
              class="lookup-search-input"
            />
            <button
              v-if="searchQuery"
              class="lookup-search-clear"
              @mousedown.prevent="
                searchQuery = '';
                searchResults = null;
                showLoading = false;
              "
              aria-label="Clear search"
            >
              <svg
                width="13"
                height="13"
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

          <Transition name="lookup-dropdown">
            <div
              class="lookup-search-results-dropdown"
              v-if="
                searchQuery.trim() &&
                (showLoading ||
                  (searchResults &&
                    (searchResults.players.length ||
                      searchResults.maps.length)))
              "
            >
              <div v-if="showLoading">
                <div class="lookup-search-section">
                  <div class="lookup-section-label">
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
                  <div class="lookup-loading-row">
                    <span class="lookup-skeleton lookup-skeleton-text"></span>
                  </div>
                </div>
                <div class="lookup-section-divider"></div>
                <div class="lookup-search-section">
                  <div class="lookup-section-label">
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
                  <div class="lookup-loading-rows">
                    <div v-for="n in 3" :key="n" class="lookup-loading-row">
                      <span
                        class="lookup-skeleton lookup-skeleton-avatar"
                      ></span>
                      <span class="lookup-skeleton lookup-skeleton-text"></span>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else>
                <div
                  v-if="searchResults.maps && searchResults.maps.length"
                  class="lookup-search-section"
                >
                  <div class="lookup-section-label">
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
                  <ul>
                    <HoverPreview
                      v-for="map in searchResults.maps"
                      :key="'map-' + map.id"
                      :mapName="map.name"
                      style="display: block"
                    >
                      <li
                        class="lookup-result-item"
                        @click="selectMap(map.id, map.name)"
                      >
                        <span
                          class="lookup-result-name"
                          v-html="sanitize(map.name) || `Map ID: ${map.id}`"
                        ></span>
                      </li>
                    </HoverPreview>
                  </ul>
                </div>
                <div
                  v-if="
                    searchResults.maps &&
                    searchResults.maps.length &&
                    searchResults.players &&
                    searchResults.players.length
                  "
                  class="lookup-section-divider"
                ></div>
                <div
                  v-if="searchResults.players && searchResults.players.length"
                  class="lookup-search-section"
                >
                  <div class="lookup-section-label">
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
                  <ul>
                    <li
                      v-for="player in searchResults.players"
                      :key="'player-' + player.id"
                      class="lookup-result-item"
                      @click="selectPlayer(player.id)"
                    >
                      <div class="lookup-player-avatar-wrapper">
                        <img
                          v-if="player.steam_avatar"
                          :src="player.steam_avatar"
                          :alt="player.name"
                          class="lookup-player-avatar"
                          @error="handleAvatarError"
                        />
                        <div v-else class="lookup-player-avatar-fallback">
                          {{ (player.name || "?")[0].toUpperCase() }}
                        </div>
                      </div>
                      <span
                        class="lookup-result-name"
                        v-html="
                          sanitize(player.name) || `Player ID: ${player.id}`
                        "
                      ></span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </Transition>
        </div>
        <div
          v-if="
            searchResults &&
            (searchResults.players.length || searchResults.maps.length)
          "
          class="dropdown-overlay"
          @click="searchResults = null"
          style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 999;
            background: transparent;
          "
        ></div>
      </div>

      <hr class="row-divider" style="width: 75%" />
      <div class="filter-section">
        <div class="filter-content">
          <div class="filter-columns" style="flex-direction: column; gap: 20px">
            <div
              class="filter-row"
              style="display: flex; gap: 40px; justify-content: center"
            >
              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Soldier tiers</h6>
                  <div class="tier-filter-container">
                    <div class="tier-filters">
                      <label
                        v-for="tier in availableTiers"
                        :key="'soldier-tier-' + tier"
                        class="tier-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="tier"
                          v-model="selectedSoldierTiers"
                          @change="onFilterChange"
                        />
                        <span :class="`tier-badge tier-${tier}`">{{
                          tier
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Demoman tiers</h6>
                  <div class="tier-filter-container">
                    <div class="tier-filters">
                      <label
                        v-for="tier in availableTiers"
                        :key="'demo-tier-' + tier"
                        class="tier-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="tier"
                          v-model="selectedDemomanTiers"
                          @change="onFilterChange"
                        />
                        <span :class="`tier-badge tier-${tier}`">{{
                          tier
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              class="filter-row responsive-ratings-row"
              style="
                display: flex;
                gap: 40px;
                justify-content: center;
                align-items: flex-start;
                width: 100%;
              "
            >
              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Soldier ratings</h6>
                  <div class="rating-filter-container">
                    <div class="rating-filters">
                      <label
                        v-for="rating in availableRatings"
                        :key="'soldier-rating-' + rating"
                        class="rating-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="rating"
                          v-model="selectedSoldierRatings"
                          @change="onFilterChange"
                        />
                        <span :class="`rating-badge rating-${rating}`">{{
                          rating
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="filter-column intended-class-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Intended Class</h6>
                  <div class="intended-class-buttons">
                    <button
                      v-for="cls in availableIntendedClasses"
                      :key="cls.id"
                      @click="toggleIntendedClass(cls.id)"
                      :class="{
                        active: selectedIntendedClasses.includes(cls.id),
                      }"
                      class="intended-class-btn"
                    >
                      <img :src="cls.icon" :alt="cls.label" />
                    </button>
                  </div>
                </div>
              </div>
              <div class="filter-column">
                <div class="filter-group">
                  <h6 class="filter-title mb-2">Demoman ratings</h6>
                  <div class="rating-filter-container">
                    <div class="rating-filters">
                      <label
                        v-for="rating in availableRatings"
                        :key="'demo-rating-' + rating"
                        class="rating-checkbox"
                      >
                        <input
                          type="checkbox"
                          :value="rating"
                          v-model="selectedDemomanRatings"
                          @change="onFilterChange"
                        />
                        <span :class="`rating-badge rating-${rating}`">{{
                          rating
                        }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="filter-group">
            <h6 class="filter-title mb-2">Placement</h6>
            <div class="group-filter-container">
              <div class="group-filters">
                <label class="group-checkbox">
                  <input
                    type="checkbox"
                    value="WR"
                    v-model="selectedGroups"
                    @change="onFilterChange"
                  />
                  <span class="group-badge group-wr">WR</span>
                </label>
                <label class="group-checkbox">
                  <input
                    type="checkbox"
                    value="TT"
                    v-model="selectedGroups"
                    @change="onFilterChange"
                  />
                  <span class="group-badge group-tt">TT</span>
                </label>
                <label
                  v-for="group in availableGroups"
                  :key="'group-' + group"
                  class="group-checkbox"
                >
                  <input
                    type="checkbox"
                    :value="group"
                    v-model="selectedGroups"
                    @change="onFilterChange"
                  />
                  <span :class="`group-badge group-${group}`"
                    >G{{ group }}</span
                  >
                </label>
                <label class="group-checkbox">
                  <input
                    type="checkbox"
                    value="BT"
                    v-model="selectedGroups"
                    @change="onFilterChange"
                  />
                  <span class="group-badge group-bt">BT</span>
                </label>
              </div>
            </div>
          </div>
          <div class="filter-columns">
            <div class="filter-group">
              <h6 class="filter-title mb-2">Class</h6>
              <div class="class-filter-container">
                <div
                  v-for="classOption in ['soldier', 'demoman']"
                  :key="classOption"
                  class="class-checkbox"
                  :class="{ selected: selectedClasses.includes(classOption) }"
                  @click="toggleClass(classOption)"
                >
                  <span>{{ classOption }}</span>
                </div>
              </div>
            </div>
            <div class="filter-group">
              <h6 class="filter-title mb-2">Type</h6>
              <div class="type-filter-container">
                <div
                  v-for="typeOption in ['map', 'course', 'bonus']"
                  :key="typeOption"
                  class="type-checkbox"
                  :class="{ selected: selectedTypes.includes(typeOption) }"
                  @click="toggleType(typeOption)"
                >
                  <span>{{ typeOption }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="filter-actions">
            <div class="filter-group">
              <h6 class="filter-title mb-2">Status</h6>
              <div class="status-filter-container">
                <div
                  v-for="statusOption in ['completed', 'incomplete']"
                  :key="statusOption"
                  class="status-checkbox"
                  :class="{ selected: selectedStatus.includes(statusOption) }"
                  @click="toggleStatus(statusOption)"
                >
                  <span>{{ statusOption }}</span>
                </div>
              </div>
            </div>
            <div class="filter-group clear-filter">
              <button
                type="button"
                @click="clearAllFilters"
                class="btn btn-secondary clear-filters-btn"
              >
                Clear filters
              </button>
              <span class="filter-count"
                >Displaying {{ filteredSortedItems.length }} of
                {{ totalRecordsLength() }} records</span
              >
            </div>
            <button
              @click="downloadAsCSV"
              class="btn btn-success"
              style="margin-left: 10px"
            >
              <span class="download-button">Download CSV</span>
            </button>
          </div>
        </div>
      </div>
      <hr class="row-divider" style="width: 75%" />
      <div v-if="loading" class="text-center">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading records...</span>
        </div>
      </div>
      <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
      <div v-else class="records-container">
        <div
          v-if="playerId != null || mapId != null"
          class="search-records-container"
        >
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
              <path d="M21 21l-4.35-4.35"></path>
            </svg>
            <input
              type="text"
              v-model="recordSearchQuery"
              :placeholder="playerId ? 'Search map...' : 'Search player...'"
              class="search-records-input"
            />
          </div>
        </div>
        <div v-if="playerId != null || mapId != null" class="table-container">
          <div class="table-responsive">
            <table class="table table-dark">
              <thead>
                <tr>
                  <th @click="setSortColumn('map')" class="sortable-header">
                    {{ playerId ? "Map" : "Player" }}
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'map'"
                      >{{ sortDirection === "desc" ? "↓" : "↑" }}</span
                    >
                  </th>
                  <th>Type</th>
                  <th>Class</th>
                  <th @click="setSortColumn('tier')" class="sortable-header">
                    T
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'tier'"
                      >{{ sortDirection === "desc" ? "↓" : "↑" }}</span
                    >
                  </th>
                  <th @click="setSortColumn('rating')" class="sortable-header">
                    R
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'rating'"
                      >{{ sortDirection === "desc" ? "↓" : "↑" }}</span
                    >
                  </th>
                  <th
                    @click="setSortColumn('duration')"
                    class="sortable-header"
                  >
                    Time
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'duration'"
                      >{{ sortDirection === "desc" ? "↓" : "↑" }}</span
                    >
                  </th>
                  <th @click="setSortColumn('rank')" class="sortable-header">
                    Rank
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'rank'"
                      >{{ sortDirection === "desc" ? "↓" : "↑" }}</span
                    >
                  </th>
                  <th
                    @click="setSortColumn('completion')"
                    class="sortable-header"
                  >
                    Completion
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'completion'"
                      >{{ sortDirection === "desc" ? "↓" : "↑" }}</span
                    >
                  </th>
                  <th
                    @click="setSortColumn('percentage')"
                    class="sortable-header"
                  >
                    Percentile
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'percentage'"
                      >{{ sortDirection === "desc" ? "↓" : "↑" }}</span
                    >
                  </th>
                  <th @click="setSortColumn('points')" class="sortable-header">
                    Points
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'points'"
                      >{{ sortDirection === "desc" ? "↓" : "↑" }}</span
                    >
                  </th>
                  <th @click="setSortColumn('time')" class="sortable-header">
                    Date
                    <span
                      class="sort-indicator"
                      v-if="sortByCategory === 'time'"
                      >{{ sortDirection === "desc" ? "↓" : "↑" }}</span
                    >
                  </th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="record in filteredRecords"
                  :key="record.id"
                  class="fade-in"
                >
                  <SmartLink
                    v-if="playerId"
                    tag="td"
                    :to="{ name: 'MapPage', params: { mapId: record.map_id } }"
                    class="fancy-hover"
                  >
                    <HoverPreview :map-name="record.map_name">{{
                      record.map_name
                    }}</HoverPreview>
                  </SmartLink>
                  <SmartLink
                    v-else-if="mapId"
                    tag="td"
                    :to="{
                      name: 'PlayerPage',
                      params: { playerId: record.player_id },
                    }"
                    class="fancy-hover"
                  >
                    {{ record.name || "Unknown Player" }}
                  </SmartLink>
                  <td>
                    {{ getRecordType(record.type) }}{{ formatIndex(record) }}
                  </td>
                  <td>
                    <img
                      :src="`/icons/${record.class}.png`"
                      :alt="`${record.class}`"
                      class="class-icon"
                    />
                  </td>
                  <td>T{{ record.tier }}</td>
                  <td>R{{ record.rating }}</td>
                  <td>
                    {{
                      record.duration !== null
                        ? formatDuration(record.duration)
                        : ""
                    }}
                  </td>
                  <td :class="getRankColorClass(record.placement)">
                    {{ record.rank !== null ? record.rank : "" }}
                    {{
                      record.placement !== null
                        ? formatPlacement(record.placement)
                        : ""
                    }}
                  </td>
                  <td>{{ record.completion_count }}</td>
                  <td>
                    {{
                      record.rank && record.completion_count
                        ? (
                            (record.rank / record.completion_count) *
                            100
                          ).toFixed(1) + "%"
                        : ""
                    }}
                  </td>
                  <td>{{ record.points !== null ? record.points : "" }}</td>
                  <td class="text-small">
                    {{
                      record.date !== null
                        ? formatDate(new Date(record.date * 1000))
                        : ""
                    }}
                  </td>
                  <td class="text-center">
                    {{ record.duration !== null ? "✓" : "X" }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="maps-footer">
            <button
              v-if="displayCount < filteredSortedItems.length"
              @click="showMore"
              class="btn btn-dark update-button show-more-btn"
            >
              Show more
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DOMPurify from "dompurify";
import { formatDuration } from "@/utils/calculations.js";
import { useHead } from "@vueuse/head";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "PlayerRecords",
  props: {
    playerId: { type: Number, default: null },
    mapId: { type: Number, default: null },
  },
  setup() {
    useHead({ title: "Lookup | Tempus Plaza" });
  },
  data: () => ({
    playerRankInfo: null,
    loadingRankInfo: false,
    playerAvatar: null,
    playerCountry: null,
    playerCountryCode: null,
    bannerColors: {
      color1: "rgba(74, 111, 165, 0.4)",
      color2: "rgba(74, 111, 165, 0.2)",
    },
    playerId: null,
    playerName: null,
    selectedPlayerName: null,
    records: [],
    loading: false,
    error: null,
    selectedClasses: [],
    selectedTypes: [],
    selectedStatus: ["completed"],
    selectedSoldierTiers: [],
    selectedSoldierRatings: [],
    selectedDemomanTiers: [],
    selectedDemomanRatings: [],
    availableTiers: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 0],
    availableRatings: [1, 2, 3, 4],
    selectedIntendedClasses: [],
    availableIntendedClasses: [
      { id: 3, label: "Soldier", icon: "/icons/soldier.png" },
      { id: 4, label: "Demoman", icon: "/icons/demoman.png" },
    ],
    availableGroups: [1, 2, 3, 4, 5],
    selectedGroups: [],
    sortByCategory: "time",
    sortDirection: "asc",
    recordSearchQuery: "",
    searchQuery: "",
    searchResults: null,
    mapId: null,
    mapInfo: null,
    selectedMapName: null,
    searchMode: "both",
    cachedMapRecords: {
      soldier_map: [],
      demoman_map: [],
      soldier_course: [],
      demoman_course: [],
      soldier_bonus: [],
      demoman_bonus: [],
    },
    showLoading: false,
    debounceTimer: null,
    cachedRecords: { records: [], courseRecords: [], bonusRecords: [] },
    validPlacements: [],
    displayCount: 300,
    // New: focus state tracker
    lookupSearchFocused: false,
  }),
  computed: {
    filteredSortedItems() {
      let recordsToFilter = [];

      if (this.selectedTypes.length === 0) {
        recordsToFilter = [
          ...this.cachedRecords.records,
          ...this.cachedRecords.courseRecords,
          ...this.cachedRecords.bonusRecords,
        ];
      } else {
        if (this.selectedTypes.includes("map"))
          recordsToFilter.push(...this.cachedRecords.records);
        if (this.selectedTypes.includes("course"))
          recordsToFilter.push(...this.cachedRecords.courseRecords);
        if (this.selectedTypes.includes("bonus"))
          recordsToFilter.push(...this.cachedRecords.bonusRecords);
      }

      const filtered = recordsToFilter.filter((record) => {
        if (
          this.selectedClasses.length > 0 &&
          !this.selectedClasses.includes(record.class)
        )
          return false;

        if (this.selectedStatus.length > 0) {
          const isComplete = record.duration !== null;
          if (isComplete && !this.selectedStatus.includes("completed"))
            return false;
          if (!isComplete && !this.selectedStatus.includes("incomplete"))
            return false;
        }

        if (record.class === "soldier") {
          if (
            this.selectedSoldierTiers.length > 0 &&
            !this.selectedSoldierTiers.includes(record.tier)
          )
            return false;
          if (
            this.selectedSoldierRatings.length > 0 &&
            !this.selectedSoldierRatings.includes(record.rating)
          )
            return false;
        } else if (record.class === "demoman") {
          if (
            this.selectedDemomanTiers.length > 0 &&
            !this.selectedDemomanTiers.includes(record.tier)
          )
            return false;
          if (
            this.selectedDemomanRatings.length > 0 &&
            !this.selectedDemomanRatings.includes(record.rating)
          )
            return false;
        }

        if (this.selectedGroups.length > 0) {
          let shouldInclude = false;
          if (
            this.selectedGroups.includes("BT") &&
            record.rank === record.completion_count
          )
            shouldInclude = true;
          if (
            this.validPlacements.length > 0 &&
            this.validPlacements.includes(record.placement)
          )
            shouldInclude = true;
          if (!shouldInclude) return false;
        }

        if (this.selectedIntendedClasses.length > 0) {
          if (
            this.selectedIntendedClasses.includes(3) &&
            this.selectedIntendedClasses.includes(4)
          ) {
            if (record.intended_class !== 5) return false;
          } else {
            if (
              !this.selectedIntendedClasses.includes(record.intended_class) &&
              record.intended_class !== 5
            )
              return false;
          }
        }

        if (this.recordSearchQuery) {
          const query = this.recordSearchQuery.toLowerCase();
          if (this.playerId && !record.map_name.toLowerCase().includes(query))
            return false;
          if (
            this.mapId &&
            (!record.name || !record.name.toLowerCase().includes(query))
          )
            return false;
        }
        return true;
      });

      return filtered.sort((a, b) => {
        let comparison = 0;
        switch (this.sortByCategory) {
          case "time":
            comparison = b.date - a.date;
            break;
          case "rank":
            comparison = a.rank - b.rank;
            break;
          case "points":
            comparison = b.points - a.points;
            break;
          case "percentage":
            comparison =
              a.rank / a.completion_count - b.rank / b.completion_count;
            break;
          case "duration":
            comparison = a.duration - b.duration;
            break;
          case "tier":
            comparison = a.tier - b.tier;
            break;
          case "rating":
            comparison = a.rating - b.rating;
            break;
          case "completion":
            comparison = a.completion_count - b.completion_count;
            break;
          case "map":
            comparison = a.map_name.localeCompare(b.map_name);
            break;
          default:
            comparison = b.date - a.date;
        }
        return this.sortDirection === "desc" ? -comparison : comparison;
      });
    },
    filteredRecords() {
      return this.filteredSortedItems.slice(0, this.displayCount);
    },
  },
  watch: {
    playerId(newPlayerId) {
      if (newPlayerId) {
        this.fetchRecords();
        this.fetchPlayerRankInfo(newPlayerId);
      }
    },
    mapId(newMapId) {
      if (newMapId) this.fetchMapRecords();
    },
    selectedGroups: {
      handler() {
        const placements = [];
        this.selectedGroups.forEach((group) => {
          if (group === "WR") placements.push(1);
          else if (group === "TT")
            placements.push(...[2, 3, 4, 5, 6, 7, 8, 9, 10]);
          else placements.push(10 + Number(group));
        });
        this.validPlacements = placements;
      },
      immediate: true,
    },
    $route(to, from) {
      if (to.params.playerId && to.params.playerId !== from.params.playerId) {
        this.playerId = to.params.playerId;
        this.mapId = null;
        this.selectedMapName = null;
        this.findPlayerName(this.playerId);
        this.fetchPlayerRankInfo(this.playerId);
        this.fetchRecords();
      } else if (to.params.mapId && to.params.mapId !== from.params.mapId) {
        this.mapId = to.params.mapId;
        this.playerId = null;
        this.selectedPlayerName = null;
        this.playerAvatar = null;
        this.playerCountry = null;
        this.playerCountryCode = null;
        this.playerRankInfo = null;
        this.findMapName(this.mapId);
        this.fetchMapRecords();
      }
    },
  },
  async mounted() {
    try {
      const response = await fetch(`${API_BASE_URL}/api/get-user`, {
        credentials: "include",
        method: "GET",
        headers: { "Content-Type": "application/json" },
      });
      if (response.ok) {
        const user = await response.json();
        this.playerId = user.data?.playerid || null;
        this.playerName = user.data?.name || null;
      } else {
        this.playerId = null;
        this.playerName = null;
      }
    } catch (error) {
      console.error("Error fetching user data:", error);
      this.playerId = null;
      this.playerName = null;
    }

    if (this.$route.params.playerId) {
      this.playerId = this.$route.params.playerId;
      this.mapId = null;
      this.selectedMapName = null;
      await this.findPlayerName(this.playerId);
      await this.fetchPlayerRankInfo(this.playerId);
      await this.fetchRecords();
    } else if (this.$route.params.mapId) {
      this.mapId = this.$route.params.mapId;
      this.playerId = null;
      this.selectedPlayerName = null;
      this.playerAvatar = null;
      this.playerCountry = null;
      this.playerCountryCode = null;
      await this.findMapName(this.mapId);
      await this.fetchMapRecords();
    } else if (
      this.playerId &&
      !this.$route.params.playerId &&
      !this.$route.params.mapId
    ) {
      this.selectedPlayerName = this.playerName;
      await this.fetchPlayerRankInfo(this.playerId);
      await this.fetchRecords();
      this.$router.push({
        name: "LookupPlayer",
        params: { playerId: this.playerId },
      });
    }
  },
  methods: {
    handleAvatarError(e) {
      e.target.style.display = "none";
      if (e.target.nextElementSibling)
        e.target.nextElementSibling.style.display = "flex";
    },
    downloadAsCSV() {
      const data = this.filteredSortedItems;
      if (data.length === 0) {
        alert("No data to export");
        return;
      }
      const headers = [
        this.playerId ? "Map" : "Player",
        "Type",
        "Class",
        "Tier",
        "Rating",
        "Time",
        "Rank",
        "Completion",
        "Percentile",
        "Points",
        "Date",
        "Status",
      ];
      const rows = data.map((record) => {
        const percentile =
          record.rank && record.completion_count
            ? ((record.rank / record.completion_count) * 100).toFixed(1) + "%"
            : "";
        const formattedDate =
          record.date !== null
            ? new Date(record.date * 1000).toISOString().split("T")[0]
            : "";
        let typeDisplay =
          record.type.charAt(0).toUpperCase() + record.type.slice(1);
        if (record.type !== "map" && record.index)
          typeDisplay += " " + record.index;
        return [
          this.playerId ? record.map_name : record.name || "Unknown",
          typeDisplay,
          record.class,
          record.tier,
          record.rating,
          record.duration !== null ? this.formatDuration(record.duration) : "",
          record.rank !== null ? record.rank : "",
          record.completion_count || "",
          percentile,
          record.points !== null ? record.points : "",
          formattedDate,
          record.duration !== null ? "Complete" : "Incomplete",
        ];
      });
      const escapeCsv = (val) => {
        const str = String(val);
        if (str.includes(",") || str.includes('"') || str.includes("\n"))
          return `"${str.replace(/"/g, '""')}"`;
        return str;
      };
      const csvContent = [
        headers.map(escapeCsv).join(","),
        ...rows.map((row) => row.map(escapeCsv).join(",")),
      ].join("\n");
      const BOM = "\uFEFF";
      const blob = new Blob([BOM + csvContent], {
        type: "text/csv;charset=utf-8;",
      });
      const link = document.createElement("a");
      const url = URL.createObjectURL(blob);
      const fileName = this.playerId
        ? `${this.selectedPlayerName}_records.csv`
        : `${this.selectedMapName}_records.csv`;
      link.href = url;
      link.download = fileName;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    },
    formatMapDate(unixTimestamp) {
      const date = new Date(unixTimestamp * 1000);
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const year = date.getFullYear();
      return `${month}/${year}`;
    },
    async fetchPlayerRankInfo(playerId) {
      this.loadingRankInfo = true;
      try {
        const response = await fetch(
          `${API_BASE_URL}/players/${playerId}/ranks`,
        );
        if (!response.ok) throw new Error("Failed to fetch rank info");
        const data = await response.json();
        this.playerRankInfo = data[0] || null;
      } catch (error) {
        console.error("Error fetching player rank info:", error);
        this.playerRankInfo = null;
      } finally {
        this.loadingRankInfo = false;
      }
    },
    async fetchMapRecords() {
      this.loading = true;
      this.error = null;
      const mapId = this.mapId;
      if (!mapId) {
        this.error = "Please select a map first.";
        this.loading = false;
        return;
      }
      try {
        const [recordsResponse, infoResponse] = await Promise.all([
          fetch(`${API_BASE_URL}/maps/${mapId}/all-records`),
          fetch(`${API_BASE_URL}/maps/${mapId}/all-info`),
        ]);
        if (!recordsResponse.ok) {
          if (recordsResponse.status === 404) throw new Error("Map not found");
          if (recordsResponse.status === 403) throw new Error("Access denied");
          throw new Error(
            `Failed to fetch records (${recordsResponse.status})`,
          );
        }
        if (!infoResponse.ok)
          throw new Error(`Failed to fetch map info (${infoResponse.status})`);
        const [mapRecords, mapInfoData] = await Promise.all([
          recordsResponse.json(),
          infoResponse.json(),
        ]);
        this.cachedMapRecords = mapRecords;
        this.mapInfo = mapInfoData;
        this.cachedRecords = {
          records: [
            ...(mapRecords.soldier_map || []).map((r) =>
              this.convertMapRecord(r, "soldier", "map"),
            ),
            ...(mapRecords.demoman_map || []).map((r) =>
              this.convertMapRecord(r, "demoman", "map"),
            ),
          ],
          courseRecords: [
            ...(mapRecords.soldier_course || []).map((r) =>
              this.convertMapRecord(r, "soldier", "course"),
            ),
            ...(mapRecords.demoman_course || []).map((r) =>
              this.convertMapRecord(r, "demoman", "course"),
            ),
          ],
          bonusRecords: [
            ...(mapRecords.soldier_bonus || []).map((r) =>
              this.convertMapRecord(r, "soldier", "bonus"),
            ),
            ...(mapRecords.demoman_bonus || []).map((r) =>
              this.convertMapRecord(r, "demoman", "bonus"),
            ),
          ],
        };
      } catch (error) {
        this.error = error.message || "Error fetching map records.";
        console.error("Error fetching map records:", error);
        this.cachedMapRecords = {
          soldier_map: [],
          demoman_map: [],
          soldier_course: [],
          demoman_course: [],
          soldier_bonus: [],
          demoman_bonus: [],
        };
        this.cachedRecords = {
          records: [],
          courseRecords: [],
          bonusRecords: [],
        };
        this.mapInfo = null;
      } finally {
        this.loading = false;
      }
    },
    convertMapRecord(record, className, type) {
      let tierInfo, ratingInfo, completionCount, intendedClass;
      if (this.mapInfo) {
        if (type === "map") {
          tierInfo =
            className === "soldier"
              ? this.mapInfo.map.soldier_tier
              : this.mapInfo.map.demoman_tier;
          ratingInfo =
            className === "soldier"
              ? this.mapInfo.map.soldier_rating
              : this.mapInfo.map.demoman_rating;
          completionCount =
            className === "soldier"
              ? this.mapInfo.map.soldier_completion_count
              : this.mapInfo.map.demoman_completion_count;
          intendedClass = this.mapInfo.map.intended_class;
        } else if (type === "course") {
          const course = this.mapInfo.courses?.find(
            (c) => c.index === record.index,
          );
          if (course) {
            tierInfo =
              className === "soldier"
                ? course.soldier_tier
                : course.demoman_tier;
            ratingInfo =
              className === "soldier"
                ? course.soldier_rating
                : course.demoman_rating;
            completionCount =
              className === "soldier"
                ? course.soldier_completion_count
                : course.demoman_completion_count;
          }
        } else if (type === "bonus") {
          const bonus = this.mapInfo.bonuses?.find(
            (b) => b.index === record.index,
          );
          if (bonus) {
            tierInfo =
              className === "soldier" ? bonus.soldier_tier : bonus.demoman_tier;
            ratingInfo =
              className === "soldier"
                ? bonus.soldier_rating
                : bonus.demoman_rating;
            completionCount =
              className === "soldier"
                ? bonus.soldier_completion_count
                : bonus.demoman_completion_count;
          }
        }
      }
      return {
        ...record,
        class: className,
        type,
        map_name: this.selectedMapName,
        map_id: this.mapId,
        tier: tierInfo || null,
        rating: ratingInfo || null,
        completion_count: completionCount || null,
        intended_class: intendedClass || null,
        name: record.name || null,
      };
    },
    async findMapName(mapId) {
      try {
        const response = await fetch(`${API_BASE_URL}/maps/${mapId}/all-info`);
        const data = await response.json();
        this.selectedMapName = data.map?.name || "Unknown Map";
        this.mapInfo = data;
      } catch (error) {
        console.error("Error fetching map name:", error);
        this.selectedMapName = "Unknown Map";
        this.mapInfo = null;
      }
    },
    setSortColumn(column) {
      if (this.sortByCategory === column) {
        this.sortDirection = this.sortDirection === "desc" ? "asc" : "desc";
      } else {
        this.sortByCategory = column;
        this.sortDirection = "asc";
      }
    },
    onFilterChange() {},
    toggleClass(classOption) {
      this.selectedClasses = this.selectedClasses.includes(classOption)
        ? this.selectedClasses.filter((c) => c !== classOption)
        : [...this.selectedClasses, classOption];
      this.onFilterChange();
    },
    toggleType(typeOption) {
      this.selectedTypes = this.selectedTypes.includes(typeOption)
        ? this.selectedTypes.filter((t) => t !== typeOption)
        : [...this.selectedTypes, typeOption];
      this.onFilterChange();
    },
    toggleStatus(statusOption) {
      this.selectedStatus = this.selectedStatus.includes(statusOption)
        ? this.selectedStatus.filter((s) => s !== statusOption)
        : [...this.selectedStatus, statusOption];
      this.onFilterChange();
    },
    toggleIntendedClass(clsId) {
      this.selectedIntendedClasses = this.selectedIntendedClasses.includes(
        clsId,
      )
        ? this.selectedIntendedClasses.filter((id) => id !== clsId)
        : [...this.selectedIntendedClasses, clsId];
      this.onFilterChange();
    },
    formatPlacement(placement) {
      if (placement <= 10) return "";
      if (placement <= 15) return "(G" + (placement - 10) + ")";
      return placement;
    },
    async findPlayerName(playerId) {
      const response = await fetch(`${API_BASE_URL}/players/${playerId}`);
      const data = await response.json();
      this.selectedPlayerName = data[0].name;
      this.playerAvatar = data[0].steam_avatar;
      this.playerCountry = data[0].country;
      this.playerCountryCode = data[0].country_code;
    },
    getFlagImageUrl(countryCode) {
      if (!countryCode) return "";
      return `https://flagcdn.com/w40/${countryCode.toLowerCase()}.png`;
    },
    formatIndex(record) {
      if (record.type === "map") return "";
      return `${record.type.charAt(0).toUpperCase()}${record.index}`;
    },
    getRecordType(type) {
      const first = type.slice(0, 1).toUpperCase();
      if (first === "M") return "🌍";
      if (first === "C") return "🚩";
      if (first === "B") return "⭐";
      return "";
    },
    sanitize(str) {
      return DOMPurify.sanitize(str || "");
    },
    showMore() {
      this.displayCount += 300;
    },
    getRankColorClass(placement) {
      if (placement === 1) return "placement-rank-gold";
      if (placement === 2) return "placement-rank-silver";
      if (placement === 3) return "placement-rank-bronze";
      if (placement >= 4 && placement <= 10) return "placement-rank-tt";
      if (placement === 11) return "placement-group-1";
      if (placement === 12) return "placement-group-2";
      if (placement === 13) return "placement-group-3";
      if (placement === 14) return "placement-group-4";
      if (placement === 15) return "placement-group-5";
      return "";
    },
    totalRecordsLength() {
      if (this.playerId || this.mapId) {
        return (
          this.cachedRecords.records.length +
          this.cachedRecords.courseRecords.length +
          this.cachedRecords.bonusRecords.length
        );
      }
      return 0;
    },
    formatDate(date) {
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const year = date.getFullYear();
      return `${year}/${month}/${day}`;
    },
    async onSearch() {
      if (this.searchQuery.trim()) {
        this.showLoading = true;
      } else {
        this.showLoading = false;
        this.searchResults = null;
        return;
      }
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(async () => {
        if (this.searchQuery.trim()) {
          try {
            const [playersResponse, mapsResponse] = await Promise.all([
              fetch(`${API_BASE_URL}/search/players`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ query: this.searchQuery }),
              }),
              fetch(`${API_BASE_URL}/search/maps`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ query: this.searchQuery }),
              }),
            ]);
            if (!playersResponse.ok || !mapsResponse.ok)
              throw new Error("Failed to search results");
            const [playersData, mapsData] = await Promise.all([
              playersResponse.json(),
              mapsResponse.json(),
            ]);
            const players = (playersData.players || []).slice(0, 10);
            const maps = (mapsData.maps || []).slice(0, 3);
            this.searchResults = { players, maps };
          } catch (error) {
            console.error("Error fetching search results:", error);
          } finally {
            this.showLoading = false;
          }
        } else {
          this.searchResults = null;
          this.showLoading = false;
        }
      }, 400);
    },
    selectPlayer(playerId) {
      this.playerId = playerId;
      this.mapId = null;
      this.selectedMapName = null;
      this.selectedPlayerName = this.searchResults.players.find(
        (p) => p.id === playerId,
      )?.name;
      this.searchQuery = "";
      this.searchResults = null;
      this.fetchPlayerRankInfo(playerId);
      this.$router.push({ name: "LookupPlayer", params: { playerId } });
    },
    selectMap(mapId, mapName) {
      this.mapId = mapId;
      this.playerId = null;
      this.selectedPlayerName = null;
      this.selectedMapName = mapName;
      this.searchQuery = "";
      this.searchResults = null;
      this.$router.push({ name: "LookupMap", params: { mapId } });
    },
    async fetchRecords() {
      this.loading = true;
      this.error = null;
      const playerId = this.playerId;
      if (!playerId) {
        this.error = "Please select a player first.";
        this.loading = false;
        return;
      }
      try {
        const response = await fetch(
          `${API_BASE_URL}/players/${playerId}/all-records`,
        );
        if (!response.ok) {
          if (response.status === 404) throw new Error("Player not found");
          if (response.status === 403) throw new Error("Access denied");
          throw new Error(`Failed to fetch records (${response.status})`);
        }
        const allRecords = await response.json();
        if (!Array.isArray(allRecords))
          throw new Error("Invalid data format received");
        this.cachedRecords = {
          records: allRecords.filter((r) => r.type === "map"),
          courseRecords: allRecords.filter((r) => r.type === "course"),
          bonusRecords: allRecords.filter((r) => r.type === "bonus"),
          allRecords,
        };
        this.records = allRecords;
      } catch (error) {
        this.error = error.message || "Error fetching records.";
        console.error("Error fetching records:", error);
        this.cachedRecords = {
          records: [],
          courseRecords: [],
          bonusRecords: [],
          allRecords: [],
        };
        this.records = [];
      } finally {
        this.loading = false;
      }
    },
    clearAllFilters() {
      this.selectedClasses = [];
      this.selectedTypes = [];
      this.selectedStatus = ["completed"];
      this.selectedSoldierTiers = [];
      this.selectedSoldierRatings = [];
      this.selectedDemomanTiers = [];
      this.selectedDemomanRatings = [];
      this.selectedIntendedClasses = [];
      this.selectedGroups = [];
      this.sortByCategory = "time";
      this.sortDirection = "desc";
      this.recordSearchQuery = "";
    },
    formatDuration(unixTimestamp) {
      return formatDuration(unixTimestamp);
    },
  },
  beforeDestroy() {
    clearTimeout(this.debounceTimer);
  },
};
</script>

<style scoped>
.search-section {
  width: 100%;
  max-width: 560px;
  margin-top: 20px;
  position: relative;
}

.lookup-search-container {
  position: relative;
  width: 100%;
}

.lookup-search-box {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 14px;
  transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3);
}
.lookup-search-box.is-focused,
.lookup-search-box:hover {
  border-color: rgba(102, 126, 234, 0.6);
  background: rgba(255, 255, 255, 0.09);
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15),
    0 4px 24px rgba(0, 0, 0, 0.35);
}

.lookup-search-icon-wrap {
  padding: 0 12px 0 16px;
  display: flex;
  align-items: center;
  flex-shrink: 0;
}
.lookup-search-icon-svg {
  color: rgba(255, 255, 255, 0.35);
}

.lookup-search-input {
  flex: 1;
  padding: 16px 0;
  background: transparent;
  border: none;
  color: #fff;
  font-size: 15px;
  outline: none;
  min-width: 0;
}
.lookup-search-input::placeholder {
  color: rgba(255, 255, 255, 0.35);
}

.lookup-search-clear {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0 14px;
  color: rgba(255, 255, 255, 0.35);
  display: flex;
  align-items: center;
  transition: color 0.15s;
  flex-shrink: 0;
}
.lookup-search-clear:hover {
  color: rgba(255, 255, 255, 0.75);
}

.lookup-search-results-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  min-width: 420px;
  background: rgba(18, 20, 30, 0.97);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6),
    0 0 0 1px rgba(255, 255, 255, 0.04);
  overflow: hidden;
  z-index: 1000;
}

.lookup-dropdown-enter-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.lookup-dropdown-leave-active {
  transition: opacity 0.1s ease, transform 0.1s ease;
}
.lookup-dropdown-enter-from,
.lookup-dropdown-leave-to {
  opacity: 0;
  transform: translateX(-50%) translateY(-6px);
}

.lookup-search-section {
  padding: 8px 0;
}

.lookup-section-label {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px 6px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.3);
}

.lookup-section-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.06);
  margin: 0 14px;
}

.lookup-search-section ul {
  list-style: none;
  margin: 0;
  padding: 4px 8px 8px;
}

.lookup-search-results-dropdown :deep(.hover-preview-wrapper) {
  display: block;
  width: 100%;
}

.lookup-result-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.15s;
  color: #fff;
}
.lookup-result-item:hover {
  background: rgba(102, 126, 234, 0.15);
}
.lookup-result-item:active {
  background: rgba(102, 126, 234, 0.25);
}

.lookup-player-avatar-wrapper {
  position: relative;
  flex-shrink: 0;
}
.lookup-player-avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  object-fit: cover;
  display: block;
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.lookup-player-avatar-fallback {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: linear-gradient(
    135deg,
    rgba(102, 126, 234, 0.5),
    rgba(154, 176, 255, 0.3)
  );
  border: 1px solid rgba(102, 126, 234, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.8);
}

.lookup-result-name {
  font-size: 18px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.88);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.lookup-loading-rows {
  padding: 4px 8px 8px;
}
.lookup-loading-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
}

.lookup-skeleton {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 0%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 100%
  );
  background-size: 200% 100%;
  animation: lookup-shimmer 1.5s infinite;
  border-radius: 6px;
  display: block;
}
.lookup-skeleton-avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  flex-shrink: 0;
}
.lookup-skeleton-text {
  flex: 1;
  height: 14px;
  max-width: 160px;
}

@keyframes lookup-shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

@media (max-width: 768px) {
  .lookup-search-results-dropdown {
    min-width: unset;
    width: 90vw;
    left: 50%;
    transform: translateX(-50%);
  }
  .lookup-dropdown-enter-from,
  .lookup-dropdown-leave-to {
    transform: translateX(-50%) translateY(-6px);
  }
}

.sortable-header {
  cursor: pointer;
  user-select: none;
  transition: background-color 0.2s ease;
  position: relative;
}
.sortable-header:hover {
  background-color: rgba(255, 255, 255, 0.1);
}
.sortable-header:active {
  background-color: rgba(255, 255, 255, 0.2);
}
.sort-indicator {
  margin-left: 5px;
  font-size: 12px;
  opacity: 0.8;
}

.clickable {
  cursor: pointer;
  color: var(--color-text-clickable) !important;
}
.text-small {
  font-size: 0.8rem;
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

.filter-section {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  width: fit-content;
  max-width: 100%;
}
.filter-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
}
.filter-columns {
  display: flex;
  gap: 48px;
  align-items: flex-start;
}
.filter-group {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.clear-filter {
  padding-left: 50px;
}
.filter-count {
  color: white;
  font-weight: bold;
  margin-top: 5px;
}
.download-button {
  color: white;
  font-weight: bold;
}
.filter-title {
  color: #ffffff;
  font-weight: bold;
  margin-bottom: 10px;
  text-align: center;
  text-transform: uppercase;
}

.class-filter-container,
.type-filter-container,
.status-filter-container {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
}

.class-checkbox,
.type-checkbox,
.status-checkbox {
  display: inline-block;
  margin: 4px;
  border: 2px solid var(--color-border-soft);
  color: white;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 6px;
  padding: 8px 16px;
  cursor: pointer;
  font-weight: bold;
  font-size: 13px;
  text-transform: capitalize;
  transition: all 0.3s ease;
  min-width: 80px;
  text-align: center;
}
.class-checkbox.selected,
.type-checkbox.selected,
.status-checkbox.selected {
  background: rgba(74, 111, 165, 0.5);
  border-color: var(--color-primary);
  color: white;
}
.class-checkbox:hover,
.type-checkbox:hover,
.status-checkbox:hover {
  background: rgba(74, 111, 165, 0.3);
  border-color: var(--color-primary);
}

.clear-filters-btn {
  margin-right: 10px;
}

.tier-filter-container,
.rating-filter-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
}
.tier-filters,
.rating-filters {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}
.tier-checkbox,
.rating-checkbox {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin: 0;
}
.tier-checkbox:hover,
.rating-checkbox:hover,
.group-checkbox:hover {
  border-radius: 4px;
  border-color: var(--color-border, #444);
  box-shadow: 0 0 0 1px var(--color-border, #444);
}
.tier-checkbox input,
.rating-checkbox input {
  display: none;
}
.tier-checkbox input:checked + .tier-badge,
.rating-checkbox input:checked + .rating-badge {
  border-color: var(--color-border, #444);
  box-shadow: 0 0 0 1px var(--color-border, #444);
}
.tier-badge,
.rating-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 12px;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  min-width: 28px;
  text-align: center;
}

.tier-badge.tier-0,
.tier-color.tier-0 {
  background: var(--color-dark);
  color: var(--color-text);
}
.tier-badge.tier-1,
.tier-color.tier-1 {
  background: #6ed0f681;
  color: var(--color-text);
}
.tier-badge.tier-2,
.tier-color.tier-2 {
  background: #56b3e977;
  color: var(--color-text);
}
.tier-badge.tier-3,
.tier-color.tier-3 {
  background: #45b8ad6e;
  color: var(--color-text);
}
.tier-badge.tier-4,
.tier-color.tier-4 {
  background: #65c18b75;
  color: var(--color-text);
}
.tier-badge.tier-5,
.tier-color.tier-5 {
  background: #a3d97775;
  color: var(--color-text);
}
.tier-badge.tier-6,
.tier-color.tier-6 {
  background: #f3e68370;
  color: var(--color-text);
}
.tier-badge.tier-7,
.tier-color.tier-7 {
  background: #f6c26767;
  color: var(--color-text);
}
.tier-badge.tier-8,
.tier-color.tier-8 {
  background: #f08d5b65;
  color: var(--color-text);
}
.tier-badge.tier-9,
.tier-color.tier-9 {
  background: #e6695e6b;
  color: var(--color-text);
}
.tier-badge.tier-10,
.tier-color.tier-10 {
  background: #d6454569;
  color: var(--color-text);
}
.rating-badge.rating-1,
.rating-color.rating-1 {
  background: #94c47d69;
  color: var(--color-text);
}
.rating-badge.rating-2,
.rating-color.rating-2 {
  background: #abd0996b;
  color: var(--color-text);
}
.rating-badge.rating-3,
.rating-color.rating-3 {
  background: #c3b29365;
  color: var(--color-text);
}
.rating-badge.rating-4,
.rating-color.rating-4 {
  background: #e0666669;
  color: var(--color-text);
}

.group-badge.group-wr {
  background: #ffd700 !important;
  color: var(--color-dark) !important;
}
.group-badge.group-tt {
  background: #30869b !important;
  color: var(--color-dark) !important;
}
.group-badge.group-bt {
  background: #383838 !important;
  color: var(--color-text) !important;
}
.group-badge.group-1 {
  background: #ff9797af !important;
  color: var(--color-dark) !important;
}
.group-badge.group-2 {
  background: #f7cf84 !important;
  color: var(--color-dark) !important;
}
.group-badge.group-3 {
  background: #d27d2dbb !important;
  color: var(--color-dark) !important;
}
.group-badge.group-4 {
  background: #b3b3b3ce !important;
  color: var(--color-dark) !important;
}
.group-badge.group-5 {
  background: #e4e4e4ce !important;
  color: var(--color-dark) !important;
}

.placement-rank-gold {
  color: #ffd700 !important;
}
.placement-rank-silver {
  color: #c0c0c0 !important;
}
.placement-rank-bronze {
  color: #cd7f32 !important;
}
.placement-rank-tt {
  color: #30869b !important;
}
.placement-group-1 {
  color: #ff9797af !important;
}
.placement-group-2 {
  color: #f7cf84 !important;
}
.placement-group-3 {
  color: #d27d2dbb !important;
}
.placement-group-4 {
  color: #b3b3b3ce !important;
}
.placement-group-5 {
  color: #e4e4e4ce !important;
}

.group-filter-container,
.rating-filter-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
}
.group-filters,
.rating-filters {
  display: flex;
  gap: 8px;
  flex-wrap: nowrap;
  justify-content: center;
}
.group-checkbox,
.rating-checkbox {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin: 0;
}
.group-checkbox input,
.rating-checkbox input {
  display: none;
}
.group-checkbox input:checked + .group-badge,
.rating-checkbox input:checked + .rating-badge {
  border-color: var(--color-border, #444);
  box-shadow: 0 0 0 1px var(--color-border, #444);
}
.group-badge,
.rating-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 12px;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  min-width: 28px;
  text-align: center;
}

.filter-actions {
  display: flex;
  align-items: center;
  gap: 20px;
}
.btn-secondary {
  border: 1px solid var(--color-dark);
  font-weight: bold;
}
.btn-secondary:hover {
  border: 1px solid var(--color-dark);
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
}
.records-container {
  width: 100%;
}
.search-records-container {
  width: 100%;
  max-width: 500px;
  margin: 0 auto 25px auto;
  display: flex;
  justify-content: center;
}
.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  width: 100%;
}
.search-icon {
  position: absolute;
  left: 16px;
  color: #888;
  z-index: 2;
}
.search-records-input {
  width: 100%;
  padding: 12px 12px 12px 50px;
  background: rgba(255, 255, 255, 0.05);
  border: 2px solid rgba(68, 68, 68, 0.3);
  border-radius: 8px;
  color: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
}
.search-records-input:focus {
  outline: none;
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  box-shadow: 0 0 0 3px rgba(74, 158, 255, 0.1);
}
.search-records-input::placeholder {
  color: #888;
}

.table-responsive {
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  border: 1px solid var(--color-border-soft);
  border-radius: 10px 10px 0 0;
  overflow: hidden;
  margin-bottom: 0;
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
.table-dark {
  margin: 0;
}
.table-dark thead {
  border-bottom: 1px solid var(--color-border-soft) !important;
}
.table-dark th {
  color: var(--color-text);
  background: rgba(74, 111, 165, 0.3) !important;
  text-align: center;
  font-weight: bold;
  padding: 16px 12px;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  vertical-align: middle;
}
.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  font-weight: 500;
  padding: 6px 12px;
  text-align: center;
  vertical-align: middle;
}
.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}
.spinner-border {
  color: white;
  font-weight: bold;
  width: 3rem;
  height: 3rem;
}
.alert-danger {
  background: rgba(220, 53, 69, 0.1);
  border: 1px solid rgba(220, 53, 69, 0.3);
  border-radius: 8px;
  color: #ff6b6b;
}

.show-more-btn {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
  font-weight: bold;
  width: 100%;
  border-radius: 0 0 10px 10px;
}
.show-more-btn:hover {
  background-color: var(--color-row) !important;
}
.class-icon {
  width: 25px;
  height: 25px;
  margin: 8px;
}

.intended-class-buttons {
  display: flex;
  gap: 15px;
  justify-content: center;
}
.intended-class-btn {
  background: none;
  border: 1px solid transparent;
  border-radius: 4px;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
}
.intended-class-btn img {
  width: 28px;
  height: 28px;
  display: block;
}
.intended-class-btn.active {
  background: rgba(165, 165, 165, 0.5);
  border-color: var(--color-border);
}
.intended-class-btn:hover {
  background: rgba(165, 165, 165, 0.3);
  border-color: var(--color-border);
}

.lookup-player-banner {
  width: 100%;
  max-width: 1000px;
  margin: 20px auto;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
}
.lookup-banner-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 32px;
  gap: 24px;
  transition: all 0.3s ease;
}
.lookup-banner-content:hover {
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
}
.lookup-banner-left {
  display: flex;
  align-items: center;
  gap: 20px;
  min-width: 0;
}
.lookup-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 3px solid var(--color-primary);
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
.lookup-player-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}
.lookup-player-name {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0;
  color: #ffffff;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.lookup-country {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.8);
  margin: 0;
}
.lookup-flag-icon {
  width: 24px;
  height: auto;
  border-radius: 2px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
.lookup-banner-stats {
  display: flex;
  gap: 16px;
  flex-shrink: 0;
}
.lookup-stat-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 12px;
  padding: 12px 20px;
  min-width: 120px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.lookup-stat-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 600;
  margin-bottom: 4px;
}
.lookup-stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #ffffff;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}
.lookup-stat-points {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
  margin-top: 2px;
}

.lookup-map-banner {
  width: 100%;
  max-width: 1000px;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  position: relative;
}
.lookup-map-banner::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.2),
    rgba(74, 111, 165, 0.1)
  );
  pointer-events: none;
}
.lookup-map-banner .lookup-banner-content {
  display: flex;
  flex-direction: column;
  padding: 24px 32px;
  gap: 12px;
  transition: all 0.3s ease;
}
.lookup-map-banner .lookup-banner-content:hover {
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
}
.lookup-map-main {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.lookup-map-name {
  font-size: 2.25rem;
  font-weight: 700;
  margin: 0;
  color: #ffffff;
  text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.8);
  text-align: center;
}
.map-primary-info {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 24px;
  flex-wrap: wrap;
}
.map-tiers {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 1.25rem;
  font-weight: 700;
  color: #ffffff;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
}
.tier-group {
  display: flex;
  align-items: center;
  gap: 8px;
}
.tier-class-icon {
  width: 28px;
  height: 28px;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
}
.tier-text,
.rating-text {
  font-size: 1.25rem;
  letter-spacing: 0.5px;
}
.tier-divider {
  color: rgba(255, 255, 255, 0.5);
  font-size: 1rem;
}
.intended-class-display {
  display: flex;
  align-items: center;
  border-radius: 50%;
  padding: 8px;
  background: rgba(0, 0, 0, 0.3);
  border: 2px solid rgba(74, 111, 165, 0.3);
}
.intended-class-display.no-circle {
  background: none;
  border: none;
  padding: 0;
}
.intended-class-icon {
  width: 24px;
  height: 24px;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.5));
}
.both-classes {
  display: flex;
  align-items: center;
  gap: 4px;
}
.class-circle {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  padding: 8px;
  background: rgba(0, 0, 0, 0.3);
  border: 2px solid rgba(74, 111, 165, 0.3);
  background: #000000b3;
}
.lookup-map-secondary {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
  padding-top: 8px;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}
.secondary-stat {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
  font-weight: 500;
}
.stat-separator {
  color: rgba(255, 255, 255, 0.4);
  font-size: 0.75rem;
}

@media (max-width: 767.98px) {
  .clear-filter {
    padding: none;
    margin: none;
    margin-top: 10px;
    padding-left: 0px;
  }
  .container {
    padding-left: 15px;
    padding-right: 15px;
    max-width: 100%;
    overflow-x: hidden;
  }
  .filter-section {
    padding: 15px;
    width: 100%;
    margin: 0;
  }
  .filter-columns {
    flex-direction: column;
    gap: 16px;
    width: 100%;
  }
  .filter-group {
    width: 100%;
  }
  .tier-filters,
  .rating-filters,
  .group-filters {
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
  }
  .search-section {
    width: 100%;
    max-width: 100%;
    margin: 20px 0;
    padding: 0;
    display: flex;
    justify-content: center;
  }
  .lookup-search-container {
    max-width: 100%;
  }
  .search-records-container {
    width: 100%;
    max-width: 100%;
    margin: 10px 0;
    padding: 0;
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
  }
  .search-records-container .search-input-wrapper {
    width: 100%;
    max-width: 500px;
  }
  .search-records-input {
    width: 100%;
    padding: 12px 12px 12px 50px;
    box-sizing: border-box;
  }
  .table-responsive {
    width: 100%;
    overflow-x: auto;
  }
  .table-dark td {
    white-space: nowrap;
  }
  .maps-footer {
    width: 100%;
    padding: 0;
  }
  .filter-actions {
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }
  .btn-secondary {
    width: 100%;
  }
  .responsive-ratings-row {
    flex-direction: column !important;
    gap: 20px !important;
    align-items: center !important;
  }
  .responsive-ratings-row .filter-column {
    width: 100%;
    max-width: 300px;
  }
  .intended-class-btn {
    width: 28px;
    height: 28px;
  }
  .intended-class-btn img {
    width: 24px;
    height: 24px;
  }
  .table-container {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  .table {
    width: max-content;
    min-width: 100%;
  }
  .lookup-banner-content {
    flex-direction: column;
    text-align: center;
    padding: 24px 20px;
  }
  .lookup-banner-left {
    flex-direction: row;
    align-items: center;
    justify-content: center;
    width: 100%;
    gap: 16px;
  }
  .lookup-player-info {
    align-items: flex-start;
  }
  .lookup-player-name {
    font-size: 1.5rem;
  }
  .lookup-banner-stats {
    width: 100%;
    justify-content: center;
    flex-wrap: wrap;
  }
  .lookup-stat-card {
    min-width: 120px;
    padding: 10px 16px;
  }
  .lookup-stat-value {
    font-size: 1.25rem;
  }
  .lookup-map-banner .lookup-banner-content {
    padding: 20px 16px;
  }
  .lookup-map-name {
    font-size: 1.5rem;
  }
  .map-tiers {
    font-size: 1rem;
  }
  .tier-class-icon {
    width: 24px;
    height: 24px;
  }
  .tier-text,
  .rating-text {
    font-size: 1rem;
  }
  .lookup-map-secondary {
    font-size: 0.8rem;
    gap: 8px;
  }
  .secondary-stat {
    font-size: 0.75rem;
  }
}

@media (max-width: 1199.98px) {
  .table-dark th {
    padding: 12px 6px;
    font-size: 12px;
    letter-spacing: 0;
  }
  .table-dark td {
    padding: 5px 6px;
    font-size: 13px;
  }
  .class-icon {
    width: 16px;
    height: 16px;
    margin: 4px;
  }
}
@media (max-width: 1075px) {
  .table-container {
    width: 100%;
    overflow-x: auto;
  }
  .table-dark th {
    padding: 10px 5px;
    font-size: 11px;
  }
  .table-dark td {
    font-size: 12px;
    white-space: nowrap;
  }
  .lookup-banner-stats {
    gap: 8px;
  }
  .lookup-stat-card {
    min-width: 110px;
    padding: 10px 12px;
  }
  .lookup-stat-value {
    font-size: 1.25rem;
  }
}
</style>
