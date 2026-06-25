<?php
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

$rom = $_GET['rom'] ?? '';
if (!$rom) die("No ROM selected");

$indexFile = __DIR__ . "/assets/db/rom_index.json";

$index = file_exists($indexFile)
    ? json_decode(file_get_contents($indexFile), true)
    : [];

$key = pathinfo($rom, PATHINFO_FILENAME);

if (!isset($index[$key])) {
    die("Game not found");
}

$game = $index[$key];

$coreMap = [
    "nes"=>"nestopia",
    "snes"=>"snes9x",
    "gba"=>"mgba",
    "gb"=>"gambatte",
    "gbc"=>"gambatte",
    "n64"=>"mupen64plus_next",
    "psx"=>"mednafen_psx_hw",
    "arcade"=>"fbneo",
    "genesis"=>"genesis_plus_gx",
    "sms"=>"genesis_plus_gx",
    "gg"=>"genesis_plus_gx"
];

$core = $coreMap[$game['system']] ?? "fbneo";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($game['name']) ?></title>

<style>
body { margin:0; background:black; }
#game { width:100vw; height:100vh; }
</style>
</head>

<body>

<div id="game"></div>

<script>
EJS_player = "#game";

/* FIXED: no hardcoded /arcade anymore */
EJS_pathtodata = "<?= $BASE ?>/emulatorjs/data/";

EJS_gameUrl = "player.php?stream=<?= urlencode($game['file']) ?>";
EJS_core = "<?= $core ?>";
EJS_startOnLoaded = true;
</script>

<script src="<?= $BASE ?>/emulatorjs/data/loader.js"></script>

</body>
</html>

<?php
// ROM STREAM (SECURE)
if (isset($_GET['stream'])) {

    $file = $_GET['stream'];
    $path = __DIR__ . "/roms/" . ltrim($file, '/');

    if (!file_exists($path)) {
        http_response_code(404);
        exit("Not found");
    }

    header("Content-Type: application/octet-stream");
    header("Content-Length: " . filesize($path));
    readfile($path);
    exit;
}
?>
