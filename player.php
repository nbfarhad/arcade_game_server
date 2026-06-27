<?php
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

$rom = $_GET['rom'] ?? '';
if (!$rom) die("No ROM selected");

$indexFile = __DIR__ . "/assets/db/rom_index.json";

$index = file_exists($indexFile)
    ? json_decode(file_get_contents($indexFile), true)
    : [];

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
$gameUrlJs = json_encode($BASE . "/roms/" . $game['file'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$coreJs = json_encode($core, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$title = htmlspecialchars($game['name'], ENT_QUOTES, 'UTF-8');
$systemLabel = htmlspecialchars(strtoupper($game['system']), ENT_QUOTES, 'UTF-8');
$loaderPath = htmlspecialchars($BASE . '/emulatorjs/data/loader.js', ENT_QUOTES, 'UTF-8');
$pathtodata = json_encode($BASE . "/emulatorjs/data/", JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #050816;
    --panel: rgba(15, 23, 42, 0.68);
    --border: rgba(255,255,255,0.08);
    --text: #e5eefc;
    --muted: #9aa8c3;
    --accent: #ff4d6d;
    --accent-2: #38bdf8;
}
* { box-sizing: border-box; }
html, body { width: 100%; height: 100%; }
body {
    margin: 0;
    font-family: 'Inter', system-ui, sans-serif;
    color: var(--text);
    background:
        radial-gradient(circle at top, rgba(56,189,248,0.16), transparent 30%),
        radial-gradient(circle at bottom right, rgba(255,77,109,0.18), transparent 26%),
        linear-gradient(180deg, #050816 0%, #070b16 100%);
    overflow: hidden;
}
body::before {
    content: '';
    position: fixed;
    inset: 0;
    pointer-events: none;
    background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 36px 36px;
    opacity: .18;
}
.shell {
    position: relative;
    width: 100%;
    height: 100%;
}
.topbar {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    z-index: 5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: linear-gradient(180deg, rgba(5,8,22,0.88), rgba(5,8,22,0.22));
    backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--border);
}
.back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text);
    text-decoration: none;
    padding: 10px 14px;
    border-radius: 999px;
    background: rgba(255,255,255,0.06);
    border: 1px solid var(--border);
}
.back:hover { border-color: rgba(56,189,248,0.35); }
.meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    text-align: right;
}
.meta strong { font-size: 0.98rem; }
.meta span { font-size: 0.8rem; color: var(--muted); }
.stage {
    position: absolute;
    inset: 0;
    padding-top: 58px;
}
#game {
    width: 100%;
    height: 100%;
    background: #000;
}
.loading {
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
    background: radial-gradient(circle at center, rgba(17,24,39,0.8), rgba(5,8,22,0.96));
    z-index: 4;
    transition: opacity .25s ease, visibility .25s ease;
}
.loading.hidden {
    opacity: 0;
    visibility: hidden;
}
.loader-card {
    width: min(420px, calc(100vw - 32px));
    padding: 26px 24px;
    border-radius: 20px;
    border: 1px solid var(--border);
    background: rgba(15,23,42,0.8);
    box-shadow: 0 18px 55px rgba(0,0,0,0.45);
    text-align: center;
}
.loader-ring {
    width: 56px;
    height: 56px;
    margin: 0 auto 14px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.14);
    border-top-color: var(--accent-2);
    animation: spin 1s linear infinite;
}
.loader-card h1 {
    margin: 0 0 6px;
    font-size: 1.08rem;
}
.loader-card p {
    margin: 0;
    color: var(--muted);
    font-size: 0.9rem;
}
@keyframes spin { to { transform: rotate(360deg); } }
@media (max-width: 720px) {
    .topbar { padding: 12px; }
    .meta { display: none; }
}
</style>
</head>

<body>
<div class="shell">
    <div class="topbar">
        <a class="back" href="<?= htmlspecialchars($BASE . '/index.php', ENT_QUOTES, 'UTF-8') ?>">← Back to library</a>
        <div class="meta">
            <strong><?= $title ?></strong>
            <span><?= $systemLabel ?> • EmulatorJS</span>
        </div>
    </div>

    <div class="stage">
        <div id="game"></div>
        <div class="loading" id="loading">
            <div class="loader-card">
                <div class="loader-ring"></div>
                <h1>Launching game…</h1>
                <p><?= $title ?></p>
            </div>
        </div>
    </div>
</div>

<script>
EJS_player = "#game";
EJS_pathtodata = <?= $pathtodata ?>;
EJS_gameUrl = <?= $gameUrlJs ?>;
EJS_core = <?= $coreJs ?>;
EJS_startOnLoaded = true;

window.addEventListener('load', () => {
    setTimeout(() => {
        const loading = document.getElementById('loading');
        if (loading) loading.classList.add('hidden');
    }, 900);
});
</script>

<script src="<?= $loaderPath ?>"></script>

</body>
</html>
