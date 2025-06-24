<template>
  <div
    class="container py-4 d-flex flex-column align-items-center bg-dark-custom"
  >
    <div class="page-header">
      <h1 class="page-title">
        <span class="title-icon">❤️</span>
        Support the website
      </h1>
    </div>
    <hr class="divider" style="width: 75%" />
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
        services
      </p>
      <hr class="divider" />
      <div class="panel-footer">
        <p class="thank-you">Thank you for your generosity!</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DonatePage",
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
  background: var(--color-row);
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  padding: 40px;
  text-align: center;
  border: 1px solid rgba(255, 255, 255, 0.2);
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
  background: linear-gradient(135deg, var(--color-primary), var(--color-box));
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
  background: linear-gradient(135deg, var(--color-primary), var(--color-box));
}

.divider {
  border: none;
  height: 2px;
  background: linear-gradient(90deg, transparent, #4a9eff, transparent);
  margin: 30px 5px;
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
