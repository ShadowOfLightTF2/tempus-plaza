<?php
header('Content-Type: application/xml; charset=utf-8');

$cache_file = __DIR__ . '/players_sitemap_cache.xml';
$cache_duration = 86400; // 24 hours

if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_duration)) {
    echo file_get_contents($cache_file);
    exit;
}

$context = stream_context_create(['http' => ['timeout' => 30]]);
$data = file_get_contents("https://api.tempusplaza.com/players", false, $context);
$players = $data ? json_decode($data, true) : [];

$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach ($players as $player) {
    $xml .= "<url>";
    $xml .= "<loc>https://tempusplaza.com/players/{$player['player_id']}</loc>";
    $xml .= "<changefreq>weekly</changefreq>";
    $xml .= "<priority>0.7</priority>";
    $xml .= "</url>";
}
$xml .= "</urlset>";

file_put_contents($cache_file, $xml);
echo $xml;