<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div class="container py-4 d-flex flex-column align-items-center">
      <div
        class="tables-wrapper d-flex flex-column flex-md-row justify-content-center"
      >
        <div class="soldier-table-container">
          <div class="table-wrapper">
            <div class="header-content">
              <div class="skeleton skeleton-class-icon"></div>
              <div class="skeleton skeleton-header-title ms-2"></div>
            </div>
            <div class="table-responsive">
              <table class="table table-dark">
                <thead>
                  <tr>
                    <th><div class="skeleton skeleton-th"></div></th>
                    <th><div class="skeleton skeleton-th-wide"></div></th>
                    <th><div class="skeleton skeleton-th"></div></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="i in 50" :key="'s-row-' + i">
                    <td><div class="skeleton skeleton-rank"></div></td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div class="skeleton skeleton-avatar"></div>
                        <div class="skeleton skeleton-name"></div>
                      </div>
                    </td>
                    <td><div class="skeleton skeleton-points"></div></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="players-footer">
              <div class="skeleton skeleton-footer-btn"></div>
            </div>
          </div>
        </div>
        <div class="demoman-table-container">
          <div class="table-wrapper">
            <div class="header-content">
              <div class="skeleton skeleton-class-icon"></div>
              <div class="skeleton skeleton-header-title ms-2"></div>
            </div>
            <div class="table-responsive">
              <table class="table table-dark">
                <thead>
                  <tr>
                    <th><div class="skeleton skeleton-th"></div></th>
                    <th><div class="skeleton skeleton-th-wide"></div></th>
                    <th><div class="skeleton skeleton-th"></div></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="i in 50" :key="'d-row-' + i">
                    <td><div class="skeleton skeleton-rank"></div></td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div class="skeleton skeleton-avatar"></div>
                        <div class="skeleton skeleton-name"></div>
                      </div>
                    </td>
                    <td><div class="skeleton skeleton-points"></div></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="players-footer">
              <div class="skeleton skeleton-footer-btn"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
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
  flex-shrink: 0;
}

.skeleton-title {
  width: 280px;
  height: 36px;
  border-radius: 8px;
}

.skeleton-subtitle {
  width: 260px;
  height: 18px;
  border-radius: 6px;
}

.skeleton-tab {
  width: 100px;
  height: 48px;
  border-radius: 25px;
}

.skeleton-pill {
  width: 90px;
  height: 38px;
  border-radius: 20px;
}

.skeleton-class-icon {
  width: 48px;
  height: 48px;
  border-radius: 6px;
  margin: 8px;
}

.skeleton-header-title {
  width: 180px;
  height: 22px;
  border-radius: 4px;
}

.skeleton-th {
  width: 50px;
  height: 14px;
  border-radius: 3px;
}

.skeleton-th-wide {
  width: 100px;
  height: 14px;
  border-radius: 3px;
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
  width: 130px;
  height: 16px;
  border-radius: 4px;
}

.skeleton-points {
  width: 70px;
  height: 16px;
  border-radius: 4px;
}

.skeleton-footer-btn {
  width: 100%;
  height: 40px;
  border-radius: 0 0 10px 10px;
}

.content-container {
  width: 100%;
  max-width: 1200px;
  text-align: center;
}

.page-header {
  margin-bottom: 1rem;
  padding: 1.5rem 0;
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

.category-tabs-container {
  display: flex;
  justify-content: center;
}

.category-tabs {
  display: flex;
  gap: 10px;
  background: rgba(255, 255, 255, 0.05);
  padding: 5px;
  border-radius: 30px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  flex-wrap: wrap;
  justify-content: center;
}

.subcategory-container {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-top: 1.5rem;
}

.subcategory-pills {
  display: flex;
  flex-direction: column;
  gap: 12px;
  align-items: center;
}

.pill-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
}

.tables-wrapper {
  display: flex;
  gap: 50px;
  width: 100%;
  align-items: flex-start;
}

.soldier-table-container,
.demoman-table-container {
  flex: 1;
}

.table-wrapper {
  position: relative;
  width: 100%;
  border-radius: 10px;
  border: 1px solid var(--color-border-soft);
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  background: transparent;
}

.header-content {
  border-radius: 10px 10px 0 0;
  display: flex;
  align-items: center;
  padding: 10px;
  background: linear-gradient(
    135deg,
    rgba(74, 111, 165, 0.3),
    rgba(37, 55, 82, 0.3)
  );
}

.table-dark {
  margin: 0;
  background: transparent;
}

.table-dark th {
  background: var(--color-primary-dark, rgba(37, 55, 82, 0.5));
  padding: 8px 6px;
  border-top: 1px solid var(--color-border-soft);
}

.table-dark td {
  background: rgba(255, 255, 255, 0.05);
  padding: 6px;
  border: 1px solid var(--color-border-soft);
}

.table-dark tr:nth-child(odd) td {
  background: rgba(119, 119, 119, 0.05);
}

.players-footer {
  border-radius: 0 0 10px 10px;
  overflow: hidden;
}

@media (max-width: 767.98px) {
  .tables-wrapper {
    flex-direction: column;
    align-items: center;
  }
  .category-tabs {
    gap: 6px;
  }
  .skeleton-tab {
    width: 80px;
    height: 40px;
  }
}
</style>
