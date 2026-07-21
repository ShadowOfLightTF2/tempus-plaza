<template>
  <footer class="footer">
    <div class="container-fluid">
      <div class="footer-content">
        <a
          href="https://tempus2.xyz"
          class="footer-link"
          target="_blank"
          rel="noopener noreferrer"
          ><i class="bi bi-globe"></i> Tempus</a
        >
        <a
          href="https://discord.gg/U48JYd9h99"
          class="footer-link"
          target="_blank"
          rel="noopener noreferrer"
          ><i class="bi bi-discord"></i> Discord</a
        >
        <a
          href="https://docs.google.com/spreadsheets/d/1kL76rEPL2AtMSu5RWI2VtW5ZJ2QIQx61BNowdfN-09M/edit?usp=sharing"
          class="footer-link"
          target="_blank"
          rel="noopener noreferrer"
          ><i class="bi bi-journals"></i> Encyclopedia</a
        >
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "AppFooter",
};
</script>

<style scoped>
.footer {
  background: var(--color-dark);
  border-top: 1px solid var(--color-border-soft);
  padding: 0.4rem 0;
  margin-top: auto;
}
.footer .container-fluid {
  max-width: 1200px;
  margin: 0 auto;
}
.footer-content {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
}
.footer-link {
  color: var(--color-text);
  text-decoration: none;
  font-weight: bold;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.footer-link:hover {
  background: linear-gradient(
    to bottom,
    rgba(74, 111, 165, 0.5),
    rgba(74, 111, 165, 0.3)
  );
  color: var(--color-text);
  text-decoration: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

@media (max-width: 1199px) {
  .footer {
    padding: 0.75rem 0;
  }
  .footer .container-fluid {
    padding: 0 0.5rem;
  }
  .footer-content {
    gap: 1rem;
    justify-content: center;
  }
  .footer-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    min-width: auto;
  }
}
</style>
