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
<title>Arcade Hub</title>

<style>
body {
    margin:0;
    font-family:Arial;
    background:#0f0f12;
    color:#fff;
}

header {
    position:sticky;
    top:0;
    background:#1c1c22;
    padding:15px;
    text-align:center;
    z-index:10;
}

input, select {
    padding:10px;
    border-radius:8px;
    border:none;
    margin:5px;
    width:40%;
}

.grid {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:12px;
    padding:20px;
}

.card {
    background:#1e1e24;
    padding:12px;
    border-radius:10px;
    cursor:pointer;
    transition:0.2s;
    text-align:center;
}

.card:hover {
    transform:scale(1.03);
    border:1px solid #ff4757;
}

.system {
    font-size:11px;
    color:#ffa502;
}

.thumb {
    width:100%;
    height:120px;
    object-fit:cover;
    border-radius:8px;
    background:#222;
    margin-bottom:8px;
}

button.play {
    width:100%;
    margin-top:8px;
    padding:6px;
    border:none;
    background:#ff4757;
    color:white;
    border-radius:6px;
    cursor:pointer;
}
</style>
</head>

<body>

<header>
<h2>🎮 Arcade Hub</h2>

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
</header>

<div class="grid">

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

<div><b><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></b></div>

<div class="system"><?= htmlspecialchars(strtoupper($sys), ENT_QUOTES, 'UTF-8') ?></div>

<button class="play"
onclick='play(<?= $fileJs ?>, <?= $keyJs ?>)'>
PLAY
</button>

</div>

<?php endforeach; ?>

</div>

<script>
const search = document.getElementById("search");
const system = document.getElementById("system");

function filter() {
    let q = search.value.toLowerCase();
    let sys = system.value;

    document.querySelectorAll(".card").forEach(c => {
        let ok =
            c.dataset.name.includes(q) &&
            (!sys || c.dataset.system === sys);

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
