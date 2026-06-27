<?php

$root = __DIR__ . "/roms";
$out  = __DIR__ . "/assets/db/rom_index.json";

/* -------------------------------------------------------
   SYSTEMS YOU ACTUALLY USE
------------------------------------------------------- */
$systems = [
    "arcade",
    "cps1",
    "cps2",
    "neogeo",
    "nes",
    "snes",
    "gba",
    "gb",
    "gbc",
    "n64",
    "psx",
    "genesis",
    "sms",
    "gg",
    "sega32x",
    "segacd",
    "saturn"
];

/* -------------------------------------------------------
   FOLDERS TO INCLUDE ON DISK BUT NOT SHOW IN FRONTEND
------------------------------------------------------- */
$hiddenFolders = [
    "neogeo"
];

$index = [];

/* -------------------------------------------------------
   SCAN EACH SYSTEM FOLDER
------------------------------------------------------- */
foreach ($systems as $sys) {

    if (in_array($sys, $hiddenFolders, true)) {
        continue;
    }

    $dir = "$root/$sys";

    if (!is_dir($dir)) continue;

    foreach (scandir($dir) as $file) {

        if ($file === '.' || $file === '..') continue;

        $full = "$dir/$file";

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

/* -------------------------------------------------------
   WRITE JSON
------------------------------------------------------- */
file_put_contents(
    $out,
    json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo "Index built: " . count($index);
