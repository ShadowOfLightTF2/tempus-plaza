// Entry format: [maxRank, maleName, femaleName]
// A player gets the first entry whose maxRank >= their rank.
// Event dates are [month, day]
export const RANK_THEMES = {
  default: {
    names: [
      [1, "Emperor", "Empress"],
      [2, "King", "Queen"],
      [3, "Archduke", "Archduchess"],
      [4, "Lord", "Lady"],
      [5, "Duke", "Duchess"],
      [6, "Prince V", "Princess V"],
      [7, "Prince IV", "Princess IV"],
      [8, "Prince III", "Princess III"],
      [9, "Prince II", "Princess II"],
      [10, "Prince I", "Princess I"],
      [11, "Earl V", "Gearl V"],
      [12, "Earl IV", "Gearl IV"],
      [13, "Earl III", "Gearl III"],
      [14, "Earl II", "Gearl II"],
      [15, "Earl I", "Gearl I"],
      [16, "Sir V", "Madam V"],
      [17, "Sir IV", "Madam IV"],
      [18, "Sir III", "Madam III"],
      [19, "Sir II", "Madam II"],
      [20, "Sir I", "Madam I"],
      [21, "Count V", "Countess"],
      [22, "Count IV", "Countess"],
      [23, "Count III", "Countess"],
      [24, "Count II", "Countess"],
      [25, "Count I", "Countess"],
      [30, "Baron V", "Baroness V"],
      [35, "Baron IV", "Baroness IV"],
      [40, "Baron III", "Baroness III"],
      [45, "Baron II", "Baroness II"],
      [50, "Baron I", "Baroness I"],
      [60, "Knight V", "Dame V"],
      [70, "Knight IV", "Dame IV"],
      [80, "Knight III", "Dame III"],
      [90, "Knight II", "Dame II"],
      [100, "Knight I", "Dame I"],
      [120, "Noble V", "Noblewoman V"],
      [140, "Noble IV", "Noblewoman IV"],
      [160, "Noble III", "Noblewoman III"],
      [180, "Noble II", "Noblewoman II"],
      [200, "Noble I", "Noblewoman I"],
      [260, "Esquire V"],
      [320, "Esquire IV"],
      [380, "Esquire III"],
      [440, "Esquire II"],
      [500, "Esquire I"],
      [600, "Jester V"],
      [700, "Jester IV"],
      [800, "Jester III"],
      [900, "Jester II"],
      [1000, "Jester I"],
      [1300, "Plebeian V"],
      [1600, "Plebeian IV"],
      [1900, "Plebeian III"],
      [2200, "Plebeian II"],
      [2500, "Plebeian I"],
      [2700, "Peasant V"],
      [2900, "Peasant IV"],
      [3100, "Peasant III"],
      [3300, "Peasant II"],
      [5000, "Peasant I"],
      [Infinity, "Peon"],
    ],
  },

  christmas: {
    start: [12, 1],
    end: [12, 31],
    names: [
      [1, "Santa"],
      [2, "The Grinch"],
      [3, "Rudolph"],
      [4, "Scrooge"],
      [5, "Frosty the Snowman"],
      [10, "Santa's Helper"],
      [15, "Reindeer"],
      [20, "Christmas Ghost"],
      [25, "Elf"],
      [50, "Bauble Boy"],
      [100, "Caroler"],
      [200, "Gingerbread Man"],
      [500, "Eggnog"],
      [1000, "Turkey"],
      [2500, "Candy Cane"],
      [5000, "Snowflake"],
      [Infinity, "Lump o' Coal"],
    ],
  },

  earthDay: {
    start: [4, 18],
    end: [4, 25],
    names: [
      [1, "Sharknado"],
      [2, "Godzilla"],
      [3, "Cthulhu"],
      [4, "Wolverine"],
      [5, "Wildebeest"],
      [10, "Dragonfly"],
      [15, "Kangaroo"],
      [20, "Moth"],
      [25, "Panda"],
      [50, "Moose"],
      [100, "Sugar Glider"],
      [200, "Chinchilla"],
      [500, "Clownfish"],
      [1000, "Meerkat"],
      [2500, "Sloth"],
      [5000, "Butterfly"],
      [Infinity, "Beetle"],
    ],
  },

  fourthOfJuly: {
    start: [7, 1],
    end: [7, 7],
    names: [
      [1, "Uncle Sam"],
      [2, "George Washington"],
      [3, "Abraham Lincoln"],
      [4, "JFK"],
      [5, "Babe Ruth"],
      [10, "Founding Father"],
      [15, "Astronaut"],
      [20, "Rockstar"],
      [25, "Bald Eagle"],
      [50, "Cowboy"],
      [100, "Rockette"],
      [200, "Baseball"],
      [500, "Burger"],
      [1000, "Hotdog"],
      [2500, "Socialist"],
      [5000, "Communist"],
      [Infinity, "Redcoat"],
    ],
  },

  halloween: {
    start: [10, 15],
    end: [11, 1],
    names: [
      [1, "Mr. Skeltel"],
      [2, "Pumpking"],
      [3, "Duke of Spook"],
      [4, "Dracula"],
      [5, "Frankenstein"],
      [10, "Headless Horseman"],
      [15, "Sir. Spookington"],
      [20, "Mutant"],
      [25, "Vampire"],
      [50, "Wraith"],
      [100, "Warlock"],
      [200, "Ghost"],
      [500, "Wisp"],
      [1000, "Skeleton"],
      [2500, "Zombie"],
      [5000, "Scamp"],
      [Infinity, "Pile O' Bones"],
    ],
  },
};

function isInRange(date, start, end) {
  const v = (date.getMonth() + 1) * 100 + date.getDate();
  const s = start[0] * 100 + start[1];
  const e = end[0] * 100 + end[1];
  return s <= e ? v >= s && v <= e : v >= s || v <= e;
}

// Returns the active event theme, or the default theme when no event is on
export function getActiveTheme(date = new Date()) {
  // Testing override: tempusplaza.com/player/123?theme=halloween
  const forced = new URLSearchParams(window.location.search).get("theme");
  if (forced && RANK_THEMES[forced]) return RANK_THEMES[forced];

  return (
    Object.values(RANK_THEMES).find(
      (t) => t.start && isInRange(date, t.start, t.end),
    ) || RANK_THEMES.default
  );
}

const findEntry = (theme, rank) => theme.names.find(([max]) => rank <= max);

export function getRankTitle(rank, gender, date = new Date()) {
  const base = findEntry(RANK_THEMES.default, rank);
  if (!base) return { title: "Unranked", color: "--color-peon" };

  const [, male, female = male] = base;
  const isMale = gender === "male";
  const defaultTitle = isMale ? male : female;

  const colorSource =
    defaultTitle === "Queen" || defaultTitle === "Empress" ? female : male;
  const colorKey = colorSource.replace(/\s[IVX]+$/, "").toLowerCase();

  const themed = findEntry(getActiveTheme(date), rank);
  const title = themed
    ? isMale
      ? themed[1]
      : (themed[2] ?? themed[1])
    : defaultTitle;

  return {
    title,
    originalTitle: defaultTitle,
    color: `rank-color-${colorKey}`,
  };
}
