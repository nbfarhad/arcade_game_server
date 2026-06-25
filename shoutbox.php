<?php
// simple include guard
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>

<style>
#shoutbox {
    position: fixed;
    right: 10px;
    bottom: 10px;
    width: 260px;
    height: 400px;
    background: #1b1c22;
    border: 1px solid #2a2b33;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    font-family: Arial;
    z-index: 9999;
}

#shout-header {
    background: #111218;
    padding: 8px;
    font-size: 13px;
    text-align: center;
    color: #fff;
    border-bottom: 1px solid #2a2b33;
}

#shout-messages {
    flex: 1;
    padding: 8px;
    overflow-y: auto;
    font-size: 12px;
    color: #ddd;
}

.msg {
    margin-bottom: 6px;
}

.msg b {
    color: #ffcc00;
}

#shout-input {
    display: flex;
    flex-direction: column;
    padding: 6px;
    border-top: 1px solid #2a2b33;
    gap: 5px;
}

#shout-input input,
#shout-input textarea {
    width: 100%;
    border: none;
    border-radius: 6px;
    padding: 6px;
    font-size: 12px;
    outline: none;
    background: #0f0f12;
    color: white;
}

#shout-input button {
    background: #ff4757;
    border: none;
    padding: 6px;
    border-radius: 6px;
    color: white;
    cursor: pointer;
}

#online {
    font-size: 11px;
    color: #aaa;
    margin-top: 4px;
}
</style>

<div id="shoutbox">
    <div id="shout-header">💬 Live Chat <span id="online"></span></div>

    <div id="shout-messages"></div>

    <div id="shout-input">
        <input id="name" placeholder="Your name">
        <textarea id="msg" placeholder="Say something..." rows="2"></textarea>
        <button onclick="sendMsg()">Send</button>
    </div>
</div>

<script>
let lastTS = 0;
let active = true;

document.addEventListener("visibilitychange", () => {
    active = !document.hidden;
});

function fetchMsgs() {
    if (!active) return;

    fetch("<?= $BASE ?>/shoutbox.php?shout_action=fetch&since=" + lastTS)
        .then(r => r.json())
        .then(data => {
            let box = document.getElementById("shout-messages");

            data.messages.forEach(m => {
                let div = document.createElement("div");
                div.className = "msg";
                div.innerHTML = `<b>${m.name}</b>: ${m.msg}`;
                box.appendChild(div);
            });

            if (data.messages.length) {
                lastTS = data.ts;
                box.scrollTop = box.scrollHeight;
            }

            document.getElementById("online").innerText =
                " • " + (data.online?.length || 0) + " online";
        });
}

function sendMsg() {
    let name = document.getElementById("name").value;
    let msg  = document.getElementById("msg").value;

    if (!name || !msg) return;

    fetch("<?= $BASE ?>/shoutbox.php?shout_action=post", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "name=" + encodeURIComponent(name) +
              "&msg=" + encodeURIComponent(msg)
    })
    .then(r => r.json())
    .then(res => {
        if (res.ok) {
            document.getElementById("msg").value = "";
            fetchMsgs();
        }
    });
}

// polling (optimized)
setInterval(() => {
    if (active) fetchMsgs();
}, 3000);

fetchMsgs();
</script>
