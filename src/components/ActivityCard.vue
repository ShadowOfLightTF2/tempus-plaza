<template>
  <div v-if="minMode" class="activity-card">
    <div class="activity-player-section">
      <img
        :src="`/icons/${record.class}.png`"
        alt="Class"
        class="class-icon-row"
      />
      <SmartLink
        :to="{ name: 'PlayerPage', params: { playerId: record.player_id } }"
        class="player-link"
      >
        <img
          :src="record.avatar"
          alt="Avatar"
          class="player-avatar-large"
          onerror="this.src='/avatars/golly.jpg'"
        />
        <div class="player-details">
          <div class="player-name-large" v-dragscroll>
            {{ record.player }}
          </div>
        </div>
      </SmartLink>
    </div>
    <SmartLink
      :to="{ name: 'MapPage', params: { mapId: record.map_id } }"
      class="activity-map-section"
    >
      <div class="min-mode-intended-class">
        <template
          v-if="record.intended_class === 3 || record.intended_class === '3'"
        >
          <img
            src="/icons/soldier.png"
            alt="Soldier"
            class="min-mode-intended-class-icon"
          />
        </template>
        <template
          v-else-if="
            record.intended_class === 4 || record.intended_class === '4'
          "
        >
          <img
            src="/icons/demoman.png"
            alt="Demoman"
            class="min-mode-intended-class-icon"
          />
        </template>
        <template
          v-else-if="
            record.intended_class === 5 || record.intended_class === '5'
          "
        >
          <img
            src="/icons/soldier.png"
            alt="Soldier"
            class="min-mode-intended-class-icon dual"
          />
          <img
            src="/icons/demoman.png"
            alt="Demoman"
            class="min-mode-intended-class-icon dual"
          />
        </template>
      </div>

      <div class="map-details" v-dragscroll>
        <div class="map-name-row">
          <HoverPreview :map-name="record.map">
            <span class="map-name-large">{{ record.map }}</span>
          </HoverPreview>
          <span v-if="record.recordType !== 'map'" class="map-type-badge">
            <template v-if="record.recordType === 'course'"
              >🚩 C{{ record.index }}</template
            >
            <template v-else-if="record.recordType === 'bonus'"
              >⭐ B{{ record.index }}</template
            >
          </span>
        </div>
      </div>

      <div class="min-mode-meta">
        <div class="map-tier-rating">
          <span
            v-if="record.tier != null"
            class="tier-badge"
            :class="`tier-${record.tier}`"
            >T{{ record.tier }}</span
          >
          <span
            v-if="record.rating != null"
            class="rating-badge"
            :class="`rating-${record.rating}`"
            >R{{ record.rating }}</span
          >
        </div>
      </div>
    </SmartLink>
    <div class="activity-time-section">
      <div class="rank-time-section">
        <div class="time-display">
          {{ record.time }}
          <span v-if="record.old_duration" class="improvement-text">
            {{ activeView.improvementLabel }}
            {{ getImprovementText(record.duration, record.old_duration) }}
          </span>
        </div>
        <div
          v-if="activeView.showRank"
          class="rank-display"
          :class="{
            'placement-silver': record.rank === 2,
            'placement-bronze': record.rank === 3,
            'placement-group-1': currentView === 'group1s',
          }"
        >
          #{{ record.rank
          }}<span v-if="record.old_rank" class="rank-change">{{
            getRankChange(record.rank, record.old_rank)
          }}</span>
        </div>
      </div>
    </div>

    <div class="activity-timeago-section">
      <span class="timeago-long">{{ getTimeAgo(record.date) }}</span>
      <span class="timeago-short">{{ getTimeAgoShort(record.date) }}</span>
    </div>
  </div>

  <div v-else class="activity-card-normal">
    <div class="normal-class-section">
      <img
        :src="`/icons/${record.class}.png`"
        alt="Class"
        class="normal-class-icon"
      />
    </div>

    <div class="normal-player-section">
      <SmartLink
        :to="{ name: 'PlayerPage', params: { playerId: record.player_id } }"
        class="normal-player-link"
      >
        <img
          :src="record.avatar"
          alt="Avatar"
          class="normal-avatar"
          onerror="this.src='/avatars/golly.jpg'"
        />
        <div class="normal-player-details">
          <div class="normal-player-name">{{ record.player }}</div>
          <div class="normal-player-country">
            <img
              :src="getFlagImageUrl(record.country_code)"
              alt="Flag"
              class="flag-icon-normal"
            />
            {{ record.country }}
          </div>
        </div>
      </SmartLink>
    </div>

    <SmartLink
      :to="{ name: 'MapPage', params: { mapId: record.map_id } }"
      class="normal-map-section"
      :style="`background-image: url('/map-backgrounds/medium/${record.map}.webp')`"
    >
      <div class="normal-map-overlay"></div>
      <div class="normal-map-details">
        <div class="normal-map-intended-class">
          <template
            v-if="record.intended_class === 3 || record.intended_class === '3'"
          >
            <img
              src="/icons/soldier.png"
              alt="Soldier"
              class="normal-map-class-icon"
            />
          </template>
          <template
            v-else-if="
              record.intended_class === 4 || record.intended_class === '4'
            "
          >
            <img
              src="/icons/demoman.png"
              alt="Demoman"
              class="normal-map-class-icon"
            />
          </template>
          <template
            v-else-if="
              record.intended_class === 5 || record.intended_class === '5'
            "
          >
            <img
              src="/icons/soldier.png"
              alt="Soldier"
              class="normal-map-class-icon dual"
            />
            <img
              src="/icons/demoman.png"
              alt="Demoman"
              class="normal-map-class-icon dual"
            />
          </template>
        </div>
        <div class="normal-map-name-row">
          <HoverPreview :map-name="record.map">
            <span class="normal-map-name">{{ record.map }}</span>
          </HoverPreview>
          <span v-if="record.recordType !== 'map'" class="map-type-badge">
            <template v-if="record.recordType === 'course'"
              >🚩 C{{ record.index }}</template
            >
            <template v-else-if="record.recordType === 'bonus'"
              >⭐ B{{ record.index }}</template
            >
          </span>
        </div>
      </div>
      <div class="normal-map-top-right">
        <div class="normal-map-tier-rating">
          <span
            v-if="record.tier != null"
            class="tier-badge"
            :class="`tier-${record.tier}`"
            >T{{ record.tier }}</span
          >
          <span
            v-if="record.rating != null"
            class="rating-badge"
            :class="`rating-${record.rating}`"
            >R{{ record.rating }}</span
          >
        </div>
      </div>
    </SmartLink>

    <div class="normal-time-section">
      <div class="normal-time-rank-row">
        <div class="normal-time-display">
          {{ record.time }}
          <span v-if="record.old_duration" class="improvement-text">
            {{ activeView.improvementLabel }}
            {{ getImprovementText(record.duration, record.old_duration) }}
          </span>
        </div>
        <div
          v-if="activeView.showRank"
          class="normal-rank-display"
          :class="{
            'placement-silver': record.rank === 2,
            'placement-bronze': record.rank === 3,
            'placement-group-1': currentView === 'group1s',
          }"
        >
          #{{ record.rank
          }}<span v-if="record.old_rank" class="rank-change">{{
            getRankChange(record.rank, record.old_rank)
          }}</span>
        </div>
      </div>
      <div class="normal-timeago">{{ getTimeAgo(record.date) }}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ActivityCard",
  directives: {
    dragscroll: {
      mounted(el) {
        let isDown = false;
        let startX;
        let scrollLeft;
        el.addEventListener("mousedown", (e) => {
          isDown = true;
          startX = e.pageX - el.offsetLeft;
          scrollLeft = el.scrollLeft;
          e.preventDefault();
        });
        el.addEventListener("mouseleave", () => {
          isDown = false;
        });
        el.addEventListener("mouseup", () => {
          isDown = false;
        });
        el.addEventListener("mousemove", (e) => {
          if (!isDown) return;
          const x = e.pageX - el.offsetLeft;
          el.scrollLeft = scrollLeft - (x - startX);
        });
      },
    },
  },
  props: {
    record: { type: Object, required: true },
    currentView: { type: String, required: true },
    activeView: { type: Object, required: true },
    minMode: { type: Boolean, default: true },
    getTimeAgo: { type: Function, required: true },
    getTimeAgoShort: { type: Function, required: true },
    getImprovementText: { type: Function, required: true },
    getRankChange: { type: Function, required: true },
    getFlagImageUrl: { type: Function, required: true },
  },
};
</script>

<style scoped>
/* ── Min-mode card ─────────────────────────────────────────────────────────── */
.activity-card {
  display: grid;
  grid-template-columns: minmax(0, 250px) 1fr 250px 100px;
  padding: 0.5rem;
  gap: 0.75rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  transition: all 0.2s ease;
}

.activity-card:hover {
  border: 1px solid var(--color-border-semi-soft);
  background: rgba(74, 111, 165, 0.2);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.activity-player-section {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  overflow: hidden;
  cursor: pointer;
}

.player-link {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: inherit;
  width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.player-avatar-large {
  width: 24px;
  height: 24px;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
  flex-shrink: 0;
}

.player-details {
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.player-name-large {
  font-size: 0.9rem;
  font-weight: bold;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  color: var(--color-text-clickable);
}

.class-icon-row {
  width: 26px;
  height: 26px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  padding: 3px;
  border: 1px solid rgba(74, 111, 165, 0.4);
  flex-shrink: 0;
}

.activity-map-section {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0.35rem;
  text-decoration: none;
  color: inherit;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 0.75rem;
  cursor: pointer;
  overflow: hidden;
  min-width: 0;
}

.map-details {
  display: flex;
  flex-direction: row;
  gap: 0.5rem;
  overflow-x: auto;
  overflow-y: hidden;
  scrollbar-width: none;
  min-width: 0;
  flex: 1;
}

.map-details::-webkit-scrollbar {
  display: none;
}

.map-name-row {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  min-width: max-content;
}

.map-name-large {
  font-size: 0.9rem;
  font-weight: bold;
  color: var(--color-text-clickable);
  white-space: nowrap;
}

.map-type-badge {
  display: inline-flex;
  align-items: center;
  flex-shrink: 0;
  font-size: 0.8rem;
  font-weight: 700;
  color: #ffffff;
  background: rgba(74, 111, 165, 0.45);
  border: 1px solid rgba(74, 111, 165, 0.5);
  border-radius: 4px;
  padding: 0.05rem 0.35rem;
  white-space: nowrap;
}

.activity-time-section {
  display: flex;
  justify-content: center;
  flex-direction: column;
  gap: 0.4rem;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 0.75rem;
  white-space: nowrap;
  overflow: hidden;
}

.rank-time-section {
  display: flex;
  flex-direction: row;
  gap: 12px;
  align-items: center;
}

.time-display {
  font-size: 0.9rem;
  font-weight: bold;
  color: var(--color-text);
}

.rank-display {
  font-size: 0.9rem;
  font-weight: bold;
  color: var(--color-primary);
  padding-left: 12px;
  border-left: 1px solid var(--color-border-semi-soft);
}

.activity-timeago-section {
  display: flex;
  align-items: center;
  justify-content: center;
  border-left: 1px solid var(--color-border-soft);
  padding-left: 0.75rem;
  font-size: 0.75rem;
  color: var(--color-text);
  opacity: 0.65;
  white-space: nowrap;
}

.timeago-short {
  display: none;
}

.improvement-text {
  font-size: 0.75rem;
  color: #4ade80;
  margin-left: 0.4rem;
  font-weight: normal;
}

.rank-change {
  font-size: 0.73rem;
  color: #4ade80;
  margin-left: 0.2rem;
  font-weight: normal;
}

.min-mode-meta {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
  margin-left: auto;
  padding-right: 0.5rem;
}

.min-mode-intended-class {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 4px;
  flex-shrink: 0;
}

.min-mode-intended-class-icon {
  width: 22px;
  height: 22px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  padding: 3px;
  border: 1px solid rgba(74, 111, 165, 0.4);
  display: block;
}

.min-mode-intended-class-icon.dual {
  width: 19px;
  height: 19px;
  padding: 2px;
}

.map-tier-rating {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 3px;
}

.activity-card-normal {
  display: grid;
  grid-template-columns: 0.2fr 1.2fr 2fr 1.5fr;
  gap: 0.5rem;
  padding: 0.3rem 0.6rem;
  border: 1px solid var(--color-border-soft);
  border-radius: 8px;
  transition: all 0.2s ease;
  min-height: 75px;
  align-items: stretch;
}

.activity-card-normal:nth-child(odd) {
  background: rgba(255, 255, 255, 0.05);
}

.activity-card-normal:nth-child(even) {
  background: rgba(255, 255, 255, 0.08);
}

.activity-card-normal:hover {
  border-color: var(--color-border-semi-soft);
  background: rgba(74, 111, 165, 0.2) !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.normal-class-section {
  display: flex;
  align-items: center;
  justify-content: center;
}

.normal-class-icon {
  width: 36px;
  height: 36px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  padding: 5px;
  border: 2px solid rgba(74, 111, 165, 0.3);
}

.normal-player-section {
  display: flex;
  align-items: center;
  padding-left: 0.5rem;
  overflow: hidden;
  border-right: 1px solid var(--color-border-soft);
}

.normal-player-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  text-decoration: none;
  color: inherit;
  width: 100%;
  cursor: pointer;
}

.normal-avatar {
  width: 54px;
  height: 54px;
  border: 2px solid var(--color-primary);
  border-radius: 4px;
  flex-shrink: 0;
}

.normal-player-details {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  flex: 1;
  overflow: hidden;
}

.normal-player-name {
  font-size: 1.1rem;
  font-weight: bold;
  color: var(--color-text-clickable);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 190px;
}

.normal-player-country {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--color-text);
  font-size: 0.85rem;
  opacity: 0.85;
}

.flag-icon-normal {
  width: 18px;
  height: auto;
}

.normal-map-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  text-decoration: none;
  color: inherit;
  cursor: pointer;
  border-radius: 8px;
  padding: 0 0.75rem 0 1.5rem;
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  position: relative;
  align-self: stretch;
}

.normal-map-overlay {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  border-radius: inherit;
  pointer-events: none;
}

.normal-map-details {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: row;
  gap: 0.3rem;
  flex: 1;
  min-width: 0;
  overflow: hidden;
}

.normal-map-name-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.normal-map-name {
  font-size: 1.1rem;
  font-weight: bold;
  color: var(--color-text-clickable);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 235px;
  display: inline-block;
}

.normal-map-top-right {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  margin-left: auto;
  flex-shrink: 0;
}

.normal-map-intended-class {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 3px;
  z-index: 10;
}

.normal-map-class-icon {
  width: 26px;
  height: 26px;
  background: rgba(0, 0, 0, 0.45);
  border-radius: 50%;
  padding: 3px;
  border: 2px solid rgba(74, 111, 165, 0.4);
  display: block;
}

.normal-map-class-icon.dual {
  width: 22px;
  height: 22px;
  padding: 3px;
}

.normal-map-tier-rating {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 3px;
}

.normal-time-section {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 0.3rem;
  border-left: 1px solid var(--color-border-soft);
  padding: 0.5rem 1rem;
  text-align: center;
}

.normal-time-rank-row {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0.75rem;
}

.normal-time-display {
  font-size: 1rem;
  font-weight: bold;
  color: var(--color-text);
  white-space: nowrap;
}

.normal-rank-display {
  font-size: 1rem;
  font-weight: bold;
  color: var(--color-primary);
}

.normal-timeago {
  font-size: 0.75rem;
  color: var(--color-text);
  opacity: 0.65;
  white-space: nowrap;
}

.tier-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 800;
  border-radius: 4px;
  padding: 0.1rem 0.35rem;
  white-space: nowrap;
  z-index: 10;
}

.rating-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 700;
  border-radius: 4px;
  padding: 0.1rem 0.3rem;
  white-space: nowrap;
  z-index: 10;
}

.placement-silver {
  color: #c0c0c0 !important;
}
.placement-bronze {
  color: #cd7f32 !important;
}
.placement-group-1 {
  color: #ff9797af !important;
}

@media (max-width: 1200px) {
  .activity-card {
    grid-template-columns: minmax(0, 200px) 1fr 250px 100px;
  }

  .activity-card-normal {
    min-height: 62px;
    gap: 0.4rem;
    padding: 0.25rem 0.5rem;
  }

  .normal-avatar {
    width: 40px;
    height: 40px;
  }
  .normal-player-link {
    gap: 0.6rem;
  }
  .normal-player-name {
    font-size: 0.88rem;
    max-width: 140px;
  }
  .normal-player-country {
    font-size: 0.75rem;
  }
  .normal-map-name {
    font-size: 0.88rem;
    max-width: 165px;
  }
  .normal-time-display {
    font-size: 0.88rem;
  }
  .normal-rank-display {
    font-size: 0.88rem;
  }
  .normal-timeago {
    font-size: 0.7rem;
  }
  .normal-class-icon {
    width: 28px;
    height: 28px;
  }

  .normal-map-class-icon {
    width: 22px;
    height: 22px;
    padding: 3px;
  }
  .normal-map-class-icon.dual {
    width: 19px;
    height: 19px;
    padding: 2px;
  }
  .normal-time-section {
    padding: 0.4rem 0.6rem;
  }
  .map-type-badge {
    font-size: 0.75rem;
  }
  .improvement-text {
    font-size: 0.68rem;
  }
}

@media (max-width: 992px) {
  .rating-badge,
  .tier-badge {
    font-size: 0.55rem;
  }

  .timeago-long {
    display: none;
  }
  .timeago-short {
    display: inline;
  }

  .activity-card {
    grid-template-columns: minmax(0, 150px) minmax(140px, 1fr) 210px 55px;
  }

  .activity-card .player-name-large,
  .activity-card .map-name-large,
  .activity-card .time-display,
  .activity-card .rank-display {
    font-size: 0.72rem;
  }

  .activity-card .map-type-badge,
  .activity-card .rank-change,
  .activity-card .improvement-text {
    font-size: 0.65rem;
  }

  .class-icon-row {
    width: 24px;
    height: 24px;
  }
  .min-mode-meta {
    display: none;
  }

  .activity-card-normal {
    grid-template-columns: 1fr;
    gap: 0.6rem;
    padding: 0.75rem;
  }

  .normal-class-section {
    display: none;
  }

  .normal-map-section {
    border-left: none;
    border-top: 1px solid var(--color-border-soft);
    padding-left: 1rem;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    min-height: 50px;
    border-radius: 6px;
  }

  .normal-time-section {
    border-left: none;
    border-top: 1px solid var(--color-border-soft);
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0 0 0;
    text-align: left;
  }

  .normal-timeago {
    margin-left: auto;
  }
}

@media (max-width: 767.98px) {
  .class-icon-row {
    width: 22px;
    height: 22px;
  }

  .activity-card {
    grid-template-columns: minmax(0, 130px) 1fr auto;
    grid-template-rows: auto;
    gap: 0.5rem;
  }

  .map-tier-rating,
  .normal-map-tier-rating {
    display: none;
  }

  .activity-player-section {
    grid-column: 1;
    grid-row: 1;
    overflow: hidden;
  }

  .activity-map-section {
    grid-column: 2;
    grid-row: 1;
    border-left: 1px solid var(--color-border-soft);
    border-top: none;
    padding-left: 0.75rem;
    padding-top: 0;
    overflow: hidden;
  }

  .activity-time-section {
    grid-column: 3;
    grid-row: 1;
    border-left: 1px solid var(--color-border-soft);
    border-top: none;
    padding-left: 0.75rem;
    padding-top: 0;
  }

  .activity-timeago-section {
    display: none;
  }

  .player-link,
  .player-details,
  .map-details,
  .map-name-row {
    overflow: hidden;
    min-width: 0;
  }

  .player-name-large {
    white-space: nowrap;
    overflow-x: scroll;
    text-overflow: unset;
    scrollbar-width: none;
    cursor: grab;
  }

  .map-details {
    white-space: nowrap;
    overflow-x: scroll;
    scrollbar-width: none;
    cursor: grab;
  }

  .map-name-large {
    white-space: nowrap;
    overflow: visible;
    text-overflow: unset;
  }

  .map-name-row {
    overflow: visible;
    min-width: max-content;
  }

  .player-name-large::-webkit-scrollbar,
  .map-details::-webkit-scrollbar {
    display: none;
  }

  .improvement-text {
    display: none;
  }
  .rank-change {
    display: none;
  }

  .rank-time-section {
    flex-direction: column;
    gap: 2px;
    align-items: flex-start;
  }

  .rank-display {
    border-left: none;
    padding-left: 0;
  }

  .time-display {
    font-size: 0.7rem;
  }

  .activity-card .player-name-large,
  .activity-card .map-name-large,
  .activity-card .time-display,
  .activity-card .rank-display {
    font-size: 0.8rem;
  }
}
</style>
