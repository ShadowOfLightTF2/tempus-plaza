<!-- Skeletons/HistorySkeleton.vue -->
<template>
  <div class="history-skeleton">
    <div v-for="i in 5" :key="i" class="skeleton-day">
      <div class="skeleton-day-marker"></div>
      <div class="skeleton-events">
        <div v-for="j in 3" :key="j" class="skeleton-event"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.history-skeleton {
  width: 100%;
  padding: 1rem 0;
}

.skeleton-day {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.skeleton-day-marker {
  flex: 0 0 100px;
  height: 30px;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.05) 25%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 20px;
}

.skeleton-events {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.skeleton-event {
  height: 100px;
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.03) 25%,
    rgba(255, 255, 255, 0.08) 50%,
    rgba(255, 255, 255, 0.03) 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 12px;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

@media (max-width: 768px) {
  .skeleton-day {
    flex-direction: column;
    gap: 0.5rem;
  }

  .skeleton-day-marker {
    flex: auto;
    width: 100px;
  }
}
</style>
