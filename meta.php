<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: public, max-age=3600, stale-while-revalidate=86400');

// Configuration
const API_BASE_URL = 'https://api.tempusplaza.com';
const SITE_URL = 'https://tempusplaza.com';
const DEFAULT_IMAGE = SITE_URL . '/preview.png';
const API_TIMEOUT = 5;
const INDEX_PATH = '/www/wwwroot/tempusplaza.xyz/index.html';

// Get request parameters
$mapId = filter_var($_SERVER['MAP_ID'] ?? null, FILTER_VALIDATE_INT);
$playerId = filter_var($_SERVER['PLAYER_ID'] ?? null, FILTER_VALIDATE_INT);
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Load base HTML
$html = @file_get_contents(INDEX_PATH);
if ($html === false) {
    http_response_code(500);
    die("Could not load index.html");
}

/**
 * Fetch data from API with error handling
 */
function fetchApi(string $url): ?array {
    $context = stream_context_create([
        'http' => [
            'timeout' => API_TIMEOUT,
            'user_agent' => 'TempusPlaza Meta Generator',
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

/**
 * Generate all meta tags + structured data as a string
 */
function generateMetaTags(array $meta): string {
    $noIndex = ($meta['no_index'] ?? false)
        ? '<meta name="robots" content="noindex, nofollow">'
        : '<meta name="robots" content="index, follow">';

    $tags = sprintf(
        '%s
        <title>%s</title>
        <meta name="description" content="%s">
        <link rel="canonical" href="%s">
        <meta property="og:title" content="%s">
        <meta property="og:description" content="%s">
        <meta property="og:image" content="%s">
        <meta property="og:image:alt" content="%s">',
        $noIndex,
        htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['url'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['image'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8')
    );

    if (isset($meta['image_width'])) {
        $tags .= sprintf(
            '
        <meta property="og:image:width" content="%d">
        <meta property="og:image:height" content="%d">',
            $meta['image_width'],
            $meta['image_height']
        );
    }

    $tags .= sprintf(
        '
        <meta property="og:url" content="%s">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Tempus Plaza">
        <meta name="theme-color" content="#253752">
        <meta name="twitter:card" content="%s">
        <meta name="twitter:title" content="%s">
        <meta name="twitter:description" content="%s">
        <meta name="twitter:image" content="%s">',
        htmlspecialchars($meta['url'], ENT_QUOTES, 'UTF-8'),
        $meta['twitter_card'] ?? 'summary',
        htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['image'], ENT_QUOTES, 'UTF-8')
    );

    // Append JSON-LD structured data if provided
    if (!empty($meta['schema'])) {
        $tags .= "\n        " . generateStructuredData($meta['schema']);
    }

    return $tags;
}

/**
 * Generate JSON-LD structured data script tag
 */
function generateStructuredData(array $schema): string {
    return '<script type="application/ld+json">'
        . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>';
}

/**
 * Inject meta tags into HTML — replaces OG block and removes stale <title>
 */
function injectMetaTags(string $html, string $metaTags): string {
    // Remove any existing meta description, canonical, og, twitter tags
    $html = preg_replace('/<meta\s+name=["\']description["\'][^>]*>/i', '', $html);
    $html = preg_replace('/<link\s+rel=["\']canonical["\'][^>]*>/i', '', $html);
    $html = preg_replace('/<meta\s+property=["\']og:[^>]*>/i', '', $html);
    $html = preg_replace('/<meta\s+name=["\']twitter:[^>]*>/i', '', $html);
    $html = preg_replace('/<meta\s+name=["\']robots["\'][^>]*>/i', '', $html);
    $html = preg_replace('/<title>[^<]*<\/title>/i', '', $html);

    // Inject fresh tags just before </head>
    return str_replace('</head>', $metaTags . "\n</head>", $html);
}

/**
 * Get meta tags for the home page
 */
function getHomeMeta(): array {
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => 'Tempus Plaza',
        'url'      => SITE_URL,
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => SITE_URL . '/lookup?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];

    return [
        'tags' => generateMetaTags([
            'title'        => 'Tempus Plaza | TF2 Jump Stats & Leaderboards',
            'description'  => 'Tempus records, player rankings, map information and everything to do with the Team Fortress 2 jump Tempus network.',
            'image'        => DEFAULT_IMAGE,
            'image_width'  => 512,
            'image_height' => 512,
            'url'          => SITE_URL,
            'twitter_card' => 'summary',
            'schema'       => $schema,
        ]),
        'not_found' => false,
    ];
}

/**
 * Get meta tags for map pages
 */
function getMapMeta(int $mapId): array {
    $apiUrl = API_BASE_URL . '/maps/' . $mapId . '/all-info';
    $data = fetchApi($apiUrl);

    $notFound = ($data === null || empty($data['map']));
    $mapName = $data['map']['name'] ?? ('Map ' . $mapId);
    $mapUrl = SITE_URL . '/maps/' . $mapId;
    $mapImage = SITE_URL . '/map-backgrounds/' . rawurlencode($mapName) . '.webp';

    $solTier        = $data['map']['soldier_tier'] ?? null;
    $solRating      = $data['map']['soldier_rating'] ?? null;
    $demTier        = $data['map']['demoman_tier'] ?? null;
    $demRating      = $data['map']['demoman_rating'] ?? null;
    $bonusCount     = $data['map']['bonus_count'] ?? 0;
    $courseCount    = $data['map']['course_count'] ?? 0;
    $solCompletions = $data['map']['soldier_completion_count'] ?? 0;
    $demCompletions = $data['map']['demoman_completion_count'] ?? 0;

    $authorNames  = array_map(fn($a) => $a['author_name'], array_slice($data['authors'] ?? [], 0, 3));
    $authorString = implode(', ', $authorNames);

    $parts = [];
    if ($courseCount > 0) $parts[] = $courseCount . ' course' . ($courseCount > 1 ? 's' : '');
    if ($bonusCount > 0) $parts[]  = $bonusCount . ' bonus' . ($bonusCount > 1 ? 'es' : '');

    $description = $mapName . ' is a TF2 jump map';
    if ($solTier) $description .= ' (Soldier T' . $solTier . '/R' . $solRating;
    if ($demTier) $description .= ', Demo T' . $demTier . '/R' . $demRating . ')';
    if ($authorString) $description .= ' by ' . $authorString;
    if (!empty($parts)) $description .= ' with ' . implode(' and ', $parts);
    $description .= '. ' . $solCompletions . ' Soldier and ' . $demCompletions . ' Demoman completions on Tempus.';

    $schema = $notFound ? null : [
        '@context'    => 'https://schema.org',
        '@type'       => 'WebPage',
        'name'        => $mapName . ' | Tempus Plaza',
        'url'         => $mapUrl,
        'image'       => $mapImage,
        'description' => $description,
        'isPartOf'    => [
            '@type' => 'WebSite',
            'name'  => 'Tempus Plaza',
            'url'   => SITE_URL,
        ],
    ];

    return [
        'tags'      => generateMetaTags([
            'title'        => $mapName . ' | Tempus Plaza',
            'description'  => $description,
            'image'        => $mapImage,
            'image_width'  => 1920,
            'image_height' => 1080,
            'url'          => $mapUrl,
            'twitter_card' => 'summary_large_image',
            'no_index'     => $notFound,
            'schema'       => $schema,
        ]),
        'not_found' => $notFound,
    ];
}

/**
 * Get meta tags for player pages
 */
function getPlayerMeta(int $playerId): array {
    $apiUrl = API_BASE_URL . '/players/' . $playerId;
    $data = fetchApi($apiUrl);

    $ranksUrl = API_BASE_URL . '/players/' . $playerId . '/ranks';
    $ranks = fetchApi($ranksUrl);

    $playerName    = $data[0]['name'] ?? ('Player ' . $playerId);
    $playerAvatar  = $data[0]['steam_avatar'] ?? DEFAULT_IMAGE;
    $playerCountry = $data[0]['country'] ?? null;
    $playerUrl = SITE_URL . '/players/' . $playerId;

    $solRank   = $ranks[0]['soldier_rank'] ?? null;
    $demRank   = $ranks[0]['demoman_rank'] ?? null;
    $solPoints = $ranks[0]['soldier_points'] ?? null;
    $demPoints = $ranks[0]['demoman_points'] ?? null;

    $description = $playerName . '\'s Tempus stats on Tempus Plaza';
    if ($solRank) $description .= '. Soldier rank #' . $solRank . ' (' . number_format($solPoints) . ' pts)';
    if ($demRank) $description .= ', Demoman rank #' . $demRank . ' (' . number_format($demPoints) . ' pts)';
    if ($playerCountry) $description .= ' from ' . $playerCountry;
    $description .= '.';

    $schema = $notFound ? null : [
        '@context'    => 'https://schema.org',
        '@type'       => 'ProfilePage',
        'name'        => $playerName . ' – Tempus Plaza',
        'url'         => $playerUrl,
        'description' => $description,
        'mainEntity'  => [
            '@type' => 'Person',
            'name'  => $playerName,
            'image' => $playerAvatar,
            'url'   => $playerUrl,
        ],
        'isPartOf' => [
            '@type' => 'WebSite',
            'name'  => 'Tempus Plaza',
            'url'   => SITE_URL,
        ],
    ];

    return [
        'tags'      => generateMetaTags([
            'title'        => $playerName . ' | Tempus Plaza',
            'description'  => $description,
            'image'        => $playerAvatar,
            'url'          => $playerUrl,
            'twitter_card' => 'summary_large_image',
            'no_index'     => $notFound,
            'schema'       => $schema,
        ]),
        'not_found' => $notFound,
    ];
}

/**
 * Get meta tags for static/generic pages
 */
function getPageMeta(string $requestUri): ?array {
    $pageMeta = [
        '/maps' => [
            'title' => 'Tempus Maps | Tempus Plaza',
            'desc'  => 'Explore Tempus maps, courses, and bonuses. Browse the complete Tempus tf2 map database.',
        ],
        '/activity' => [
            'title' => 'Tempus Activity | Tempus Plaza',
            'desc'  => 'Track the latest Tempus world records, top times and group 1 times in real-time.',
        ],
        '/servers' => [
            'title' => 'Tempus Servers | Tempus Plaza',
            'desc'  => 'View Tempus top players currently jumping on tf2 and all the Tempus server information.',
        ],
        '/players' => [
            'title' => 'Top Tempus Players | Tempus Plaza',
            'desc'  => 'View the top Tempus players leaderboards and rankings across all sorts of categories.',
        ],
        '/history' => [
            'title' => 'Tempus jump History | Tempus Plaza',
            'desc'  => 'View the history of Tempus map, players and records changes since 2014*',
        ],
        '/compare' => [
            'title' => 'Compare Tempus Players | Tempus Plaza',
            'desc'  => 'Compare Tempus records and stats between two players side by side including points, rankings and more.',
        ],
        '/donate' => [
            'title' => 'Support Tempus Plaza',
            'desc'  => 'Help keep the servers running and unlock exclusive perks.',
        ],
        '/lookup' => [
            'title' => 'Lookup | Search Players and Maps',
            'desc'  => 'Search and filter through Tempus players, map records and completions with all the extra information you need.',
        ],
        '/lookup/player' => [
            'title' => 'Tempus Player Records | Tempus Plaza',
            'desc'  => 'View all Tempus records for this player, including completions, tiers, ratings, points and more.',
        ],
        '/lookup/map' => [
            'title' => 'Tempus Map Records | Tempus Plaza',
            'desc'  => 'View all Tempus records for this map, including completions, tiers, ratings, points and more.',
        ],
    ];

    $page = strtok($requestUri, '?');

    // Exact match
    if (isset($pageMeta[$page])) {
        $meta = $pageMeta[$page];
    } else {
        // Prefix match for dynamic sub-routes
        $meta = null;
        foreach ($pageMeta as $basePath => $pageData) {
            if (strpos($page, $basePath . '/') === 0) {
                $meta = $pageData;
                break;
            }
        }
        if (!$meta) {
            return null;
        }
    }

    $pageUrl = SITE_URL . $page;

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'WebPage',
        'name'        => $meta['title'],
        'url'         => $pageUrl,
        'description' => $meta['desc'],
        'isPartOf'    => [
            '@type' => 'WebSite',
            'name'  => 'Tempus Plaza',
            'url'   => SITE_URL,
        ],
    ];

    return [
        'tags'      => generateMetaTags([
            'title'        => $meta['title'],
            'description'  => $meta['desc'],
            'image'        => DEFAULT_IMAGE,
            'image_width'  => 512,
            'image_height' => 512,
            'url'          => $pageUrl,
            'twitter_card' => 'summary',
            'schema'       => $schema,
        ]),
        'not_found' => false,
    ];
}

// -------------------------------------------------
// Route to appropriate meta generator
// -------------------------------------------------
$result = null;

if ($mapId) {
    $result = getMapMeta($mapId);
} elseif ($playerId) {
    $result = getPlayerMeta($playerId);
} elseif ($requestUri === '/') {
    $result = getHomeMeta();
} else {
    $result = getPageMeta($requestUri);
}

// Return 404 for unknown entities so Google doesn't index thin/empty pages
if ($result === null || !empty($result['not_found'])) {
    http_response_code(404);
    header('Cache-Control: no-store');
}

// Inject and output
if ($result !== null) {
    $html = injectMetaTags($html, $result['tags']);
}

echo $html;