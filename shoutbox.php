<?php
// ---------------------------------------------------------------
// shoutbox.php — handles AJAX read/write requests
// Include this at the top of any page that needs the shoutbox.
// ---------------------------------------------------------------

$messagesFile  = __DIR__ . '/assets/db/shout_messages.json';
$rateLimitFile = __DIR__ . '/assets/db/shout_ratelimit.json';
$onlineFile    = __DIR__ . '/assets/db/shout_online.json';

// --- Config ---
$maxMessages   = 100;
$maxNameLen    = 20;
$maxMsgLen     = 200;
$onlineTimeout = 300;  // 5 minutes until user considered offline

// --- Flood config ---
$floodWindow   = 10;   // seconds to track
$floodMaxMsgs  = 3;    // max messages allowed within that window

if (!is_dir(__DIR__ . '/assets/db')) {
    @mkdir(__DIR__ . '/assets/db', 0775, true);
}

function shoutbox_read_json($path, $default = []) {
    if (!file_exists($path)) {
        return $default;
    }
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : $default;
}

function shoutbox_write_json($path, $data) {
    file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

function shoutbox_trim_text($value, $maxLen) {
    $value = trim(strip_tags((string) $value));
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $maxLen);
    }
    return substr($value, 0, $maxLen);
}

function shoutbox_html($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------------
// AJAX endpoint — called by JS, returns JSON
// ---------------------------------------------------------------
if (isset($_GET['shout_action'])) {
    header('Content-Type: application/json; charset=UTF-8');

    $action = $_GET['shout_action'];
    $ip     = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $name   = shoutbox_trim_text($_POST['name'] ?? $_GET['name'] ?? '', $maxNameLen);

    // --- Track online users ---
    $online = shoutbox_read_json($onlineFile, []);

    $now = time();

    // Remove stale users
    foreach ($online as $k => $v) {
        if (($now - (int)($v['time'] ?? 0)) > $onlineTimeout) unset($online[$k]);
    }

    // Update current user if name provided
    if ($name !== '') {
        $online[$ip] = ['name' => shoutbox_html($name), 'time' => $now];
    }

    shoutbox_write_json($onlineFile, $online);

    // --- POST message ---
    if ($action === 'post') {
        $msg = shoutbox_trim_text($_POST['msg'] ?? '', $maxMsgLen);

        if ($name === '' || $msg === '') {
            echo json_encode(['ok' => false, 'error' => 'Name and message required.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (strlen($name) > $maxNameLen) {
            echo json_encode(['ok' => false, 'error' => 'Name too long (max ' . $maxNameLen . ' chars).'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (strlen($msg) > $maxMsgLen) {
            echo json_encode(['ok' => false, 'error' => 'Message too long (max ' . $maxMsgLen . ' chars).'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // --- Flood check: max N messages per window ---
        $rateLimit = shoutbox_read_json($rateLimitFile, []);
        $timestamps = $rateLimit[$ip] ?? [];
        $timestamps = array_values(array_filter($timestamps, fn($t) => ($now - (int)$t) < $floodWindow));

        if (count($timestamps) >= $floodMaxMsgs) {
            $wait = $floodWindow - ($now - (int)$timestamps[0]);
            echo json_encode(['ok' => false, 'error' => "Too many messages! Wait {$wait}s."], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $timestamps[] = $now;
        $rateLimit[$ip] = $timestamps;

        foreach ($rateLimit as $k => $v) {
            $rateLimit[$k] = array_values(array_filter($v, fn($t) => ($now - (int)$t) < $floodWindow));
            if (empty($rateLimit[$k])) unset($rateLimit[$k]);
        }

        // Save message
        $messages = shoutbox_read_json($messagesFile, []);

        $messages[] = [
            'name' => shoutbox_html($name),
            'msg'  => shoutbox_html($msg),
            'time' => date('H:i'),
            'ts'   => $now,
        ];

        if (count($messages) > $maxMessages) {
            $messages = array_slice($messages, -$maxMessages);
        }

        shoutbox_write_json($messagesFile, $messages);
        shoutbox_write_json($rateLimitFile, $rateLimit);

        echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // --- GET messages + online users ---
    if ($action === 'fetch') {
        $since = intval($_GET['since'] ?? 0);
        $messages = shoutbox_read_json($messagesFile, []);

        $new = array_values(array_filter($messages, fn($m) => (int)($m['ts'] ?? 0) > $since));

        echo json_encode([
            'messages' => $new,
            'online'   => array_values($online),
            'ts'       => $now,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['ok' => false, 'error' => 'Unknown action.'], JSON_UNESCAPED_UNICODE);
    exit;
}
