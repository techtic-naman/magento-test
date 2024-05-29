<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));
$diFiles = new RegexIterator($files, '/\/etc\/di.xml$/');

$plugins = [];

// foreach ($files as $file) {
//     echo "Searching in: " . $file->getPathname() . "\n";
// }

foreach ($diFiles as $file) {
    $xml = simplexml_load_file($file->getPathname());
    foreach ($xml->xpath('//type/plugin') as $plugin) {
        $plugins[] = (string)$plugin['type'];
    }
}

print_r($plugins);
exit;