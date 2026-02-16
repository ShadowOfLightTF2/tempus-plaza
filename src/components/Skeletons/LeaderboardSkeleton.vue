<template>
  <div
    class="container d-flex flex-column align-items-center"
    style="z-index: 1"
  >
    <hr class="row-divider-no-top" style="width: 100%" />
    <div class="category-tabs-container">
      <div class="category-tabs">
        <div v-for="i in 3" :key="i" class="skeleton-tab shimmer" />
      </div>
    </div>
    <hr class="row-divider" style="width: 100%" />
    <div
      class="tables-wrapper d-flex flex-column flex-md-row justify-content-center"
    >
      <div
        v-for="tableClass in ['soldier', 'demoman']"
        :key="tableClass"
        class="table-container"
      >
        <div class="table-wrapper">
          <div class="maps-header">
            <div class="header-content">
              <div class="skeleton-icon shimmer" />
              <div class="header-text">
                <div
                  class="skeleton-line shimmer"
                  style="width: 80px; height: 16px; margin-bottom: 8px"
                />
                <div
                  class="skeleton-line shimmer"
                  style="width: 60px; height: 13px"
                />
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-dark">
              <thead>
                <tr>
                  <th>
                    <div
                      class="skeleton-line shimmer"
                      style="width: 36px; height: 13px"
                    />
                  </th>
                  <th>
                    <div
                      class="skeleton-line shimmer"
                      style="width: 62px; height: 13px"
                    />
                  </th>
                  <th>
                    <div
                      class="skeleton-line shimmer"
                      style="width: 48px; height: 13px"
                    />
                  </th>
                  <th style="text-align: right">
                    <div
                      class="skeleton-line shimmer"
                      style="width: 36px; height: 13px; margin-left: auto"
                    />
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in 50" :key="row" class="skeleton-row">
                  <td class="rank-column">
                    <div
                      class="skeleton-line shimmer"
                      style="width: 28px; height: 14px; margin-left: auto"
                    />
                  </td>
                  <td class="duration-column">
                    <div
                      class="skeleton-line shimmer"
                      style="height: 14px"
                      :style="{ width: `${55 + ((row * 17) % 4) * 10}px` }"
                    />
                  </td>
                  <td class="name-column">
                    <div
                      class="skeleton-line shimmer"
                      style="height: 14px"
                      :style="{ width: `${90 + ((row * 31) % 7) * 15}px` }"
                    />
                  </td>
                  <td class="date-column">
                    <div
                      class="skeleton-line shimmer"
                      style="width: 52px; height: 14px; margin-left: auto"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="maps-footer">
            <div class="skeleton-show-more shimmer" />
          </div>
        </div>
      </div>
    </div>
  </div>
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

.shimmer {
  background: linear-gradient(
    90deg,
    rgba(255, 255, 255, 0.04) 25%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.04) 75%
  );
  background-size: 600px 100%;
  animation: shimmer 1.6s infinite linear;
  border-radius: 6px;
}

.category-tabs-container {
  display: flex;
  justify-content: center;
}
.category-tabs {
  display: flex;
  gap: 10px;
  background: rgba(255, 255, 255, 0.05);
  padding: 5px;
  border-radius: 25px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.skeleton-tab {
  width: 80px;
  height: 40px;
  border-radius: 25px;
}
.row-divider {
  border: none;
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent,
    var(--color-primary, #4a6fa5),
    transparent
  );
  margin: 15px 0 30px 0;
  opacity: 0.6;
}
.row-divider-no-top {
  border: none;
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent,
    var(--color-primary, #4a6fa5),
    transparent
  );
  margin: 5px 0 15px 0;
  opacity: 0.6;
}

.tables-wrapper {
  min-height: 2113px;
  display: flex;
  gap: 50px;
  width: 100%;
  align-items: flex-start;
}
.table-container {
  flex: 1;
  min-width: 0;
}
.table-wrapper {
  width: 100%;
  border-radius: 12px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  overflow: hidden;
}

.maps-header {
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
  border-radius: 10px 10px 0 0;
}
.header-content {
  display: flex;
  align-items: center;
  padding: 10px;
}
.header-text {
  margin-left: 10px;
}
.skeleton-icon {
  width: 56px;
  height: 56px;
  border-radius: 8px;
  flex-shrink: 0;
}
.skeleton-line {
  border-radius: 4px;
}

.table-responsive {
  overflow-x: auto;
  overflow-y: hidden;
  margin-bottom: 0;
}
.table-dark {
  margin: 0;
  border-collapse: collapse;
  min-width: 500px;
  width: 100%;
}
.table-dark th {
  background: var(--color-primary-dark, #1a2535);
  padding: 8px 7px 5px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}
.table-dark td {
  padding: 6px 7px;
  background: rgba(255, 255, 255, 0.05);
}
.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}
.skeleton-row {
  height: 34px;
}

.rank-column {
  width: auto;
  white-space: nowrap;
  text-align: right;
}
.duration-column {
  width: auto;
  white-space: nowrap;
  text-align: right;
}
.name-column {
  width: 100%;
  white-space: nowrap;
}
.date-column {
  width: auto;
  white-space: nowrap;
  text-align: right;
}

.maps-footer {
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}
.skeleton-show-more {
  height: 38px;
  border-radius: 0 0 12px 12px;
  width: 100%;
}

@media (max-width: 767.98px) {
  .tables-wrapper {
    flex-direction: column;
    align-items: center;
    width: 115%;
  }
}
</style>
