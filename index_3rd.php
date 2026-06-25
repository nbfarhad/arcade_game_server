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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Arcade Hub</title>

<style>
body { margin:0; font-family:Arial; background:#0f0f12; color:#fff; }

header { position:sticky; top:0; background:#1c1c22; padding:15px; text-align:center; }

input, select { padding:10px; border-radius:8px; border:none; margin:5px; width:40%; }

.grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px; padding:20px; }

.card { background:#1e1e24; padding:12px; border-radius:10px; cursor:pointer; }

.card:hover { border:1px solid #ff4757; }

.system { font-size:11px; color:#ffa502; }

button.play {
    width:100%; margin-top:8px; padding:6px;
    border:none; background:#ff4757; color:white; border-radius:6px;
}
</style>
</head>

<body>

<header>
<h2>🎮 Arcade Hub</h2>

<input id="search" placeholder="Search games...">

<select id="system">
    <option value="">All Systems</option>
    <option value="nes">NES</option>
    <option value="snes">SNES</option>
    <option value="gba">GBA</option>
    <option value="gb">GB</option>
    <option value="gbc">GBC</option>
    <option value="n64">N64</option>
    <option value="psx">PSX</option>
    <option value="arcade">Arcade</option>
    <option value="cps1">CPS1</option>
    <option value="cps2">CPS2</option>
    <option value="neogeo">NeoGeo</option>
    <option value="genesis">Genesis</option>
</select>
</header>

<div class="grid">

<?php foreach ($roms as $key => $g): ?>
<?php
$name = $names[$key] ?? ($g['name'] ?? $key);
?>

<div class="card"
data-name="<?= strtolower($name) ?>"
data-system="<?= strtolower($g['system']) ?>">

<div><b><?= htmlspecialchars($name) ?></b></div>

<div class="system"><?= strtoupper($g['system']) ?></div>

<button class="play"
onclick="play('<?= $g['file'] ?>','<?= $key ?>')">
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
    let sys = system.value.toLowerCase();

    document.querySelectorAll(".card").forEach(c => {
        let name = c.dataset.name || "";
        let s = c.dataset.system || "";

        let ok = name.includes(q) && (!sys || s === sys);
        c.style.display = ok ? "block" : "none";
    });
}

search.addEventListener("input", filter);
system.addEventListener("change", filter);

function play(file,id){
    window.location.href = "player.php?rom=" + encodeURIComponent(file);
}

</script>

<?php include __DIR__ . "/shoutbox_widget.php"; ?>

</body>
</html>
