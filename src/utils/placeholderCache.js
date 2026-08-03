const CACHE_KEY = "map-placeholders";
const CACHE_VERSION = 1;

export function getPlaceholderCache() {
  try {
    const raw = localStorage.getItem(CACHE_KEY);
    if (!raw) return { data: {}, order: [] };
    const parsed = JSON.parse(raw);
    if (parsed.version !== CACHE_VERSION) return { data: {}, order: [] };
    return { data: parsed.data || {}, order: parsed.order || [] };
  } catch {
    return { data: {}, order: [] };
  }
}

export function setPlaceholderCache(data, order) {
  try {
    localStorage.setItem(
      CACHE_KEY,
      JSON.stringify({ version: CACHE_VERSION, data, order }),
    );
  } catch {
    // localStorage full or unavailable
  }
}
