<?php

header('Content-Type: application/xml; charset=utf-8');

$maps = json_decode(file_get_contents("https://api.tempusplaza.com/maps"), true);

echo '<?xml version="1.0" encoding="UTF-8"?>';

echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

foreach ($maps as $map) {

    echo "<url>";
    echo "<loc>https://tempusplaza.com/maps/{$map['id']}</loc>";
    echo "<changefreq>weekly</changefreq>";
    echo "<priority>0.8</priority>";
    echo "</url>";
}

echo "</urlset>";