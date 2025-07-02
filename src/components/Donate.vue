<template>
  <div
    class="position-relative min-vh-100 w-100 overflow-hidden background-container"
  >
    <div
      class="container mx-auto py-4 d-flex flex-column align-items-center"
      style="z-index: 1"
    >
      <div class="content-container" style="z-index: 1">
        <div class="page-header">
          <h1 class="page-title">
            <span class="title-icon">❤️</span>
            Support the Website
          </h1>
        </div>
        <hr class="divider" style="width: 100%" />
        <div class="donation-panel">
          <div class="donation-options">
            <a
              href="https://ko-fi.com/shadowoflight"
              target="_blank"
              rel="noopener noreferrer"
              class="kofi-button"
            >
              <span class="kofi-text">Donate through ko-fi</span>
            </a>
          </div>
          <p class="panel-description">
            Your support helps me pay for server uptime and upgrade the websites
            services. Donator perks will be added to the website in the future.
            (cosmetic changes only)
          </p>
          <hr class="divider" />
          <div class="panel-footer">
            <p class="thank-you">Thank you for your generosity!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useHead } from "@vueuse/head";
export default {
  name: "DonatePage",
  setup() {
    useHead({
      title: "Tempus Plaza | Donate",
    });
  },
  data() {
    return {};
  },
  methods: {
    openKofi() {
      window.open(
        "https://ko-fi.com/shadowoflight",
        "_blank",
        "noopener,noreferrer"
      );
    },
  },
};
</script>

<style scoped>
.donation-panel {
  max-width: 500px;
  margin: 40px auto;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  box-shadow: 0 0px 20px rgb(0, 0, 0);
  padding: 40px;
  text-align: center;
}

.panel-header h2 {
  color: var(--color-text);
  margin: 0;
  font-size: 28px;
  font-weight: 700;
}

.panel-description {
  color: var(--color-text);
  font-size: 16px;
  line-height: 1.6;
  margin-bottom: 30px;
}

.kofi-button {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: linear-gradient(
    135deg,
    var(--color-primary),
    var(--color-primary-dark)
  );
  color: white;
  text-decoration: none;
  padding: 16px 32px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 16px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px var(--color-primary);
  margin-bottom: 25px;
}

.kofi-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 20px var(--color-primary);
  background: linear-gradient(
    225deg,
    var(--color-primary-dark),
    var(--color-primary)
  );
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
  margin: 30px 0;
  opacity: 0.6;
}

.thank-you {
  color: var(--color-text);
  font-style: italic;
  margin: 0;
  font-size: 14px;
}

@media (max-width: 768px) {
  .donation-panel {
    margin: 20px;
    padding: 30px 20px;
  }

  .panel-header h2 {
    font-size: 24px;
  }
}
</style>
