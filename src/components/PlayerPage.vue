<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container py-4 d-flex" style="margin-bottom: 10rem !important">
      <div
        v-if="playerNotFound"
        class="alert alert-warning player-not-found text-center"
      >
        <p class="mb-3">No player found</p>
        <button @click="returnToAllPlayers" class="btn return-button">
          Return to all players
        </button>
      </div>
      <div v-else>
        <div
          class="profile-banner mb-4"
          :class="{ 'golden-border': player.donator }"
          :style="{
            background: `linear-gradient(135deg, ${bannerColors.color1}, ${bannerColors.color2})`,
          }"
        >
          <div v-if="loading.ranks" class="text-center">
            <div class="spinner-border text-light" role="status">
              <span class="visually-hidden">Loading ranks...</span>
            </div>
          </div>
          <div
            class="row g-0"
            style="height: 100%; display: flex; align-items: center"
          >
            <div
              class="col-md-4 d-flex flex-column align-items-center profile-left p-4"
              style="height: 100%"
            >
              <div v-if="player.donator" class="donator-badge">
                <span class="badge-text">Donator</span>
              </div>
              <a
                :href="
                  player.steamidconverted && player.steamidconverted !== '#'
                    ? `https://steamcommunity.com/profiles/${player.steamidconverted}`
                    : '#'
                "
                :class="{
                  'pointer-events-none':
                    !player.steamidconverted || player.steamidconverted === '#',
                }"
                target="_blank"
                rel="noopener noreferrer"
              >
                <img
                  :src="`${player.steam_avatar}`"
                  alt="Avatar"
                  class="rounded-circle avatar mb-3"
                  :class="{ 'golden-avatar': player.donator }"
                  onerror="this.src='/avatars/golly.jpg'"
                />
              </a>
              <div v-if="player.donator" class="donator-badge">
                <span class="badge-text">Donator</span>
              </div>
              <div class="profile-info text-center">
                <h1 v-if="player.name" class="player-name">
                  {{ player.name }}
                </h1>
                <p class="rank-name mb-2">
                  <span :class="playerRankInfo.color">
                    {{ playerRankInfo.title }}
                  </span>
                </p>
                <p
                  class="country mb-3"
                  style="font-weight: bold; color: #d5d5d5"
                >
                  <img
                    :src="getFlagImageUrl(player.country_code)"
                    alt="flag"
                    class="flag-icon"
                    @error="handleImageError"
                  />
                  {{ player.country }} ({{ player.country_code }})
                </p>
              </div>
            </div>
            <div class="col-md-8 d-flex align-items-center profile-right">
              <div class="row p-3 profile-overview">
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Overall rank</h3>
                      <p class="card-text player-stats">
                        #{{ player.overall_rank }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Soldier rank</h3>
                      <p class="card-text player-stats">
                        #{{ player.soldier_rank }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Demoman rank</h3>
                      <p class="card-text player-stats">
                        #{{ player.demoman_rank }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Overall points</h3>
                      <p class="card-text player-stats">
                        {{ player.overall_points }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Soldier points</h3>
                      <p class="card-text player-stats">
                        {{ player.soldier_points }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="card banner-block h-100">
                    <div class="rank-card-body text-center">
                      <h3 class="card-title">Demoman points</h3>
                      <p class="card-text player-stats">
                        {{ player.demoman_points }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="card chart-container">
              <div class="chart-header">
                <p class="section-header">Soldier Points History</p>
              </div>
              <div class="chart-body">
                <div v-if="loading.points" class="text-center">
                  <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading chart...</span>
                  </div>
                </div>
                <apexchart
                  v-else
                  type="line"
                  height="250"
                  :options="soldierChartOptions"
                  :series="soldierChartSeries"
                />
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card chart-container">
              <div class="chart-header">
                <p class="section-header">Overall Points History</p>
              </div>
              <div class="chart-body">
                <div v-if="loading.points" class="text-center">
                  <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading chart...</span>
                  </div>
                </div>
                <apexchart
                  v-else
                  type="line"
                  height="250"
                  :options="overallChartOptions"
                  :series="overallChartSeries"
                />
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card chart-container">
              <div class="chart-header">
                <p class="section-header">Demoman Points History</p>
              </div>
              <div class="chart-body">
                <div v-if="loading.points" class="text-center">
                  <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading chart...</span>
                  </div>
                </div>
                <apexchart
                  v-else
                  type="line"
                  height="250"
                  :options="demomanChartOptions"
                  :series="demomanChartSeries"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="row main-content-wrapper">
          <div class="col-md-3 stats-boxes">
            <div class="stats-container">
              <div class="class-section">
                <div class="card stat-block mb-3">
                  <div
                    class="nav-bar left-nav-bar"
                    @click="prevStatType('soldier')"
                  ></div>
                  <div class="card-header">
                    <p class="section-header">Soldier Stats</p>
                    <p class="stat-type-label">{{ currentStatType.soldier }}</p>
                  </div>
                  <div class="card-body">
                    <div v-if="loading.stats" class="text-center">
                      <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                    <div v-else>
                      <div class="row card-row">
                        <div class="col-md-12 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h4 class="card-title">Completion</h4>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["soldier"]].completion
                                  .soldier
                              }}%
                            </p>
                          </div>
                        </div>
                        <div class="col-md-6 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h6 class="card-title">World records</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["soldier"]]
                                  .worldRecordAmount.soldier
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-6 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h6 class="card-title">Top times</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["soldier"]].topTimesAmount
                                  .soldier
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G1</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["soldier"]].placement
                                  .soldier.G1
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G2</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["soldier"]].placement
                                  .soldier.G2
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G3</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["soldier"]].placement
                                  .soldier.G3
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G4</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["soldier"]].placement
                                  .soldier.G4
                              }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="nav-bar right-nav-bar"
                    @click="nextStatType('soldier')"
                  ></div>
                </div>
              </div>
              <div
                v-if="
                  stats.map.placement.soldier.G1 > 0 ||
                  stats.map.topTimesAmount.soldier > 0
                "
                class="class-section"
              >
                <div class="card stat-block mb-3">
                  <div class="card-header">
                    <p class="section-header">
                      {{ player.shared_soldier_type }} (maps)
                    </p>
                  </div>
                  <div class="card-body">
                    <div class="row shared-card-row">
                      <div v-if="loading.shared" class="text-center">
                        <div class="spinner-border text-light" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                      </div>
                      <div v-else class="shared-body">
                        <div
                          class="d-flex align-items-center justify-content-between px-2 mb-2"
                          style="
                            background-color: transparent;
                            color: #aaa;
                            font-size: 0.85rem;
                            font-weight: 600;
                          "
                        >
                          <div class="d-flex align-items-center">
                            <i class="bi bi-people-fill me-2"></i>
                            <span>Player</span>
                          </div>
                          <div class="text-end">Count</div>
                        </div>
                        <SmartLink
                          v-for="(sharedPlayer, index) in sharedTimesSoldier"
                          :key="index"
                          :to="{
                            name: 'PlayerPage',
                            params: { playerId: sharedPlayer.playerId },
                          }"
                          tag="div"
                          class="d-flex align-items-center justify-content-between px-2 py-2 mb-2 shared-row"
                        >
                          <div
                            class="d-flex align-items-center shared-row-name"
                          >
                            <img
                              :src="sharedPlayer.avatar"
                              alt="Avatar"
                              class="rounded me-2 shared-avatar"
                            />
                            <span
                              class="fw-semibold"
                              style="font-size: 0.95rem"
                            >
                              {{ sharedPlayer.playerName }}
                            </span>
                          </div>
                          <div
                            class="text-end fw-bold shared-count"
                            style="min-width: 24px"
                          >
                            {{ sharedPlayer.count }}
                          </div>
                        </SmartLink>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 tabs-container">
            <div class="records-card">
              <div
                class="card-header tabs-header"
                style="background: rgba(255, 255, 255, 0.05)"
              >
                <div class="button-group">
                  <button
                    @click="switchRecordsTab('recent-runs')"
                    class="toggle-btn btn btn-dark update-button"
                    :class="{ active: activeRecordsTab === 'recent-runs' }"
                  >
                    Recent Runs
                  </button>
                  <button
                    @click="switchRecordsTab('changed-placements')"
                    class="toggle-btn btn btn-dark update-button"
                    :class="{
                      active: activeRecordsTab === 'changed-placements',
                    }"
                  >
                    Point Changes
                  </button>
                </div>
                <div
                  class="d-flex justify-content-between align-items-center button-container-wrapper mt-3"
                >
                  <div class="allruns-button-container">
                    <SmartLink
                      :to="{ name: 'Lookup', params: { playerId: player.id } }"
                      tag="button"
                      class="btn 'btn-dark' global-btn"
                      :style="{
                        color: 'var(--color-text)',
                        'font-weight': 'bold',
                      }"
                    >
                      All Runs
                    </SmartLink>
                  </div>
                  <div class="filter-button-container">
                    <button
                      @click="toggleFilterSection"
                      class="btn 'btn-dark' global-btn"
                      :style="{
                        color: 'var(--color-text)',
                        'font-weight': 'bold',
                      }"
                    >
                      <i class="bi bi-filter"></i> Filters
                      <i class="bi bi-chevron-down"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div
                v-if="showFilterSection"
                class="filter-section"
                style="background: rgba(255, 255, 255, 0.05)"
              >
                <div class="filter-content">
                  <div class="filter-columns">
                    <div class="filter-group">
                      <h6 class="filter-title text-light mb-2">Class</h6>
                      <div class="class-filter-container">
                        <button
                          @click="toggleClassFilter('soldier')"
                          :class="{
                            active:
                              filterOptions.selectedClasses.includes('soldier'),
                          }"
                          class="global-btn"
                        >
                          Soldier
                        </button>
                        <button
                          @click="toggleClassFilter('demoman')"
                          :class="{
                            active:
                              filterOptions.selectedClasses.includes('demoman'),
                          }"
                          class="global-btn"
                        >
                          Demoman
                        </button>
                      </div>
                    </div>

                    <div class="filter-group">
                      <h6 class="filter-title text-light mb-2">Type</h6>
                      <div class="type-filter-container">
                        <button
                          @click="toggleTypeFilter('map')"
                          :class="{
                            active: filterOptions.selectedTypes.includes('map'),
                          }"
                          class="global-btn"
                        >
                          Map
                        </button>
                        <button
                          @click="toggleTypeFilter('course')"
                          :class="{
                            active:
                              filterOptions.selectedTypes.includes('course'),
                          }"
                          class="global-btn"
                        >
                          Course
                        </button>
                        <button
                          @click="toggleTypeFilter('bonus')"
                          :class="{
                            active:
                              filterOptions.selectedTypes.includes('bonus'),
                          }"
                          class="global-btn"
                        >
                          Bonus
                        </button>
                      </div>
                    </div>
                  </div>
                  <div
                    class="filter-group"
                    style="margin-bottom: 20px; margin-top: 10px"
                  >
                    <h6 class="filter-title text-light mb-2">Placement</h6>
                    <div class="group-filter-container">
                      <div class="group-filters">
                        <button
                          @click="togglePlacementFilter(1)"
                          :class="{
                            active:
                              filterOptions.selectedPlacements.includes(1),
                          }"
                          class="filter-button group-badge group-wr"
                        >
                          WR
                        </button>
                        <button
                          @click="togglePlacementFilter(2)"
                          :class="{
                            active:
                              filterOptions.selectedPlacements.includes(2),
                          }"
                          class="filter-button group-badge group-tt"
                        >
                          TT
                        </button>
                        <button
                          v-for="group in [11, 12, 13, 14, 15]"
                          :key="'group-' + group"
                          @click="togglePlacementFilter(group)"
                          class="filter-button group-badge"
                          :class="{
                            active:
                              filterOptions.selectedPlacements.includes(group),
                            'group-1': group - 10 === 1,
                            'group-2': group - 10 === 2,
                            'group-3': group - 10 === 3,
                            'group-4': group - 10 === 4,
                            'group-5': group - 10 === 5,
                          }"
                        >
                          G{{ group - 10 }}
                        </button>
                      </div>
                    </div>
                  </div>
                  <div
                    class="filter-columns"
                    :class="{
                      'no-padding-bottom':
                        activeRecordsTab !== 'changed-placements',
                    }"
                  >
                    <div
                      v-if="activeRecordsTab === 'changed-placements'"
                      class="filter-group"
                    >
                      <h6 class="filter-title text-light mb-2">Change</h6>
                      <div class="gain-loss-filter-container">
                        <button
                          @click="toggleGainLossFilter('lost')"
                          :class="{
                            active:
                              filterOptions.selectedGainLoss.includes('lost'),
                          }"
                          class="global-btn"
                        >
                          Gained
                        </button>
                        <button
                          @click="toggleGainLossFilter('gained')"
                          :class="{
                            active:
                              filterOptions.selectedGainLoss.includes('gained'),
                          }"
                          class="global-btn"
                        >
                          Lost
                        </button>
                      </div>
                    </div>
                    <div
                      class="filter-actions"
                      :class="{
                        'no-margin-top':
                          activeRecordsTab !== 'changed-placements',
                      }"
                    >
                      <button
                        type="button"
                        @click="clearAllFilters"
                        class="btn btn-secondary"
                      >
                        Clear filters
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="card-body tabs-content"
                style="background: rgba(255, 255, 255, 0.05)"
              >
                <div class="records-section">
                  <div v-if="currentLoadingState" class="text-center">
                    <div class="spinner-border text-light" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </div>
                  <div v-else>
                    <div v-if="activeRecordsTab === 'recent-runs'">
                      <div
                        v-for="(group, date) in filteredAndPaginatedRecords"
                        :key="date"
                        class="date-group fade-in"
                      >
                        <div class="date-header">
                          <h5>{{ precomputedHeaders[date] }}</h5>
                        </div>
                        <ul class="list-group">
                          <SmartLink
                            v-for="record in group"
                            :key="record.id"
                            :to="{
                              name: 'MapPage',
                              params: { mapId: record.map_id },
                            }"
                            tag="li"
                            class="list-group-item record-item"
                            style="background: rgba(255, 255, 255, 0.05)"
                          >
                            <div
                              class="d-flex align-items-center record-class-map"
                            >
                              <img
                                :src="`/icons/${record.class}.png`"
                                :alt="record.class"
                                class="class-icon"
                                loading="lazy"
                              />
                              <span class="ms-2 record-map">
                                {{ record.map_name }}
                                <span v-if="record.type !== 'map'">
                                  |
                                  <template v-if="record.type === 'course'"
                                    >🚩</template
                                  >
                                  <template v-if="record.type === 'bonus'"
                                    >⭐</template
                                  >
                                  {{ record.type.slice(0, 1).toUpperCase()
                                  }}{{ record.index }}
                                </span>
                              </span>
                            </div>
                            <div
                              class="text-end align-items-center record-time-detail"
                            >
                              <div class="d-flex flex-column align-items-end">
                                <div class="d-flex align-items-center gap-2">
                                  <span
                                    class="record-detail record-duration"
                                    :class="record.rankClass"
                                  >
                                    {{ record.durationFormatted }}
                                  </span>
                                  <span
                                    class="record-rank"
                                    :class="record.placementClass"
                                  >
                                    {{ record.medal }} #{{ record.rank }}
                                  </span>
                                </div>

                                <span class="record-detail record-date">
                                  {{ record.formattedDate }}
                                </span>
                              </div>
                            </div>
                          </SmartLink>
                        </ul>
                      </div>
                    </div>
                    <div v-else-if="activeRecordsTab === 'changed-placements'">
                      <div
                        v-for="(group, date) in processedChangedPlacements"
                        :key="date"
                        class="date-group fade-in"
                      >
                        <div class="date-header">
                          <h5>{{ precomputedHeaders[date] }}</h5>
                        </div>
                        <ul class="list-group">
                          <SmartLink
                            v-for="placement in group"
                            :key="placement.id"
                            :to="{
                              name: 'MapPage',
                              params: { mapId: placement.map_id },
                            }"
                            tag="li"
                            class="list-group-item record-item"
                            :class="placement.placementClass"
                            style="background: rgba(255, 255, 255, 0.05)"
                          >
                            <div
                              class="d-flex align-items-center record-class-map"
                            >
                              <img
                                :src="`/icons/${placement.class}.png`"
                                :alt="placement.class"
                                class="class-icon"
                                loading="lazy"
                              />
                              <span class="ms-2 record-map">
                                {{ placement.map_name }}
                                <span v-if="placement.type !== 'map'">
                                  |
                                  <template v-if="placement.type === 'course'"
                                    >🚩</template
                                  >
                                  <template v-if="placement.type === 'bonus'"
                                    >⭐</template
                                  >
                                  {{ placement.type.slice(0, 1).toUpperCase()
                                  }}{{ placement.index }}
                                </span>
                              </span>
                            </div>
                            <div
                              class="text-end align-items-center record-time-detail"
                              :class="placement.placementClass"
                            >
                              <div class="d-flex flex-column align-items-end">
                                <div class="placement-change-indicator">
                                  <span class="placement-arrows">
                                    <span
                                      class="old-rank"
                                      :class="placement.oldRankClass"
                                    >
                                      {{ placement.oldRankDisplay }}
                                    </span>
                                    <i
                                      class="bi bi-arrow-right placement-arrow"
                                    ></i>
                                    <span
                                      class="new-rank"
                                      :class="placement.newRankClass"
                                    >
                                      {{ placement.newRankDisplay }}
                                    </span>
                                  </span>

                                  <span
                                    class="points-change"
                                    :class="placement.pointsChangeClass"
                                  >
                                    {{ placement.pointsChangeText }}
                                  </span>
                                </div>

                                <span class="record-detail record-date">
                                  {{ placement.formattedDate }}
                                </span>
                              </div>
                            </div>
                          </SmartLink>
                        </ul>
                      </div>
                    </div>
                    <div class="pagination-controls">
                      <div class="pagination-side">
                        <button
                          v-if="currentPage > 1"
                          @click="prevPage"
                          class="btn btn-dark global-btn"
                        >
                          Previous
                        </button>
                      </div>

                      <div
                        class="pagination-info"
                        v-if="activeRecordsTab === 'changed-placements'"
                      >
                        Next update in {{ nextUpdateCountdown }}
                      </div>

                      <div class="pagination-side">
                        <button
                          v-if="shouldShowNextButton"
                          @click="nextPage"
                          class="btn btn-dark global-btn"
                        >
                          Next
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 stats-boxes">
            <div class="stats-container">
              <div class="class-section">
                <div class="card stat-block mb-3">
                  <div
                    class="nav-bar left-nav-bar"
                    @click="prevStatType('demoman')"
                  ></div>
                  <div class="card-header">
                    <p class="section-header">Demoman Stats</p>
                    <p class="stat-type-label">{{ currentStatType.demoman }}</p>
                  </div>
                  <div class="card-body">
                    <div v-if="loading.stats" class="text-center">
                      <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                    <div v-else>
                      <div class="row card-row">
                        <div class="col-md-12 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h4 class="card-title">Completion</h4>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["demoman"]].completion
                                  .demoman
                              }}%
                            </p>
                          </div>
                        </div>
                        <div class="col-md-6 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h6 class="card-title">World records</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["demoman"]]
                                  .worldRecordAmount.demoman
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-6 text-center mb-3 p-1">
                          <div class="stat-item">
                            <h6 class="card-title">Top times</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["demoman"]].topTimesAmount
                                  .demoman
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G1</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["demoman"]].placement
                                  .demoman.G1
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G2</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["demoman"]].placement
                                  .demoman.G2
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G3</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["demoman"]].placement
                                  .demoman.G3
                              }}
                            </p>
                          </div>
                        </div>
                        <div class="col-md-3 text-center p-1">
                          <div class="stat-item">
                            <h6 class="card-title">G4</h6>
                            <p class="card-text player-stats">
                              {{
                                stats[currentStatType["demoman"]].placement
                                  .demoman.G4
                              }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="nav-bar right-nav-bar"
                    @click="nextStatType('demoman')"
                  ></div>
                </div>
              </div>
              <div
                v-if="
                  stats.map.placement.demoman.G1 > 0 ||
                  stats.map.topTimesAmount.demoman > 0
                "
                class="class-section"
              >
                <div class="card stat-block mb-3">
                  <div class="card-header">
                    <p class="section-header">
                      {{ player.shared_demoman_type }} (maps)
                    </p>
                  </div>
                  <div class="card-body">
                    <div class="row shared-card-row">
                      <div v-if="loading.shared" class="text-center">
                        <div class="spinner-border text-light" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                      </div>
                      <div v-else class="shared-body">
                        <div
                          class="d-flex align-items-center justify-content-between px-2 mb-2"
                          style="
                            background-color: transparent;
                            color: #aaa;
                            font-size: 0.85rem;
                            font-weight: 600;
                          "
                        >
                          <div class="d-flex align-items-center">
                            <i class="bi bi-people-fill me-2"></i>
                            <span>Player</span>
                          </div>
                          <div class="text-end">Count</div>
                        </div>
                        <div
                          v-for="(sharedPlayer, index) in sharedTimesDemoman"
                          :key="index"
                          class="d-flex align-items-center justify-content-between px-2 py-2 mb-2 shared-row"
                          @click="goToPlayer(sharedPlayer.playerId)"
                        >
                          <div
                            class="d-flex align-items-center shared-row-name"
                          >
                            <img
                              :src="sharedPlayer.avatar"
                              alt="Avatar"
                              class="rounded me-2 shared-avatar"
                            />
                            <span
                              class="fw-semibold"
                              style="font-size: 0.95rem"
                            >
                              {{ sharedPlayer.playerName }}
                            </span>
                          </div>
                          <div
                            class="text-end fw-bold shared-count"
                            style="min-width: 24px"
                          >
                            {{ sharedPlayer.count }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-if="isCurrentUser || hasFavoriteMaps" class="map-section">
            <div class="map-container">
              <h4 class="map-section-title">Favourite Maps</h4>
              <div class="map-grid">
                <div
                  class="map-card"
                  v-for="(map, index) in displayedMaps"
                  :key="index"
                  v-show="!map.invisible"
                  :class="{ 'map-empty-map': !map.name }"
                  @click="
                    isCurrentUser ? openMapSearch(index) : goToRecords(map.id)
                  "
                  :style="
                    map.name
                      ? {
                          background: `
                  linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
                  radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                  url('/map-backgrounds/thumbnails/${map.name}.jpg') center/cover no-repeat
                `,
                          backgroundBlendMode: 'multiply, normal, normal',
                          backgroundSize: 'cover, cover, cover',
                          backgroundPosition: 'center, center, center',
                        }
                      : {}
                  "
                >
                  <h5 class="map-card-subtitle" v-if="map.name">
                    {{ map.class_type }}
                  </h5>
                  <div class="map-header-nonmargin">
                    <h3 class="map-name">
                      {{ map.name || "Click to add a map" }}
                    </h3>
                  </div>
                  <div class="map-compact-ratings-grid" v-if="map.name">
                    <div
                      v-if="map.class_type === 'soldier'"
                      class="map-rating-section intended-class-section"
                    >
                      <div class="map-rating-label">Soldier</div>
                      <div class="map-rating-pills">
                        <span
                          class="map-rating-pill map-tier-color"
                          :class="'tier-' + map.map_soldier_tier"
                        >
                          T{{ map.map_soldier_tier }}
                        </span>
                        <span
                          class="map-rating-pill map-rating-color"
                          :class="'rating-' + map.map_soldier_rating"
                        >
                          R{{ map.map_soldier_rating }}
                        </span>
                      </div>
                    </div>
                    <div
                      v-else
                      class="map-rating-section intended-class-section"
                    >
                      <div class="map-rating-label">Demoman</div>
                      <div class="map-rating-pills">
                        <span
                          class="map-rating-pill map-tier-color"
                          :class="'tier-' + map.map_demoman_tier"
                        >
                          T{{ map.map_demoman_tier }}
                        </span>
                        <span
                          class="map-rating-pill map-rating-color"
                          :class="'rating-' + map.map_demoman_rating"
                        >
                          R{{ map.map_demoman_rating }}
                        </span>
                      </div>
                    </div>
                    <div
                      v-if="map.class_type === 'soldier'"
                      class="map-rating-section"
                    >
                      <div class="map-rating-label">Demoman</div>
                      <div class="map-rating-pills">
                        <span
                          class="map-rating-pill map-tier-color"
                          :class="'tier-' + map.map_demoman_tier"
                        >
                          T{{ map.map_demoman_tier }}
                        </span>
                        <span
                          class="map-rating-pill map-rating-color"
                          :class="'rating-' + map.map_demoman_rating"
                        >
                          R{{ map.map_demoman_rating }}
                        </span>
                      </div>
                    </div>
                    <div v-else class="map-rating-section">
                      <div class="map-rating-label">Soldier</div>
                      <div class="map-rating-pills">
                        <span
                          class="map-rating-pill map-tier-color"
                          :class="'tier-' + map.map_soldier_tier"
                        >
                          T{{ map.map_soldier_tier }}
                        </span>
                        <span
                          class="map-rating-pill map-rating-color"
                          :class="'rating-' + map.map_soldier_rating"
                        >
                          R{{ map.map_soldier_rating }}
                        </span>
                      </div>
                    </div>
                    <div class="record-row">
                      <img
                        :src="`/icons/${map.class_type}.png`"
                        :alt="map.class_type"
                        class="map-class-icon"
                        loading="lazy"
                      />
                      {{ formatDuration(map.player_record_duration) }} |
                      <span :class="getPlacementClass(map.record_placement)">
                        #{{ map.record_rank }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-if="visibleRotwVideos.length > 0" class="rotw-section">
            <div class="rotw-container">
              <h4 class="rotw-section-title">
                Runs of the Week
                <span class="rotw-count">({{ rotwVideos.length }})</span>
              </h4>
              <div class="rotw-grid">
                <div
                  v-for="(video, index) in visibleRotwVideos"
                  :key="index"
                  class="rotw-card"
                  :class="{
                    'rotw-card-active': activeRotwVideo === video.video_id,
                    'rotw-card-left': index % 2 === 0,
                    'rotw-card-right': index % 2 !== 0,
                  }"
                  @click.stop="
                    toggleRotwVideo(
                      video.video_id,
                      index % 2 === 0 ? 'left' : 'right'
                    )
                  "
                >
                  <div class="rotw-video-embed">
                    <div class="video-scale-wrapper">
                      <iframe
                        :src="`https://www.youtube.com/embed/${video.video_id}`"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        :style="{
                          pointerEvents:
                            activeRotwVideo === video.video_id
                              ? 'auto'
                              : 'none',
                        }"
                      ></iframe>
                    </div>
                  </div>
                  <div class="rotw-video-info">
                    <h5>{{ video.map_name }}</h5>
                    <p>{{ video.formatted_upload_date }}</p>
                  </div>
                </div>
              </div>
              <div class="load-more-container">
                <button
                  v-if="visibleRotwVideos.length < rotwVideos.length"
                  @click="loadMoreRotwVideos"
                  class="global-btn"
                >
                  Load More
                </button>
                <button
                  v-if="visibleRotwVideos.length < rotwVideos.length"
                  @click="showAllRotwVideos"
                  class="global-btn"
                >
                  Show All
                </button>
                <button
                  v-if="visibleRotwVideos.length > 2"
                  @click="closeAllRotwVideos"
                  class="global-btn"
                >
                  Close All
                </button>
              </div>
            </div>
          </div>
          <div
            v-if="authoredMaps.length > 0"
            id="authored-maps"
            class="map-section"
          >
            <div class="map-container">
              <h4 class="map-section-title">
                Authored maps
                <span class="rotw-count">({{ authoredMaps.length }})</span>
              </h4>
              <div class="author-map-grid">
                <SmartLink
                  v-for="(map, index) in visibleAuthoredMaps"
                  :key="index"
                  :to="{
                    name: 'MapPage',
                    params: { mapId: map.map_id },
                  }"
                  tag="div"
                  class="map-card author-card"
                  :style="{
                    background: `
      linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
      radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
      url('/map-backgrounds/thumbnails/${map.map_name}.jpg') center/cover no-repeat
    `,
                  }"
                >
                  <div class="class-icon-container">
                    <img
                      v-for="icon in map.classIcons"
                      :key="icon.alt"
                      :src="icon.src"
                      :class="icon.class"
                      :alt="icon.alt"
                    />
                  </div>
                  <div class="map-header">
                    <h3 class="map-name">{{ map.map_name }}</h3>
                  </div>
                  <div class="map-compact-ratings-grid">
                    <div
                      v-if="map.intended_class === 4"
                      class="map-rating-section intended-class-section"
                    >
                      <div class="map-rating-label">Demoman</div>
                      <div class="map-rating-pills">
                        <span
                          class="map-rating-pill map-tier-color"
                          :class="'tier-' + map.demoman_tier"
                        >
                          T{{ map.demoman_tier }}
                        </span>
                        <span
                          class="map-rating-pill map-rating-color"
                          :class="'rating-' + map.demoman_rating"
                        >
                          R{{ map.demoman_rating }}
                        </span>
                      </div>
                    </div>
                    <div
                      v-else
                      class="map-rating-section intended-class-section"
                    >
                      <div class="map-rating-label">Soldier</div>
                      <div class="map-rating-pills">
                        <span
                          class="map-rating-pill map-tier-color"
                          :class="'tier-' + map.soldier_tier"
                        >
                          T{{ map.soldier_tier }}
                        </span>
                        <span
                          class="map-rating-pill map-rating-color"
                          :class="'rating-' + map.soldier_rating"
                        >
                          R{{ map.soldier_rating }}
                        </span>
                      </div>
                    </div>
                    <div
                      v-if="map.intended_class === 4"
                      class="map-rating-section"
                    >
                      <div class="map-rating-label">Soldier</div>
                      <div class="map-rating-pills">
                        <span
                          class="map-rating-pill map-tier-color"
                          :class="'tier-' + map.soldier_tier"
                        >
                          T{{ map.soldier_tier }}
                        </span>
                        <span
                          class="map-rating-pill map-rating-color"
                          :class="'rating-' + map.soldier_rating"
                        >
                          R{{ map.soldier_rating }}
                        </span>
                      </div>
                    </div>
                    <div v-else class="map-rating-section">
                      <div class="map-rating-label">Demoman</div>
                      <div class="map-rating-pills">
                        <span
                          class="map-rating-pill map-tier-color"
                          :class="'tier-' + map.demoman_tier"
                        >
                          T{{ map.demoman_tier }}
                        </span>
                        <span
                          class="map-rating-pill map-rating-color"
                          :class="'rating-' + map.demoman_rating"
                        >
                          R{{ map.demoman_rating }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="map-date-added">
                    Date Added:
                    {{ map.date_added }}
                  </div>
                </SmartLink>
              </div>
              <div class="load-more-container">
                <button
                  v-if="visibleAuthoredMaps.length < authoredMaps.length"
                  @click="loadMoreAuthoredMaps"
                  class="global-btn"
                >
                  Load More
                </button>
                <button
                  v-if="visibleAuthoredMaps.length < authoredMaps.length"
                  @click="showAllAuthoredMaps"
                  class="global-btn"
                >
                  Show All
                </button>
                <button
                  v-if="visibleAuthoredMaps.length > authoredMapsPerPage"
                  @click="closeAllAuthoredMaps"
                  class="global-btn"
                >
                  Close All
                </button>
              </div>
            </div>
          </div>

          <div v-if="showMapSearch" class="map-search-overlay">
            <div class="map-search-container">
              <div class="class-selection">
                <h3>Select a class:</h3>
                <div class="class-icons">
                  <div
                    class="class-option"
                    :class="{ active: selectedClass === 'soldier' }"
                    @click="selectClass('soldier')"
                  >
                    <img
                      src="/icons/soldier.png"
                      alt="Soldier"
                      class="class-icon"
                      loading="lazy"
                    />
                    <span>Soldier</span>
                  </div>
                  <div
                    class="class-option"
                    :class="{ active: selectedClass === 'demoman' }"
                    @click="selectClass('demoman')"
                  >
                    <img
                      src="/icons/demoman.png"
                      alt="Demoman"
                      class="class-icon"
                      loading="lazy"
                    />
                    <span>Demoman</span>
                  </div>
                </div>
              </div>
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
                  v-model="mapSearchQuery"
                  placeholder="Search maps..."
                  class="search-input"
                  @input="searchMap"
                />
              </div>
              <div
                class="search-results-dropdown"
                v-if="mapSearchResults && mapSearchResults.length > 0"
              >
                <div>
                  <ul>
                    <li
                      v-for="map in mapSearchResults"
                      :key="map.id"
                      @click="handleMapClick(map)"
                      :class="{ disabled: !selectedClass }"
                    >
                      {{ map.name || `Map ID: ${map.id}` }}
                    </li>
                  </ul>
                </div>
              </div>
              <div v-if="showClassWarning" class="class-warning-popup">
                Select a class first
              </div>
              <div class="search-buttons">
                <button @click="cancelMapSearch" class="btn cancel-button">
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import VueApexCharts from "vue3-apexcharts";
import { ref } from "vue";
import { useHead } from "@vueuse/head";
import { formatDuration } from "@/utils/calculations.js";
import { formatDate } from "@/utils/calculations.js";
const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  inject: ["profileUpdateTracker"],
  name: "PlayerPage",
  setup() {
    const pageTitle = ref("Tempus Plaza | Player");
    useHead({
      title: pageTitle,
    });
    return {
      updateTitle: (playerName) => {
        pageTitle.value = `Tempus Plaza | ${playerName}`;
      },
    };
  },
  props: {
    playerId: {
      type: Number,
      required: true,
    },
  },
  components: {
    apexchart: VueApexCharts,
  },
  data: () => ({
    authoredMaps: [],
    visibleAuthoredMaps: [],
    authoredMapsPage: 1,
    authoredMapsPerPage: 3,
    rotwVideos: [],
    visibleRotwVideos: [],
    activeRotwVideo: null,
    activeRotwSide: null,
    rotwVideosPage: 1,
    rotwVideosPerPage: 2,
    currentTime: new Date(),
    updateTimer: null,
    debounceTimer: null,
    currentUser: null,
    selectedClass: null,
    activeRecordsTab: "recent-runs",
    _cachedRankInfo: null,
    changedPlacements: [],
    showClassWarning: false,
    classWarningTimeout: null,
    favoriteMaps: [
      {
        id: null,
        name: "",
        index: 0,
        class_type: null,
        map_soldier_tier: null,
        map_soldier_rating: null,
        map_demoman_tier: null,
        map_demoman_rating: null,
        player_record_duration: null,
        record_rank: null,
        record_placement: null,
      },
      {
        id: null,
        name: "",
        index: 1,
        class_type: null,
        map_soldier_tier: null,
        map_soldier_rating: null,
        map_demoman_tier: null,
        map_demoman_rating: null,
        player_record_duration: null,
        record_rank: null,
        record_placement: null,
      },
      {
        id: null,
        name: "",
        index: 2,
        class_type: null,
        map_soldier_tier: null,
        map_soldier_rating: null,
        map_demoman_tier: null,
        map_demoman_rating: null,
        player_record_duration: null,
        record_rank: null,
        record_placement: null,
      },
    ],
    showMapSearch: false,
    currentMapIndex: null,
    mapSearchQuery: "",
    mapSearchResults: [],
    player: {
      id: null,
      steamid: null,
      steamidconverted: null,
      avatar: null,
      name: null,
      country: "unknown",
      country_code: "unknown",
      overall_rank: null,
      soldier_rank: null,
      demoman_rank: null,
      overall_points: null,
      soldier_points: null,
      demoman_points: null,
      gender: "male",
      launcher_pref: 1,
      rank_pref: "",
      donator: false,
      shared_soldier_type: "",
      shared_demoman_type: "",
    },
    launcherOptions: [
      { value: 1, icon: "/icons/rocketlauncher.png" },
      { value: 2, icon: "/icons/cowmangler.png" },
      { value: 3, icon: "/icons/original.png" },
    ],
    rankOptions: [
      { value: 1, label: "Overall Rank" },
      { value: 2, label: "Soldier Rank" },
      { value: 3, label: "Demoman Rank" },
    ],
    showRankMenu: false,
    records: {
      recentRecords: [],
    },
    loading: {
      "Latest runs": true,
      shared: true,
      ranks: true,
      stats: true,
      points: true,
      "Lost placements": false,
    },
    currentPage: 1,
    pageSize: 8,
    playerNotFound: false,
    stats: {
      total: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
      map: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
      course: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
      bonus: {
        completion: { soldier: 0, demoman: 0 },
        worldRecordAmount: { soldier: 0, demoman: 0 },
        topTimesAmount: { soldier: 0, demoman: 0 },
        placement: {
          soldier: { G1: 0, G2: 0, G3: 0, G4: 0 },
          demoman: { G1: 0, G2: 0, G3: 0, G4: 0 },
        },
      },
    },
    currentStatType: {
      soldier: "total",
      demoman: "total",
    },
    sharedTimesSoldier: [],
    sharedTimesDemoman: [],
    pointsHistory: [],
    overallChartSeries: [],
    soldierChartSeries: [],
    demomanChartSeries: [],
    overallChartOptions: {
      chart: {
        type: "line",
        height: 250,
        background: "rgba(255, 255, 255, 0.05)",
        dropShadow: {
          enabled: true,
          color: "red",
          top: 5,
          left: 3,
          blur: 8,
          opacity: 0.5,
        },
        toolbar: {
          show: false,
        },
        zoom: {
          enabled: false,
        },
      },
      markers: {
        size: 1,
      },
      theme: {
        mode: "dark",
      },
      colors: ["#FF6B6B"],
      stroke: {
        curve: "straight",
        width: 3,
      },
      grid: {
        borderColor: "#444",
        strokeDashArray: 5,
      },
      xaxis: {
        type: "datetime",
        tooltip: {
          enabled: false,
        },
        labels: {
          tooltip: false,
          datetimeUTC: true,
          style: {
            colors: "#aaa",
          },
          formatter: function (value, timestamp) {
            const date = new Date(timestamp);
            const day = date.getUTCDate();
            const month = date.toLocaleDateString("en-US", {
              month: "short",
              timeZone: "UTC",
            });
            return `${day} ${month}`;
          },
          maxHeight: undefined,
          rotate: 0,
        },
        tickAmount: 6,
      },
      yaxis: {
        labels: {
          style: {
            colors: "#aaa",
          },
        },
      },
      tooltip: {
        theme: "dark",
        x: {
          format: "dd MMM yyyy",
        },
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          const data =
            w.globals.initialSeries[seriesIndex].data[dataPointIndex];
          const date = new Date(data.x).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            timeZone: "UTC",
          });
          const points = data.y;
          const rank = data.overall_rank;
          return `
        <div class="apexcharts-tooltip-title" style="font-size: 12px; font-weight:bold; margin: 2px 0 5px; padding: 4px;">${date}</div>
        <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: center; padding: 0 10px 0 10px;">
          <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #FF6B6B;"></span>
          <div class="apexcharts-tooltip-text" style="font-size: 12px;">
            <div class="apexcharts-tooltip-y-group">
              <span class="apexcharts-tooltip-text-y-label">Overall points: </span>
              <span class="apexcharts-tooltip-text-y-value">${points}</span>
            </div>
          </div>
        </div>
        <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 2; display: flex; align-items: center; padding: 0 10px 7px 10px;">
          <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #FF6B6B;"></span>
          <div class="apexcharts-tooltip-text" style="font-size: 12px;">
            <div class="apexcharts-tooltip-y-group">
              <span class="apexcharts-tooltip-text-y-label">Overall rank: </span>
              <span class="apexcharts-tooltip-text-y-value">#${rank}</span>
            </div>
          </div>
        </div>
      `;
        },
      },
      legend: {
        labels: {
          colors: "#fff",
        },
      },
    },
    soldierChartOptions: {
      chart: {
        type: "line",
        height: 250,
        background: "rgba(255, 255, 255, 0.05)",
        dropShadow: {
          enabled: true,
          color: "cyan",
          top: 5,
          left: 3,
          blur: 8,
          opacity: 0.5,
        },
        toolbar: {
          show: false,
        },
        zoom: {
          enabled: false,
        },
      },
      markers: {
        size: 1,
      },
      theme: {
        mode: "dark",
      },
      colors: ["#4ECDC4"],
      stroke: {
        curve: "straight",
        width: 3,
      },
      grid: {
        borderColor: "#444",
        strokeDashArray: 5,
      },
      xaxis: {
        type: "datetime",
        tooltip: {
          enabled: false,
        },
        labels: {
          tooltip: false,
          datetimeUTC: true,
          style: {
            colors: "#aaa",
          },
          formatter: function (value, timestamp) {
            const date = new Date(timestamp);
            const day = date.getUTCDate();
            const month = date.toLocaleDateString("en-US", {
              month: "short",
              timeZone: "UTC",
            });
            return `${day} ${month}`;
          },
          maxHeight: undefined,
          rotate: 0,
        },
        tickAmount: 6,
      },
      yaxis: {
        labels: {
          style: {
            colors: "#aaa",
          },
        },
      },
      tooltip: {
        theme: "dark",
        x: {
          format: "dd MMM yyyy",
        },
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          const data =
            w.globals.initialSeries[seriesIndex].data[dataPointIndex];
          const date = new Date(data.x).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            timeZone: "UTC",
          });
          const points = data.y;
          const rank = data.soldier_rank;
          return `
            <div class="apexcharts-tooltip-title" style="font-size: 12px; font-weight:bold; margin: 2px 0 5px; padding: 4px;">${date}</div>
            <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: center; padding: 0 10px 0 10px;">
              <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #4ECDC4;"></span>
              <div class="apexcharts-tooltip-text" style="font-size: 12px;">
                <div class="apexcharts-tooltip-y-group">
                  <span class="apexcharts-tooltip-text-y-label">Soldier points: </span>
                  <span class="apexcharts-tooltip-text-y-value">${points}</span>
                </div>
              </div>
            </div>
            <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 2; display: flex; align-items: center; padding: 0 10px 7px 10px;">
              <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #4ECDC4;"></span>
              <div class="apexcharts-tooltip-text" style="font-size: 12px;">
                <div class="apexcharts-tooltip-y-group">
                  <span class="apexcharts-tooltip-text-y-label">Soldier rank: </span>
                  <span class="apexcharts-tooltip-text-y-value">#${rank}</span>
                </div>
              </div>
            </div>
          `;
        },
      },
      legend: {
        labels: {
          colors: "#fff",
        },
      },
    },
    demomanChartOptions: {
      chart: {
        type: "line",
        height: 250,
        background: "rgba(255, 255, 255, 0.05)",
        dropShadow: {
          enabled: true,
          color: "blue",
          top: 5,
          left: 3,
          blur: 8,
          opacity: 0.5,
        },
        toolbar: {
          show: false,
        },
        zoom: {
          enabled: false,
        },
      },
      markers: {
        size: 1,
      },
      theme: {
        mode: "dark",
      },
      colors: ["#45B7D1"],
      stroke: {
        curve: "straight",
        width: 3,
      },
      grid: {
        borderColor: "#444",
        strokeDashArray: 5,
      },
      xaxis: {
        type: "datetime",
        tooltip: {
          enabled: false,
        },
        labels: {
          tooltip: false,
          datetimeUTC: true,
          style: {
            colors: "#aaa",
          },
          formatter: function (value, timestamp) {
            const date = new Date(timestamp);
            const day = date.getUTCDate();
            const month = date.toLocaleDateString("en-US", {
              month: "short",
              timeZone: "UTC",
            });
            return `${day} ${month}`;
          },
          maxHeight: undefined,
          rotate: 0,
        },
        tickAmount: 6,
      },
      yaxis: {
        labels: {
          style: {
            colors: "#aaa",
          },
        },
      },
      tooltip: {
        theme: "dark",
        x: {
          format: "dd MMM yyyy",
        },
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          const data =
            w.globals.initialSeries[seriesIndex].data[dataPointIndex];
          const date = new Date(data.x).toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric",
            timeZone: "UTC",
          });
          const points = data.y;
          const rank = data.demoman_rank;
          return `
            <div class="apexcharts-tooltip-title" style="font-size: 12px; font-weight:bold; margin: 2px 0 5px; padding: 4px;">${date}</div>
            <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: center; padding: 0 10px 0 10px;">
              <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #45B7D1;"></span>
              <div class="apexcharts-tooltip-text" style="font-size: 12px;">
                <div class="apexcharts-tooltip-y-group">
                  <span class="apexcharts-tooltip-text-y-label">Demoman points: </span>
                  <span class="apexcharts-tooltip-text-y-value">${points}</span>
                </div>
              </div>
            </div>
            <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 2; display: flex; align-items: center; padding: 0 10px 7px 10px;">
              <span class="apexcharts-tooltip-marker" style="width: 12px; height: 12px; position: relative; top: 0; margin-right: 4px; border-radius: 50%; background-color: #45B7D1;"></span>
              <div class="apexcharts-tooltip-text" style="font-size: 12px;">
                <div class="apexcharts-tooltip-y-group">
                  <span class="apexcharts-tooltip-text-y-label">Demoman rank: </span>
                  <span class="apexcharts-tooltip-text-y-value">#${rank}</span>
                </div>
              </div>
            </div>
          `;
        },
      },
      legend: {
        labels: {
          colors: "#fff",
        },
      },
    },
    showFilterSection: false,
    filterOptions: {
      selectedClasses: [],
      selectedTypes: [],
      selectedPlacements: [],
      selectedGainLoss: [],
    },
  }),
  computed: {
    precomputedHeaders() {
      const headers = {};

      Object.keys(this.filteredAndPaginatedRecords || {}).forEach((date) => {
        headers[date] = this.formatDateHeader(date);
      });

      Object.keys(this.filteredAndPaginatedChangedPlacements || {}).forEach(
        (date) => {
          headers[date] = this.formatDateHeader(date);
        }
      );

      return headers;
    },
    processedChangedPlacements() {
      const processed = {};

      for (const [date, placements] of Object.entries(
        this.filteredAndPaginatedChangedPlacements
      )) {
        processed[date] = placements.map((placement) => {
          const rankChangeClass =
            placement.points_change > 0 ? "rank-gain" : "rank-loss";
          const placementClass =
            placement.points_change > 0 ? "gained-placement" : "lost-placement";

          return {
            ...placement,
            rankChangeClass: rankChangeClass,
            placementClass: placementClass,
          };
        });
      }

      return processed;
    },
    visibleAuthoredMaps() {
      const start = 0;
      const end = this.authoredMapsPage * this.authoredMapsPerPage;
      return this.authoredMaps.slice(start, end);
    },
    nextUpdateCountdown() {
      const now = this.currentTime;
      const nextUpdate = new Date(now);

      const nextHour = Math.ceil((now.getHours() + 1) / 2) * 2;
      nextUpdate.setHours(nextHour, 0, 0, 0);

      const diff = nextUpdate - now;

      const hours = Math.floor(diff / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((diff % (1000 * 60)) / 1000);

      if (hours > 0) {
        return `${hours}h ${minutes}m ${seconds}s`;
      } else if (minutes > 0) {
        return `${minutes}m ${seconds}s`;
      }
      return `${seconds}s`;
    },
    currentTimeString() {
      return this.currentTime.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      });
    },
    highestRank() {
      if (this.player.rank_pref === "overall") return this.player.overall_rank;
      else if (this.player.rank_pref === "soldier")
        return this.player.soldier_rank;
      else if (this.player.rank_pref === "demoman")
        return this.player.demoman_rank;
      return Math.min(
        this.player.overall_rank,
        this.player.soldier_rank,
        this.player.demoman_rank
      );
    },
    playerRankInfo() {
      return this.getRankName(this.highestRank, this.player.gender);
    },
    filteredAndPaginatedRecords() {
      const grouped = this.groupRecords(this.filteredRecords);
      return this.paginateGroupedData(grouped);
    },
    filteredAndPaginatedChangedPlacements() {
      return this.paginateGroupedData(this.groupedChangedPlacements);
    },
    shouldShowNextButton() {
      if (this.activeRecordsTab === "recent-runs") {
        return this.currentPage * this.pageSize < this.filteredRecords.length;
      } else {
        const totalItems = this.filteredChangedPlacements.length;
        return this.currentPage * this.pageSize < totalItems;
      }
    },
    hasFavoriteMaps() {
      return this.favoriteMaps.some(
        (map) => map.name && map.name.trim() !== ""
      );
    },

    filteredFavoriteMaps() {
      return this.favoriteMaps.filter(
        (map) => map.name && map.name.trim() !== ""
      );
    },

    displayedMaps() {
      const displayArray = [
        { name: null, id: null, invisible: true },
        { name: null, id: null, invisible: true },
        { name: null, id: null, invisible: true },
      ];

      this.favoriteMaps.forEach((map, index) => {
        if (index < 3) {
          if (map.name && map.name.trim() !== "") {
            displayArray[index] = { ...map, invisible: false };
          } else if (this.isCurrentUser) {
            displayArray[index] = { name: null, id: null, invisible: false };
          }
        }
      });

      if (!this.isCurrentUser) {
        return displayArray.filter((map) => !map.invisible);
      }

      return displayArray;
    },

    isCurrentUser() {
      return this.currentUser && this.currentUser.playerid === this.playerId;
    },
    bannerColors() {
      const color = this.player.color || "blue";
      return {
        color1: `var(--color-banner-${color}-1)`,
        color2: `var(--color-banner-${color}-2)`,
      };
    },
    filteredRecords() {
      let recordsToFilter = this.records.recentRecords;
      return recordsToFilter.filter((record) => {
        if (
          this.filterOptions.selectedClasses.length > 0 &&
          !this.filterOptions.selectedClasses.includes(record.class)
        ) {
          return false;
        }
        if (
          this.filterOptions.selectedTypes.length > 0 &&
          !this.filterOptions.selectedTypes.includes(record.type)
        ) {
          return false;
        }
        if (
          this.filterOptions.selectedPlacements.length > 0 &&
          !this.filterOptions.selectedPlacements.some((placement) => {
            if (placement === 1) return record.placement === 1;
            if (placement === 2)
              return record.placement >= 2 && record.placement <= 10;
            return record.placement === placement;
          })
        ) {
          return false;
        }
        return true;
      });
    },
    filteredChangedPlacements() {
      if (!this.changedPlacements || !this.changedPlacements.length) return [];

      let filtered = this.changedPlacements.filter((placement) => {
        // Class filter
        if (
          this.filterOptions.selectedClasses.length > 0 &&
          !this.filterOptions.selectedClasses.includes(placement.class)
        ) {
          return false;
        }

        // Type filter
        if (
          this.filterOptions.selectedTypes.length > 0 &&
          !this.filterOptions.selectedTypes.includes(placement.type)
        ) {
          return false;
        }

        // Placement filter
        if (this.filterOptions.selectedPlacements.length > 0) {
          const matchesPlacement = this.filterOptions.selectedPlacements.some(
            (selectedPlacement) => {
              // Check old_rank
              const oldRankMatches = this.checkPlacementMatch(
                placement.old_rank,
                selectedPlacement
              );
              // Check new_rank
              const newRankMatches = this.checkPlacementMatch(
                placement.new_rank,
                selectedPlacement
              );

              return oldRankMatches || newRankMatches;
            }
          );

          if (!matchesPlacement) {
            return false;
          }
        }
        if (this.filterOptions.selectedGainLoss.length > 0) {
          const isGain = placement.rank_change > 0;
          const isLoss = placement.rank_change < 0;

          const matchesGainLoss = this.filterOptions.selectedGainLoss.some(
            (filter) => {
              if (filter === "gained" && isGain) return true;
              if (filter === "lost" && isLoss) return true;
              return false;
            }
          );

          if (!matchesGainLoss) {
            return false;
          }
        }

        return true;
      });

      return filtered;
    },
    groupedChangedPlacements() {
      if (
        !this.filteredChangedPlacements ||
        !Array.isArray(this.filteredChangedPlacements) ||
        this.filteredChangedPlacements.length === 0
      ) {
        return {};
      }
      return this.groupRecords(this.filteredChangedPlacements);
    },
    currentRecordsData() {
      return this.activeRecordsTab === "recent-runs"
        ? this.filteredAndPaginatedRecords
        : this.filteredChangedPlacements;
    },

    currentLoadingState() {
      return this.activeRecordsTab === "recent-runs"
        ? this.loading["Latest runs"]
        : this.loading["Lost placements"];
    },
  },
  async mounted() {
    document.addEventListener("click", this.handleClickOutside);
    await this.loadPlayerPageData(this.playerId);
    this.startUpdateTimer();
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
    if (this.updateTimer) {
      clearInterval(this.updateTimer);
    }
  },
  watch: {
    authoredMaps(newMaps) {
      if (newMaps.length > 0 && this.$route.hash === "#authored-maps") {
        this.scrollToAuthoredMaps();
      }
    },
    profileUpdateTracker: {
      handler(newTracker) {
        this.fetchUserData(this.playerId);
      },
      deep: true,
    },
    playerId: {
      immediate: false,
      async handler(newId, oldId) {
        if (newId !== oldId) {
          this.playerNotFound = false;
          this.loading = {
            ...this.loading,
            ranks: true,
            stats: true,
            shared: true,
            "Latest runs": true,
          };
          this.activeRecordsTab = "recent-runs";
          this.currentPage = 1;
          this.filterOptions = {
            selectedClasses: [],
            selectedTypes: [],
            selectedPlacements: [],
            selectedGainLoss: [],
          };
          this.closeAllRotwVideos();
          this.currentStatType.soldier = "total";
          this.currentStatType.demoman = "total";
          this.favoriteMaps = [
            {
              id: null,
              name: "",
              index: 0,
              class_type: null,
              map_soldier_tier: null,
              map_soldier_rating: null,
              map_demoman_tier: null,
              map_demoman_rating: null,
              player_record_duration: null,
              record_rank: null,
              record_placement: null,
            },
            {
              id: null,
              name: "",
              index: 1,
              class_type: null,
              map_soldier_tier: null,
              map_soldier_rating: null,
              map_demoman_tier: null,
              map_demoman_rating: null,
              player_record_duration: null,
              record_rank: null,
              record_placement: null,
            },
            {
              id: null,
              name: "",
              index: 2,
              class_type: null,
              map_soldier_tier: null,
              map_soldier_rating: null,
              map_demoman_tier: null,
              map_demoman_rating: null,
              player_record_duration: null,
              record_rank: null,
              record_placement: null,
            },
          ];
          this.rotwVideos = [];
          this.visibleRotwVideos = [];
          this.rotwVideosPage = 1;
          await this.loadPlayerPageData(newId);
        }
      },
    },
  },
  methods: {
    scrollToAuthoredMaps() {
      const attemptScroll = () => {
        const element = document.getElementById("authored-maps");
        if (element) {
          element.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      };

      // Initial scroll
      this.$nextTick(attemptScroll);

      // Retry scrolling after potential layout shifts
      setTimeout(attemptScroll, 500);
    },
    formatUploadDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    },
    getClassIcons(intendedClass) {
      if (intendedClass === 3) {
        return [
          {
            src: "/icons/soldier.png",
            alt: "Soldier class icon",
            class: "author-class-icon",
          },
        ];
      }

      if (intendedClass === 4) {
        return [
          {
            src: "/icons/demoman.png",
            alt: "Demoman class icon",
            class: "author-class-icon",
          },
        ];
      }

      if (intendedClass === 5) {
        return [
          {
            src: "/icons/soldier.png",
            alt: "Soldier class icon",
            class: "author-class-icon dual-icon",
          },
          {
            src: "/icons/demoman.png",
            alt: "Demoman class icon",
            class: "author-class-icon dual-icon",
          },
        ];
      }

      return [];
    },
    async fetchAuthoredMaps(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/maps-from-author`
        );
        this.authoredMaps = response.data
          .map((map) => ({
            map_id: map.map_id,
            map_name: map.name,
            soldier_tier: map.soldier_tier,
            soldier_rating: map.soldier_rating,
            demoman_tier: map.demoman_tier,
            demoman_rating: map.demoman_rating,
            intended_class: map.intended_class,
            classIcons: this.getClassIcons(map.intended_class),
            date_added: this.formatDate2(map.date_added),
          }))
          .sort((a, b) => b.map_id - a.map_id);
      } catch (error) {
        console.error("Error fetching authored maps:", error);
      }
    },
    formatDate2(unixTimestamp) {
      const date = new Date(unixTimestamp * 1000);
      const day = String(date.getDate()).padStart(2, "0");
      const monthNames = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];
      const monthName = monthNames[date.getMonth()];
      const year = date.getFullYear();
      return `${day} ${monthName} ${year}`;
    },
    async fetchRotwVideos(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/rotw-videos`
        );

        const uniqueVideos = response.data.filter(
          (video, index, self) =>
            index === self.findIndex((v) => v.video_id === video.video_id)
        );

        this.rotwVideos = uniqueVideos.map((video) => ({
          ...video,
          formatted_upload_date: this.formatUploadDate(video.uploaded_at),
        }));

        this.visibleRotwVideos = this.rotwVideos.slice(
          0,
          this.rotwVideosPerPage
        );
      } catch (error) {
        console.error("Error fetching ROTW videos:", error);
      }
    },

    loadMoreRotwVideos() {
      const start = this.rotwVideosPage * this.rotwVideosPerPage;
      const end = start + this.rotwVideosPerPage;
      this.visibleRotwVideos = [
        ...this.visibleRotwVideos,
        ...this.rotwVideos.slice(start, end),
      ];
      this.rotwVideosPage++;
    },
    showAllRotwVideos() {
      this.visibleRotwVideos = [...this.rotwVideos];
    },
    closeAllRotwVideos() {
      this.visibleRotwVideos = this.rotwVideos.slice(0, 2);
      this.rotwVideosPage = 1;
      this.activeRotwVideo = null;
      this.activeRotwSide = null;
    },
    toggleRotwVideo(videoId, side) {
      this.activeRotwVideo = this.activeRotwVideo === videoId ? null : videoId;
      this.activeRotwSide = side;
    },
    loadMoreAuthoredMaps() {
      const start = this.authoredMapsPage * this.authoredMapsPerPage;
      const end = start + this.authoredMapsPerPage;
      this.visibleAuthoredMaps = [
        ...this.visibleAuthoredMaps,
        ...this.authoredMaps.slice(start, end),
      ];
      this.authoredMapsPage++;
    },
    showAllAuthoredMaps() {
      this.visibleAuthoredMaps = [...this.authoredMaps];
      this.authoredMapsPage = Math.ceil(
        this.authoredMaps.length / this.authoredMapsPerPage
      );
    },
    closeAllAuthoredMaps() {
      this.visibleAuthoredMaps = this.authoredMaps.slice(
        0,
        this.authoredMapsPerPage
      );
      this.authoredMapsPage = 1;
    },
    handleClickOutside(e) {
      if (!e.target.closest(".rotw-card")) {
        this.activeRotwVideo = null;
      }
    },
    startUpdateTimer() {
      this.updateTimer = setInterval(() => {
        this.currentTime = new Date();
      }, 1000);
    },
    async loadPlayerPageData(playerId) {
      try {
        fetch(
          `https://api.tempusplaza.xyz/players/${playerId}/update-player-last-seen`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
          }
        );
        this.currentUser = await this.fetchUser();
        await Promise.all([
          this.fetchPlayerData(playerId),
          this.fetchUserData(playerId),
          this.fetchPlayerRanks(playerId),
          this.fetchRecentRecords(playerId),
          this.fetchPlayerPoints(playerId),
          this.fetchFavoriteMaps(playerId),
          this.fetchChangedPlacements(playerId),
          this.fetchRotwVideos(playerId),
          this.fetchAuthoredMaps(playerId),
        ]);
        await this.fetchPlayerStats(playerId);
        await this.fetchSharedTimes(playerId);
      } catch (error) {
        console.error("Error loading player page data:", error);
      }
    },
    paginateGroupedData(groupedData) {
      if (!groupedData || typeof groupedData !== "object") {
        return {};
      }

      const start = (this.currentPage - 1) * this.pageSize;
      const end = start + this.pageSize;
      const dates = Object.keys(groupedData);
      const paginated = {};
      let count = 0;

      for (const date of dates) {
        if (!Array.isArray(groupedData[date])) {
          continue;
        }

        for (const item of groupedData[date]) {
          if (count >= start && count < end) {
            if (!paginated[date]) {
              paginated[date] = [];
            }
            paginated[date].push(item);
          }
          count++;
        }
      }

      return paginated;
    },
    checkPlacementMatch(rank, selectedPlacement) {
      if (selectedPlacement === 1) return rank === 1;
      if (selectedPlacement === 2) return rank >= 2 && rank <= 10;
      return rank === selectedPlacement;
    },
    switchRecordsTab(tab) {
      this.activeRecordsTab = tab;
      this.currentPage = 1;
    },
    async fetchChangedPlacements(playerId) {
      this.loading["Lost placements"] = true;
      try {
        const response = await fetch(
          `${API_BASE_URL}/players/${playerId}/lost-records`
        );
        const data = await response.json();

        this.changedPlacements = data.map((p) => {
          const old_rank = p.old_placement;
          const new_rank = p.new_placement;
          const points_change = p.points_change;
          const date = p.change_date;

          return {
            ...p,
            class: p.record_type.split("_")[0], // 'soldier' | 'demoman'
            type: p.record_type.split("_")[1], // 'map' | 'course' | 'bonus'
            old_rank,
            new_rank,
            rank_change: p.placement_change,
            date,
            points_change,

            oldRankDisplay: `${this.getMedal(old_rank)}${this.formatRankDisplay(
              old_rank
            )}`,
            newRankDisplay: `${this.getMedal(new_rank)}${this.formatRankDisplay(
              new_rank
            )}`,
            oldRankClass: this.getPlacementClass(old_rank),
            newRankClass: this.getPlacementClass(new_rank),

            pointsChangeClass: this.getPointsChangeClass(points_change),
            pointsChangeText: this.formatPointsChange(points_change),

            formattedDate: this.formatDate(date),
          };
        });
      } catch (error) {
        console.error("Error fetching lost placements:", error);
      } finally {
        this.loading["Lost placements"] = false;
      }
    },
    convertSteamId(steamId) {
      if (!steamId) {
        return "#";
      }

      const parts = steamId.split(":");
      if (parts.length === 3 && parts[0] === "STEAM_0") {
        const y = parseInt(parts[1]);
        const z = parseInt(parts[2]);
        return (
          BigInt(z) * BigInt(2) +
          BigInt(y) +
          BigInt("76561197960265728")
        ).toString();
      }
      return "#";
    },
    openMapSearch(index) {
      this.currentMapIndex = index;
      this.showMapSearch = true;
      this.mapSearchResults = [];
      this.mapSearchQuery = "";
    },
    searchMap() {
      if (this.debounceTimer) {
        clearTimeout(this.debounceTimer);
      }

      if (!this.mapSearchQuery.trim()) {
        console.log("Search query is empty");
        this.mapSearchResults = [];
        return;
      }

      this.debounceTimer = setTimeout(async () => {
        console.log("Executing search after timeout for:", this.mapSearchQuery);

        if (!this.mapSearchQuery.trim()) {
          this.mapSearchResults = [];
          return;
        }

        try {
          const response = await fetch(`${API_BASE_URL}/search/maps`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ query: this.mapSearchQuery }),
          });

          if (!response.ok) throw new Error("Failed to fetch search results");

          const data = await response.json();
          this.mapSearchResults = data.maps || data || [];

          if (this.mapSearchResults.length > 5) {
            this.mapSearchResults = this.mapSearchResults.slice(0, 5);
          }

          console.log(
            "Search completed, results:",
            this.mapSearchResults.length
          );
        } catch (error) {
          console.error("Error searching for maps:", error);
          this.mapSearchResults = [];
        }
      }, 500);
    },
    selectClass(className) {
      this.selectedClass = className;
      this.hideClassWarning();
    },
    handleMapClick(map) {
      if (!this.selectedClass) {
        this.showClassWarningPopup();
        return;
      }
      this.selectMap(map, this.selectedClass);
    },
    async selectMap(mapData, classType) {
      this.cancelMapSearch();
      await this.updateMap(mapData.id, classType);
      await this.fetchFavoriteMaps(this.playerId);
    },
    showClassWarningPopup() {
      this.showClassWarning = true;
      if (this.classWarningTimeout) {
        clearTimeout(this.classWarningTimeout);
      }
      this.classWarningTimeout = setTimeout(() => {
        this.hideClassWarning();
      }, 2000);
    },
    hideClassWarning() {
      this.showClassWarning = false;
      if (this.classWarningTimeout) {
        clearTimeout(this.classWarningTimeout);
        this.classWarningTimeout = null;
      }
    },
    async fetchUser() {
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
        return result.data;
      } catch (error) {
        console.error("Error fetching user data:", error);
        return null;
      }
    },
    async updateMap(mapId, classType) {
      const currentUser = await this.fetchUser();
      const index = this.currentMapIndex;
      try {
        const response = await fetch(
          `${API_BASE_URL}/users/${currentUser.playerid}/update-favorite-maps/${mapId}/${index}/${classType}`,
          {
            method: "POST",
          }
        );

        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);
      } catch (error) {
        console.error("Failed to update favorite map:", error);
      }
    },
    async clearFavoriteMaps() {
      const currentUser = await this.fetchUser();
      try {
        const response = await fetch(
          `${API_BASE_URL}/users/update-user/${currentUser.playerid}`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            credentials: "include",
            body: JSON.stringify({
              favorite_maps: [],
            }),
          }
        );

        if (!response.ok)
          throw new Error(`HTTP error! status: ${response.status}`);

        console.log("Favorite maps cleared successfully");
      } catch (error) {
        console.error("Failed to clear favorite maps:", error);
      }
    },
    cancelMapSearch() {
      this.selectedClass = null;
      this.hideClassWarning();
      this.showMapSearch = false;
      this.mapSearchQuery = "";
      this.mapSearchResults = [];
    },
    prevStatType(classType) {
      const statTypes = ["total", "map", "course", "bonus"];
      const currentIndex = statTypes.indexOf(this.currentStatType[classType]);
      const prevIndex =
        (currentIndex - 1 + statTypes.length) % statTypes.length;
      this.currentStatType[classType] = statTypes[prevIndex];
    },
    nextStatType(classType) {
      const statTypes = ["total", "map", "course", "bonus"];
      const currentIndex = statTypes.indexOf(this.currentStatType[classType]);
      const nextIndex = (currentIndex + 1) % statTypes.length;
      this.currentStatType[classType] = statTypes[nextIndex];
    },
    toggleClassFilter(classType) {
      const index = this.filterOptions.selectedClasses.indexOf(classType);
      if (index === -1) {
        this.filterOptions.selectedClasses.push(classType);
      } else {
        this.filterOptions.selectedClasses.splice(index, 1);
      }
    },
    toggleTypeFilter(type) {
      const index = this.filterOptions.selectedTypes.indexOf(type);
      if (index === -1) {
        this.filterOptions.selectedTypes.push(type);
      } else {
        this.filterOptions.selectedTypes.splice(index, 1);
      }
    },
    togglePlacementFilter(placement) {
      const index = this.filterOptions.selectedPlacements.indexOf(placement);
      if (index === -1) {
        this.filterOptions.selectedPlacements.push(placement);
      } else {
        this.filterOptions.selectedPlacements.splice(index, 1);
      }
    },
    toggleGainLossFilter(type) {
      const index = this.filterOptions.selectedGainLoss.indexOf(type);
      if (index === -1) {
        this.filterOptions.selectedGainLoss.push(type);
      } else {
        this.filterOptions.selectedGainLoss.splice(index, 1);
      }
    },
    toggleFilterSection() {
      this.showFilterSection = !this.showFilterSection;
    },
    groupRecords(records) {
      const grouped = {};
      records.forEach((record) => {
        const date = new Date(record.date * 1000).toDateString();
        if (!grouped[date]) {
          grouped[date] = [];
        }
        grouped[date].push(record);
      });
      return grouped;
    },
    clearAllFilters() {
      this.filterOptions = {
        selectedClasses: [],
        selectedTypes: [],
        selectedPlacements: [],
        selectedGainLoss: [],
      };
      this.currentPage = 1;
    },
    nextPage() {
      this.currentPage++;
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    formatDateHeader(dateString) {
      const date = new Date(dateString);
      const day = String(date.getDate()).padStart(2, "0");
      const month = date.toLocaleDateString("en-US", { month: "short" });
      return `${day} ${month}`;
    },
    async fetchPlayerPoints(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/points`
        );
        this.pointsHistory = response.data;
        this.updateChartData();
      } catch (error) {
        console.error("Error fetching player points:", error);
      }
    },
    updateChartData() {
      if (!this.pointsHistory.length) {
        this.loading.points = false;
        return;
      }

      const sortedData = [...this.pointsHistory].sort(
        (a, b) => a.date - b.date
      );

      const filterDuplicates = (data, pointsKey, rankKey) => {
        return data.filter((point, index) => {
          if (index === 0) return true;

          const prev = data[index - 1];
          return (
            point[pointsKey] !== prev[pointsKey] ||
            point[rankKey] !== prev[rankKey]
          );
        });
      };

      const filteredOverallData = filterDuplicates(
        sortedData,
        "overall_points",
        "overall_rank"
      );
      const filteredSoldierData = filterDuplicates(
        sortedData,
        "soldier_points",
        "soldier_rank"
      );
      const filteredDemomanData = filterDuplicates(
        sortedData,
        "demoman_points",
        "demoman_rank"
      );

      this.overallChartSeries = [
        {
          name: "Overall Points",
          data: filteredOverallData.map((point) => ({
            x: point.date * 1000,
            y: point.overall_points,
            overall_rank: point.overall_rank,
          })),
        },
      ];

      this.soldierChartSeries = [
        {
          name: "Soldier Points",
          data: filteredSoldierData.map((point) => ({
            x: point.date * 1000,
            y: point.soldier_points,
            soldier_rank: point.soldier_rank,
          })),
        },
      ];

      this.demomanChartSeries = [
        {
          name: "Demoman Points",
          data: filteredDemomanData.map((point) => ({
            x: point.date * 1000,
            y: point.demoman_points,
            demoman_rank: point.demoman_rank,
          })),
        },
      ];

      this.loading.points = false;
    },
    goToRecords(mapId) {
      this.$router.push({
        name: "MapPage",
        params: { mapId: mapId },
      });
    },
    goToPlayer(playerId) {
      this.$router.push({
        name: "PlayerPage",
        params: { playerId: playerId },
      });
    },
    goToLookup() {
      this.$router.push({
        name: "Lookup",
        params: { playerId: this.player.id },
      });
    },
    getPlacementClass(placement) {
      const placementClasses = {
        1: "placement-gold",
        2: "placement-silver",
        3: "placement-bronze",
        11: "placement-g1",
        12: "placement-g2",
        13: "placement-g3",
        14: "placement-g4",
        15: "placement-g5",
      };
      if (placement >= 4 && placement <= 10) return "placement-blue";
      return placementClasses[placement] || "";
    },
    getMedal(rank) {
      return ["🥇", "🥈", "🥉"][rank - 1] || "";
    },
    formatRankDisplay(rank) {
      if (rank >= 11 && rank <= 15) {
        return `G${rank - 10}`;
      }
      return `#${rank}`;
    },
    getPointsChangeClass(pointsChange) {
      if (pointsChange > 0) return "points-gain";
      if (pointsChange < 0) return "points-loss";
      return "points-neutral";
    },
    formatPointsChange(pointsChange) {
      const roundedPoints = Math.round(pointsChange * 100) / 100;

      if (roundedPoints > 0) return `+${roundedPoints.toFixed(2)}pts`;
      if (roundedPoints < 0) return `${roundedPoints.toFixed(2)}pts`;
      return "0.00pts";
    },
    getRankName(rank, gender) {
      const titles = [
        { range: [1, 1], male: "Emperor", female: "Empress" },
        { range: [2, 2], male: "King", female: "Queen" },
        { range: [3, 3], male: "Archduke", female: "Archduchess" },
        { range: [4, 4], male: "Lord", female: "Lady" },
        { range: [5, 5], male: "Duke", female: "Duchess" },
        { range: [6, 6], male: "Prince V", female: "Princess V" },
        { range: [7, 7], male: "Prince IV", female: "Princess IV" },
        { range: [8, 8], male: "Prince III", female: "Princess III" },
        { range: [9, 9], male: "Prince II", female: "Princess II" },
        { range: [10, 10], male: "Prince I", female: "Princess I" },
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
      for (const title of titles) {
        if (rank >= title.range[0] && rank <= title.range[1]) {
          const selectedTitle = gender === "male" ? title.male : title.female;
          let cleanedTitle = title.male.replace(/\s[IVX]+$/, "").toLowerCase();
          if (selectedTitle === "Queen" || selectedTitle === "Empress")
            cleanedTitle = title.female.replace(/\s[IVX]+$/, "").toLowerCase();
          return {
            title: selectedTitle,
            color: `rank-color-${cleanedTitle}`,
          };
        }
      }
      return { title: "Unranked", color: "--color-peon" };
    },
    getFlagImageUrl(countryCode) {
      const validCode = /^[a-zA-Z]{2}$/.test(countryCode)
        ? countryCode.toLowerCase()
        : "unknown";
      return `https://flagcdn.com/24x18/${validCode}.png`;
    },
    handleImageError(event) {
      event.target.src = "/icons/default-flag.jpg";
    },
    returnToAllPlayers() {
      this.$router.push({ name: "Players" });
    },
    async fetchUserData(playerId) {
      try {
        const response = await axios.get(`${API_BASE_URL}/users/${playerId}`);
        const data = response.data;
        if (!data || data.length === 0) {
          this.player = {
            ...this.player,
            gender: "male",
            donator: false,
            color: "blue",
          };
          return;
        }
        this.player = {
          ...this.player,
          gender: data.gender,
          rank_pref: data.rank_pref,
          donator: Boolean(data.donator),
          color: data.color,
        };
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    },
    async fetchFavoriteMaps(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/users/${playerId}/get-favorite-maps`
        );
        this.populateFavoriteMaps(response.data);
      } catch (error) {
        console.error("Error fetching favorite maps:", error);
      }
    },
    populateFavoriteMaps(favoriteMapData) {
      if (!favoriteMapData) {
        return;
      }

      let parsedMaps = [];

      if (typeof favoriteMapData === "string") {
        try {
          parsedMaps = JSON.parse(favoriteMapData);
        } catch (e) {
          console.error("Error parsing favorite_maps JSON:", e);
          return;
        }
      } else if (Array.isArray(favoriteMapData)) {
        parsedMaps = favoriteMapData;
      }

      if (Array.isArray(parsedMaps)) {
        parsedMaps.forEach((mapData) => {
          if (mapData.favorite_index >= 0 && mapData.favorite_index <= 2) {
            this.favoriteMaps[mapData.favorite_index] = {
              id: mapData.map_id || null,
              name: mapData.map_name || "",
              index: mapData.favorite_index,
              class_type: mapData.class_type || null,
              map_soldier_tier: mapData.map_soldier_tier || null,
              map_soldier_rating: mapData.map_soldier_rating || null,
              map_demoman_tier: mapData.map_demoman_tier || null,
              map_demoman_rating: mapData.map_demoman_rating || null,
              player_record_duration: mapData.player_record_duration || null,
              record_rank: mapData.record_rank || null,
              record_placement: mapData.record_placement || null,
            };
          }
        });
      }
    },
    async fetchPlayerData(playerId) {
      try {
        const response = await axios.get(`${API_BASE_URL}/players/${playerId}`);
        if (response.data.length === 0) {
          this.playerNotFound = true;
          return;
        }
        const playerData = response.data[0];
        this.player = {
          ...this.player,
          ...playerData,
          avatar: playerData.steam_avatar || "golly.jpg",
          country: playerData.country || "unknown",
          country_code: playerData.country_code || "unknown",
          steamidconverted: this.convertSteamId(playerData.steamid),
        };
        this.updateTitle(this.player.name);
      } catch (error) {
        this.error = "Failed to fetch player data. Please try again later.";
        console.error("Error fetching player data:", error);
        this.playerNotFound = true;
      } finally {
        this.loading.ranks = false;
      }
    },
    async fetchPlayerRanks(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/ranks`
        );
        const playerRanks = response.data[0];
        this.player.overall_rank = playerRanks.overall_rank;
        this.player.soldier_rank = playerRanks.soldier_rank;
        this.player.demoman_rank = playerRanks.demoman_rank;
        this.player.overall_points =
          playerRanks.soldier_points + playerRanks.demoman_points;
        this.player.soldier_points = playerRanks.soldier_points;
        this.player.demoman_points = playerRanks.demoman_points;
      } catch (error) {
        this.error = "Failed to fetch player ranks. Please try again later.";
        console.error("Error fetching player ranks:", error);
      } finally {
        this.loading.ranks = false;
      }
    },
    async fetchRecentRecords(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/recent-records`
        );

        this.records.recentRecords = response.data.map((r) => ({
          ...r,
          durationFormatted: this.formatDuration(r.duration),
          rankClass:
            r.rank >= 1 && r.rank <= 3 ? this.getPlacementClass(r.rank) : "",
          placementClass: this.getPlacementClass(r.placement),
          medal: this.getMedal(r.rank),
          formattedDate: this.formatDate(r.date),
        }));
      } catch (error) {
        this.error =
          "Failed to fetch player recent records. Please try again later.";
        console.error("Error fetching recent records:", error);
      } finally {
        this.loading["Latest runs"] = false;
      }
    },
    async fetchPlayerStats(playerId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/players/${playerId}/stats`
        );
        const stats = response.data;
        this.stats = {
          total: {
            completion: {
              soldier:
                Math.round(
                  (stats.soldier.completion.total + Number.EPSILON) * 100
                ) / 100,
              demoman:
                Math.round(
                  (stats.demoman.completion.total + Number.EPSILON) * 100
                ) / 100,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.total,
              demoman: stats.demoman.worldRecords.total,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.total,
              demoman: stats.demoman.top10Records.total,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.total,
                G2: stats.soldier.g2Records.total,
                G3: stats.soldier.g3Records.total,
                G4: stats.soldier.g4Records.total,
              },
              demoman: {
                G1: stats.demoman.g1Records.total,
                G2: stats.demoman.g2Records.total,
                G3: stats.demoman.g3Records.total,
                G4: stats.demoman.g4Records.total,
              },
            },
          },
          map: {
            completion: {
              soldier:
                Math.round(
                  (stats.soldier.completion.map + Number.EPSILON) * 100
                ) / 100,
              demoman:
                Math.round(
                  (stats.demoman.completion.map + Number.EPSILON) * 100
                ) / 100,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.map,
              demoman: stats.demoman.worldRecords.map,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.map,
              demoman: stats.demoman.top10Records.map,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.map,
                G2: stats.soldier.g2Records.map,
                G3: stats.soldier.g3Records.map,
                G4: stats.soldier.g4Records.map,
              },
              demoman: {
                G1: stats.demoman.g1Records.map,
                G2: stats.demoman.g2Records.map,
                G3: stats.demoman.g3Records.map,
                G4: stats.demoman.g4Records.map,
              },
            },
          },
          course: {
            completion: {
              soldier:
                Math.round(
                  (stats.soldier.completion.course + Number.EPSILON) * 100
                ) / 100,
              demoman:
                Math.round(
                  (stats.demoman.completion.course + Number.EPSILON) * 100
                ) / 100,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.course,
              demoman: stats.demoman.worldRecords.course,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.course,
              demoman: stats.demoman.top10Records.course,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.course,
                G2: stats.soldier.g2Records.course,
                G3: stats.soldier.g3Records.course,
                G4: stats.soldier.g4Records.course,
              },
              demoman: {
                G1: stats.demoman.g1Records.course,
                G2: stats.demoman.g2Records.course,
                G3: stats.demoman.g3Records.course,
                G4: stats.demoman.g4Records.course,
              },
            },
          },
          bonus: {
            completion: {
              soldier:
                Math.round(
                  (stats.soldier.completion.bonus + Number.EPSILON) * 100
                ) / 100,
              demoman:
                Math.round(
                  (stats.demoman.completion.bonus + Number.EPSILON) * 100
                ) / 100,
            },
            worldRecordAmount: {
              soldier: stats.soldier.worldRecords.bonus,
              demoman: stats.demoman.worldRecords.bonus,
            },
            topTimesAmount: {
              soldier: stats.soldier.top10Records.bonus,
              demoman: stats.demoman.top10Records.bonus,
            },
            placement: {
              soldier: {
                G1: stats.soldier.g1Records.bonus,
                G2: stats.soldier.g2Records.bonus,
                G3: stats.soldier.g3Records.bonus,
                G4: stats.soldier.g4Records.bonus,
              },
              demoman: {
                G1: stats.demoman.g1Records.bonus,
                G2: stats.demoman.g2Records.bonus,
                G3: stats.demoman.g3Records.bonus,
                G4: stats.demoman.g4Records.bonus,
              },
            },
          },
        };
      } catch (error) {
        this.error = "Failed to fetch player stats. Please try again later.";
        console.error("Error fetching player stats:", error);
      } finally {
        this.loading.stats = false;
      }
    },
    async fetchSharedTimes(playerId) {
      try {
        const soldierTtCount = this.stats.map.topTimesAmount.soldier;
        const demomanTtCount = this.stats.map.topTimesAmount.demoman;
        const stats = {
          soldier_maps_count: soldierTtCount,
          demoman_maps_count: demomanTtCount,
        };
        const fetchSharedData = async (classType, placement, isGroupOne) => {
          try {
            const response = await axios.get(
              `${API_BASE_URL}/players/shared-names/${playerId}/${classType}/${placement}`
            );
            return response.data;
          } catch (e) {
            console.error(
              `${classType} ${
                isGroupOne ? "group1" : "top time"
              } fetch failed:`,
              e
            );
            return [];
          }
        };
        const processClassData = async (
          classType,
          countProperty,
          sharedTypeProperty,
          groupOnePlacement
        ) => {
          const placement = stats[countProperty] === 0 ? groupOnePlacement : 10;
          const isGroupOne = stats[countProperty] === 0;
          this.player[sharedTypeProperty] = isGroupOne
            ? "Shared Group 1s"
            : "Shared Top Times";
          return await fetchSharedData(classType, placement, isGroupOne);
        };
        const [sharedSoldiers, sharedDemomans] = await Promise.all([
          processClassData(
            "soldier",
            "soldier_maps_count",
            "shared_soldier_type",
            11
          ),
          processClassData(
            "demoman",
            "demoman_maps_count",
            "shared_demoman_type",
            11
          ),
        ]);
        this.sharedTimesSoldier = Object.entries(sharedSoldiers).map(
          ([_, data]) => ({
            playerId: data.player_id,
            count: data.record_count,
            playerName: data.player_name,
            avatar: data.avatar,
          })
        );
        this.sharedTimesDemoman = Object.entries(sharedDemomans).map(
          ([_, data]) => ({
            playerId: data.player_id,
            count: data.record_count,
            playerName: data.player_name,
            avatar: data.avatar,
          })
        );
      } catch (error) {
        console.error("Error fetching shared top times:", error);
        throw error;
      } finally {
        this.loading.shared = false;
      }
    },
    formatDuration(duration) {
      return formatDuration(duration);
    },
    formatDate(unixTimestamp) {
      return formatDate(unixTimestamp);
    },
  },
  beforeDestroy() {
    clearTimeout(this.debounceTimer);
    this._cachedRankInfo = null;
  },
};
</script>

<style scoped>
.return-button {
  background: var(--color-box);
  color: var(--color-text);
}
.return-button:hover {
  background: rgba(74, 111, 165, 0.8) !important;
  color: var(--color-text);
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
}
.golden-border {
  border-radius: 12px;
  border: 2px solid gold;
  animation: goldenGlow 3s infinite;
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
.vue-apexcharts {
  min-height: 200px !important;
  box-shadow: 0 0px 20px rgba(0, 0, 0, 0.75);
}
.avatar {
  width: 96px;
  height: 96px;
  border: 3px solid var(--color-primary);
}
.golden-avatar {
  width: 96px;
  height: 96px;
  border: 3px solid gold;
}
.shared-avatar {
  width: 25px;
  height: 25px;
  border: 2px solid var(--color-primary);
}
.shared-row {
  border: 1px solid var(--color-border-soft);
  background: rgba(255, 255, 255, 0.05);
  box-shadow: 0 0px 15px rgb(0, 0, 0, 0.5);
  color: var(--color-text-clickable);
  cursor: pointer;
}
.shared-row-name {
  white-space: nowrap;
  overflow: hidden;
  width: 80%;
}
.shared-count {
  color: var(--color-text);
  font-weight: bold;
}
.profile-info {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  max-width: 100%;
}
.player-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  color: var(--color-text);
  font-size: 2.5rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}
.flag-icon {
  width: 24px;
  height: 18px;
  vertical-align: middle;
  border-radius: 2px;
}
.banner-block {
  background: rgba(255, 255, 255, 0.05);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  border-radius: 10px;
}
.shared-row:hover,
.record-item:hover {
  transform: scale(1.03);
  background-color: var(--color-primary);
}
.stat-item {
  background: rgba(255, 255, 255, 0.05);
  box-shadow: 0 0px 15px rgb(0, 0, 0, 0.5);
  border: 1px solid var(--color-border-soft);
  border-collapse: collapse;
  border-radius: 2px;
  padding: 10px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}
.banner-block:hover {
  transform: translateY(-2px);
}
.stat-block {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}
.stat-block .card-title,
.banner-block .card-title {
  color: #aaa;
  font-weight: bold;
}
.stat-block .card-text,
.banner-block .card-text {
  font-size: 1.4rem;
  font-weight: bold;
}
.player-stats {
  color: var(--color-text) !important;
}
.player-shared-stats {
  color: var(--color-text) !important;
  font-size: 14px !important;
  font-weight: bold !important;
}
.section-header {
  color: var(--color-text);
  font-size: 20px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 0;
}
.button-container-wrapper {
  display: flex;
  width: 100%;
}

.allruns-button-container,
.filter-button-container {
  display: flex;
  justify-content: center;
  width: 50%;
}

.global-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  border-radius: 8px;
  max-width: 150px;
}

.filter-section {
  padding: 20px;
  width: 100%;
  max-width: 100%;
}

.filter-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 0px 15px rgba(0, 0, 0, 0.75);
  border: 1px solid var(--color-border-soft);
  width: 100%;
  box-sizing: border-box;
}

.filter-columns {
  display: flex;
  gap: 48px;
  align-items: flex-start;
  justify-content: center;
  padding-bottom: 15px;
}

.no-padding-bottom {
  padding-bottom: 0;
}

.no-margin-top {
  margin-top: 0 !important;
}

.filter-group {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.filter-title {
  font-weight: bold;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-align: center;
}

.class-filter-container,
.type-filter-container {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: center;
}

.filter-button {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border: 2px solid rgba(68, 68, 68, 0.3);
  border-radius: 8px;
  padding: 8px 16px;
  margin: 0 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: bold;
  font-size: 13px;
  text-transform: capitalize;
  box-shadow: 0 0px 15px rgb(0, 0, 0, 0.35);
}

.filter-button.active {
  background: rgba(74, 111, 165, 0.8);
  border-color: var(--color-border, #444);
}

.filter-button:hover:not(.active) {
  background: rgba(74, 111, 165, 0.8);
}

.gain-loss-filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.group-filter-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
}

.group-filters {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.group-checkbox {
  display: flex;
  align-items: center;
  cursor: pointer;
  margin: 0;
}

.group-checkbox input {
  display: none;
}

.group-checkbox input:checked + .group-badge {
  border-color: var(--color-border, #444);
  box-shadow: 0 0 0 1px var(--color-border, #444);
}

.group-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
  font-size: 12px;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  min-width: 28px;
  text-align: center;
}

.group-badge.group-wr {
  background: #ffd700 !important;
  color: var(--color-dark) !important;
}

.group-badge.group-tt {
  background: #30869b !important;
  color: var(--color-dark) !important;
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

.filter-actions {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-top: 25px;
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.164);
  border: 1px solid var(--color-border-soft);
  font-weight: bold;
}

.btn-secondary:hover {
  background: rgba(74, 111, 165, 0.8) !important;
}

.card-header {
  margin-bottom: 0;
  border-bottom: none;
  color: var(--color-text);
  border-top-left-radius: 10px !important;
  border-top-right-radius: 10px !important;
}
.card-header.tabs-header .button-group {
  width: 100%;
  display: flex;
  padding: 0;
  margin: 0;
}
.button-group {
  border: none;
}
.card-header.tabs-header .btn {
  flex: 1;
  margin: 0;
  padding: 10px;
  text-align: center;
}
.card-body,
.tabs-content,
.records-section,
.card-map-body {
  width: 100%;
  padding: 0;
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}

.nav-bar {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  height: 100%;
  width: 5%;
  background: rgba(172, 172, 172, 0);
  cursor: pointer;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-bar:hover {
  background: rgba(172, 172, 172, 0.1);
}

.left-nav-bar {
  left: 1px;
}

.left-nav-bar::before {
  content: "";
  width: 0;
  height: 0;
  border-top: 8px solid transparent;
  border-bottom: 8px solid transparent;
  border-right: 9px solid rgba(255, 255, 255, 0.3);
}

.right-nav-bar {
  right: 1px;
}

.right-nav-bar::before {
  content: "";
  width: 0;
  height: 0;
  border-top: 8px solid transparent;
  border-bottom: 8px solid transparent;
  border-left: 9px solid rgba(255, 255, 255, 0.3);
}

.stat-type-label {
  color: var(--color-text);
  font-size: 1rem;
  font-weight: bold;
  text-align: center;
  margin-bottom: 0;
  text-transform: capitalize;
}

.card-body {
  position: relative;
  padding: 20px;
}
.chart-body {
  padding: 0px 10px !important;
  border-radius: 0 0 10px 10px !important;
  margin-bottom: 20px;
}
.card-row {
  padding: 0px 15px 10px 15px;
  display: flex;
}
.rank-card-body {
  padding: 15px;
}
.shared-card-row {
  padding: 5px 5px 5px 5px;
}
.list-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0;
  margin: 0;
  width: 100%;
}
.list-group-item.record-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border: none;
  border-radius: 8px;
  padding: 12px 16px;
  border: 1px solid var(--color-border-soft);
  width: 100%;
  box-sizing: border-box;
  box-shadow: 0 0px 10px rgba(0, 0, 0, 0.75);
}
.list-group-item.record-item:hover {
  background: rgba(74, 111, 165, 0.8) !important;
  cursor: pointer;
}
.card-body,
.card-header {
  padding: 10px;
  padding-bottom: 0;
}
.records-card {
  border-radius: 10px !important;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}
.completion-boxes,
.records-boxes {
  display: flex !important;
  gap: 10px !important;
}
.completion-boxes .card,
.records-boxes .card {
  flex: 1;
}
.record-class-map {
  display: flex;
  align-items: center;
}
.shared-avatar {
  width: 25px;
  height: 25px;
  border: 2px solid var(--color-primary);
}
.record-map {
  color: var(--color-text-clickable);
  font-size: 15px;
  font-weight: bold;
}
.record-time-detail {
  min-width: 180px;
}
.record-detail {
  font-size: 14px;
  color: #aaa;
}
.record-duration {
  font-weight: bold;
  color: var(--color-text);
  font-size: 15px;
  white-space: nowrap;
}
.record-rank {
  min-width: 40px;
  text-align: right;
  white-space: nowrap;
}
.record-date {
  white-space: nowrap;
}
.record-item {
  background: var(--color-border-soft);
  border-radius: 5px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
}
.record-time-detail-grid {
  display: grid;
  grid-template-rows: auto auto auto;
  gap: 2px;
  justify-items: end;
  min-width: 180px;
}
.pagination-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  padding-bottom: 10px;
}

.pagination-info {
  color: var(--color-text-soft);
  font-weight: bold;
  text-align: center;
  white-space: nowrap;
  flex: 1;
}

.pagination-side {
  flex: 1;
  display: flex;
  justify-content: flex-start;
}

.pagination-side:last-child {
  justify-content: flex-end;
}

.records-section {
  position: relative;
  min-height: 630px !important;
  padding-bottom: 80px !important;
}

.records-section .pagination-controls {
  position: absolute;
  bottom: 10px;
  left: 0;
  width: 100%;
  padding: 10px;
  box-sizing: border-box;
}
.date-header {
  color: var(--color-text);
  padding-top: 5px;
}
.placement-change-indicator {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  font-size: 0.9rem;
}

.points-change {
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 0.85rem;
}

.points-gain {
  color: #51cf66;
  background: rgba(81, 207, 102, 0.1);
}

.points-loss {
  color: #ff6b6b;
  background: rgba(255, 107, 107, 0.1);
}

.points-neutral {
  color: #6c757d;
  background: rgba(108, 117, 125, 0.1);
}

.placement-arrows {
  display: flex;
  align-items: center;
  gap: 4px;
  color: var(--color-text-soft);
}

.placement-arrow {
  font-size: 0.8rem;
}

.old-rank {
  color: var(--color-text-soft);
  text-decoration: line-through;
}

.new-rank {
  font-weight: bold;
}

.record-item.gained-placement {
  border-left: 3px solid #51cf66;
}
.record-item.lost-placement {
  border-left: 3px solid #ff6b6b;
}

.record-time-detail.gained-placement,
.record-time-detail.lost-placement {
  flex-direction: column;
  gap: 4px;
}
.placement-gold {
  color: #ffd700;
  font-weight: bold;
}
.placement-silver {
  color: #c0c0c0;
  font-weight: bold;
}
.placement-bronze {
  color: #cd7f32;
  font-weight: bold;
}
.placement-blue {
  color: #30869b;
  font-weight: bold;
}
.placement-g1 {
  color: #ff9797af !important;
  font-weight: bold;
}
.placement-g2 {
  color: #f7cf84 !important;
  font-weight: bold;
}
.placement-g3 {
  color: #d27d2dbb !important;
  font-weight: bold;
}
.placement-g4 {
  color: #b3b3b3ce !important;
  font-weight: bold;
}
.placement-g5 {
  color: var(--color-text) !important;
  font-weight: bold;
}
.class-icon {
  width: 36px;
  height: 36px;
}
.tabs-header .btn-group {
  width: 100%;
}
.tabs-header .btn {
  flex: 1;
}
.chart-container {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  padding: 0 5px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  margin-bottom: 20px;
  color: #fff;
}
.chart-header {
  margin-bottom: 10px;
  margin-top: 10px;
  color: var(--color-text);
}
.stats-boxes {
  flex: 1;
  padding: 10px;
}
.tabs-container {
  flex: 2;
  padding: 10px;
}
.record-item {
  font-size: 0.9rem;
  padding: 8px;
}
.clickable {
  cursor: pointer;
}
.clickable:hover {
  background: rgba(74, 111, 165, 0.8) !important;
}
.clickable-text {
  color: var(--color-text-clickable) !important;
}
.rank-name {
  color: var(--color-text);
  font-weight: bold;
  font-size: large;
  margin-bottom: 10px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
}
.rank-dropdown {
  position: absolute;
  background: var(--color-dark);
  border: 1px solid var(--color-border);
  z-index: 10;
  border-radius: 8px;
  left: 50%;
  transform: translateX(-50%);
  padding: 5px;
}
.rank-option {
  padding: 5px;
  cursor: pointer;
  color: var(--color-text);
}
.rank-option:hover {
  background: var(--color-primary);
}
.rank-option.selected {
  background: var(--color-primary);
  border-radius: 4px;
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

.map-section {
  padding: 15px 0;
  display: flex;
  justify-content: center;
}

.map-container {
  width: fit-content;
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.map-section-title {
  font-size: 1.75rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 30px;
  color: var(--color-text);
}

.map-grid {
  display: grid;
  gap: 30px;
  margin-top: 20px;
  margin-bottom: 20px;
  justify-content: center;
  max-width: 1200px;
}

.map-grid:has(.map-card:nth-child(1):last-child) {
  grid-template-columns: 1fr;
  width: 600px;
}

.map-grid:has(.map-card:nth-child(2):last-child) {
  grid-template-columns: repeat(2, 1fr);
  width: 800px;
}

.map-grid:has(.map-card:nth-child(3)) {
  grid-template-columns: repeat(3, minmax(300px, 1fr));
  width: 1200px;
}

.author-map-grid {
  display: grid;
  gap: 30px;
  margin: 20px auto;
  justify-content: center;
  max-width: 1200px;
}

.author-map-grid:has(.map-card:nth-child(1):last-child) {
  grid-template-columns: 1fr;
  width: 500px;
}

.author-map-grid:has(.map-card:nth-child(2):last-child) {
  grid-template-columns: repeat(2, 1fr);
  width: 800px;
}

.author-map-grid:has(.map-card:nth-child(3)) {
  grid-template-columns: repeat(3, 1fr);
  width: 1200px;
}

.author-card {
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.class-icon-container {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 2;
  display: flex;
  gap: 8px;
}

.author-class-icon {
  width: 50px;
  height: 50px;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 50%;
  padding: 8px;
  border: 2px solid rgba(74, 111, 165, 0.3);
}

.author-class-icon.dual-icon {
  width: 40px;
  height: 40px;
  padding: 6px;
}

.author-class-icon {
  width: 50px;
  height: 50px;
  background: rgba(0, 0, 0, 0.7);
  border-radius: 50%;
  padding: 8px;
  border: 2px solid rgba(74, 111, 165, 0.3);
}
.map-card {
  box-shadow: 0 0 20px rgba(0, 0, 0);
  background: linear-gradient(
      to bottom,
      rgba(255, 255, 255, 0.15) 0%,
      rgba(255, 255, 255, 0.288) 20%,
      rgba(255, 255, 255, 0.15) 40%,
      rgba(255, 255, 255, 0) 100%
    ),
    rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  padding: 10px;
  padding-bottom: 25px;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  text-decoration: none;
  min-height: 300px;
}

.map-card:hover {
  transform: scale(1.05);
  box-shadow: 0 0 40px rgba(102, 126, 234, 0.6);
  cursor: pointer;
}

.map-card-subtitle {
  font-size: 1.75rem;
  font-weight: 700;
  text-align: center;
  color: #ffffff;
  margin-bottom: 35px;
  background: linear-gradient(135deg, #ffffff 0%, var(--color-primary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
  z-index: 10;
  text-transform: capitalize;
}

.map-card h3 {
  font-size: 1.5rem;
  margin-bottom: 15px;
  color: #ffffff;
  text-align: center;
}

.map-header {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
  margin-top: 50px;
}

.map-header-nonmargin {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
}

.map-name {
  color: white;
  font-size: 16px;
  font-weight: bold;
  margin: 0;
  text-shadow: 2px 3px 1px rgba(0, 0, 0, 0.4);
  line-height: 1.2;
}

.map-compact-ratings-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.map-rating-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.map-rating-pills {
  display: flex;
  gap: 8px;
}

.map-rating-pill {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.8rem;
  color: var(--color-dark);
  text-shadow: none;
  min-width: 40px;
  text-align: center;
}

.map-rating-label {
  font-size: 0.75rem;
  color: var(--color-text);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.8;
}

.map-date-added {
  text-align: center;
  font-size: 0.85rem;
  margin-top: 0.75rem;
  color: var(--color-text-soft);
  font-style: italic;
  margin-top: auto;
}

.record-row {
  margin-top: 20px;
  text-align: center;
  font-size: 1rem;
  color: var(--color-text);
  font-weight: 600;
  letter-spacing: 0.5px;
}

.map-class-icon {
  height: 24px;
  width: 24px;
}

.map-tier-color.tier-0 {
  background: rgba(51, 51, 51, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-1 {
  background: rgba(110, 208, 246, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-2 {
  background: rgba(86, 179, 233, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-3 {
  background: rgba(69, 184, 173, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-4 {
  background: rgba(101, 193, 139, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-5 {
  background: rgba(163, 217, 119, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-6 {
  background: rgba(243, 230, 131, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-7 {
  background: rgba(246, 194, 103, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-8 {
  background: rgba(240, 141, 91, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-9 {
  background: rgba(230, 105, 94, 0.5);
  color: var(--color-text);
}
.map-tier-color.tier-10 {
  background: rgba(214, 69, 69, 0.5);
  color: var(--color-text);
}

.map-rating-color.rating-1 {
  background: rgba(148, 196, 125, 0.5);
  color: var(--color-text);
}
.map-rating-color.rating-2 {
  background: rgba(171, 208, 153, 0.5);
  color: var(--color-text);
}
.map-rating-color.rating-3 {
  background: rgba(195, 178, 147, 0.5);
  color: var(--color-text);
}
.map-rating-color.rating-4 {
  background: rgba(224, 102, 102, 0.5);
  color: var(--color-text);
}

.map-empty-map {
  justify-content: center;
  align-items: center;
  background: rgba(255, 255, 255, 0.05) !important;
  border: 2px dashed rgba(255, 255, 255, 0.3);
}

.map-empty-map h3 {
  color: rgba(255, 255, 255, 0.6);
  text-align: center;
}

.map-search-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.map-search-container {
  background: var(--color-dark);
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  position: relative;
  min-width: 300px;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.search-icon {
  position: absolute;
  left: 16px;
  color: #888;
  z-index: 2;
}

.search-input {
  width: 100%;
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
  background: var(--color-box);
  border: 1px solid rgba(68, 68, 68, 0.3);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
  margin-bottom: 15px;
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

.search-results-dropdown li.disabled {
  cursor: not-allowed;
  opacity: 0.5;
  background: var(--color-box);
}

.search-results-dropdown li.disabled:hover {
  background: var(--color-box);
  transform: none;
}

.search-results-dropdown h6 {
  margin: 12px 16px 8px;
  font-size: 12px;
  font-weight: 700;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.search-buttons {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.cancel-button {
  background: var(--color-box);
  color: var(--color-text);
  border: 1px solid var(--color-border-soft);
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.cancel-button:hover {
  color: var(--color-text);
  background: var(--color-primary);
}

.class-selection {
  margin-bottom: 20px;
}

.class-selection h3 {
  margin: 0 0 10px 0;
  font-size: 16px;
  color: #ffffff;
}

.class-icons {
  display: flex;
  gap: 15px;
  justify-content: center;
}

.class-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 10px;
  border: 2px solid var(--color-border-soft);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 80px;
  background: var(--color-box);
}

.class-option:hover {
  border-color: var(--color-primary);
  background: rgba(74, 111, 165, 0.8);
}

.class-option.active {
  border-color: var(--color-primary);
  background: rgba(74, 111, 165, 0.8);
}

.class-icon {
  width: 32px;
  height: 32px;
  margin-bottom: 5px;
}

.class-option span {
  font-size: 12px;
  color: #ffffff;
  text-transform: capitalize;
}

.class-warning-popup {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #ff4444;
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  z-index: 1001;
  pointer-events: none;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}

.rotw-section {
  padding: 15px 0;
  display: flex;
  justify-content: center;
}

.rotw-container {
  width: fit-content;
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.rotw-section-title {
  font-size: 1.75rem;
  font-weight: 700;
  text-align: center;
  margin-bottom: 30px;
  color: var(--color-text);
}
.rotw-count {
  font-size: 1.5rem;
  color: var(--color-text-soft);
  font-weight: normal;
}
.rotw-grid {
  display: grid;
  gap: 30px;
  margin-top: 20px;
  margin-bottom: 20px;
  justify-content: center;
  max-width: 1250px;
  width: 100%;
  grid-template-columns: repeat(2, minmax(590px, 1fr));
}

.rotw-grid:has(.rotw-card:nth-child(1):last-child) {
  grid-template-columns: minmax(750px, 1fr);
}

.rotw-grid:has(.rotw-card:nth-child(1):last-child) .rotw-card-active {
  transform: scale(1.416);
}

.rotw-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  padding: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  transition: all 0.3s ease;
  min-width: 300px;
  max-width: 750px;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  z-index: 1;
}

.rotw-card-active {
  z-index: 100;
  position: relative;
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
  background: var(--color-primary-dark);
}

.rotw-card-active.rotw-card-left {
  transform: translateX(52%) scale(1.8);
}

.rotw-card-active.rotw-card-right {
  transform: translateX(-52%) scale(1.8);
}

.rotw-card-active .video-scale-wrapper iframe {
  pointer-events: auto;
}

.rotw-video-embed {
  position: relative;
  width: 100%;
  height: 0;
  padding-bottom: 56.25%;
  overflow: hidden;
  border-radius: 8px;
}

.rotw-video-embed iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 8px;
}

.video-scale-wrapper {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transform: scale(0.75);
  transform-origin: top left;
}

.video-scale-wrapper iframe {
  width: calc(100% / 0.75);
  height: calc(100% / 0.75);
  border-radius: 8px;
}

.rotw-video-info {
  padding-top: 10px;
  text-align: center;
}

.rotw-video-info h5 {
  margin: 0;
  color: var(--color-text);
  font-size: 1.1rem;
}

.rotw-video-info p {
  margin: 5px 0 0;
  color: #aaa;
  font-size: 0.9rem;
}

.load-more-container {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 20px;
}

@media (max-width: 768px) {
  .rotw-grid,
  .map-grid,
  .author-map-grid {
    grid-template-columns: 1fr !important;
    width: 100% !important;
    max-width: none !important;
    overflow-x: hidden;
    gap: 15px;
  }
  .rotw-section-title {
    font-size: 1.5rem;
  }
  .rotw-count {
    font-size: 1rem;
  }
  .rotw-card-active,
  .map-card {
    transform: none !important;
    width: 100%;
    max-width: 100%;
  }
  .nav-bar {
    height: 50px;
    width: 30px;
    background: rgba(172, 172, 172, 0.4);
  }

  .left-nav-bar {
    left: 1px;
  }

  .right-nav-bar {
    right: 1px;
  }
  .record-time-detail {
    min-width: 0;
    flex-shrink: 1;
  }

  .placement-change-indicator {
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    min-width: 0;
  }

  .placement-arrows {
    font-size: 0.8rem;
    white-space: nowrap;
  }

  .points-change {
    font-size: 0.75rem;
    padding: 1px 4px;
  }

  .record-detail.record-date {
    font-size: 0.75rem;
    margin-top: 2px;
  }
  .record-item .d-flex {
    min-width: 0;
  }

  .record-class-map {
    flex: 1;
    min-width: 0;
    overflow: hidden;
  }

  .record-map {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .record-time-detail .d-flex.flex-column {
    align-items: flex-end;
    min-width: 0;
  }
  .map-section {
    padding: 15px 10px;
  }

  .map-container,
  .rotw-container {
    padding: 10px;
    width: 100%;
    box-sizing: border-box;
  }

  .map-section-title {
    font-size: 1.5rem;
    margin-bottom: 20px;
  }

  .map-card {
    border-radius: 15px;
    padding: 15px;
    padding-bottom: 20px;
    min-height: 280px;
  }

  .map-card:hover {
    transform: scale(1.01);
    box-shadow: 0 0 30px rgba(102, 126, 234, 0.6);
  }

  .map-card-subtitle {
    font-size: 1.3rem;
    margin-bottom: 20px;
  }

  .map-card h3 {
    font-size: 1.1rem;
  }

  .map-rating-pill {
    padding: 3px 8px;
    border-radius: 15px;
    font-size: 0.7rem;
    min-width: 30px;
  }

  .map-rating-label {
    font-size: 0.65rem;
  }

  .record-row {
    margin-top: 10px;
    font-size: 0.8rem;
  }

  .map-class-icon {
    height: 18px;
    width: 18px;
  }

  .map-search-overlay {
    padding: 20px;
  }

  .map-search-container {
    padding: 15px;
    margin: 10px;
    max-height: 85vh;
    width: 100%;
    max-width: 400px;
  }

  .search-icon {
    left: 10px;
  }

  .search-input {
    padding: 10px 10px 10px 35px;
    font-size: 16px;
  }

  .search-results-dropdown {
    max-height: 150px;
  }

  .search-results-dropdown li {
    padding: 8px 10px;
    font-size: 14px;
  }

  .cancel-button {
    padding: 8px 16px;
    min-width: 70px;
  }

  .class-icons {
    gap: 10px;
  }

  .class-option {
    min-width: 70px;
    padding: 8px;
  }

  .class-icon {
    width: 28px;
    height: 28px;
  }

  .class-option span {
    font-size: 11px;
  }
}
</style>
