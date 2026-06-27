<?php

$root = __DIR__ . "/roms";
$out  = __DIR__ . "/assets/db/rom_index.json";

$index = [];

/* -------------------------------------------------------
   SCAN EVERY ROM CATEGORY FOLDER
------------------------------------------------------- */
$systemDirs = glob($root . '/*', GLOB_ONLYDIR);

if ($systemDirs === false) {
    $systemDirs = [];
}

foreach ($systemDirs as $dirPath) {
    $sys = basename($dirPath);

    foreach (scandir($dirPath) as $file) {
        if ($file === '.' || $file === '..') continue;

        $full = "$dirPath/$file";
        if (!is_file($full)) continue;

        $name = pathinfo($file, PATHINFO_FILENAME);

        $index[] = [
            "file"   => "$sys/$file",
            "system" => $sys,
            "name"   => ucwords(str_replace(['_','-'], ' ', strtolower($name))),
            "ext"    => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
            "size"   => filesize($full)
        ];
    }
}

file_put_contents(
    $out,
    json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo "Index built: " . count($index);
