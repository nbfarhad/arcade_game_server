<?php
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

$rom = $_GET['rom'] ?? '';
if (!$rom) die("No ROM selected");

$indexFile = __DIR__ . "/assets/db/rom_index.json";

$index = file_exists($indexFile)
    ? json_decode(file_get_contents($indexFile), true)
    : [];

/* -------------------------------------------------------
   FIXED: match ROM by actual file path (NOT array key)
------------------------------------------------------- */
$game = null;

foreach ($index as $g) {
    if (isset($g['file']) && $g['file'] === $rom) {
        $game = $g;
        break;
    }
}

if (!$game) {
    die("Game not found in index");
}

/* -------------------------------------------------------
   CORE MAP
------------------------------------------------------- */
$coreMap = [
    "nes"      => "nestopia",
    "snes"     => "snes9x",
    "gba"      => "mgba",
    "gb"       => "gambatte",
    "gbc"      => "gambatte",
    "n64"      => "mupen64plus_next",
    "psx"      => "mednafen_psx_hw",
    "arcade"   => "fbneo",
    "neogeo"   => "fbneo",
    "cps1"     => "fbalpha2012_cps1",
    "cps2"     => "fbalpha2012_cps2",
    "genesis"  => "genesis_plus_gx",
    "sms"      => "genesis_plus_gx",
    "gg"       => "genesis_plus_gx",
    "sega32x"  => "picodrive",
    "segacd"   => "genesis_plus_gx",
    "saturn"   => "yabause"
];

$core = $coreMap[$game['system']] ?? "fbneo";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($game['name']) ?></title>

<style>
body {
    margin:0;
    background:black;
}
#game {
    width:100vw;
    height:100vh;
}
</style>
</head>

<body>

<div id="game"></div>

<script>
EJS_player = "#game";

/* IMPORTANT: correct local path */
EJS_pathtodata = "<?= $BASE ?>/emulatorjs/data/";

/* DIRECT ROM LOAD (BEST PRACTICE) */
EJS_gameUrl = "<?= $BASE ?>/roms/<?= htmlspecialchars($game['file']) ?>";

EJS_core = "<?= $core ?>";
EJS_startOnLoaded = true;
</script>

<script src="<?= $BASE ?>/emulatorjs/data/loader.js"></script>

</body>
</html>
