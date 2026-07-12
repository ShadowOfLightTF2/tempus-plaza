<template>
  <div
    class="card map-information mb-4"
    :style="{
      background: `
      linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%),
      radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
      url('/map-backgrounds/${map.name}.webp') center/cover no-repeat
    `,
      backgroundBlendMode: 'multiply, normal, normal',
      backgroundSize: 'cover, cover, cover',
      backgroundPosition: 'center, center, center',
    }"
  >
    <div class="row g-0 information-content">
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
                    {{ map.soldier_completion_count || 0 }}
                    completions
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
                    {{ map.demoman_completion_count || 0 }}
                    completions
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
                      <h5 class="card-title mb-3">Course {{ course.index }}</h5>
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
                      <h5 class="card-title mb-3">Bonus {{ bonus.index }}</h5>
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
                    @click="emitShowTagModal"
                    :style="{
                      backgroundColor: '#ffffff20',
                      borderColor: 'white',
                    }"
                  >
                    <i class="bi bi-tag-fill me-1" :style="{ color: 'white' }"
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
</template>

<script>
import SmartLink from "./SmartLink.vue";

export default {
  name: "MapInformation",
  components: {
    SmartLink,
  },
  props: {
    map: {
      type: Object,
      required: true,
    },
    courses: {
      type: Array,
      default: () => [],
    },
    bonuses: {
      type: Array,
      default: () => [],
    },
    authors: {
      type: Array,
      default: () => [],
    },
    mapTags: {
      type: Array,
      default: () => [],
    },
  },
  emits: ["show-tag-modal"],
  methods: {
    emitShowTagModal() {
      this.$emit("show-tag-modal");
    },
    isIntendedClass(className) {
      if (!this.map || !this.map.intended_class) return false;

      const intendedClass = this.map.intended_class;

      if (intendedClass === 5) return true;
      if (intendedClass === 3 && className === "soldier") return true;
      if (intendedClass === 4 && className === "demoman") return true;

      return false;
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
  },
};
</script>

<style scoped>
.map-information {
  border-radius: 16px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
}

.information-content {
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

.author-name a {
  color: inherit;
  text-decoration: none;
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

  .authors-block {
    gap: 16px;
  }
}
</style>
