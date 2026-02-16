<template>
  <div class="class-section">
    <div class="card stat-block mb-3">
      <div class="nav-bar left-nav-bar" @click="$emit('prev-stat')"></div>
      <div class="card-header">
        <p class="section-header">
          {{ classType.charAt(0).toUpperCase() + classType.slice(1) }} Stats
        </p>
        <p class="stat-type-label">{{ currentStatType }}</p>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center">
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
                  {{ stats.completion[classType] }}%
                </p>
              </div>
            </div>
            <div class="col-md-6 text-center mb-3 p-1">
              <div class="stat-item">
                <h6 class="card-title">World records</h6>
                <p class="card-text player-stats">
                  {{ stats.worldRecordAmount[classType] }}
                </p>
              </div>
            </div>
            <div class="col-md-6 text-center mb-3 p-1">
              <div class="stat-item">
                <h6 class="card-title">Top times</h6>
                <p class="card-text player-stats">
                  {{ stats.topTimesAmount[classType] }}
                </p>
              </div>
            </div>
            <div class="col-md-3 text-center p-1">
              <div class="stat-item">
                <h6 class="card-title">G1</h6>
                <p class="card-text player-stats">
                  {{ stats.placement[classType].G1 }}
                </p>
              </div>
            </div>
            <div class="col-md-3 text-center p-1">
              <div class="stat-item">
                <h6 class="card-title">G2</h6>
                <p class="card-text player-stats">
                  {{ stats.placement[classType].G2 }}
                </p>
              </div>
            </div>
            <div class="col-md-3 text-center p-1">
              <div class="stat-item">
                <h6 class="card-title">G3</h6>
                <p class="card-text player-stats">
                  {{ stats.placement[classType].G3 }}
                </p>
              </div>
            </div>
            <div class="col-md-3 text-center p-1">
              <div class="stat-item">
                <h6 class="card-title">G4</h6>
                <p class="card-text player-stats">
                  {{ stats.placement[classType].G4 }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="nav-bar right-nav-bar" @click="$emit('next-stat')"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ClassStatsCard",
  props: {
    classType: {
      type: String,
      required: true,
      validator: (value) => ["Soldier", "Demoman"].includes(value),
    },
    currentStatType: {
      type: String,
      required: true,
    },
    stats: {
      type: Object,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["prev-stat", "next-stat"],
};
</script>

<style scoped>
.stat-block {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  min-height: 395px;
}

.stat-block .card-title {
  color: #aaa;
  font-weight: bold;
}

.stat-block .card-text {
  font-size: 1.4rem;
  font-weight: bold;
}

.player-stats {
  color: var(--color-text) !important;
}

.section-header {
  color: var(--color-text);
  font-size: 20px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 0;
}

.stat-type-label {
  color: var(--color-text);
  font-size: 1rem;
  font-weight: bold;
  text-align: center;
  margin-bottom: 0;
  text-transform: capitalize;
}

.card-header {
  margin-bottom: 0;
  border-bottom: none;
  color: var(--color-text);
  border-top-left-radius: 10px !important;
  border-top-right-radius: 10px !important;
}

.card-body {
  position: relative;
  padding-top: 0px;
  padding-bottom: 0px;
}

.card-row {
  padding: 0px 15px 10px 15px;
  display: flex;
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

.right-nav-bar:hover {
  background: linear-gradient(
    to right,
    rgba(74, 111, 165, 0.3),
    rgba(74, 111, 165, 0.1)
  );
}

.left-nav-bar:hover {
  background: linear-gradient(
    to left,
    rgba(74, 111, 165, 0.3),
    rgba(74, 111, 165, 0.1)
  );
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

@media (max-width: 1400px) {
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
}
</style>
