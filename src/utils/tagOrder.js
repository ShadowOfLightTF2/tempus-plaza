/**
 * Controls the display order of tags everywhere they appear (vote modal,
 * map banner, map information, etc).
 *
 * HOW TO USE:
 * - To reorder existing tags, just reorder the lines below.
 * - To add a new tag, add its exact name (must match the tag's "name" in
 *   the database, case-sensitive) at the position where you want it to show
 *   up, it does NOT have to go at the end.
 * - Any tag that exists in the database but is NOT listed here will be
 *   sorted after all listed tags (fallback order: by id).
 */
export const TAG_ORDER = [
  // Soldier
  "Wallshot",
  "Wallpogo",
  "Sync",
  "Prefire",
  "Speedshot",
  "Floorpogo",
  "Jurf",
  "Edgebug",
  "Wallbug",
  "Texturebug",
  "Bounce",
  // Demoman
  "Airpogo",
  "Hardpogo",
  "Vertical",
  "Downair",
  "Pogo",
  "Compact",
  // Both
  "Mixed",
  "Hole",
  "Phase",
  "Limited ammo",
  "Strafe",
  "Collab",
  // Extra
  "KZ",
  "Gimmick",
  "Button",
  "Classic",
  "Endurance",
];

/**
 * Sorts a list of tag objects ({ id, name, ... }) according to TAG_ORDER.
 * Tags not present in TAG_ORDER are placed after all listed tags, ordered
 * by id as a fallback.
 */
export function sortTagsByOrder(tags) {
  return [...tags].sort((a, b) => {
    const aIndex = TAG_ORDER.indexOf(a.name);
    const bIndex = TAG_ORDER.indexOf(b.name);
    const aRank = aIndex === -1 ? Infinity : aIndex;
    const bRank = bIndex === -1 ? Infinity : bIndex;

    if (aRank !== bRank) return aRank - bRank;
    return (a.id ?? 0) - (b.id ?? 0);
  });
}
