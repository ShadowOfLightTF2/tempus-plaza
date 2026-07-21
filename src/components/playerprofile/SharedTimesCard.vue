<template>
  <div v-if="shouldShow" class="class-section">
    <div class="card stat-block mb-3">
      <div class="card-header">
        <p class="section-header">{{ sharedType }} (maps)</p>
      </div>
      <div class="card-body">
        <div class="row shared-card-row">
          <div v-if="loading" class="text-center">
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
              v-for="(sharedPlayer, index) in sharedTimes"
              :key="index"
              :to="{
                name: 'PlayerPage',
                params: { playerId: sharedPlayer.playerId },
              }"
              tag="div"
              class="d-flex align-items-center justify-content-between px-2 py-2 mb-2 shared-row"
            >
              <div class="d-flex align-items-center shared-row-name">
                <img
                  :src="sharedPlayer.avatar"
                  alt="Avatar"
                  class="rounded me-2 shared-avatar"
                />
                <span class="fw-semibold player-name">
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
</template>

<script>
export default {
  name: "SharedTimesCard",
  props: {
    sharedTimes: {
      type: Array,
      required: true,
    },
    sharedType: {
      type: String,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    g1Count: {
      type: Number,
      default: 0,
    },
    topTimesCount: {
      type: Number,
      default: 0,
    },
  },
  computed: {
    shouldShow() {
      return this.g1Count > 0 || this.topTimesCount > 0;
    },
  },
};
</script>
<style scoped>
.stat-block {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  min-height: 345px;
}
.card-header {
  margin-bottom: 0;
  border-bottom: none;
  color: var(--color-text);
  border-top-left-radius: 10px !important;
  border-top-right-radius: 10px !important;
  padding: 10px;
  padding-bottom: 0;
}
.section-header {
  color: var(--color-text);
  font-size: 20px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 0;
}
.card-body {
  padding: 10px;
  padding-bottom: 0;
}
.shared-card-row {
  padding: 5px 5px 5px 5px;
}
.shared-avatar {
  width: 25px;
  height: 25px;
  border: 2px solid var(--color-primary);
  flex-shrink: 0;
}
.shared-row {
  border: 1px solid var(--color-border-soft);
  background: rgba(255, 255, 255, 0.05);
  box-shadow: 0 0px 15px rgb(0, 0, 0, 0.5);
  color: var(--color-text-clickable);
  cursor: pointer;
  transition: transform 0.25s ease;
}
.shared-row:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.3),
    rgba(74, 111, 165, 0.1)
  );
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
.player-name {
  font-size: 0.95rem;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (max-width: 1400px) {
  .stat-block {
    min-height: 310px;
  }
  .section-header {
    font-size: 17px;
  }
  .player-name {
    font-size: 0.82rem;
  }
  .shared-count {
    font-size: 0.82rem;
  }
  .shared-avatar {
    width: 22px;
    height: 22px;
  }
}

@media (max-width: 1200px) {
  .stat-block {
    min-height: 275px;
  }
  .section-header {
    font-size: 15px;
  }
  .player-name {
    font-size: 0.72rem;
  }
  .shared-count {
    font-size: 0.72rem;
  }
  .shared-avatar {
    width: 18px;
    height: 18px;
  }
  .shared-row {
    padding-top: 4px !important;
    padding-bottom: 4px !important;
  }
}

@media (max-width: 991px) {
  .stat-block {
    min-height: unset;
  }
  .section-header {
    font-size: 13px;
  }
  .player-name {
    font-size: 0.65rem;
  }
  .shared-count {
    font-size: 0.65rem;
  }
  .shared-avatar {
    width: 16px;
    height: 16px;
  }
  .shared-card-row {
    padding: 3px;
  }
}

@media (max-width: 767px) {
  .stat-block {
    min-height: unset;
  }
  .section-header {
    font-size: 20px;
  }
  .player-name {
    font-size: 0.95rem;
  }
  .shared-count {
    font-size: 0.95rem;
  }
  .shared-avatar {
    width: 25px;
    height: 25px;
  }
  .shared-row {
    padding-top: 8px !important;
    padding-bottom: 8px !important;
  }
  .shared-card-row {
    padding: 5px;
  }
}
</style>
