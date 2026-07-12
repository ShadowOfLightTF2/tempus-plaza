<template>
  <div class="notfound">
    <h1 class="code">404</h1>

    <p class="route-line">
      <span class="route-key">route</span>
      <span class="route-path">{{ attemptedPath }}</span>
      <span class="route-status">not found</span>
    </p>

    <div class="actions">
      <router-link to="/" class="btn btn-primary">Go to Home</router-link>
      <router-link to="/maps" class="btn btn-ghost">Browse Maps</router-link>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const attemptedPath = computed(() => route.fullPath);
</script>

<style scoped>
.notfound {
  min-height: calc(100vh - 300px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  padding: 4rem 1.5rem;
  color: var(--color-text);
  text-align: center;
}
.code {
  margin: 0;
  font-size: clamp(5rem, 18vw, 9rem);
  line-height: 0.9;
  font-weight: 800;
  letter-spacing: 0.02em;
  background: linear-gradient(
    180deg,
    var(--color-primary),
    var(--color-primary-dark)
  );
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.route-line {
  display: flex;
  gap: 0.6rem;
  align-items: baseline;
  font-size: 0.85rem;
  background: var(--color-box);
  border: 1px solid var(--color-border-soft);
  padding: 0.5rem 1rem;
  border-radius: 4px;
  max-width: 90vw;
  overflow-wrap: anywhere;
}

.route-key {
  color: var(--color-text-soft);
  text-transform: uppercase;
  font-size: 0.7rem;
  letter-spacing: 0.15em;
}

.route-path {
  color: var(--color-text-clickable);
}

.route-status {
  color: var(--color-primary-red);
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  justify-content: center;
}

.btn {
  font-size: 0.85rem;
  letter-spacing: 0.03em;
  text-decoration: none;
  padding: 0.65rem 1.4rem;
  border-radius: 4px;
  border: 1px solid var(--color-border-soft);
  transition:
    transform 0.12s ease,
    border-color 0.12s ease,
    background 0.12s ease;
}

.btn:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.btn-primary {
  background: var(--color-primary);
  color: var(--color-text);
  border-color: var(--color-primary);
  font-weight: 700;
}
.btn-primary:hover {
  transform: translateY(-1px);
  background: var(--color-primary-dark);
}

.btn-ghost {
  color: var(--color-text);
  background: var(--color-button-light);
}
.btn-ghost:hover {
  border-color: var(--color-primary);
  color: var(--color-text-clickable);
}
</style>
