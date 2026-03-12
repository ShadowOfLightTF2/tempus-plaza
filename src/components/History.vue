<template>
  <div class="position-relative min-vh-100 w-100 background-container">
    <div class="container mx-auto py-4 d-flex flex-column align-items-center">
      <div class="content-container">
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">📜</span> History Timeline
          </h1>
          <p class="page-subtitle">
            Track map releases, tier changes and top 10 shifts over time
          </p>
        </div>
        <hr class="divider" style="width: 100%" />

        <!-- API Error State -->
        <div v-if="fetchError" class="api-error-state">
          <div class="error-icon">⚠️</div>
          <h3>Failed to load history</h3>
          <p>Unable to fetch timeline data. Please try again.</p>
          <button @click="retryFetch" class="global-btn retry-btn">
            Retry
          </button>
        </div>

        <div class="filter-bar-row" v-else>
          <div class="filter-group">
            <h6 class="filter-title">Event type</h6>
            <div class="filter-btns">
              <button
                v-for="type in eventTypes"
                :key="type.value"
                @click="toggleFilter('type', type.value)"
                :class="[
                  'global-btn',
                  { active: activeFilters.type.includes(type.value) },
                ]"
              >
                {{ type.label }}
              </button>
            </div>
          </div>
          <div
            class="filter-group"
            v-if="
              activeFilters.type.includes('tier') ||
              activeFilters.type.includes('map_release') ||
              activeFilters.type.includes('top_player')
            "
          >
            <h6 class="filter-title">Class</h6>
            <div class="filter-btns">
              <button
                @click="toggleFilter('class', 'soldier')"
                :class="[
                  'global-btn',
                  { active: activeFilters.class.includes('soldier') },
                ]"
              >
                Soldier
              </button>
              <button
                @click="toggleFilter('class', 'demoman')"
                :class="[
                  'global-btn',
                  { active: activeFilters.class.includes('demoman') },
                ]"
              >
                Demoman
              </button>
            </div>
          </div>
          <div class="filter-group filter-group-clear">
            <button
              @click="clearFilters"
              class="global-btn clear-btn"
              :disabled="isFiltersEmpty"
            >
              Clear filters
            </button>
          </div>
        </div>

        <div class="table-container-wrapper" v-if="!fetchError">
          <div v-if="loadingInitial" class="initial-loading">
            <div class="spinner-ring"></div>
            <span class="spinner-label">Loading history…</span>
          </div>
          <div class="timeline-container" v-else>
            <div v-if="visibleHistory.length === 0" class="no-events">
              No events match your filters.
            </div>
            <div
              v-for="section in timelineSections"
              :key="section.monthLabel"
              class="timeline-month-section"
            >
              <!-- Month Header -->
              <div class="timeline-month-header">
                <span class="month-label">{{ section.monthLabel }}</span>
              </div>

              <!-- Days within Month -->
              <div
                v-for="dayObj in section.days"
                :key="section.monthLabel + '-' + dayObj.dayLabel"
                class="timeline-day"
              >
                <div class="day-marker">
                  <span class="day-badge">{{ dayObj.dayLabel }}</span>
                </div>
                <div class="day-events">
                  <div class="day-events-grid">
                    <div
                      v-for="event in dayObj.events"
                      :key="event.type + '-' + event.id"
                      :class="[
                        'timeline-event',
                        event.type === 'tier'
                          ? 'event-col-half'
                          : 'event-col-full',
                      ]"
                    >
                      <SmartLink
                        v-if="event.type === 'tier'"
                        class="hist-card hist-card-sm fancy-hover"
                        :to="{
                          name: 'MapPage',
                          params: { mapId: String(event.map_id) },
                        }"
                      >
                        <div
                          class="hist-card-bg"
                          :style="{
                            backgroundImage: `url('/map-backgrounds/${event.name}.webp')`,
                          }"
                        ></div>
                        <div class="hist-badge tier-badge-label">
                          <span class="badge-dot tier-dot"></span> Tier / Rating
                          Update
                        </div>
                        <div class="hist-card-content">
                          <div class="hist-spacer"></div>
                          <h3 class="hist-map-name">
                            {{ event.name }}
                            <span
                              v-if="event.zone_type !== 'map'"
                              class="hist-zone-badge"
                            >
                              {{
                                event.zone_type === "course"
                                  ? "🚩 Course"
                                  : "⭐ Bonus"
                              }}
                              {{ event.zone_index }}
                            </span>
                          </h3>
                          <div class="hist-ratings-grid">
                            <div
                              v-if="soldierChanged(event)"
                              class="hist-rating-section"
                            >
                              <div class="hist-rating-label">Soldier</div>
                              <div
                                v-if="
                                  event.old_soldier_tier !==
                                  event.new_soldier_tier
                                "
                                class="hist-pill-row"
                              >
                                <span
                                  class="hist-pill tier-color"
                                  :class="'tier-' + event.old_soldier_tier"
                                  >T{{ event.old_soldier_tier }}</span
                                >
                                <span class="hist-arrow">→</span>
                                <span
                                  class="hist-pill tier-color changed"
                                  :class="'tier-' + event.new_soldier_tier"
                                  >T{{ event.new_soldier_tier }}</span
                                >
                              </div>
                              <div
                                v-if="
                                  event.old_soldier_rating !==
                                  event.new_soldier_rating
                                "
                                class="hist-pill-row"
                              >
                                <span
                                  class="hist-pill rating-color"
                                  :class="'rating-' + event.old_soldier_rating"
                                  >R{{ event.old_soldier_rating }}</span
                                >
                                <span class="hist-arrow">→</span>
                                <span
                                  class="hist-pill rating-color changed"
                                  :class="'rating-' + event.new_soldier_rating"
                                  >R{{ event.new_soldier_rating }}</span
                                >
                              </div>
                            </div>
                            <div
                              v-if="
                                soldierChanged(event) && demomanChanged(event)
                              "
                              class="hist-ratings-divider"
                            ></div>
                            <div
                              v-if="demomanChanged(event)"
                              class="hist-rating-section"
                            >
                              <div class="hist-rating-label">Demoman</div>
                              <div
                                v-if="
                                  event.old_demoman_tier !==
                                  event.new_demoman_tier
                                "
                                class="hist-pill-row"
                              >
                                <span
                                  class="hist-pill tier-color"
                                  :class="'tier-' + event.old_demoman_tier"
                                  >T{{ event.old_demoman_tier }}</span
                                >
                                <span class="hist-arrow">→</span>
                                <span
                                  class="hist-pill tier-color changed"
                                  :class="'tier-' + event.new_demoman_tier"
                                  >T{{ event.new_demoman_tier }}</span
                                >
                              </div>
                              <div
                                v-if="
                                  event.old_demoman_rating !==
                                  event.new_demoman_rating
                                "
                                class="hist-pill-row"
                              >
                                <span
                                  class="hist-pill rating-color"
                                  :class="'rating-' + event.old_demoman_rating"
                                  >R{{ event.old_demoman_rating }}</span
                                >
                                <span class="hist-arrow">→</span>
                                <span
                                  class="hist-pill rating-color changed"
                                  :class="'rating-' + event.new_demoman_rating"
                                  >R{{ event.new_demoman_rating }}</span
                                >
                              </div>
                            </div>
                          </div>
                          <div class="hist-meta-row">
                            <svg
                              viewBox="0 0 24 24"
                              fill="none"
                              stroke="currentColor"
                              stroke-width="2"
                              aria-hidden="true"
                            >
                              <rect
                                x="3"
                                y="4"
                                width="18"
                                height="18"
                                rx="2"
                                ry="2"
                              />
                              <line x1="16" y1="2" x2="16" y2="6" />
                              <line x1="8" y1="2" x2="8" y2="6" />
                              <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            {{ formatDay(event.changed_at) }}
                          </div>
                        </div>
                      </SmartLink>
                      <SmartLink
                        v-else-if="event.type === 'map_release'"
                        class="hist-card fancy-hover"
                        :to="{
                          name: 'MapPage',
                          params: { mapId: String(event.id) },
                        }"
                      >
                        <div
                          class="hist-card-bg"
                          :style="{
                            backgroundImage: `url('/map-backgrounds/${event.name}.webp')`,
                          }"
                        ></div>
                        <div class="hist-badge map-badge-label">
                          <span class="badge-dot map-dot"></span> New Map
                        </div>
                        <div class="hist-class-icons">
                          <img
                            v-for="icon in getClassIcons(event.intended_class)"
                            :key="icon.alt"
                            :src="icon.src"
                            :class="icon.cls"
                            :alt="icon.alt"
                          />
                        </div>
                        <div class="hist-card-content">
                          <div class="hist-spacer"></div>
                          <h3 class="hist-map-name">{{ event.name }}</h3>
                          <div class="hist-ratings-grid map-release-ratings">
                            <div class="hist-rating-section">
                              <div class="hist-rating-label">Soldier</div>
                              <div class="hist-pill-row">
                                <span
                                  class="hist-pill tier-color"
                                  :class="'tier-' + event.soldier_tier"
                                  >T{{ event.soldier_tier }}</span
                                >
                                <span
                                  class="hist-pill rating-color"
                                  :class="'rating-' + event.soldier_rating"
                                  >R{{ event.soldier_rating }}</span
                                >
                              </div>
                            </div>
                            <div class="hist-ratings-divider"></div>
                            <div class="hist-rating-section">
                              <div class="hist-rating-label">Demoman</div>
                              <div class="hist-pill-row">
                                <span
                                  class="hist-pill tier-color"
                                  :class="'tier-' + event.demoman_tier"
                                  >T{{ event.demoman_tier }}</span
                                >
                                <span
                                  class="hist-pill rating-color"
                                  :class="'rating-' + event.demoman_rating"
                                  >R{{ event.demoman_rating }}</span
                                >
                              </div>
                            </div>
                          </div>
                          <!-- Authors row — now below tiers/ratings -->
                          <div
                            class="hist-authors-row"
                            v-if="event.authors && event.authors.length"
                          >
                            <div class="hist-authors-avatars">
                              <img
                                v-for="author in event.authors.slice(0, 3)"
                                :key="author.name"
                                :src="author.avatar"
                                class="hist-author-avatar"
                                :title="author.name"
                                @error="avatarError"
                              />
                            </div>
                            <span class="hist-author-name"
                              >by
                              {{
                                event.authors
                                  .slice(0, 3)
                                  .map((a) => a.name)
                                  .join(", ")
                              }}</span
                            >
                          </div>
                          <!-- Meta row: courses, bonuses, tags, date pushed right -->
                          <div class="hist-meta-row">
                            <span v-if="event.course_count">
                              <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                aria-hidden="true"
                              >
                                <path
                                  d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"
                                />
                                <line x1="4" y1="22" x2="4" y2="15" />
                              </svg>
                              {{ event.course_count }} course{{
                                event.course_count !== 1 ? "s" : ""
                              }}
                            </span>
                            <span v-if="event.bonus_count">
                              <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                aria-hidden="true"
                              >
                                <polygon
                                  points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                                />
                              </svg>
                              {{ event.bonus_count }} bonus{{
                                event.bonus_count !== 1 ? "es" : ""
                              }}
                            </span>
                            <template
                              v-if="event.tags && event.tags.length > 0"
                            >
                              <div
                                v-for="tag in event.tags"
                                :key="tag.id"
                                class="map-tag"
                                :style="{
                                  backgroundColor: tag.color + '20',
                                  borderColor: tag.color,
                                }"
                              >
                                <i
                                  class="bi bi-tag-fill me-1"
                                  :style="{ color: tag.color }"
                                ></i
                                >{{ tag.name }}
                              </div>
                            </template>
                            <span class="hist-date-right">
                              <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                aria-hidden="true"
                              >
                                <rect
                                  x="3"
                                  y="4"
                                  width="18"
                                  height="18"
                                  rx="2"
                                  ry="2"
                                />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                              </svg>
                              {{ formatDay(event.date_added) }}
                            </span>
                          </div>
                        </div>
                      </SmartLink>
                      <div
                        v-else-if="event.type === 'top_player'"
                        class="tp-swap-card"
                      >
                        <div class="tp-swap-header">
                          <span class="tp-swap-badge"
                            >🏆 Top 10 —
                            {{
                              event.rankType.charAt(0).toUpperCase() +
                              event.rankType.slice(1)
                            }}</span
                          >
                          <span class="tp-swap-date">{{
                            formatDay(event.date)
                          }}</span>
                        </div>
                        <div class="tp-swap-body">
                          <SmartLink
                            v-if="event.gainer"
                            class="tp-player-col tp-gainer"
                            :to="{
                              name: 'PlayerPage',
                              params: {
                                playerId: String(event.gainer.player_id),
                              },
                            }"
                          >
                            <div class="tp-avatar-wrap">
                              <img
                                :src="event.gainer.player_avatar"
                                class="tp-avatar"
                                @error="avatarError"
                              />
                            </div>
                            <div class="tp-player-info">
                              <span class="tp-name">{{
                                event.gainer.player_name
                              }}</span>
                              <div
                                class="tp-country"
                                v-if="event.gainer.country"
                              >
                                <img
                                  v-if="event.gainer.country_code"
                                  :src="`https://flagcdn.com/16x12/${event.gainer.country_code.toLowerCase()}.png`"
                                  class="tp-flag"
                                />
                                {{ event.gainer.country }}
                              </div>
                            </div>
                            <div class="tp-rank-pill rank-up">
                              <span class="tp-rank-old"
                                >#{{
                                  event.gainer.details[event.rankType].from
                                }}</span
                              >
                              <span>→</span>
                              <span class="tp-rank-new"
                                >#{{
                                  event.gainer.details[event.rankType].to
                                }}</span
                              >
                              <span class="tp-delta"
                                >▲{{
                                  Math.abs(
                                    event.gainer.details[event.rankType].from -
                                      event.gainer.details[event.rankType].to,
                                  )
                                }}</span
                              >
                            </div>
                          </SmartLink>
                          <div
                            v-if="event.gainer && event.loser"
                            class="tp-swap-vs"
                          >
                            ⇄
                          </div>
                          <SmartLink
                            v-if="event.loser"
                            class="tp-player-col tp-loser"
                            :class="{ 'tp-solo-loser': !event.gainer }"
                            :to="{
                              name: 'PlayerPage',
                              params: {
                                playerId: String(event.loser.player_id),
                              },
                            }"
                          >
                            <div class="tp-avatar-wrap">
                              <img
                                :src="event.loser.player_avatar"
                                class="tp-avatar"
                                @error="avatarError"
                              />
                            </div>
                            <div class="tp-player-info">
                              <span class="tp-name">{{
                                event.loser.player_name
                              }}</span>
                              <div
                                class="tp-country"
                                v-if="event.loser.country"
                              >
                                <img
                                  v-if="event.loser.country_code"
                                  :src="`https://flagcdn.com/16x12/${event.loser.country_code.toLowerCase()}.png`"
                                  class="tp-flag"
                                />
                                {{ event.loser.country }}
                              </div>
                            </div>
                            <div class="tp-rank-pill rank-down">
                              <span class="tp-rank-old"
                                >#{{
                                  event.loser.details[event.rankType].from
                                }}</span
                              >
                              <span>→</span>
                              <span class="tp-rank-new"
                                >#{{
                                  event.loser.details[event.rankType].to
                                }}</span
                              >
                              <span class="tp-delta"
                                >▼{{
                                  Math.abs(
                                    event.loser.details[event.rankType].from -
                                      event.loser.details[event.rankType].to,
                                  )
                                }}</span
                              >
                            </div>
                          </SmartLink>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div ref="sentinel" class="sentinel"></div>
            <div v-if="loadingMore" class="load-more-spinner">
              <div class="spinner-ring small"></div>
              <span class="spinner-label">Loading more…</span>
            </div>
            <div
              v-if="!loadingMore && !hasMore && filteredHistory.length > 0"
              class="end-of-feed"
            >
              — End of history —
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useHead } from "@vueuse/head";

const API_BASE_URL = import.meta.env.VITE_APP_API_BASE_URL;
const PAGE_SIZE = 20;
const WEEK_MS = 7 * 24 * 60 * 60 * 1000;

export default {
  name: "History",
  setup() {
    useHead({ title: "Tempus Plaza | History" });
  },
  data() {
    return {
      loadingInitial: true,
      loadingMore: false,
      fetchError: false,
      tierHistory: [],
      maps: [],
      topPlayerHistory: [],
      visibleCount: PAGE_SIZE,
      observer: null,
      activeFilters: {
        type: ["tier", "map_release", "top_player"],
        class: ["soldier", "demoman"],
      },
      eventTypes: [
        { value: "tier", label: "Tier changes" },
        { value: "map_release", label: "Map releases" },
        { value: "top_player", label: "Top 10 changes" },
      ],
    };
  },
  computed: {
    combinedHistory() {
      const toMs = (d) =>
        typeof d === "number" ? d * 1000 : new Date(d).getTime();
      const events = [];
      this.tierHistory.forEach((item) => {
        events.push({ ...item, type: "tier", date: item.changed_at });
      });
      this.maps.forEach((map) => {
        if (map.date_added) {
          events.push({ ...map, type: "map_release", date: map.date_added });
        }
      });
      const usedKeys = new Set();
      ["overall", "soldier", "demoman"].forEach((rt) => {
        const entries = this.topPlayerHistory.filter(
          (p) => p.details && p.details[rt],
        );
        entries.forEach((a, ai) => {
          const aKey = `${rt}-${ai}`;
          if (usedKeys.has(aKey)) return;
          const ac = a.details[rt];
          const isGain = ac.to < ac.from;
          if (!isGain) return;
          const aMs =
            typeof a.date === "number"
              ? a.date * 1000
              : new Date(a.date).getTime();
          const bi = entries.findIndex((b, bi) => {
            if (bi === ai) return false;
            if (usedKeys.has(`${rt}-${bi}`)) return false;
            if (String(b.player_id) === String(a.player_id)) return false;
            const bc = b.details[rt];
            if (bc.from !== ac.to) return false;
            const bMs =
              typeof b.date === "number"
                ? b.date * 1000
                : new Date(b.date).getTime();
            return Math.abs(aMs - bMs) <= WEEK_MS;
          });
          if (bi !== -1) {
            usedKeys.add(aKey);
            usedKeys.add(`${rt}-${bi}`);
            events.push({
              id: `tp-pair-${rt}-${ai}-${bi}`,
              type: "top_player",
              date: a.date,
              rankType: rt,
              gainer: a,
              loser: entries[bi],
            });
          } else {
            usedKeys.add(aKey);
            events.push({
              id: `tp-solo-${rt}-${ai}`,
              type: "top_player",
              date: a.date,
              rankType: rt,
              gainer: a,
              loser: null,
            });
          }
        });
        entries.forEach((a, ai) => {
          const aKey = `${rt}-${ai}`;
          if (usedKeys.has(aKey)) return;
          usedKeys.add(aKey);
          events.push({
            id: `tp-solo-${rt}-${ai}`,
            type: "top_player",
            date: a.date,
            rankType: rt,
            gainer: null,
            loser: a,
          });
        });
      });
      const soloGainers = events.filter(
        (e) => e.type === "top_player" && e.gainer && !e.loser,
      );
      const soloLosers = events.filter(
        (e) => e.type === "top_player" && !e.gainer && e.loser,
      );
      const suppressIds = new Set();
      soloGainers.forEach((g) => {
        const match = soloLosers.find(
          (l) =>
            String(l.loser.player_id) === String(g.gainer.player_id) &&
            l.rankType === g.rankType &&
            Math.abs(toMs(l.date) - toMs(g.date)) <= WEEK_MS,
        );
        if (match) {
          suppressIds.add(g.id);
          suppressIds.add(match.id);
        }
      });
      const filtered = events.filter((e) => !suppressIds.has(e.id));
      return filtered.sort((a, b) => toMs(b.date) - toMs(a.date));
    },
    filteredHistory() {
      return this.combinedHistory.filter((event) => {
        if (!this.activeFilters.type.includes(event.type)) return false;
        if (event.type === "tier") {
          const soldierSelected = this.activeFilters.class.includes("soldier");
          const demomanSelected = this.activeFilters.class.includes("demoman");
          const showForSoldier =
            soldierSelected && this.hasSoldierChange(event);
          const showForDemoman =
            demomanSelected && this.hasDemomanChange(event);
          if (!showForSoldier && !showForDemoman) return false;
        }
        if (event.type === "map_release") {
          const ic = event.intended_class;
          const soldierSelected = this.activeFilters.class.includes("soldier");
          const demomanSelected = this.activeFilters.class.includes("demoman");
          if (ic === 3 && !soldierSelected) return false;
          if (ic === 4 && !demomanSelected) return false;
          if (ic === 5 && !soldierSelected && !demomanSelected) return false;
        }
        if (event.type === "top_player") {
          const soldierSelected = this.activeFilters.class.includes("soldier");
          const demomanSelected = this.activeFilters.class.includes("demoman");
          const bothOn = soldierSelected && demomanSelected;
          if (event.rankType === "overall" && !bothOn) return false;
          if (event.rankType === "soldier" && !soldierSelected) return false;
          if (event.rankType === "demoman" && !demomanSelected) return false;
        }
        return true;
      });
    },
    visibleHistory() {
      return this.filteredHistory.slice(0, this.visibleCount);
    },
    hasMore() {
      return this.visibleCount < this.filteredHistory.length;
    },
    timelineSections() {
      const sections = [];
      let currentMonthLabel = null;
      let currentMonthObj = null;
      let currentDayLabel = null;
      let currentDayObj = null;

      for (const event of this.visibleHistory) {
        const mLabel = this.formatMonthYear(event.date);
        const dLabel = this.formatDay(event.date);

        if (mLabel !== currentMonthLabel) {
          currentMonthLabel = mLabel;
          currentMonthObj = { monthLabel: mLabel, days: [] };
          sections.push(currentMonthObj);
          currentDayLabel = null;
        }

        if (dLabel !== currentDayLabel) {
          currentDayLabel = dLabel;
          currentDayObj = { dayLabel: dLabel, events: [] };
          currentMonthObj.days.push(currentDayObj);
        }

        currentDayObj.events.push(event);
      }
      return sections;
    },
    isFiltersEmpty() {
      return (
        this.activeFilters.type.length === 3 &&
        this.activeFilters.class.length === 2
      );
    },
  },
  watch: {
    filteredHistory() {
      this.visibleCount = PAGE_SIZE;
      this.$nextTick(() => this.attachObserver());
    },
  },
  async created() {
    await this.fetchAllHistory();
  },
  mounted() {
    this.attachObserver();
  },
  beforeUnmount() {
    this.detachObserver();
  },
  methods: {
    async fetchAllHistory() {
      this.loadingInitial = true;
      this.fetchError = false;
      try {
        await Promise.all([
          this.fetchTierHistory(),
          this.fetchMaps(),
          this.fetchTopPlayerHistory(),
        ]);
        const hasAnyData =
          this.tierHistory.length > 0 ||
          this.maps.length > 0 ||
          this.topPlayerHistory.length > 0;
        if (!hasAnyData && this.activeFilters.type.length > 0) {
          this.fetchError = true;
        }
      } catch (e) {
        this.fetchError = true;
      } finally {
        this.loadingInitial = false;
        this.$nextTick(() => this.attachObserver());
      }
    },
    async fetchTierHistory() {
      try {
        const res = await fetch(
          `${API_BASE_URL}/maps/tier-rating-history/99999`,
        );
        if (res.ok) {
          const data = await res.json();
          this.tierHistory = Array.isArray(data) ? data : [];
        } else {
          this.tierHistory = [];
        }
      } catch (e) {
        this.tierHistory = [];
      }
    },
    async fetchMaps() {
      try {
        const res = await fetch(`${API_BASE_URL}/maps`);
        this.maps = res.ok ? await res.json() : [];
      } catch (e) {
        this.maps = [];
      }
    },
    async fetchTopPlayerHistory() {
      try {
        const res = await fetch(`${API_BASE_URL}/maps/top-player-history`);
        if (res.ok) {
          const data = await res.json();
          this.topPlayerHistory = Array.isArray(data) ? data : [];
        } else {
          this.topPlayerHistory = [];
        }
      } catch (e) {
        this.topPlayerHistory = [];
      }
    },
    retryFetch() {
      this.fetchAllHistory();
    },
    attachObserver() {
      this.detachObserver();
      const sentinel = this.$refs.sentinel;
      if (!sentinel) return;
      this.observer = new IntersectionObserver(
        (entries) => {
          if (entries[0].isIntersecting && this.hasMore && !this.loadingMore) {
            this.loadMore();
          }
        },
        { rootMargin: "300px" },
      );
      this.observer.observe(sentinel);
    },
    detachObserver() {
      if (this.observer) {
        this.observer.disconnect();
        this.observer = null;
      }
    },
    async loadMore() {
      if (!this.hasMore || this.loadingMore) return;
      this.loadingMore = true;
      await new Promise((r) => setTimeout(r, 300));
      this.visibleCount += PAGE_SIZE;
      this.loadingMore = false;
    },
    toggleFilter(category, value) {
      const arr = this.activeFilters[category];
      const i = arr.indexOf(value);
      if (i === -1) arr.push(value);
      else arr.splice(i, 1);
    },
    clearFilters() {
      this.activeFilters = {
        type: ["tier", "map_release", "top_player"],
        class: ["soldier", "demoman"],
      };
    },
    soldierChanged(event) {
      return (
        event.old_soldier_tier !== event.new_soldier_tier ||
        event.old_soldier_rating !== event.new_soldier_rating
      );
    },
    demomanChanged(event) {
      return (
        event.old_demoman_tier !== event.new_demoman_tier ||
        event.old_demoman_rating !== event.new_demoman_rating
      );
    },
    hasSoldierChange(e) {
      return this.soldierChanged(e);
    },
    hasDemomanChange(e) {
      return this.demomanChanged(e);
    },
    toDate(v) {
      return typeof v === "number" ? new Date(v * 1000) : new Date(v);
    },
    formatDay(v) {
      if (!v) return "";
      return this.toDate(v).toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
      });
    },
    formatMonthYear(v) {
      if (!v) return "";
      return this.toDate(v).toLocaleDateString("en-GB", {
        month: "long",
        year: "numeric",
      });
    },
    getClassIcons(intendedClass) {
      if (intendedClass === 3)
        return [
          { src: "/icons/soldier.png", alt: "Soldier", cls: "hist-class-icon" },
        ];
      if (intendedClass === 4)
        return [
          { src: "/icons/demoman.png", alt: "Demoman", cls: "hist-class-icon" },
        ];
      if (intendedClass === 5)
        return [
          {
            src: "/icons/soldier.png",
            alt: "Soldier",
            cls: "hist-class-icon hist-class-icon-dual",
          },
          {
            src: "/icons/demoman.png",
            alt: "Demoman",
            cls: "hist-class-icon hist-class-icon-dual",
          },
        ];
      return [];
    },
    avatarError(e) {
      e.target.src = "/avatars/golly.jpg";
    },
  },
};
</script>

<style scoped>
.content-container {
  width: 100%;
  max-width: 1200px;
}
.page-header {
  text-align: center;
  margin-bottom: 1.5rem;
  padding: 1rem 0 0;
}
.divider {
  border: none;
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent,
    var(--color-primary),
    transparent
  );
  margin: 20px 0 30px;
  opacity: 0.6;
}

.api-error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 4rem 2rem;
  gap: 1.4rem;
  background: rgba(248, 113, 113, 0.1);
  border: 1px solid rgba(248, 113, 113, 0.3);
  border-radius: 12px;
  margin: 2rem 0;
}
.error-icon {
  font-size: 3rem;
}
.api-error-state h3 {
  color: #f87171;
  margin: 0;
}
.api-error-state p {
  color: var(--color-text-soft);
  margin: 0;
}
.retry-btn {
  margin-top: 0.5rem;
  background: rgba(248, 113, 113, 0.2);
  border-color: #f87171;
  color: #f87171;
}
.retry-btn:hover {
  background: rgba(248, 113, 113, 0.3);
}

.table-container-wrapper {
  border-radius: 8px;
  overflow: hidden;
}
.filter-bar-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  padding: 0.75rem 1rem;
  position: sticky;
  top: 75px;
  z-index: 100;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  margin: 0 -1rem;
}
.filter-group {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 6px;
}
.filter-group-clear {
  justify-content: flex-end;
  align-self: flex-end;
}
.filter-title {
  font-weight: bold;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: var(--color-text-soft);
  margin: 0;
}
.filter-btns {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}
.global-btn:disabled {
  opacity: 0.3;
  cursor: default;
}
.global-btn:disabled:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: var(--color-border-soft);
}
.clear-btn {
  border-style: dashed;
  color: var(--color-text-soft);
}
.btn-class-icon {
  width: 16px;
  height: 16px;
}

.initial-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 5rem 2rem;
  gap: 1rem;
}
.load-more-spinner {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem;
  gap: 0.75rem;
}
.spinner-label {
  font-size: 0.85rem;
  color: var(--color-text-soft);
}
.spinner-ring {
  width: 48px;
  height: 48px;
  border: 4px solid rgba(74, 111, 165, 0.2);
  border-top-color: #d6e4ff;
  border-radius: 50%;
  animation: spin 0.75s linear infinite;
}
.spinner-ring.small {
  width: 28px;
  height: 28px;
  border-width: 3px;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.sentinel {
  height: 1px;
  width: 100%;
}
.end-of-feed {
  text-align: center;
  padding: 2rem;
  font-size: 0.8rem;
  color: var(--color-text-soft);
  opacity: 0.5;
  letter-spacing: 1px;
}

.timeline-container {
  width: 100%;
  padding: 1.5rem;
  position: relative;
}
.timeline-container::before {
  content: "";
  position: absolute;
  left: calc(1.5rem + 55px);
  top: 1.5rem;
  bottom: 1.5rem;
  width: 2px;
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.1),
    rgba(74, 111, 165, 0.4),
    rgba(74, 111, 165, 0.1)
  );
  z-index: 0;
  pointer-events: none;
}

.timeline-month-section {
  margin-bottom: 3rem;
  position: relative;
  z-index: 1;
}

/* Centered month label */
.timeline-month-header {
  margin: 0 0 1.5rem 0;
  padding: 0.5rem 0;
  z-index: 50;
  background: rgba(18, 26, 40, 0.95);
  backdrop-filter: blur(8px);
  border-bottom: 1px solid rgba(74, 111, 165, 0.3);
  border-top: 1px solid rgba(74, 111, 165, 0.3);
  text-align: center;
}
.month-label {
  font-size: 1.1rem;
  font-weight: 800;
  color: #d6e4ff;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.timeline-day {
  display: flex;
  margin-bottom: 1.5rem;
  gap: 1.5rem;
  position: relative;
  z-index: 1;
}
.day-marker {
  flex: 0 0 110px;
  text-align: right;
  padding-top: 0.3rem;
  position: relative;
}
.day-badge {
  background: rgba(18, 26, 40, 0.95);
  border: 1px solid var(--color-primary);
  padding: 0.25rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: bold;
  color: white;
  display: inline-block;
  white-space: nowrap;
  position: relative;
  z-index: 2;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}

.day-events {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  min-width: 0;
}

/* ── Authors row (below ratings) ── */
.hist-authors-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.8);
}
.hist-authors-avatars {
  display: flex;
  align-items: center;
}
.hist-authors-avatars .hist-author-avatar {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.35);
  margin-right: -6px;
  object-fit: cover;
}
.hist-authors-avatars .hist-author-avatar:last-child {
  margin-right: 0;
}
.hist-author-name {
  font-weight: 600;
  margin-left: 10px;
}

/* Date pushed to far right in meta row */
.hist-date-right {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-left: auto;
  font-weight: 600;
  opacity: 0.85;
  white-space: nowrap;
}
.hist-date-right svg {
  width: 13px;
  height: 13px;
  flex-shrink: 0;
}

.day-events-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  width: 100%;
}
.event-col-full {
  width: 100%;
}
.event-col-half {
  flex: 0 0 calc(50% - 0.5rem);
  max-width: calc(50% - 0.5rem);
  min-width: 220px;
}
.hist-card {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  background: linear-gradient(
      to bottom,
      rgba(255, 255, 255, 0.12) 0%,
      rgba(255, 255, 255, 0.06) 40%,
      rgba(255, 255, 255, 0) 100%
    ),
    rgba(255, 255, 255, 0.04);
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.6);
  cursor: pointer;
  color: #fff;
  text-decoration: none;
  transition: box-shadow 0.35s ease;
  transform: translateZ(0);
  will-change: transform;
  min-height: 300px;
  display: flex;
  flex-direction: column;
  width: 100%;
}
.hist-card-sm {
  min-height: 260px;
}
.hist-card:hover {
  box-shadow: 0 0 50px rgba(102, 126, 234, 0.4), 0 20px 40px rgba(0, 0, 0, 0.5);
}
.hist-card-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  transition: transform 0.4s ease;
  z-index: 0;
  background-color: #121a28;
}
.hist-card-bg::before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.2) 0%,
    rgba(0, 0, 0, 0.55) 50%,
    rgba(0, 0, 0, 0.85) 100%
  );
  z-index: 1;
}
.hist-card:hover .hist-card-bg {
  transform: scale(1.04);
}
.hist-badge {
  position: absolute;
  top: 18px;
  left: 18px;
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  z-index: 10;
}

/* Distinct badge colours per event type */
.tier-badge-label {
  background: rgba(251, 191, 36, 0.18);
  border: 1px solid rgba(251, 191, 36, 0.55);
  color: #fde68a;
}
.map-badge-label {
  background: rgba(196, 181, 253, 0.18);
  border: 1px solid rgba(196, 181, 253, 0.55);
  color: #c4b5fd;
}
/* top-10 badge is on the tp-swap-header, styled via tp-swap-badge below */

.badge-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  box-shadow: 0 0 8px currentColor;
  animation: pulse-dot 2s ease-in-out infinite;
  flex-shrink: 0;
}
/* Tier dot — amber */
.tier-dot {
  background: #fde68a;
}
/* Map dot — emerald */
.map-dot {
  background: #c4b5fd;
}

@keyframes pulse-dot {
  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.5;
    transform: scale(0.7);
  }
}
.hist-class-icons {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 10;
  display: flex;
  gap: 6px;
}
.hist-class-icon {
  width: 40px;
  height: 40px;
  background: rgba(0, 0, 0, 0.65);
  border-radius: 50%;
  padding: 7px;
  border: 2px solid rgba(74, 111, 165, 0.35);
}
.hist-class-icon-dual {
  width: 32px;
  height: 32px;
  padding: 5px;
}
.hist-card-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  flex: 1;
  padding: 20px 22px 20px;
  gap: 10px;
}
.hist-spacer {
  flex: 1;
}
.hist-map-name {
  font-size: 1.7rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.5px;
  margin: 0;
  text-shadow: 0 2px 12px rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.hist-zone-badge {
  font-size: 0.95rem;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.9);
  background: rgba(74, 111, 165, 0.4);
  border: 1px solid rgba(74, 111, 165, 0.6);
  border-radius: 8px;
  padding: 3px 10px;
}
.hist-ratings-grid {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  flex-wrap: wrap;
}
.hist-rating-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}
.hist-rating-label {
  font-size: 1rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: rgba(255, 255, 255, 0.6);
}
.hist-pill-row {
  display: flex;
  align-items: center;
  gap: 5px;
}
.hist-pill {
  display: inline-block;
  padding: 5px 11px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.82rem;
  color: var(--color-text, #eee);
  text-align: center;
  min-width: 40px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  transition: all 0.2s;
}
.hist-pill.changed {
  border: 2px solid rgba(255, 255, 255, 0.85);
  box-shadow: 0 0 12px rgba(102, 126, 234, 0.55), 0 4px 12px rgba(0, 0, 0, 0.3);
}
.hist-arrow {
  color: #d6e4ff;
  font-size: 1rem;
  font-weight: bold;
  opacity: 0.9;
}
.hist-ratings-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  align-self: center;
}
.tier-color.tier-0 {
  background: rgba(51, 51, 51, 0.5);
  color: var(--color-text);
}
.tier-color.tier-1 {
  background: rgba(110, 208, 246, 0.5);
  color: var(--color-text);
}
.tier-color.tier-2 {
  background: rgba(86, 179, 233, 0.5);
  color: var(--color-text);
}
.tier-color.tier-3 {
  background: rgba(69, 184, 173, 0.5);
  color: var(--color-text);
}
.tier-color.tier-4 {
  background: rgba(101, 193, 139, 0.5);
  color: var(--color-text);
}
.tier-color.tier-5 {
  background: rgba(163, 217, 119, 0.5);
  color: var(--color-text);
}
.tier-color.tier-6 {
  background: rgba(243, 230, 131, 0.5);
  color: var(--color-text);
}
.tier-color.tier-7 {
  background: rgba(246, 194, 103, 0.5);
  color: var(--color-text);
}
.tier-color.tier-8 {
  background: rgba(240, 141, 91, 0.5);
  color: var(--color-text);
}
.tier-color.tier-9 {
  background: rgba(230, 105, 94, 0.5);
  color: var(--color-text);
}
.tier-color.tier-10 {
  background: rgba(214, 69, 69, 0.5);
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
.hist-meta-row {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.6);
}
.hist-meta-row span {
  display: flex;
  align-items: center;
  gap: 4px;
}
.hist-meta-row svg {
  width: 13px;
  height: 13px;
  flex-shrink: 0;
}

/* Tags inline in meta row */
.map-tag {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 20px;
  border: 1px solid;
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--color-text) !important;
}

.tp-swap-card {
  width: 100%;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.1),
    rgba(37, 55, 82, 0.15)
  );
  border: 1px solid rgba(74, 111, 165, 0.25);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
}
.tp-swap-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 16px;
  border-bottom: 1px solid rgba(74, 111, 165, 0.2);
  background: rgba(0, 0, 0, 0.2);
}

/* Top-10 badge — violet/purple to distinguish */
.tp-swap-badge {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.7px;
  color: #6ee7b7;
}

.tp-swap-date {
  font-size: 0.72rem;
  color: var(--color-text-soft);
}
.tp-swap-body {
  display: flex;
  align-items: stretch;
}
.tp-player-col {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 18px;
  text-decoration: none;
  color: var(--color-text);
  transition: background 0.2s;
  min-width: 0;
  cursor: pointer;
}
.tp-gainer {
  background: rgba(74, 222, 128, 0.06);
}
.tp-gainer:hover {
  background: rgba(74, 222, 128, 0.12);
}
.tp-loser {
  background: rgba(248, 113, 113, 0.06);
  justify-content: flex-end;
  text-align: right;
  flex-direction: row-reverse;
}
.tp-loser:hover {
  background: rgba(248, 113, 113, 0.12);
}
.tp-solo-loser {
  justify-content: flex-start !important;
  text-align: left !important;
  flex-direction: row !important;
}
.tp-swap-vs {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 10px;
  font-size: 1.3rem;
  color: var(--color-text-soft);
  background: rgba(0, 0, 0, 0.15);
  border-left: 1px solid rgba(255, 255, 255, 0.06);
  border-right: 1px solid rgba(255, 255, 255, 0.06);
  flex-shrink: 0;
}
.tp-avatar-wrap {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}
.tp-gainer .tp-avatar-wrap {
  border: 2px solid #4ade80;
}
.tp-loser .tp-avatar-wrap {
  border: 2px solid #f87171;
}
.tp-avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.tp-player-info {
  display: flex;
  flex-direction: column;
  gap: 3px;
  min-width: 0;
}
.tp-name {
  font-size: 1rem;
  font-weight: 800;
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.tp-country {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.75rem;
  color: var(--color-text-soft);
}
.tp-loser .tp-country {
  flex-direction: row-reverse;
}
.tp-flag {
  width: 16px;
  height: 12px;
  border-radius: 2px;
  flex-shrink: 0;
}
.tp-rank-pill {
  display: flex;
  align-items: center;
  gap: 5px;
  border-radius: 8px;
  padding: 5px 10px;
  font-size: 0.85rem;
  font-weight: 700;
  white-space: nowrap;
  flex-shrink: 0;
  margin-left: auto;
}
.tp-loser .tp-rank-pill {
  margin-left: 0;
  margin-right: auto;
}
.tp-rank-pill.rank-up {
  background: rgba(74, 222, 128, 0.15);
  border: 1px solid rgba(74, 222, 128, 0.4);
}
.tp-rank-pill.rank-down {
  background: rgba(248, 113, 113, 0.15);
  border: 1px solid rgba(248, 113, 113, 0.4);
}
.tp-rank-old {
  color: rgba(255, 255, 255, 0.45);
  text-decoration: line-through;
  font-size: 0.8rem;
}
.tp-rank-new {
  font-size: 1rem;
}
.rank-up .tp-rank-new,
.rank-up .tp-delta {
  color: #4ade80;
}
.rank-down .tp-rank-new,
.rank-down .tp-delta {
  color: #f87171;
}
.tp-delta {
  font-size: 0.7rem;
  font-weight: 800;
}
.rank-up {
  color: #4ade80;
}
.rank-down {
  color: #f87171;
}
.no-events {
  text-align: center;
  padding: 3rem;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 12px;
  color: var(--color-text-soft);
  font-style: italic;
}

@media (max-width: 992px) {
  .filter-bar-row {
    gap: 1rem;
  }
  .hist-map-name {
    font-size: 1.2rem;
  }
  .hist-rating-label {
    font-size: 0.8rem;
  }
  .hist-pill {
    font-size: 0.72rem;
    padding: 4px 8px;
    min-width: 32px;
  }
  .hist-arrow {
    font-size: 0.85rem;
  }
  .hist-zone-badge {
    font-size: 0.8rem;
    padding: 2px 7px;
  }
  .hist-card-sm {
    min-height: 220px;
  }
  .tp-swap-body {
    flex-direction: column;
  }
  .tp-player-col {
    width: 100%;
    justify-content: flex-start !important;
    flex-direction: row !important;
    text-align: left !important;
  }
  .tp-loser .tp-country {
    flex-direction: row;
  }
  .tp-rank-pill {
    margin-left: auto !important;
    margin-right: 0 !important;
  }
  .tp-swap-vs {
    padding: 6px;
    border-left: none;
    border-right: none;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    justify-content: center;
  }
  .tp-name {
    font-size: 0.9rem;
  }
  .tp-avatar-wrap {
    width: 40px;
    height: 40px;
  }
}
@media (max-width: 768px) {
  .timeline-container::before {
    display: none;
  }
  .timeline-month-header {
    margin: 0 0 1rem 0;
    border-left: 4px solid var(--color-primary);
    border-bottom: none;
    padding-left: 1rem;
    text-align: left;
  }
  .timeline-day {
    flex-direction: column;
    gap: 0.5rem;
  }
  .day-marker {
    flex: auto;
    text-align: left;
  }
  .event-col-half {
    flex: 0 0 100%;
    max-width: 100%;
  }
}
@media (max-width: 480px) {
  .timeline-container {
    padding: 1rem 0.5rem;
  }
  .filter-bar-row {
    padding: 1rem;
  }
  .tp-rank-pill {
    padding: 4px 7px;
    font-size: 0.75rem;
  }
  .tp-rank-old {
    font-size: 0.7rem;
  }
  .tp-rank-new {
    font-size: 0.85rem;
  }
}

.timeline-event {
  opacity: 0;
  transform: translateY(10px);
  animation: timelineFade 0.5s ease forwards;
}

@keyframes timelineFade {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
