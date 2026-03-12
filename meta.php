<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

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

// Redirect to home if no params
if (!$mapId && !$playerId && $requestUri === '/') {
    header('Location: /');
    exit;
}

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
 * Generate meta tags string
 */
function generateMetaTags(array $meta): string {
    $tags = sprintf('
        <meta property="og:title" content="%s">
        <meta property="og:description" content="%s">
        <meta property="og:image" content="%s">',
        htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($meta['image'], ENT_QUOTES, 'UTF-8')
    );
    
    if (isset($meta['image_width'])) {
        $tags .= sprintf('
        <meta property="og:image:width" content="%d">
        <meta property="og:image:height" content="%d">',
            $meta['image_width'],
            $meta['image_height']
        );
    }
    
    $tags .= sprintf('
        <meta property="og:url" content="%s">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Tempus Plaza">
        <meta name="theme-color" content="#253752">
        <meta name="twitter:card" content="%s">
        <meta name="twitter:image" content="%s">',
        htmlspecialchars($meta['url'], ENT_QUOTES, 'UTF-8'),
        $meta['twitter_card'] ?? 'summary',
        htmlspecialchars($meta['image'], ENT_QUOTES, 'UTF-8')
    );
    
    return $tags;
}

/**
 * Get meta tags for map pages
 */
function getMapMeta(int $mapId): string {
    $apiUrl = API_BASE_URL . '/maps/' . $mapId . '/all-info';
    $data = fetchApi($apiUrl);
    
    $mapName = $data['map']['name'] ?? 'Map ' . $mapId;
    
    return generateMetaTags([
        'title' => 'Tempus Plaza | ' . $mapName,
        'description' => 'View ' . $mapName . ' Tempus leaderboards and info',
        'image' => SITE_URL . '/map-backgrounds/' . urlencode($mapName) . '.jpg',
        'image_width' => 1920,
        'image_height' => 1080,
        'url' => SITE_URL . '/maps/' . $mapId,
        'twitter_card' => 'summary_large_image'
    ]);
}

/**
 * Get meta tags for player pages
 */
function getPlayerMeta(int $playerId): string {
    $apiUrl = API_BASE_URL . '/players/' . $playerId;
    $data = fetchApi($apiUrl);
    
    $playerName = $data[0]['name'] ?? 'Player ' . $playerId;
    $playerAvatar = $data[0]['steam_avatar'] ?? DEFAULT_IMAGE;
    
    return generateMetaTags([
        'title' => 'Tempus Plaza | ' . $playerName,
        'description' => 'View ' . $playerName . '\'s Tempus stats and records',
        'image' => $playerAvatar,
        'url' => SITE_URL . '/players/' . $playerId,
        'twitter_card' => 'summary_large_image'
    ]);
}

/**
 * Get meta tags for generic pages
 */
function getPageMeta(string $requestUri): ?string {
    $pageMeta = [
        '/maps' => [
            'title' => 'Tempus Maps | Tempus Plaza',
            'desc'  => 'Explore Tempus maps, courses, and bonuses. Browse the complete Tempus map database.',
        ],
        '/activity' => [
            'title' => 'Tempus Activity Dashboard | Tempus Plaza',
            'desc'  => 'Track the latest Tempus records and player activity in real-time',
        ],
        '/servers' => [
            'title' => 'Tempus Servers | Tempus Plaza',
            'desc'  => 'View Tempus server status, top players online, and server information',
        ],
        '/players' => [
            'title' => 'Top Tempus Players | Tempus Plaza',
            'desc'  => 'View the top Tempus players and rankings across all categories',
        ],
        '/compare' => [
            'title' => 'Compare Tempus Players | Tempus Plaza',
            'desc'  => 'Compare Tempus records and stats between two players',
        ],
        '/donate' => [
            'title' => 'Support Tempus Plaza',
            'desc'  => 'Help keep the servers running and unlock exclusive perks',
        ],
        '/lookup' => [
            'title' => 'Tempus Lookup | Search Players And Maps',
            'desc'  => 'Search and filter through Tempus players and map records',
        ],
        '/lookup/player' => [
            'title' => 'Tempus Player Records | Tempus Plaza',
            'desc'  => 'View all Tempus records for this player',
        ],
        '/lookup/map' => [
            'title' => 'Tempus Map Records | Tempus Plaza', 
            'desc'  => 'View all Tempus records for this map',
        ]
    ];

    $page = strtok($requestUri, '?');
    
    // Try exact match first
    if (isset($pageMeta[$page])) {
        $meta = $pageMeta[$page];
    } else {
        // Try prefix match for dynamic routes
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
    
    return generateMetaTags([
        'title' => $meta['title'],
        'description' => $meta['desc'],
        'image' => DEFAULT_IMAGE,
        'image_width' => 512,
        'image_height' => 512,
        'url' => SITE_URL . $page,
        'twitter_card' => 'summary'
    ]);
}

// Generate appropriate meta tags
$metaTags = null;

if ($mapId) {
    $metaTags = getMapMeta($mapId);
} elseif ($playerId) {
    $metaTags = getPlayerMeta($playerId);
} else {
    $metaTags = getPageMeta($requestUri);
}

// Inject meta tags into HTML
if ($metaTags) {
    $html = preg_replace(
        '/<meta property="og:title".*?<meta name="theme-color"[^>]*>/s',
        $metaTags,
        $html,
        1 // Only replace first occurrence
    );
}

echo $html;