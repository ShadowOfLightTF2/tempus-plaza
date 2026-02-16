<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container py-4">
      <div class="page-header">
        <h1 class="page-title">
          <span class="title-icon">🔍</span>
          Map Page
        </h1>
        <p class="page-subtitle">Records and information about the map</p>
      </div>
      <hr class="row-divider" style="width: 100%" />
      <div
        v-if="mapNotFound"
        class="alert alert-warning map-not-found text-center"
      >
        <p class="mb-3">No map found</p>
        <button @click="returnToAllMaps" class="btn return-button">
          Return to all maps
        </button>
      </div>
      <div v-else>
        <div class="map-name-container">
          <SmartLink
            tag="div"
            :to="{ name: 'LookupMap', params: { mapId: mapId } }"
            class="lookup-map-banner fancy-hover"
            :style="{
              backgroundImage: `linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/map-backgrounds/${
                map?.name || mapName
              }.jpg')`,
              backgroundSize: 'cover',
              backgroundPosition: 'center',
            }"
          >
            <div class="lookup-banner-content">
              <div class="lookup-map-main">
                <h1
                  class="lookup-map-name"
                  v-html="mapName || 'Selected Map'"
                ></h1>
                <div v-if="map" class="map-primary-info">
                  <div class="map-tiers">
                    <div class="tier-group">
                      <img
                        src="/icons/soldier.png"
                        alt="Soldier"
                        class="tier-class-icon"
                      />
                      <span class="tier-text">T{{ map.soldier_tier }}</span>
                      <span class="rating-text">R{{ map.soldier_rating }}</span>
                    </div>
                    <span class="tier-divider">•</span>
                    <div class="tier-group">
                      <img
                        src="/icons/demoman.png"
                        alt="Demoman"
                        class="tier-class-icon"
                      />
                      <span class="tier-text">T{{ map.demoman_tier }}</span>
                      <span class="rating-text">R{{ map.demoman_rating }}</span>
                    </div>
                  </div>
                  <div
                    v-if="map.intended_class"
                    :class="[
                      'intended-class-display',
                      { 'no-circle': map.intended_class === 5 },
                    ]"
                  >
                    <div v-if="map.intended_class === 5" class="both-classes">
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
                      v-else-if="isIntendedClass('soldier')"
                      src="/icons/soldier.png"
                      alt="Soldier"
                      class="intended-class-icon"
                    />
                    <img
                      v-else-if="isIntendedClass('demoman')"
                      src="/icons/demoman.png"
                      alt="Demoman"
                      class="intended-class-icon"
                    />
                  </div>
                </div>
              </div>
              <div v-if="map" class="lookup-map-secondary">
                <span class="secondary-stat"
                  >{{ map.course_count }} courses</span
                >
                <span class="stat-separator">•</span>
                <span class="secondary-stat"
                  >{{ map.bonus_count }} bonuses</span
                >
                <span class="stat-separator">•</span>
                <span class="secondary-stat"
                  >{{ map.soldier_completion_count }} [S] completions</span
                >
                <span class="stat-separator">•</span>
                <span class="secondary-stat"
                  >{{ map.demoman_completion_count }} [D] completions</span
                >
                <span class="stat-separator">•</span>
                <span class="secondary-stat"
                  >Added {{ formatDate(map.date_added) }}</span
                >
              </div>
              <div v-else class="loading-ranks">
                <div class="loading-spinner"></div>
                <span>Loading...</span>
              </div>
              <a
                :href="`https://static.tempus2.xyz/tempus/server/maps/${mapName}.bsp.bz2`"
                class="download-btn"
                :download="`${mapName}.bsp.bz2`"
                title="Download Map"
                @click.stop
              >
                <i class="bi bi-download"></i>
              </a>
            </div>
          </SmartLink>
        </div>
        <hr class="row-divider" style="width: 100%" />
        <div class="collapsible-header" @click="toggleMapBanner">
          <h5 class="video-section-title mb-0">
            🗺️ More Map Information
            <i
              class="bi"
              :class="showMapBanner ? 'bi-chevron-up' : 'bi-chevron-down'"
              style="margin-left: auto"
            ></i>
          </h5>
        </div>
        <div v-if="showMapBanner" class="collapsible-content">
          <div
            v-if="map"
            class="card map-banner mb-4"
            :style="{
              background: `
                linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
                radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                url('/map-backgrounds/${map.name}.jpg') center/cover no-repeat
              `,
              backgroundBlendMode: 'multiply, normal, normal',
              backgroundSize: 'cover, cover, cover',
              backgroundPosition: 'center, center, center',
            }"
          >
            <div class="row g-0 banner-content">
              <div class="col-md-12 d-flex flex-column align-items-center p-4">
                <div class="map-info text-center w-100">
                  <div class="row map-overview w-100">
                    <div class="col-lg-3 col-md-6">
                      <div
                        class="card stat-block h-100"
                        :class="{
                          'stat-block-highlighted': isIntendedClass('soldier'),
                        }"
                      >
                        <div class="card-body text-center p-3">
                          <div class="stat-icon mb-2">
                            <i class="bi bi-bar-chart"></i>
                          </div>
                          <h5 class="card-title mb-2">Soldier Tier</h5>
                          <p
                            class="card-text main-tier-color stat-value"
                            :class="'tier-' + map.soldier_tier"
                          >
                            T{{ map.soldier_tier }}
                          </p>
                          <div class="completion-count">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ map.soldier_completion_count || 0 }} completions
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div
                        class="card stat-block h-100"
                        :class="{
                          'stat-block-highlighted': isIntendedClass('soldier'),
                        }"
                      >
                        <div class="card-body text-center p-3">
                          <div class="stat-icon mb-2">
                            <i class="bi bi-star-fill"></i>
                          </div>
                          <h5 class="card-title mb-2">Soldier Rating</h5>
                          <p
                            class="card-text main-rating-color stat-value"
                            :class="'rating-' + map.soldier_rating"
                          >
                            R{{ map.soldier_rating }}
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div
                        class="card stat-block h-100"
                        :class="{
                          'stat-block-highlighted': isIntendedClass('demoman'),
                        }"
                      >
                        <div class="card-body text-center p-3">
                          <div class="stat-icon mb-2">
                            <i class="bi bi-bar-chart"></i>
                          </div>
                          <h5 class="card-title mb-2">Demoman Tier</h5>
                          <p
                            class="card-text main-tier-color stat-value"
                            :class="'tier-' + map.demoman_tier"
                          >
                            T{{ map.demoman_tier }}
                          </p>
                          <div class="completion-count">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ map.demoman_completion_count || 0 }} completions
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div
                        class="card stat-block h-100"
                        :class="{
                          'stat-block-highlighted': isIntendedClass('demoman'),
                        }"
                      >
                        <div class="card-body text-center p-3">
                          <div class="stat-icon mb-2">
                            <i class="bi bi-star-fill"></i>
                          </div>
                          <h5 class="card-title mb-2">Demoman Rating</h5>
                          <p
                            class="card-text main-rating-color stat-value"
                            :class="'rating-' + map.demoman_rating"
                          >
                            R{{ map.demoman_rating }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <hr class="row-divider" style="width: 85%" />
                  <div class="row w-100" v-if="courses.length > 0">
                    <div class="col-md-12">
                      <h2 class="section-header mb-4">
                        <i class="me-2"></i>🚩 Courses
                      </h2>
                      <div class="courses-block">
                        <div
                          v-for="course in courses"
                          :key="course.id"
                          class="course-item"
                        >
                          <div class="card stat-block h-100">
                            <div class="card-body text-center p-3">
                              <h5 class="card-title mb-3">
                                Course {{ course.index }}
                              </h5>
                              <div class="compact-ratings-grid">
                                <div class="rating-section">
                                  <div class="rating-label">Soldier</div>
                                  <div class="rating-pills">
                                    <span
                                      class="rating-pill tier-color"
                                      :class="'tier-' + course.soldier_tier"
                                    >
                                      T{{ course.soldier_tier }}
                                    </span>
                                    <span
                                      class="rating-pill rating-color"
                                      :class="'rating-' + course.soldier_rating"
                                    >
                                      R{{ course.soldier_rating }}
                                    </span>
                                  </div>
                                  <div class="completion-count-small">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ course.soldier_completion_count || 0 }}
                                  </div>
                                </div>
                                <div class="rating-section">
                                  <div class="rating-label">Demoman</div>
                                  <div class="rating-pills">
                                    <span
                                      class="rating-pill tier-color"
                                      :class="'tier-' + course.demoman_tier"
                                    >
                                      T{{ course.demoman_tier }}
                                    </span>
                                    <span
                                      class="rating-pill rating-color"
                                      :class="'rating-' + course.demoman_rating"
                                    >
                                      R{{ course.demoman_rating }}
                                    </span>
                                  </div>
                                  <div class="completion-count-small">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ course.demoman_completion_count || 0 }}
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <hr
                    class="row-divider"
                    style="width: 85%"
                    v-if="courses.length > 0"
                  />
                  <div class="row w-100" v-if="bonuses.length > 0">
                    <div class="col-md-12">
                      <h2 class="section-header mb-4">⭐ Bonuses</h2>
                      <div class="bonuses-block">
                        <div
                          v-for="bonus in bonuses"
                          :key="bonus.id"
                          class="bonus-item"
                        >
                          <div class="card stat-block h-100">
                            <div class="card-body text-center p-3">
                              <h5 class="card-title mb-3">
                                Bonus {{ bonus.index }}
                              </h5>
                              <div class="compact-ratings-grid">
                                <div class="rating-section">
                                  <div class="rating-label">Soldier</div>
                                  <div class="rating-pills">
                                    <span
                                      class="rating-pill tier-color"
                                      :class="'tier-' + bonus.soldier_tier"
                                    >
                                      T{{ bonus.soldier_tier }}
                                    </span>
                                    <span
                                      class="rating-pill rating-color"
                                      :class="'rating-' + bonus.soldier_rating"
                                    >
                                      R{{ bonus.soldier_rating }}
                                    </span>
                                  </div>
                                  <div class="completion-count-small">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ bonus.soldier_completion_count || 0 }}
                                  </div>
                                </div>
                                <div class="rating-section">
                                  <div class="rating-label">Demoman</div>
                                  <div class="rating-pills">
                                    <span
                                      class="rating-pill tier-color"
                                      :class="'tier-' + bonus.demoman_tier"
                                    >
                                      T{{ bonus.demoman_tier }}
                                    </span>
                                    <span
                                      class="rating-pill rating-color"
                                      :class="'rating-' + bonus.demoman_rating"
                                    >
                                      R{{ bonus.demoman_rating }}
                                    </span>
                                  </div>
                                  <div class="completion-count-small">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ bonus.demoman_completion_count || 0 }}
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <hr
                    class="row-divider"
                    style="width: 85%"
                    v-if="bonuses.length > 0"
                  />
                  <div class="row w-100">
                    <div class="col-md-12">
                      <h2 class="section-header mb-4">
                        <i class="bi bi-people-fill me-2"></i>Authors
                      </h2>
                      <div class="authors-wrapper">
                        <div class="authors-block">
                          <div
                            v-for="author in authors"
                            :key="author.author_id"
                            class="author-card"
                          >
                            <div class="author-avatar-wrapper">
                              <SmartLink
                                v-if="author.player && author.player.id"
                                :to="{
                                  name: 'PlayerPage',
                                  params: { playerId: author.player.id },
                                  hash: '#authored-maps',
                                }"
                              >
                                <img
                                  :src="
                                    author &&
                                    author.player &&
                                    author.player.steam_avatar
                                      ? `${author.player.steam_avatar}`
                                      : '/avatars/default-avatar.jpg'
                                  "
                                  alt="Author Avatar"
                                  class="author-avatar"
                                />
                              </SmartLink>
                              <img
                                v-else
                                src="/avatars/default-avatar.jpg"
                                alt="Default Author Avatar"
                                class="author-avatar"
                              />
                            </div>
                            <h6 class="author-name mt-2">
                              <SmartLink
                                v-if="author.player && author.player.id"
                                :to="{
                                  name: 'PlayerPage',
                                  params: { playerId: author.player.id },
                                  hash: '#authored-maps',
                                }"
                              >
                                {{
                                  author.author_name
                                    ? author.author_name
                                    : "Unknown Author"
                                }}
                              </SmartLink>
                              <span v-else>
                                {{
                                  author.author_name
                                    ? author.author_name
                                    : "Unknown Author"
                                }}
                              </span>
                            </h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="map-date-wrapper">
                    <p class="map-date-added">
                      <i class="bi bi-calendar-plus me-2"></i>
                      Added on: {{ formatDate(map.date_added) }}
                    </p>
                  </div>
                  <hr class="row-divider" style="width: 85%" />
                  <!-- <div class="row w-100" v-if="mapTags && mapTags.length > 0"> -->
                  <div class="row w-100">
                    <div class="col-md-12">
                      <h2 class="section-header mb-4">
                        <i class="bi bi-tags-fill me-2"></i>Tags
                      </h2>
                      <div class="tags-wrapper">
                        <div class="tags-block">
                          <div
                            v-for="tag in mapTags"
                            :key="tag.id"
                            class="tag-chip"
                            :style="{
                              backgroundColor: tag.color + '20',
                              borderColor: tag.color,
                            }"
                          >
                            <i
                              class="bi bi-tag-fill me-1"
                              :style="{ color: tag.color }"
                            ></i>
                            {{ tag.name }}
                          </div>
                          <div
                            class="tag-chip-default"
                            @click="showTagModal = true"
                            :style="{
                              backgroundColor: '#ffffff20',
                              borderColor: 'white',
                            }"
                          >
                            <i
                              class="bi bi-tag-fill me-1"
                              :style="{ color: 'white' }"
                              >Vote for tag(s)</i
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row main-content-wrapper">
          <div class="col-md-12 map-details">
            <div class="card map-videos mb-4">
              <div class="card-body p-4">
                <!-- Map WR Videos -->
                <div class="collapsible-header" @click="toggleMapVideos">
                  <h5 class="video-section-title mb-0">
                    🌍 Map WR Videos
                    <i
                      class="bi"
                      :class="
                        showMapVideos ? 'bi-chevron-up' : 'bi-chevron-down'
                      "
                      style="margin-left: auto"
                    ></i>
                  </h5>
                </div>
                <div
                  v-if="showMapVideos"
                  class="collapsible-content"
                  @click.self="clearActive"
                >
                  <div class="video-section mb-3">
                    <div class="section-content">
                      <div class="row g-4">
                        <!-- Map Soldier -->
                        <div class="col-lg-6">
                          <div
                            class="video-card video-card-soldier"
                            :class="{ active: activeVideo === 'map-soldier' }"
                            @click.stop="toggleCard('map-soldier')"
                          >
                            <h5 class="video-title">
                              <i class="bi bi-person-fill me-2"></i>Soldier
                              record
                            </h5>
                            <div class="video-container">
                              <div
                                v-if="
                                  map &&
                                  map.soldier_video &&
                                  map.soldier_video !== 'null'
                                "
                                class="video-scale-wrapper"
                                @click.stop="toggleCard('map-soldier')"
                              >
                                <iframe
                                  :src="
                                    'https://www.youtube.com/embed/' +
                                    map.soldier_video
                                  "
                                  frameborder="0"
                                  allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                  allowfullscreen
                                ></iframe>
                              </div>
                              <div v-else class="no-video-placeholder">
                                <i class="bi bi-camera-video-off"></i>
                                <span>No video available</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- Map Demoman -->
                        <div class="col-lg-6">
                          <div
                            class="video-card video-card-demoman"
                            :class="{ active: activeVideo === 'map-demoman' }"
                            @click.stop="toggleCard('map-demoman')"
                          >
                            <h5 class="video-title">
                              <i class="bi bi-person-fill me-2"></i>Demoman
                              record
                            </h5>
                            <div class="video-container">
                              <div
                                v-if="
                                  map &&
                                  map.demoman_video &&
                                  map.demoman_video !== 'null'
                                "
                                class="video-scale-wrapper"
                                @click.stop="toggleCard('map-demoman')"
                              >
                                <iframe
                                  :src="
                                    'https://www.youtube.com/embed/' +
                                    map.demoman_video
                                  "
                                  frameborder="0"
                                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                  allowfullscreen
                                ></iframe>
                              </div>
                              <div v-else class="no-video-placeholder">
                                <i class="bi bi-camera-video-off"></i>
                                <span>No video available</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Course WR Videos -->
                <div v-if="courses.length > 0" class="video-section mb-3">
                  <div class="collapsible-header" @click="toggleCourseVideos">
                    <h5 class="video-section-title mb-0">
                      🚩 Course WR Videos
                      <i
                        class="bi"
                        :class="
                          showCourseVideos ? 'bi-chevron-up' : 'bi-chevron-down'
                        "
                        style="margin-left: auto"
                      ></i>
                    </h5>
                  </div>
                  <div
                    v-if="showCourseVideos"
                    class="collapsible-content"
                    @click.self="clearActive"
                  >
                    <div
                      v-for="course in courses"
                      :key="course.id"
                      class="course-videos mb-4"
                    >
                      <h5 class="course-video-title mb-3">
                        Course {{ course.index }}
                      </h5>
                      <div class="row g-4">
                        <!-- Course Soldier -->
                        <div class="col-lg-6">
                          <div
                            class="video-card video-card-soldier"
                            :class="{
                              active:
                                activeVideo === 'course-soldier-' + course.id,
                            }"
                            @click.stop="
                              toggleCard('course-soldier-' + course.id)
                            "
                          >
                            <h6 class="video-title">
                              <i class="bi bi-person-fill me-2"></i>Soldier
                              record
                            </h6>
                            <div class="video-container">
                              <div
                                v-if="
                                  course.soldier_video &&
                                  course.soldier_video !== 'null'
                                "
                                class="video-scale-wrapper"
                                @click.stop="
                                  toggleCard('course-soldier-' + course.id)
                                "
                              >
                                <iframe
                                  :src="
                                    'https://www.youtube.com/embed/' +
                                    course.soldier_video
                                  "
                                  frameborder="0"
                                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                  allowfullscreen
                                ></iframe>
                              </div>
                              <div v-else class="no-video-placeholder">
                                <i class="bi bi-camera-video-off"></i>
                                <span>No video available</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- Course Demoman -->
                        <div class="col-lg-6">
                          <div
                            class="video-card video-card-demoman"
                            :class="{
                              active:
                                activeVideo === 'course-demoman-' + course.id,
                            }"
                            @click.stop="
                              toggleCard('course-demoman-' + course.id)
                            "
                          >
                            <h6 class="video-title">
                              <i class="bi bi-person-fill me-2"></i>Demoman
                              record
                            </h6>
                            <div class="video-container">
                              <div
                                v-if="
                                  course.demoman_video &&
                                  course.demoman_video !== 'null'
                                "
                                class="video-scale-wrapper"
                                @click.stop="
                                  toggleCard('course-demoman-' + course.id)
                                "
                              >
                                <iframe
                                  :src="
                                    'https://www.youtube.com/embed/' +
                                    course.demoman_video
                                  "
                                  frameborder="0"
                                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                  allowfullscreen
                                ></iframe>
                              </div>
                              <div v-else class="no-video-placeholder">
                                <i class="bi bi-camera-video-off"></i>
                                <span>No video available</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Bonus WR Videos -->
                <div v-if="bonuses.length > 0" class="video-section">
                  <div class="collapsible-header" @click="toggleBonusVideos">
                    <h5 class="video-section-title mb-0">
                      ⭐ Bonus WR Videos
                      <i
                        class="bi"
                        :class="
                          showBonusVideos ? 'bi-chevron-up' : 'bi-chevron-down'
                        "
                        style="margin-left: auto"
                      ></i>
                    </h5>
                  </div>
                  <div
                    v-if="showBonusVideos"
                    class="collapsible-content"
                    @click.self="clearActive"
                  >
                    <div
                      v-for="bonus in bonuses"
                      :key="bonus.id"
                      class="bonus-videos"
                    >
                      <h5 class="bonus-video-title mb-3">
                        Bonus {{ bonus.index }}
                      </h5>
                      <div class="row g-4">
                        <!-- Bonus Soldier -->
                        <div class="col-lg-6">
                          <div
                            class="video-card video-card-soldier"
                            :class="{
                              active:
                                activeVideo === 'bonus-soldier-' + bonus.id,
                            }"
                            @click.stop="
                              toggleCard('bonus-soldier-' + bonus.id)
                            "
                          >
                            <h6 class="video-title">
                              <i class="bi bi-person-fill me-2"></i>Soldier
                              record
                            </h6>
                            <div class="video-container">
                              <div
                                v-if="
                                  bonus.soldier_video &&
                                  bonus.soldier_video !== 'null'
                                "
                                class="video-scale-wrapper"
                                @click.stop="
                                  toggleCard('bonus-soldier-' + bonus.id)
                                "
                              >
                                <iframe
                                  :src="
                                    'https://www.youtube.com/embed/' +
                                    bonus.soldier_video
                                  "
                                  frameborder="0"
                                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                  allowfullscreen
                                ></iframe>
                              </div>
                              <div v-else class="no-video-placeholder">
                                <i class="bi bi-camera-video-off"></i>
                                <span>No video available</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- Bonus Demoman -->
                        <div class="col-lg-6">
                          <div
                            class="video-card video-card-demoman"
                            :class="{
                              active:
                                activeVideo === 'bonus-demoman-' + bonus.id,
                            }"
                            @click.stop="
                              toggleCard('bonus-demoman-' + bonus.id)
                            "
                          >
                            <h6 class="video-title">
                              <i class="bi bi-person-fill me-2"></i>Demoman
                              record
                            </h6>
                            <div class="video-container">
                              <div
                                v-if="
                                  bonus.demoman_video &&
                                  bonus.demoman_video !== 'null'
                                "
                                class="video-scale-wrapper"
                                @click.stop="
                                  toggleCard('bonus-demoman-' + bonus.id)
                                "
                              >
                                <iframe
                                  :src="
                                    'https://www.youtube.com/embed/' +
                                    bonus.demoman_video
                                  "
                                  frameborder="0"
                                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                  allowfullscreen
                                ></iframe>
                              </div>
                              <div v-else class="no-video-placeholder">
                                <i class="bi bi-camera-video-off"></i>
                                <span>No video available</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- ROTW Videos -->
                <div v-if="rotwVideos.length > 0" class="video-section">
                  <div class="collapsible-header" @click="toggleRotwVideos">
                    <h5 class="video-section-title mb-0">
                      🏆 ROTW Video(s)
                      <i
                        class="bi"
                        :class="
                          showRotwVideos ? 'bi-chevron-up' : 'bi-chevron-down'
                        "
                        style="margin-left: auto"
                      ></i>
                    </h5>
                  </div>
                  <div
                    v-if="showRotwVideos"
                    class="collapsible-content"
                    @click.self="clearActive"
                  >
                    <div
                      class="row g-4 justify-content-center align-items-center"
                    >
                      <div
                        v-for="rotwVideo in displayedRotwVideos"
                        :key="rotwVideo.video_id"
                        class="col-lg-8 col-md-10 col-12"
                      >
                        <div
                          class="video-card video-card-rotw"
                          :class="{
                            active:
                              activeVideo === 'rotw-' + rotwVideo.video_id,
                          }"
                          @click.stop="toggleCard('rotw-' + rotwVideo.video_id)"
                        >
                          <h6 class="video-title">
                            {{ rotwVideo.player_name || "Unknown Player" }}
                          </h6>
                          <div class="video-container">
                            <div
                              v-if="rotwVideo.video_id"
                              class="video-scale-wrapper-rotw"
                              @click.stop="
                                toggleCard('rotw-' + rotwVideo.video_id)
                              "
                            >
                              <iframe
                                :src="
                                  'https://www.youtube.com/embed/' +
                                  rotwVideo.video_id
                                "
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                              ></iframe>
                            </div>
                            <div v-else class="no-video-placeholder">
                              <i class="bi bi-camera-video-off"></i>
                              <span>No video available</span>
                            </div>
                          </div>
                          <div class="rotw-info mt-2">
                            <small class="rotw-text">
                              <i class="bi bi-calendar me-1"></i>
                              {{
                                formatDate(
                                  new Date(rotwVideo.uploaded_at).getTime() /
                                    1000,
                                )
                              }}
                            </small>
                          </div>
                        </div>
                      </div>
                      <div
                        v-if="hasMoreRotwVideos && !showAllRotwVideos"
                        class="text-center mt-3"
                      >
                        <button
                          @click="toggleShowAllRotwVideos"
                          class="btn load-more-btn"
                        >
                          <i class="bi bi-chevron-down me-2"></i>
                          Load {{ rotwVideos.length - 1 }} older ROTW video{{
                            rotwVideos.length - 1 > 1 ? "s" : ""
                          }}
                        </button>
                      </div>
                      <div
                        v-if="showAllRotwVideos && hasMoreRotwVideos"
                        class="text-center mt-3"
                      >
                        <button
                          @click="toggleShowAllRotwVideos"
                          class="btn load-more-btn"
                        >
                          <i class="bi bi-chevron-up me-2"></i>
                          Show only most recent
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <Leaderboard :map-id="mapId" :type="type" :index="index" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showTagModal" class="tag-modal-backdrop" @click="closeTagModal">
    <div class="tag-modal" @click.stop>
      <div class="tag-modal-header">
        <h3><i class="bi bi-tags-fill me-2"></i>Vote for Map Tags</h3>
        <button @click="closeTagModal" class="close-btn">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <div class="tag-modal-body">
        <!-- Description Section -->
        <div class="tag-description">
          <div class="beta-notice">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>This is a beta test for the tagging system</strong>
          </div>
          <p class="description-text">
            Vote for tags that describe the majority of this map's gameplay. A
            tag needs at least
            <strong>2 votes (eventually it will be more)</strong> to be applied
            to the map. You can vote for multiple tags, but only the
            <strong>top most voted</strong> tags will be displayed on the map.
            Currently only maps can have tags (courses/bonuses might come later)
          </p>
          <div class="warning-notice">
            <i class="bi bi-eye me-2"></i>
            <em
              >Note: All votes are tracked and monitored. Please vote
              responsibly and don't abuse the system.</em
            >
          </div>
        </div>

        <!-- Map Tags Section -->
        <div class="tag-section">
          <h4><i class="bi bi-map me-2"></i>Map Tags</h4>

          <!-- Soldier Tags -->
          <div
            class="tag-class-group"
            v-if="getTagsByClass('soldier').length > 0"
          >
            <h5>
              <img
                src="/icons/soldier.png"
                alt="Soldier"
                class="class-icon me-1"
              />
              Soldier
            </h5>
            <div class="tag-selector">
              <div class="available-tags">
                <div
                  v-for="tag in getTagsByClass('soldier')"
                  :key="'map-soldier-' + tag.id"
                  class="tag-option"
                  :class="{ active: isMapTagSelected(tag.id) }"
                  @click="toggleMapTag(tag.id)"
                  :style="{
                    backgroundColor: isMapTagSelected(tag.id)
                      ? tag.color + '40'
                      : 'transparent',
                    borderColor: tag.color,
                  }"
                >
                  <i
                    class="bi bi-tag-fill me-1"
                    :style="{ color: tag.color }"
                  ></i>
                  {{ tag.name }}
                  <span class="vote-count" v-if="tag.votes > 0">
                    ({{ tag.votes }})
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Demoman Tags -->
          <div
            class="tag-class-group"
            v-if="getTagsByClass('demoman').length > 0"
          >
            <h5>
              <img
                src="/icons/demoman.png"
                alt="Demoman"
                class="class-icon me-1"
              />
              Demoman
            </h5>
            <div class="tag-selector">
              <div class="available-tags">
                <div
                  v-for="tag in getTagsByClass('demoman')"
                  :key="'map-demoman-' + tag.id"
                  class="tag-option"
                  :class="{ active: isMapTagSelected(tag.id) }"
                  @click="toggleMapTag(tag.id)"
                  :style="{
                    backgroundColor: isMapTagSelected(tag.id)
                      ? tag.color + '40'
                      : 'transparent',
                    borderColor: tag.color,
                  }"
                >
                  <i
                    class="bi bi-tag-fill me-1"
                    :style="{ color: tag.color }"
                  ></i>
                  {{ tag.name }}
                  <span class="vote-count" v-if="tag.votes > 0">
                    ({{ tag.votes }})
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Both Classes Tags -->
          <div class="tag-class-group" v-if="getTagsByClass('both').length > 0">
            <h5><i class="bi bi-people me-1"></i>Both Classes</h5>
            <div class="tag-selector">
              <div class="available-tags">
                <div
                  v-for="tag in getTagsByClass('both')"
                  :key="'map-both-' + tag.id"
                  class="tag-option"
                  :class="{ active: isMapTagSelected(tag.id) }"
                  @click="toggleMapTag(tag.id)"
                  :style="{
                    backgroundColor: isMapTagSelected(tag.id)
                      ? tag.color + '40'
                      : 'transparent',
                    borderColor: tag.color,
                  }"
                >
                  <i
                    class="bi bi-tag-fill me-1"
                    :style="{ color: tag.color }"
                  ></i>
                  {{ tag.name }}
                  <span class="vote-count" v-if="tag.votes > 0">
                    ({{ tag.votes }})
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Extra Tags -->
          <div
            class="tag-class-group"
            v-if="getTagsByClass('extra').length > 0"
          >
            <h5><i class="bi bi-gear me-1"></i>Extra</h5>
            <div class="tag-selector">
              <div class="available-tags">
                <div
                  v-for="tag in getTagsByClass('extra')"
                  :key="'map-extra-' + tag.id"
                  class="tag-option"
                  :class="{ active: isMapTagSelected(tag.id) }"
                  @click="toggleMapTag(tag.id)"
                  :style="{
                    backgroundColor: isMapTagSelected(tag.id)
                      ? tag.color + '40'
                      : 'transparent',
                    borderColor: tag.color,
                  }"
                >
                  <i
                    class="bi bi-tag-fill me-1"
                    :style="{ color: tag.color }"
                  ></i>
                  {{ tag.name }}
                  <span class="vote-count" v-if="tag.votes > 0">
                    ({{ tag.votes }})
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="tag-modal-footer">
        <button @click="saveAllTags" class="save-tags-btn">
          <i class="bi bi-check-lg me-2"></i>Save Changes
        </button>
        <button @click="closeTagModal" class="cancel-btn">Cancel</button>
      </div>
    </div>
  </div>
  <div
    v-if="showLoginRequiredMessage"
    class="login-popup-backdrop"
    @click="closeLoginPopup"
  >
    <div class="login-popup" @click.stop>
      <div class="login-popup-content">
        <h4>Login Required</h4>
        <p>You need to be logged in to vote on map tags</p>
        <div class="login-popup-actions">
          <button @click="loginWithSteam" class="btn-login">
            <i class="bi bi-steam"></i> Login with Steam
          </button>
          <button @click="closeLoginPopup" class="btn-cancel">
            Maybe Later
          </button>
        </div>
      </div>
      <button @click="closeLoginPopup" class="login-popup-close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Leaderboard from "./Leaderboard.vue";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;

export default {
  name: "MapPage",
  components: {
    Leaderboard,
  },
  props: {
    mapId: {
      type: Number,
      required: true,
    },
    type: {
      type: String,
      default: "map",
      validator: (val) => ["map", "course", "bonus"].includes(val),
    },
    index: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      map: null,
      mapName: "",
      authors: [],
      courses: [],
      bonuses: [],
      showTagModal: false,
      showLoginRequiredMessage: false,
      availableTags: [],
      mapTags: [],
      selectedMapTags: [],
      selectedCourseTags: {},
      selectedBonusTags: {},
      rotwVideos: [],
      showAllRotwVideos: false,
      showRotwVideos: false,
      mapNotFound: false,
      showCourseVideos: false,
      showBonusVideos: false,
      showMapVideos: false,
      showMapBanner: false,
      activeVideo: null,
      playerId: null,
    };
  },
  async mounted() {
    await this.fetchUser();
    await this.fetchAllMapData(this.mapId);
    await this.loadTags(this.mapId);
    await this.loadMapTags(this.mapId);
    document.addEventListener("click", this.handleClickOutside);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.handleClickOutside);
  },
  watch: {
    mapId: {
      immediate: false,
      async handler(newMapId, oldMapId) {
        if (newMapId !== oldMapId) {
          this.resetComponentState();
          await this.loadTags(this.mapId);
          await this.loadMapTags(this.mapId);
          await this.fetchAllMapData(newMapId);
        }
      },
    },
  },
  computed: {
    displayedRotwVideos() {
      if (this.showAllRotwVideos || this.rotwVideos.length <= 1) {
        return this.rotwVideos;
      }
      return this.rotwVideos.slice(0, 1);
    },
    hasMoreRotwVideos() {
      return this.rotwVideos.length > 1;
    },
  },
  methods: {
    closeLoginPopup() {
      this.showLoginRequiredMessage = false;
    },
    loginWithSteam() {
      window.location.href = `${API_BASE_URL}/auth/steam`;
    },
    async loadTags(mapId = null) {
      try {
        const tagsResponse = await fetch(`${API_BASE_URL}/maps/tags`);
        const tagsData = await tagsResponse.json();

        let voteCountsByName = {};
        let playerVotedTags = [];

        if (mapId) {
          try {
            const votesResponse = await fetch(
              `${API_BASE_URL}/maps/${mapId}/map-tag-votes`,
            );
            const votesData = await votesResponse.json();

            if (votesData && votesData[0]) {
              votesData[0].forEach((tagVote) => {
                voteCountsByName[tagVote.name] = tagVote.votes;

                if (this.playerId && tagVote.player_ids) {
                  const playerIds = JSON.parse(tagVote.player_ids);
                  if (playerIds.includes(this.playerId)) {
                    playerVotedTags.push(tagVote.name);
                  }
                }
              });
            }
          } catch (voteError) {
            console.log("Could not load vote counts:", voteError);
          }
        }

        if (Array.isArray(tagsData)) {
          this.availableTags = tagsData.map((tag) => ({
            ...tag,
            votes: voteCountsByName[tag.name] || 0,
            playerVoted: playerVotedTags.includes(tag.name),
          }));

          this.selectedMapTags = this.availableTags
            .filter((tag) => tag.playerVoted)
            .map((tag) => tag.id);
        } else {
          console.error("Expected tags data to be an array:", tagsData);
          this.availableTags = [];
        }
      } catch (error) {
        console.error("Error loading tags:", error);
      }
    },
    async loadMapTags(mapId) {
      try {
        const response = await fetch(`${API_BASE_URL}/maps/${mapId}/tags`);
        const data = await response.json();

        this.mapTags = [...data.tags].sort((a, b) => a.id - b.id);
        //this.selectedMapTags = this.mapTags.map((tag) => tag.id);

        // Set course tags
        // this.courses.forEach((course) => {
        //   if (data.courseTags[course.id]) {
        //     course.tags = data.courseTags[course.id];
        //     this.selectedCourseTags[course.id] = data.courseTags[course.id].map(
        //       (tag) => tag.id
        //     );
        //   } else {
        //     course.tags = [];
        //     this.selectedCourseTags[course.id] = [];
        //   }
        // });

        // Set bonus tags
        // this.bonuses.forEach((bonus) => {
        //   if (data.bonusTags[bonus.id]) {
        //     bonus.tags = data.bonusTags[bonus.id];
        //     this.selectedBonusTags[bonus.id] = data.bonusTags[bonus.id].map(
        //       (tag) => tag.id
        //     );
        //   } else {
        //     bonus.tags = [];
        //     this.selectedBonusTags[bonus.id] = [];
        //   }
        // });
      } catch (error) {
        console.error("Error loading map tags:", error);
      }
    },
    async saveAllTags() {
      try {
        let response;
        const plainTagIds = [...this.selectedMapTags];

        if (this.playerId) {
          response = await fetch(
            `${API_BASE_URL}/maps/${this.map.id}/${this.playerId}/vote-tags`,
            {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ mapTags: plainTagIds }),
            },
          );
        } else {
          this.showLoginRequiredMessage = true;
          return;
        }

        if (response && response.ok) {
          await this.loadTags(this.map.id);
          await this.loadMapTags(this.map.id);
          this.showTagModal = false;
        } else {
          const errorText = await response.text();
          console.error("Failed to save tags:", response.status, errorText);
        }
      } catch (error) {
        console.error("Error saving tags:", error);
      }
    },
    isMapTagSelected(tagId) {
      return this.selectedMapTags.includes(tagId);
    },

    isCourseTagSelected(courseId, tagId) {
      return (
        this.selectedCourseTags[courseId] &&
        this.selectedCourseTags[courseId].includes(tagId)
      );
    },

    isBonusTagSelected(bonusId, tagId) {
      return (
        this.selectedBonusTags[bonusId] &&
        this.selectedBonusTags[bonusId].includes(tagId)
      );
    },

    toggleMapTag(tagId) {
      const index = this.selectedMapTags.indexOf(tagId);
      if (index > -1) {
        this.selectedMapTags.splice(index, 1);
      } else {
        this.selectedMapTags.push(tagId);
      }
    },

    toggleCourseTag(courseId, tagId) {
      if (!this.selectedCourseTags[courseId]) {
        this.$set(this.selectedCourseTags, courseId, []);
      }

      const tags = this.selectedCourseTags[courseId];
      const index = tags.indexOf(tagId);

      if (index > -1) {
        tags.splice(index, 1);
      } else {
        tags.push(tagId);
      }
    },

    toggleBonusTag(bonusId, tagId) {
      if (!this.selectedBonusTags[bonusId]) {
        this.$set(this.selectedBonusTags, bonusId, []);
      }

      const tags = this.selectedBonusTags[bonusId];
      const index = tags.indexOf(tagId);

      if (index > -1) {
        tags.splice(index, 1);
      } else {
        tags.push(tagId);
      }
    },
    getTagsByClass(className) {
      return this.availableTags.filter((tag) => tag.class === className);
    },
    async fetchUser() {
      try {
        const res = await fetch(`${API_BASE_URL}/api/get-user`, {
          credentials: "include",
        });
        const data = await res.json();
        this.playerId = data.data?.playerid || null;
      } catch (err) {
        console.error("Error fetching user:", err);
        this.playerId = null;
      }
    },
    closeTagModal() {
      this.showTagModal = false;
      this.loadMapTags(this.mapId);
    },
    toggleCard(cardId) {
      this.activeVideo = this.activeVideo === cardId ? null : cardId;
    },
    handleClickOutside(e) {
      if (!e.target.closest(".video-card")) {
        this.activeVideo = null;
      }
    },
    clearActive() {
      this.activeVideo = null;
    },
    toggleShowAllRotwVideos() {
      const wasShowingAll = this.showAllRotwVideos;
      this.showAllRotwVideos = !this.showAllRotwVideos;

      if (wasShowingAll && !this.showAllRotwVideos) {
        this.$nextTick(() => {
          const rotwSection = document.querySelector(
            ".video-section:has(.video-card-rotw)",
          );
          if (rotwSection) {
            rotwSection.scrollIntoView({
              behavior: "smooth",
              block: "start",
            });
          }
        });
      }
    },
    resetComponentState() {
      this.map = null;
      this.mapName = "";
      this.authors = [];
      this.courses = [];
      this.bonuses = [];
      this.rotwVideos = [];
      this.showRotwVideos = false;
      this.showAllRotwVideos = false;
      this.mapNotFound = false;
      this.showCourseVideos = false;
      this.showBonusVideos = false;
      this.showMapVideos = false;
      this.showMapBanner = true;
    },
    toggleRotwVideos() {
      this.showRotwVideos = !this.showRotwVideos;
    },
    returnToAllMaps() {
      this.$router.push({ name: "Maps" });
    },
    goBack() {
      this.$router.go(-1);
    },
    async fetchAllMapData(mapId) {
      try {
        const response = await axios.get(
          `${API_BASE_URL}/maps/${mapId}/all-info`,
        );
        if (!response.data.map) {
          this.mapNotFound = true;
          return;
        }
        this.map = response.data.map;
        this.mapName = response.data.map.name;
        this.authors = response.data.authors;
        this.courses = (response.data.courses || []).sort(
          (a, b) => a.index - b.index,
        );
        this.bonuses = (response.data.bonuses || []).sort(
          (a, b) => a.index - b.index,
        );
        this.rotwVideos = response.data.rotw_videos || [];
      } catch (error) {
        this.error = "Failed to fetch map data. Please try again later.";
        console.error("Error fetching map data:", error);
        this.mapNotFound = true;
      }
    },
    formatDate(unixTimestamp) {
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
    toggleCourseVideos() {
      this.showCourseVideos = !this.showCourseVideos;
    },
    toggleBonusVideos() {
      this.showBonusVideos = !this.showBonusVideos;
    },
    toggleMapVideos() {
      this.showMapVideos = !this.showMapVideos;
    },
    toggleMapBanner() {
      this.showMapBanner = !this.showMapBanner;
    },
    toggleMapInfo() {
      this.showMapInfo = !this.showMapInfo;
    },
    isIntendedClass(className) {
      if (!this.map || !this.map.intended_class) return false;

      const intendedClass = this.map.intended_class;

      if (intendedClass === 5) return true;
      if (intendedClass === 3 && className === "soldier") return true;
      if (intendedClass === 4 && className === "demoman") return true;

      return false;
    },
  },
};
</script>

<style scoped>
.return-button {
  background: var(--color-box);
  color: var(--color-text);
  border-radius: 8px;
  padding: 12px 24px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.return-button:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  color: var(--color-text);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.download-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 6px;
  padding: 6px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text);
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 1rem;
  pointer-events: auto;
  z-index: 10;
}

.download-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
  color: var(--color-text);
  text-decoration: none;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.download-btn:active {
  transform: translateY(0);
}

.map-banner {
  border-radius: 16px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.banner-content {
  border-radius: 16px;
  min-height: 200px;
}

.map-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  max-width: 100%;
}

.map-overview {
  padding: 0;
}

.clickable {
  cursor: pointer;
  color: var(--color-text-clickable) !important;
}

.clickable:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  ) !important;
}

.map-name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  color: var(--color-text);
  margin: 0 auto;
  font-size: 2.5rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.map-videos {
  background-color: transparent;
  border: none;
}

.back-button {
  background: var(--color-row);
  font-weight: 600;
  position: absolute;
  top: 20px;
  left: 20px;
  border-radius: 8px;
  padding: 10px 16px;
  border: 1px solid var(--color-border-soft);
  transition: all 0.3s ease;
  z-index: 10;
}

.back-button:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.stat-block {
  background: rgba(29, 34, 43, 0.842);
  transition: all 0.3s ease;
  align-items: center;
  border-radius: 12px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  border: 1px solid rgba(255, 255, 255, 0.1);
  overflow: hidden;
  transition: transform 0.3s ease;
}

.stat-block:hover {
  transform: translateY(-2px);
}

.stat-icon {
  font-size: 1.5rem;
  color: var(--color-primary);
  opacity: 0.8;
}

.stat-block .card-title {
  color: var(--color-text);
  font-weight: 600;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-block-highlighted {
  border: 2px solid var(--color-primary) !important;
  box-shadow: 0 0px 25px rgba(102, 126, 234, 0.4) !important;
}

.stat-value {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--color-dark);
  border-radius: 12px;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

.section-header {
  color: var(--color-text);
  font-weight: 700;
  text-align: center;
  font-size: 1.5rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.completion-count {
  font-size: 0.75rem;
  color: var(--color-text);
  font-weight: 500;
  opacity: 0.8;
  margin-top: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--color-box);
  padding: 4px 8px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.completion-count i {
  font-size: 0.7rem;
  color: var(--color-primary);
}

.completion-count-small {
  font-size: 0.65rem;
  color: var(--color-text);
  font-weight: 500;
  opacity: 0.7;
  margin-top: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.completion-count-small i {
  font-size: 0.6rem;
  color: var(--color-primary);
}

.video-section-title {
  color: var(--color-text);
  font-weight: 600;
  display: flex;
  align-items: center;
}

.course-video-title,
.bonus-video-title {
  color: var(--color-text);
  font-weight: bold;
  text-align: center;
  background: rgba(255, 255, 255, 0.05);
  padding: 4px 12px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 20px rgba(0, 0, 0, 0.4);
}

.video-title {
  color: var(--color-text) !important;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
}

.video-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px !important;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
}

.video-card:hover {
  cursor: pointer;
  background: var(--color-primary-dark);
}

.video-card-soldier.active {
  transform: scale(2) translateX(26%);
  z-index: 100;
  background: var(--color-primary-dark);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
}

.video-card-demoman.active {
  transform: scale(2) translateX(-26%);
  z-index: 100;
  background: var(--color-primary-dark);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
}

.authors-wrapper {
  display: flex;
  justify-content: center;
  width: 100%;
}

.author-card:hover,
.map-date-added:hover {
  transform: translateY(-1px);
}

.authors-block {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
  max-width: 600px;
  transition: transform 0.3s ease;
}

.author-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: all 0.3s ease;
  cursor: pointer;
}

.author-avatar-wrapper {
  position: relative;
  border-radius: 50%;
  padding: 3px;
  border: 2px solid var(--color-primary);
}

.author-name {
  color: var(--color-text);
  font-weight: 600;
  font-size: 0.9rem;
}

.author-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  border: 2px solid var(--color-dark);
  transition: all 0.3s ease;
}

.map-date-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 10px;
  width: 100%;
}

.map-date-added {
  color: var(--color-text);
  font-weight: 600;
  font-size: 0.9rem;
  background: rgba(29, 34, 43, 0.842);
  padding: 8px 16px;
  margin: 0;
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  display: inline-flex;
  align-items: center;
  transition: transform 0.3s ease;
}

.video-container {
  position: relative;
  overflow: hidden;
  width: 100%;
  padding-top: 56.25%;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.05);
}

.video-container iframe {
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
  transform: scale(0.5);
  transform-origin: top left;
}

.video-scale-wrapper iframe {
  width: 200%;
  height: 200%;
  border-radius: 8px;
}

.video-scale-wrapper-rotw {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transform: scale(1);
  transform-origin: top left;
}

.video-scale-wrapper-rotw iframe {
  width: 100%;
  height: 100%;
  border-radius: 8px;
}

.no-video-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.05);
  border: 2px dashed rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.9rem;
}

.no-video-placeholder i {
  font-size: 2rem;
  margin-bottom: 8px;
}

.compact-ratings-grid {
  display: flex;
  flex-direction: row;
  gap: 16px;
  justify-content: space-around;
  align-items: flex-start;
}

.rating-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  flex: 1;
}

.rating-pills {
  display: flex;
  gap: 4px;
  flex-wrap: nowrap;
  justify-content: center;
}

.rating-pill {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.75rem;
  color: var(--color-dark);
  text-shadow: none;
  text-align: center;
  white-space: nowrap;
}

.rating-label {
  font-size: 0.7rem;
  color: var(--color-text);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.8;
  text-align: center;
}

.courses-block,
.bonuses-block {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 16px;
  max-width: 100%;
}

.course-item,
.bonus-item {
  flex: 0 0 auto;
  min-width: 150px;
  max-width: 250px;
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

.map-name-container {
  text-align: center;
  width: 100%;
}

.map-name-display {
  text-align: center;
  display: inline-block;
}

.map-name-display h2 {
  color: var(--color-text);
  font-size: 2.5rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  margin: 0 auto;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.main-tier-color.tier-0 {
  background: var(--color-dark);
  color: var(--color-text);
}
.main-tier-color.tier-1 {
  background: #6ecff6;
  color: var(--color-dark);
}
.main-tier-color.tier-2 {
  background: #56b4e9;
  color: var(--color-dark);
}
.main-tier-color.tier-3 {
  background: #45b8ac;
  color: var(--color-dark);
}
.main-tier-color.tier-4 {
  background: #65c18c;
  color: var(--color-dark);
}
.main-tier-color.tier-5 {
  background: #a3d977;
  color: var(--color-dark);
}
.main-tier-color.tier-6 {
  background: #f3e683;
  color: var(--color-dark);
}
.main-tier-color.tier-7 {
  background: #f6c267;
  color: var(--color-dark);
}
.main-tier-color.tier-8 {
  background: #f08c5b;
  color: var(--color-dark);
}
.main-tier-color.tier-9 {
  background: #e66a5e;
  color: var(--color-dark);
}
.main-tier-color.tier-10 {
  background: #d64545;
  color: var(--color-dark);
}

.main-rating-color.rating-1 {
  background: #93c47d;
  color: var(--color-dark);
}
.main-rating-color.rating-2 {
  background: #aad099;
  color: var(--color-dark);
}
.main-rating-color.rating-3 {
  background: #c3b293;
  color: var(--color-dark);
}
.main-rating-color.rating-4 {
  background: #e06666;
  color: var(--color-dark);
}

.tier-color.tier-0 {
  background: rgba(51, 51, 51, 0.5);
  color: var(--color-text);
}
.tier-color.tier-1 {
  background: rgba(110, 208, 246, 0.5); /* Cold Blue */
  color: var(--color-text);
}
.tier-color.tier-2 {
  background: rgba(86, 179, 233, 0.5); /* Sky Blue */
  color: var(--color-text);
}
.tier-color.tier-3 {
  background: rgba(69, 184, 173, 0.5); /* Teal */
  color: var(--color-text);
}
.tier-color.tier-4 {
  background: rgba(101, 193, 139, 0.5); /* Mint */
  color: var(--color-text);
}
.tier-color.tier-5 {
  background: rgba(163, 217, 119, 0.5); /* Lime Green */
  color: var(--color-text);
}
.tier-color.tier-6 {
  background: rgba(243, 230, 131, 0.5); /* Yellow */
  color: var(--color-text);
}
.tier-color.tier-7 {
  background: rgba(246, 194, 103, 0.5); /* Orange-Yellow */
  color: var(--color-text);
}
.tier-color.tier-8 {
  background: rgba(240, 141, 91, 0.5); /* Orange */
  color: var(--color-text);
}
.tier-color.tier-9 {
  background: rgba(230, 105, 94, 0.5); /* Coral */
  color: var(--color-text);
}
.tier-color.tier-10 {
  background: rgba(214, 69, 69, 0.5); /* Red */
  color: var(--color-text);
}

.rating-color.rating-1 {
  background: rgba(148, 196, 125, 0.5);
  color: var(--color-text);
}
.rating-color.rating-2 {
  background: rgba(171, 208, 153, 0.5);
  color: var(--color-text);
}
.rating-color.rating-3 {
  background: rgba(195, 178, 147, 0.5);
  color: var(--color-text);
}
.rating-color.rating-4 {
  background: rgba(224, 102, 102, 0.5);
  color: var(--color-text);
}

.section-header-styled {
  padding: 12px 16px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  margin-bottom: 16px;
}

.section-header-styled .video-section-title {
  display: flex;
  align-items: center;
  border-bottom: none;
  padding-bottom: 0;
  margin-bottom: 0;
}

.section-content {
  margin-bottom: 16px;
}

.collapsible-header {
  cursor: pointer;
  padding: 8px 12px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  margin-bottom: 16px;
  transition: all 0.3s ease;
}

.collapsible-header:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.collapsible-header .video-section-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  border-bottom: none;
  padding-bottom: 0;
  margin-bottom: 0;
}

.collapsible-content {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .compact-ratings-grid {
    gap: 12px;
  }

  .rating-pill {
    font-size: 0.7rem;
    padding: 2px 6px;
  }

  .rating-label {
    font-size: 0.65rem;
  }
  .map-name {
    font-size: 2rem;
  }

  .maps-title {
    font-size: 1.75rem;
    color: var(--color-text);
  }

  .back-button {
    position: static;
    margin-bottom: 20px;
  }

  .map-overview {
    padding: 0;
  }

  .map-overview .row {
    margin: 0;
  }

  .map-overview .col-lg-3,
  .map-overview .col-md-6 {
    padding: 4px;
    margin-bottom: 8px;
  }

  .stat-block .card-body {
    padding: 12px !important;
  }

  .stat-block .card-title {
    font-size: 0.8rem;
    margin-bottom: 8px !important;
  }

  .stat-value {
    font-size: 1.1rem;
  }

  .completion-count {
    font-size: 0.7rem;
    padding: 3px 6px;
    margin-top: 6px;
  }

  .courses-block,
  .bonuses-block {
    justify-content: center;
    gap: 12px;
  }

  .course-item,
  .bonus-item {
    min-width: 140px;
    max-width: 100%;
    flex: 0 0 calc(50% - 6px);
  }

  .authors-block {
    gap: 16px;
  }

  .video-card.active {
    transform: none;
  }
  .video-card-rotw.active {
    transform: none !important;
  }
}
.video-card-rotw {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.video-card-rotw.active {
  background: var(--color-primary-dark);
  box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
  transform: scale(1.5);
  z-index: 999;
}

.rotw-info {
  text-align: center;
  padding: 8px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 6px;
}

.rotw-info small {
  color: var(--color-text);
  font-size: 0.75rem;
}
.load-more-btn {
  background: rgba(255, 255, 255, 0.05);
  color: var(--color-text);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
}

.load-more-btn:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  color: var(--color-text);
  border-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.video-scale-wrapper iframe,
.video-scale-wrapper-rotw iframe {
  pointer-events: none;
}

.video-card.active .video-scale-wrapper iframe,
.video-card.active .video-scale-wrapper-rotw iframe {
  pointer-events: auto;
}

.tag-btn {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 6px;
  padding: 6px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.tag-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.4);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.tags-wrapper {
  display: flex;
  justify-content: center;
  width: 100%;
}

.tags-block {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 8px;
  max-width: 800px;
}

.tag-chip,
.tag-chip-default {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 20px;
  border: 2px solid;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-text);
  transition: all 0.3s ease;
}

.tag-chip-default:hover {
  cursor: pointer;
}

.tag-chip:hover,
.tag-chip-default:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.tag-chip-small {
  display: inline-flex;
  align-items: center;
  padding: 2px 6px;
  border-radius: 12px;
  border: 1px solid;
  font-size: 0.6rem;
  font-weight: 500;
  color: var(--color-text);
  margin-right: 4px;
  margin-bottom: 2px;
}

.course-tags,
.bonus-tags {
  /* Removed - course and bonus tags no longer used */
}

.tag-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.tag-modal {
  background: var(--color-box);
  border-radius: 16px;
  border: 1px solid var(--color-border-soft);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
  max-width: 800px;
  max-height: 80vh;
  width: 90%;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.tag-modal-header {
  padding: 20px;
  border-bottom: 1px solid var(--color-border-soft);
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--color-primary-dark);
}

.tag-modal-header h3 {
  margin: 0;
  color: var(--color-text);
  font-weight: 700;
}

.close-btn {
  background: none;
  border: none;
  color: var(--color-text);
  font-size: 1.2rem;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}

.tag-modal-body {
  padding: 20px;
  overflow-y: auto;
  flex: 1;
}

.tag-section {
  margin-bottom: 24px;
}

.tag-section h4 {
  color: var(--color-text);
  font-weight: 600;
  margin-bottom: 12px;
  font-size: 1.1rem;
}

.tag-class-group {
  margin-bottom: 20px;
  padding: 16px;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.class-icon {
  width: 20px;
  height: 20px;
  object-fit: contain;
  vertical-align: middle;
}

.tag-class-group h5 {
  color: var(--color-text);
  font-weight: 600;
  margin-bottom: 12px;
  font-size: 1rem;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.course-tag-section,
.bonus-tag-section {
}

.available-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.tag-option {
  display: inline-flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 16px;
  border: 2px solid;
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--color-text);
  cursor: pointer;
  transition: all 0.3s ease;
  background: transparent;
}

.tag-option:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.tag-option.active {
  font-weight: 600;
  transform: scale(1.05);
}

.tag-modal-footer {
  padding: 20px;
  border-top: 1px solid var(--color-border-soft);
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  background: var(--color-row);
}

.save-tags-btn {
  background: var(--color-primary);
  color: var(--color-text);
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.save-tags-btn:hover {
  background: var(--color-primary-dark);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.cancel-btn {
  background: transparent;
  color: var(--color-text);
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateY(-1px);
}

.vote-count {
  color: #666;
  font-size: 0.9em;
  margin-left: 0.25rem;
  opacity: 0.8;
}

.tag-option.active .vote-count {
  color: inherit;
  font-weight: 500;
}

.tag-description {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.description-text {
  margin: 0;
  color: #495057;
  font-size: 0.95rem;
  line-height: 1.5;
}

.description-text strong {
  color: #212529;
  font-weight: 600;
}

.description-text i {
  color: #6c757d;
}

.beta-notice {
  background-color: #fff3cd;
  border: 1px solid #ffeaa7;
  border-radius: 6px;
  padding: 0.75rem;
  margin-bottom: 1rem;
  color: #856404;
  font-size: 0.9rem;
}

.beta-notice i {
  color: #f39c12;
}

.warning-notice {
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 6px;
  padding: 0.75rem;
  margin-top: 1rem;
  color: #721c24;
  font-size: 0.85rem;
}

.warning-notice i {
  color: #dc3545;
}

@media (max-width: 768px) {
  .tag-modal {
    width: 95%;
    max-height: 90vh;
  }

  .tag-btn {
    right: 48px;
  }

  .tags-block {
    gap: 6px;
  }

  .tag-chip {
    font-size: 0.8rem;
    padding: 4px 10px;
  }
}
.login-popup-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.login-popup {
  background: #343a40;
  border-radius: 1rem;
  padding: 2rem;
  max-width: 400px;
  width: 90%;
  margin: 1rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
  position: relative;
  text-align: center;
  animation: popupSlideIn 0.3s ease-out;
  border: 1px solid #495057;
}

.login-popup-content h4 {
  color: #f8f9fa;
  margin-bottom: 0.5rem;
  font-size: 1.4rem;
  font-weight: 600;
}

.login-popup-content p {
  color: #adb5bd;
  margin-bottom: 2rem;
  line-height: 1.5;
  font-size: 1rem;
}

.login-popup-actions {
  display: flex;
  gap: 1rem;
  flex-direction: column;
}

.btn-login {
  background: var(--color-primary);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}

.btn-login:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.btn-cancel {
  background: transparent;
  color: #adb5bd;
  border: 1px solid #495057;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 1rem;
}

.btn-cancel:hover {
  background: #495057;
  color: #f8f9fa;
  border-color: #6c757d;
}

.login-popup-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #adb5bd;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.login-popup-close:hover {
  background: #495057;
  color: #f8f9fa;
}

@keyframes popupSlideIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@media (max-width: 576px) {
  .login-popup {
    padding: 1.5rem;
    margin: 0.5rem;
  }

  .login-popup-actions {
    gap: 0.75rem;
  }

  .btn-login,
  .btn-cancel {
    padding: 0.875rem 1.25rem;
    font-size: 0.95rem;
  }
}

.lookup-map-banner {
  width: 100%;
  max-width: 1000px;
  margin: 20px auto;
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
  min-height: 188px;
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

.tier-text {
  font-size: 1.25rem;
  letter-spacing: 0.5px;
}

.rating-text {
  font-size: 1.25rem;
  color: rgba(255, 255, 255, 0.9);
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

  .intended-class-display {
    padding: 5px;
  }

  .intended-class-icon {
    width: 20px;
    height: 20px;
  }

  .lookup-map-secondary {
    font-size: 0.8rem;
    gap: 8px;
  }

  .secondary-stat {
    font-size: 0.75rem;
  }
}
</style>
