<?php require_once __DIR__ . "/shoutbox.php"; ?>

<?php
$indexFile = __DIR__ . "/assets/db/rom_index.json";
$cacheFile = __DIR__ . "/assets/db/cache.json";

$roms = file_exists($indexFile)
    ? json_decode(file_get_contents($indexFile), true)
    : [];

$names = file_exists($cacheFile)
    ? json_decode(file_get_contents($cacheFile), true)
    : [];

$thumbPathFS  = __DIR__ . "/assets/thumbnails/";
$thumbPathURL = "assets/thumbnails/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Arcade Hub</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #070b16;
    --bg2: #0b1223;
    --panel: rgba(17, 24, 39, 0.88);
    --panel-2: rgba(15, 23, 42, 0.9);
    --border: rgba(255,255,255,0.08);
    --text: #e5eefc;
    --muted: #9aa8c3;
    --accent: #ff4d6d;
    --accent-2: #38bdf8;
    --accent-3: #fbbf24;
    --shadow: 0 18px 50px rgba(0,0,0,0.42);
}
* { box-sizing: border-box; }
html, body { min-height: 100%; }
body {
    margin: 0;
    font-family: 'Inter', system-ui, sans-serif;
    color: var(--text);
    background:
        radial-gradient(circle at top left, rgba(56,189,248,0.16), transparent 26%),
        radial-gradient(circle at top right, rgba(255,77,109,0.18), transparent 22%),
        linear-gradient(180deg, var(--bg), var(--bg2));
}
body::before {
    content: '';
    position: fixed;
    inset: 0;
    pointer-events: none;
    background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 36px 36px;
    mask-image: linear-gradient(180deg, rgba(0,0,0,0.7), transparent 100%);
    opacity: .22;
}
header {
    position: sticky;
    top: 0;
    z-index: 10;
    padding: 18px 16px 16px;
    background: linear-gradient(180deg, rgba(7,11,22,0.96), rgba(7,11,22,0.72));
    backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--border);
}
.header-inner {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1.2fr 1fr 1fr;
    gap: 12px;
    align-items: center;
}
.brand {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.brand h2 {
    margin: 0;
    font-size: 1.3rem;
    letter-spacing: 0.3px;
}
.brand span {
    color: var(--muted);
    font-size: 0.88rem;
}
.controls {
    display: contents;
}
input, select {
    width: 100%;
    border: 1px solid var(--border);
    background: rgba(10,15,28,0.92);
    color: var(--text);
    padding: 13px 14px;
    border-radius: 14px;
    outline: none;
    font: inherit;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.03);
}
input::placeholder { color: #71819f; }
input:focus, select:focus {
    border-color: rgba(56,189,248,0.45);
    box-shadow: 0 0 0 4px rgba(56,189,248,0.12);
}
.grid {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 16px;
    padding: 22px 16px 110px;
}
.card {
    position: relative;
    overflow: hidden;
    background: linear-gradient(180deg, rgba(17,24,39,0.96), rgba(10,15,28,0.96));
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    padding: 12px;
    box-shadow: var(--shadow);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    text-align: center;
    backdrop-filter: blur(8px);
}
.card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(56,189,248,0.08), transparent 30%, rgba(255,77,109,0.08));
    opacity: 0;
    transition: opacity .2s ease;
    pointer-events: none;
}
.card:hover {
    transform: translateY(-4px) scale(1.01);
    border-color: rgba(56,189,248,0.3);
    box-shadow: 0 22px 55px rgba(0,0,0,0.55), 0 0 0 1px rgba(56,189,248,0.18);
}
.card:hover::after { opacity: 1; }
.thumb {
    width: 100%;
    aspect-ratio: 4 / 3;
    object-fit: cover;
    border-radius: 14px;
    background:
        linear-gradient(135deg, rgba(255,77,109,0.14), rgba(56,189,248,0.12)),
        #121826;
    border: 1px solid rgba(255,255,255,0.08);
    margin-bottom: 10px;
}
.card .title {
    font-weight: 700;
    line-height: 1.3;
    margin: 2px 0 8px;
    font-size: 0.98rem;
}
.system {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.73rem;
    color: #ffd166;
    background: rgba(251,191,36,0.12);
    border: 1px solid rgba(251,191,36,0.18);
    padding: 6px 9px;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
button.play {
    width: 100%;
    margin-top: 10px;
    padding: 10px 12px;
    border: none;
    background: linear-gradient(135deg, var(--accent), #ff6b3d);
    color: white;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 700;
    letter-spacing: 0.02em;
    box-shadow: 0 10px 24px rgba(255,77,109,0.28);
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
}
button.play:hover {
    transform: translateY(-1px);
    filter: brightness(1.04);
    box-shadow: 0 12px 28px rgba(255,77,109,0.36);
}
button.play:active { transform: translateY(0); }
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 64px 20px;
    color: var(--muted);
}
.empty-state h3 {
    margin: 0 0 8px;
    color: var(--text);
    font-size: 1.15rem;
}
@media (max-width: 900px) {
    .header-inner { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .grid { grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); }
}
</style>
</head>

<body>
<header>
    <div class="header-inner">
        <div class="brand">
            <h2>🎮 Arcade Hub</h2>
            <span>Browse systems, search fast, and launch instantly.</span>
        </div>
        <input id="search" placeholder="Search games...">
        <select id="system">
            <option value="">All Systems</option>
            <option value="arcade">Arcade</option>
            <option value="neogeo">NeoGeo</option>
            <option value="cps1">CPS1</option>
            <option value="cps2">CPS2</option>
            <option value="nes">NES</option>
            <option value="snes">SNES</option>
            <option value="gba">GBA</option>
            <option value="gb">GB</option>
            <option value="gbc">GBC</option>
            <option value="n64">N64</option>
            <option value="psx">PSX</option>
            <option value="genesis">Genesis</option>
            <option value="sms">SMS</option>
            <option value="gg">GG</option>
            <option value="sega32x">Sega 32X</option>
            <option value="segacd">Sega CD</option>
            <option value="saturn">Saturn</option>
        </select>
    </div>
</header>

<div class="grid">
<?php if (empty($roms)): ?>
    <div class="empty-state">
        <h3>No ROMs found</h3>
        <div>Run <code>php build_index.php</code> after adding files under <code>roms/</code>.</div>
    </div>
<?php endif; ?>

<?php foreach ($roms as $key => $g): ?>
<?php
$name = $names[$key] ?? $g['name'];
$sys  = $g['system'];

$thumbFile = null;
foreach (['png','jpg','jpeg','webp'] as $ext) {
    $try = $thumbPathFS . $key . "." . $ext;
    if (file_exists($try)) {
        $thumbFile = $thumbPathURL . $key . "." . $ext;
        break;
    }
}

$fileJs = json_encode($g['file'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$keyJs  = json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>
<div class="card"
data-name="<?= htmlspecialchars(strtolower($name), ENT_QUOTES, 'UTF-8') ?>"
data-system="<?= htmlspecialchars($sys, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($thumbFile): ?>
        <img class="thumb" src="<?= htmlspecialchars($thumbFile, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
    <?php else: ?>
        <div class="thumb"></div>
    <?php endif; ?>

    <div class="title"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></div>
    <div class="system"><?= htmlspecialchars(strtoupper($sys), ENT_QUOTES, 'UTF-8') ?></div>

    <button class="play" onclick='play(<?= $fileJs ?>, <?= $keyJs ?>)'>PLAY</button>
</div>
<?php endforeach; ?>
</div>

<script>
const search = document.getElementById("search");
const system = document.getElementById("system");

function filter() {
    const q = search.value.toLowerCase();
    const sys = system.value;

    document.querySelectorAll(".card").forEach(c => {
        const ok = c.dataset.name.includes(q) && (!sys || c.dataset.system === sys);
        c.style.display = ok ? "block" : "none";
    });
}

search.addEventListener("input", filter);
system.addEventListener("change", filter);

function play(file, id) {
    window.location.href = "player.php?rom=" + encodeURIComponent(file);
}
</script>

<?php include __DIR__ . "/shoutbox_widget.php"; ?>

</body>
</html>
