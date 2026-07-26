<template>
  <tr v-for="row in 50" :key="'skel-row-' + row">
    <td class="rank-col">
      <div
        class="skeleton"
        style="width: 28px; height: 14px; margin-left: auto"
      />
    </td>
    <td class="duration-col">
      <div class="skeleton" style="width: 72px; height: 14px" />
    </td>
    <td class="name-col">
      <div
        class="skeleton"
        :style="{ width: `${90 + ((row * 31) % 7) * 15}px`, height: '14px' }"
      />
    </td>
    <td class="date-col">
      <div
        class="skeleton"
        style="width: 100px; height: 14px; margin-left: auto"
      />
    </td>
  </tr>
</template>

<script>
export default {
  name: "LeaderboardSkeleton",
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
    rgba(255, 255, 255, 0.04) 25%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.04) 75%
  );
  background-size: 600px 100%;
  animation: shimmer 1.6s infinite linear;
  border-radius: 4px;
}

.rank-col {
  width: 1%;
  white-space: nowrap;
  text-align: right;
  padding: 5px 7px;
}
.duration-col {
  width: 1%;
  white-space: nowrap;
  padding: 5px 7px;
}
.name-col {
  width: 100%;
  white-space: nowrap;
  padding: 5px 7px;
}
.date-col {
  width: 1%;
  white-space: nowrap;
  text-align: right;
  padding: 5px 7px;
}

@media (max-width: 1100px) and (min-width: 768px) {
  .rank-col,
  .duration-col,
  .name-col,
  .date-col {
    font-size: 12px;
    padding: 4px 5px;
  }
  .name-col {
    max-width: 120px;
    overflow: hidden;
  }
  .name-col .skeleton {
    max-width: 100%;
  }
}

@media (max-width: 767.98px) {
  .rank-col,
  .duration-col,
  .name-col,
  .date-col {
    font-size: 12px;
    padding: 4px 5px;
  }
  .name-col {
    max-width: 0;
    overflow: hidden;
    padding: 0;
  }
  .skeleton {
    height: 11px !important;
  }
  .rank-col .skeleton {
    width: 20px !important;
  }
  .duration-col .skeleton {
    width: 42px !important;
  }
  .date-col .skeleton {
    width: 38px !important;
  }
}
</style>
