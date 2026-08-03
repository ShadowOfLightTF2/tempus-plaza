<template>
  <template v-if="type === 'podium'">
    <div
      v-for="i in 3"
      :key="'skel-podium-' + i"
      class="skel-podium-card"
      :class="`skel-podium-rank-${i}`"
    >
      <span class="skel-podium-rank-number">{{ i }}</span>
      <div class="skel-podium-flag"></div>
      <div class="skel-podium-player-link">
        <div class="skel-podium-avatar"></div>
        <div class="skel-podium-name"></div>
      </div>
      <div class="skel-podium-main-stat"></div>
      <div class="skel-podium-substats">
        <div
          class="skel-podium-substat"
          v-for="j in 3"
          :key="'skel-substat-' + i + '-' + j"
        >
          <div class="skel-substat-label"></div>
          <div class="skel-substat-value"></div>
        </div>
      </div>
    </div>
  </template>
  <template v-else>
    <tr v-for="i in 50" :key="'skel-row-' + i">
      <td class="rank-col"><div class="skeleton skeleton-rank"></div></td>
      <td class="name-col">
        <div class="d-flex align-items-center gap-2">
          <div class="skeleton skeleton-avatar"></div>
          <div class="skeleton skeleton-name"></div>
        </div>
      </td>
      <td class="points-col"><div class="skeleton skeleton-points"></div></td>
    </tr>
  </template>
</template>

<script>
export default {
  name: "PlayersSkeleton",
  props: {
    type: {
      type: String,
      default: "rows", // 'rows' | 'podium'
    },
  },
};
</script>

<style scoped>
@keyframes shimmer {
  0% {
    background-position: -600px 0;
  }
  100% {
    background-position: 600px 0;
  }
}

.skeleton,
.skel-podium-flag,
.skel-podium-avatar,
.skel-podium-name,
.skel-podium-main-stat,
.skel-substat-label,
.skel-substat-value {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 25%,
    rgba(255, 255, 255, 0.12) 50%,
    rgba(255, 255, 255, 0.05) 75%
  );
  background-size: 600px 100%;
  animation: shimmer 1.6s infinite linear;
  border-radius: 6px;
}

.rank-col {
  width: 1%;
  white-space: nowrap;
  text-align: right;
}
.name-col {
  width: 100%;
}
.points-col {
  width: 1%;
  white-space: nowrap;
  text-align: right;
}
.skeleton-rank {
  width: 36px;
  height: 16px;
  border-radius: 3px;
}
.skeleton-avatar {
  width: 25px;
  height: 25px;
  border-radius: 2px;
  flex-shrink: 0;
}
.skeleton-name {
  width: 120px;
  height: 16px;
  border-radius: 4px;
}
.skeleton-points {
  width: 55px;
  height: 16px;
  border-radius: 4px;
  margin-left: auto;
}

.skel-podium-card {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0 16px 10px;
  border-radius: 12px;
  background: linear-gradient(
    160deg,
    rgba(74, 111, 165, 0.18),
    rgba(37, 55, 82, 0.18)
  );
  border: 1px solid var(--color-border-soft, rgba(255, 255, 255, 0.1));
}
.skel-podium-rank-1 {
  border-color: rgba(244, 196, 48, 0.4);
}
.skel-podium-rank-2 {
  border-color: rgba(192, 192, 192, 0.35);
}
.skel-podium-rank-3 {
  border-color: rgba(205, 127, 50, 0.35);
}
.skel-podium-rank-number {
  position: absolute;
  top: 12px;
  left: 16px;
  font-size: 24px;
  font-weight: 800;
  line-height: 1;
  color: var(--color-text-soft, rgba(255, 255, 255, 0.3));
  opacity: 0.4;
}
.skel-podium-flag {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 28px;
  height: 20px;
  border-radius: 2px;
}
.skel-podium-player-link {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  margin-top: 26px;
  max-width: 100%;
}
.skel-podium-avatar {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  flex-shrink: 0;
}
.skel-podium-name {
  width: 100px;
  height: 17px;
}
.skel-podium-main-stat {
  width: 70px;
  height: 18px;
  margin-top: 9px;
}
.skel-podium-substats {
  display: flex;
  gap: 22px;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  width: 100%;
  justify-content: center;
}
.skel-podium-substat {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 58px;
}
.skel-substat-label {
  width: 46px;
  height: 10px;
  border-radius: 3px;
}
.skel-substat-value {
  width: 32px;
  height: 16px;
  margin-top: 4px;
}

@media (max-width: 767.98px) {
  .skel-podium-card {
    padding: 16px 12px 12px;
  }
  .skel-podium-avatar {
    width: 52px;
    height: 52px;
  }
  .skel-podium-name {
    width: 80px;
    height: 14px;
  }
}
</style>
