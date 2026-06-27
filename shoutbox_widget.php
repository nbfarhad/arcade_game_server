<!-- ============================================================
     shoutbox_widget.php — include this inside <body> on any page
     Requires shoutbox.php to be included at the top of that page
     ============================================================ -->

<div id="shoutbox">
    <div id="shout-header">
        <span class="shout-title">💬 Live Chat</span>
        <span id="shout-online-count">● 0 online</span>
    </div>

    <div id="shout-online-list"></div>
    <div id="shout-messages"></div>

    <div id="shout-form">
        <input id="shout-name" type="text" placeholder="Your name" maxlength="20">
        <div id="shout-input-row">
            <input id="shout-msg" type="text" placeholder="Say something..." maxlength="200">
            <button id="shout-send" onclick="shoutSend()">➤</button>
        </div>
        <div id="shout-error"></div>
    </div>
</div>

<button id="shout-toggle" onclick="toggleShoutbox()">💬 Chat</button>

<style>
:root {
    --chat-bg: rgba(11, 18, 35, 0.92);
    --chat-panel: rgba(17, 24, 39, 0.88);
    --chat-border: rgba(255,255,255,0.08);
    --chat-text: #e5eefc;
    --chat-muted: #8b9ab8;
    --chat-accent: #ff4d6d;
    --chat-accent-2: #38bdf8;
}

body {
    padding-right: 320px;
}

#shoutbox {
    position: fixed;
    top: 0;
    right: 0;
    width: 310px;
    height: 100vh;
    background: linear-gradient(180deg, rgba(17,24,39,0.96), rgba(10,15,28,0.96));
    border-left: 1px solid var(--chat-border);
    box-shadow: -16px 0 50px rgba(0,0,0,0.35);
    display: flex;
    flex-direction: column;
    z-index: 9999;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    backdrop-filter: blur(14px);
}

#shout-toggle { display: none; }

#shout-header {
    padding: 14px 14px 12px;
    color: var(--chat-text);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--chat-border);
    background: linear-gradient(180deg, rgba(255,77,109,0.14), rgba(56,189,248,0.06));
    flex-shrink: 0;
}

.shout-title {
    font-weight: 800;
    letter-spacing: 0.02em;
}

#shout-online-count {
    font-size: 0.78rem;
    color: #9cf5c7;
}

#shout-online-list {
    background: rgba(6,10,20,0.65);
    padding: 8px 12px;
    font-size: 0.75rem;
    color: #9cf5c7;
    min-height: 26px;
    flex-shrink: 0;
    border-bottom: 1px solid var(--chat-border);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#shout-messages {
    flex: 1;
    overflow-y: auto;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    scroll-behavior: smooth;
}

#shout-messages::-webkit-scrollbar { width: 5px; }
#shout-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.18); border-radius: 99px; }

.shout-msg {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 14px;
    padding: 10px 12px;
    font-size: 0.85rem;
    line-height: 1.45;
    box-shadow: 0 10px 24px rgba(0,0,0,0.18);
    word-break: break-word;
}

.shout-msg-header {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 4px;
}

.shout-msg-name {
    color: #ff90a3;
    font-weight: 700;
}

.shout-msg-time {
    color: var(--chat-muted);
    font-size: 0.72rem;
}

.shout-msg-text {
    color: var(--chat-text);
}

.shout-empty {
    color: var(--chat-muted);
    font-size: 0.82rem;
    text-align: center;
    padding: 24px 0;
}

#shout-form {
    padding: 12px;
    border-top: 1px solid var(--chat-border);
    background: rgba(10,15,28,0.92);
    flex-shrink: 0;
}

#shout-form input {
    width: 100%;
    background: rgba(6,10,20,0.88);
    border: 1px solid var(--chat-border);
    border-radius: 10px;
    color: var(--chat-text);
    padding: 9px 11px;
    font-size: 0.86rem;
    outline: none;
    margin-bottom: 7px;
    font-family: inherit;
    box-sizing: border-box;
}

#shout-form input:focus {
    border-color: rgba(56,189,248,0.4);
    box-shadow: 0 0 0 3px rgba(56,189,248,0.12);
}

#shout-input-row {
    display: flex;
    gap: 8px;
}

#shout-input-row input { margin-bottom: 0; }

#shout-send {
    background: linear-gradient(135deg, var(--chat-accent), #ff6b3d);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 9px 12px;
    cursor: pointer;
    font-size: 1rem;
    flex-shrink: 0;
    transition: transform .15s ease, filter .15s ease;
    box-shadow: 0 10px 22px rgba(255,77,109,0.24);
}

#shout-send:hover { transform: translateY(-1px); filter: brightness(1.05); }

#shout-error {
    color: #ff7b93;
    font-size: 0.75rem;
    margin-top: 6px;
    min-height: 16px;
}

@media (max-width: 768px) {
    body { padding-right: 0 !important; }

    #shoutbox {
        top: auto;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 62vh;
        border-left: none;
        border-top: 1px solid var(--chat-border);
        border-radius: 18px 18px 0 0;
        display: none;
    }

    #shoutbox.mobile-open { display: flex; }

    #shout-toggle {
        display: block;
        position: fixed;
        bottom: 16px;
        right: 16px;
        background: linear-gradient(135deg, var(--chat-accent), #ff6b3d);
        color: #fff;
        border: none;
        border-radius: 999px;
        padding: 11px 18px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        z-index: 10001;
        box-shadow: 0 10px 24px rgba(255,77,109,0.34);
    }
}
</style>

<script>
function toggleShoutbox() {
    const box = document.getElementById('shoutbox');
    const btn = document.getElementById('shout-toggle');
    box.classList.toggle('mobile-open');
    btn.textContent = box.classList.contains('mobile-open') ? '✕ Close' : '💬 Chat';
}

(function () {
    const POLL_INTERVAL = 3000;
    let lastTs = 0;
    let allMessages = [];

    const nameInput = document.getElementById('shout-name');
    const msgInput  = document.getElementById('shout-msg');
    const errorDiv  = document.getElementById('shout-error');

    const escapeHtml = (str) => String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');

    nameInput.value = localStorage.getItem('shout_name') || '';
    nameInput.addEventListener('change', () => {
        localStorage.setItem('shout_name', nameInput.value.trim());
    });

    msgInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') shoutSend();
    });

    function shoutFetch() {
        const name = nameInput.value.trim();
        const url  = `?shout_action=fetch&since=${lastTs}&name=${encodeURIComponent(name)}`;

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    allMessages = allMessages.concat(data.messages);
                    renderMessages();
                }
                if (data.ts) lastTs = data.ts;
                renderOnline(data.online || []);
            })
            .catch(() => {});
    }

    function renderMessages() {
        const box = document.getElementById('shout-messages');
        const wasAtBottom = box.scrollHeight - box.scrollTop <= box.clientHeight + 40;

        if (allMessages.length === 0) {
            box.innerHTML = '<div class="shout-empty">No messages yet. Say hi! 👋</div>';
            return;
        }

        box.innerHTML = allMessages.map(m => `
            <div class="shout-msg">
                <div class="shout-msg-header">
                    <span class="shout-msg-name">${escapeHtml(m.name)}</span>
                    <span class="shout-msg-time">${escapeHtml(m.time)}</span>
                </div>
                <div class="shout-msg-text">${escapeHtml(m.msg)}</div>
            </div>
        `).join('');

        if (wasAtBottom) box.scrollTop = box.scrollHeight;
    }

    function renderOnline(users) {
        const count = document.getElementById('shout-online-count');
        const list  = document.getElementById('shout-online-list');
        count.textContent = `● ${users.length} online`;
        if (users.length === 0) {
            list.textContent = 'No one online right now';
            list.style.color = '#8b9ab8';
        } else {
            list.textContent = users.map(u => u.name || 'Guest').join(' · ');
            list.style.color = '#9cf5c7';
        }
    }

    window.shoutSend = function () {
        const name = nameInput.value.trim();
        const msg  = msgInput.value.trim();
        errorDiv.textContent = '';

        if (!name) { errorDiv.textContent = 'Please enter your name first.'; return; }
        if (!msg)  { errorDiv.textContent = 'Message cannot be empty.'; return; }

        localStorage.setItem('shout_name', name);

        const body = new URLSearchParams({ name, msg });

        fetch('?shout_action=post', { method: 'POST', body })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    msgInput.value = '';
                    shoutFetch();
                } else {
                    errorDiv.textContent = data.error || 'Could not send.';
                }
            })
            .catch(() => { errorDiv.textContent = 'Network error. Try again.'; });
    };

    shoutFetch();
    setInterval(shoutFetch, POLL_INTERVAL);
})();
</script>
