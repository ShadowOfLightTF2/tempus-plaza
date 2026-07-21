<template>
  <div v-if="modelValue" class="calc-popup-overlay" @click="close">
    <div class="calc-popup" @click.stop>
      <button class="calc-popup-close" @click="close" aria-label="Close">
        <svg
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <h2>Points calculator</h2>

      <div class="fields-row">
        <div class="field">
          <label for="type">Type</label>
          <select id="type" v-model="type">
            <option v-for="t in types" :key="t" :value="t">
              {{ capitalize(t) }}
            </option>
          </select>
        </div>

        <div class="field">
          <label for="tier">Tier</label>
          <select id="tier" v-model.number="tier">
            <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>

        <div class="field">
          <label for="rating">Rating</label>
          <select id="rating" v-model.number="rating">
            <option v-for="n in 4" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>

        <div class="field">
          <label for="placement">Placement</label>
          <select id="placement" v-model.number="placement">
            <option v-for="n in 15" :key="n" :value="n">
              {{ placementLabel(n) }}
            </option>
          </select>
        </div>
      </div>

      <label class="checkbox-field" for="include-completion">
        <input
          id="include-completion"
          type="checkbox"
          v-model="includeCompletion"
        />
        <span>Include completion points</span>
      </label>

      <div class="result">
        <span class="result-label">Points</span>
        <span class="result-value">{{ points }}</span>
      </div>

      <div class="breakdown">
        <div class="breakdown-title">How it's calculated</div>
        <div class="breakdown-line">
          <span
            class="breakdown-term"
            :class="{ disabled: !includeCompletion }"
          >
            completion[{{ type }}][{{ tier - 1 }}]
            <span class="breakdown-value">= {{ completionValue }}</span>
          </span>
          <span class="breakdown-op">+</span>
          <span class="breakdown-term">
            rating[{{ type }}][{{ rating - 1 }}]
            <span class="breakdown-value">= {{ ratingValue }}</span>
          </span>
          <span class="breakdown-op">&times;</span>
          <span class="breakdown-term">
            placementPercent[{{ placement }}]
            <span class="breakdown-value">= {{ placementValue }}</span>
          </span>
        </div>
        <div class="breakdown-line breakdown-result">
          {{ includeCompletion ? completionValue : 0 }}
          + ({{ ratingValue }} &times; {{ placementValue }}) =
          <strong>{{ points }}</strong>
        </div>
      </div>

      <div class="source">
        <pre><code v-html="highlightedSource"></code></pre>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue"]);

function close() {
  emit("update:modelValue", false);
}

function onKeydown(e) {
  if (e.key === "Escape" && props.modelValue) close();
}

onMounted(() => document.addEventListener("keydown", onKeydown));
onBeforeUnmount(() => document.removeEventListener("keydown", onKeydown));

const types = ["map", "course", "bonus"];

const type = ref("map");
const tier = ref(1);
const rating = ref(1);
const placement = ref(1);
const includeCompletion = ref(true);

const completionTable = {
  map: [1, 2.5, 5, 10, 25, 50, 100, 200, 300, 400],
  course: [0.5, 1, 2, 5, 12, 25, 50, 100, 150, 200],
  bonus: [0.2, 0.5, 1, 2, 5, 10, 20, 50, 75, 100],
};

const ratingTable = {
  map: [1000, 700, 200, 0],
  course: [500, 250, 75, 0],
  bonus: [200, 75, 20, 0],
};

const placementPercent = {
  1: 1,
  2: 0.5,
  3: 0.4,
  4: 0.35,
  5: 0.3,
  6: 0.25,
  7: 0.225,
  8: 0.2,
  9: 0.175,
  10: 0.15,
  11: 0.05,
  12: 0.02,
  13: 0.01,
  14: 0.002,
  15: 0,
};

function calculatePoints(type, tier, rating, placement, useCompletion) {
  const completion = useCompletion ? completionTable[type][tier - 1] : 0;
  return (
    completion + ratingTable[type][rating - 1] * placementPercent[placement]
  );
}

const completionValue = computed(
  () => completionTable[type.value][tier.value - 1],
);
const ratingValue = computed(() => ratingTable[type.value][rating.value - 1]);
const placementValue = computed(() => placementPercent[placement.value]);

const points = computed(() => {
  const value = calculatePoints(
    type.value,
    tier.value,
    rating.value,
    placement.value,
    includeCompletion.value,
  );
  return Math.round(value * 100) / 100;
});

function capitalize(s) {
  return s.charAt(0).toUpperCase() + s.slice(1);
}

function ordinal(n) {
  const s = ["th", "st", "nd", "rd"];
  const v = n % 100;
  return n + (s[(v - 20) % 10] || s[v] || s[0]);
}

function placementLabel(n) {
  if (n <= 10) return ordinal(n);
  return "G" + (n - 10);
}

function wrap(text, cond) {
  return cond ? `<span class="hl">${text}</span>` : `${text}`;
}

const highlightedSource = computed(() => {
  const t = type.value;
  const ti = tier.value;
  const ra = rating.value;
  const pl = placement.value;

  const completionRows = [
    { key: "map", values: completionTable.map },
    { key: "course", values: completionTable.course },
    { key: "bonus", values: completionTable.bonus },
  ];
  const completionLines = completionRows
    .map((row, idx) => {
      const keyHtml = wrap(row.key, row.key === t);
      const valuesHtml = row.values
        .map((v, j) => wrap(v, row.key === t && j === ti - 1))
        .join(", ");
      const comma = idx < completionRows.length - 1 ? "," : "";
      return `        ${keyHtml}: [${valuesHtml}]${comma}`;
    })
    .join("\n");

  const ratingRows = [
    { key: "map", values: ratingTable.map },
    { key: "course", values: ratingTable.course },
    { key: "bonus", values: ratingTable.bonus },
  ];
  const ratingLines = ratingRows
    .map((row, idx) => {
      const keyHtml = wrap(row.key, row.key === t);
      const valuesHtml = row.values
        .map((v, j) => wrap(v, row.key === t && j === ra - 1))
        .join(", ");
      const comma = idx < ratingRows.length - 1 ? "," : "";
      return `        ${keyHtml}: [${valuesHtml}]${comma}`;
    })
    .join("\n");

  const keys = Object.keys(placementPercent).map(Number);
  const placementLinesArr = [];
  for (let i = 0; i < keys.length; i += 5) {
    const chunk = keys.slice(i, i + 5);
    const parts = chunk.map((k, idx2) => {
      const isLast = i + idx2 === keys.length - 1;
      const isSelected = k === pl;
      const keyHtml = wrap(k, isSelected);
      const valueHtml = wrap(placementPercent[k], isSelected);
      return `${keyHtml}: ${valueHtml}${isLast ? "" : ","}`;
    });
    placementLinesArr.push("        " + parts.join(" "));
  }
  const placementLines = placementLinesArr.join("\n");

  const completionBlock = includeCompletion.value
    ? `    const completionTable = {
${completionLines}
    };
`
    : "";

  const returnStatement = includeCompletion.value
    ? `    return completionTable[type][tier - 1]
        + ratingTable[type][rating - 1] * placementPercent[placement];`
    : `    return ratingTable[type][rating - 1] * placementPercent[placement];`;

  return `function calculatePoints(type, tier, rating, placement) {
${completionBlock}    const ratingTable = {
${ratingLines}
    };
    const placementPercent = {
${placementLines}
    };
${returnStatement}
}`;
});
</script>

<style scoped>
.calc-popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  padding: 1rem;
}

.calc-popup {
  position: relative;
  max-width: 700px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-sizing: border-box;
  padding: 2rem;
  background: var(--color-box);
  border: 1px solid var(--color-border-soft);
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  font-family:
    "Segoe UI",
    system-ui,
    -apple-system,
    sans-serif;
  color: var(--color-text);
}

.calc-popup-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  color: var(--color-text);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.calc-popup-close:hover {
  background: var(--color-dark);
}

.calc-popup h2 {
  margin: 0 0 1.25rem;
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--color-text);
  padding-right: 2rem;
}

.fields-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.field label {
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--color-text-soft);
}

.field select {
  padding: 0.5rem;
  border: 1px solid var(--color-border-semi-soft);
  border-radius: 6px;
  font-size: 0.95rem;
  background: var(--color-dark);
  color: var(--color-text);
}

.field select:focus {
  outline: none;
  border-color: var(--color-primary);
}

.checkbox-field {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 1.25rem 0 0.5rem;
  font-size: 0.9rem;
  color: var(--color-text-soft);
  cursor: pointer;
  user-select: none;
}

.checkbox-field input[type="checkbox"] {
  width: 16px;
  height: 16px;
  accent-color: var(--color-primary);
  cursor: pointer;
}

.result {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid var(--color-border-soft);
}

.result-label {
  font-size: 0.9rem;
  color: var(--color-text-soft);
}

.result-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--color-text-clickable);
}

.breakdown {
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid var(--color-border-soft);
}

.breakdown-title {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-text-soft);
  margin-bottom: 0.5rem;
}

.breakdown-line {
  display: flex;
  flex-wrap: nowrap;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  font-family: "Consolas", monospace;
  color: var(--color-text-soft);
  overflow-x: auto;
  padding-bottom: 2px;
}

.breakdown-term {
  background: var(--color-dark);
  border: 1px solid var(--color-border-soft);
  border-radius: 6px;
  padding: 0.25rem 0.5rem;
  white-space: nowrap;
  flex-shrink: 0;
}

.breakdown-term.disabled {
  opacity: 0.4;
  text-decoration: line-through;
}

.breakdown-value {
  color: var(--color-text-clickable);
  margin-left: 0.25rem;
}

.breakdown-op {
  color: var(--color-text-soft);
  font-weight: 600;
}

.breakdown-result {
  margin-top: 0.75rem;
  color: var(--color-text);
}

.source {
  margin-top: 1.25rem;
  padding-top: 1rem;
  border-top: 1px solid var(--color-border-soft);
}

.source pre {
  margin: 0;
  padding: 0.75rem;
  background: var(--color-dark);
  border: 1px solid var(--color-border-soft);
  border-radius: 6px;
  overflow-x: auto;
}

.source code {
  font-family: "Consolas", monospace;
  font-size: 0.8rem;
  color: var(--color-text-soft);
  white-space: pre;
}

.source code :deep(.hl) {
  background: var(--color-primary);
  color: var(--color-text);
  border-radius: 3px;
  padding: 0 3px;
  font-weight: 700;
}

@media (max-width: 640px) {
  .calc-popup {
    margin-top: 60px;
  }
  .fields-row {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 420px) {
  .calc-popup {
    margin-top: 60px;
    padding: 1.5rem;
  }

  .fields-row {
    grid-template-columns: 1fr;
  }

  .result {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
}
</style>
