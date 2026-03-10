<template>
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

<script>
export default {
  name: "PlayersSkeleton",
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

.skeleton {
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
</style>
