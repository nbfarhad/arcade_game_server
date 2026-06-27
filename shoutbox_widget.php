<!-- ============================================================
     shoutbox_widget.php — include this inside <body> on any page
     Requires shoutbox.php to be included at the top of that page
     ============================================================ -->

<div id="shoutbox">
    <div id="shout-header">
        <span>💬 Live Chat</span>
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

<!-- Mobile toggle button -->
<button id="shout-toggle" onclick="toggleShoutbox()">💬 Chat</button>

<style>
    /* ---- Desktop: sidebar on the right ---- */
    body {
        padding-right: 310px;
    }

    #shoutbox {
        position: fixed;
        top: 0;
        right: 0;
        width: 300px;
        height: 100vh;
        background: #1a1a20;
        border-left: 2px solid #2f3542;
        display: flex;
        flex-direction: column;
        z-index: 500;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    #shout-toggle { display: none; }

    /* ---- Mobile: hidden by default, toggled ---- */
    @media (max-width: 768px) {
        body {
            padding-right: 0 !important;
        }

        #shoutbox {
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 60vh;
            border-left: none;
            border-top: 2px solid #ff4757;
            border-radius: 12px 12px 0 0;
            display: none;
            z-index: 1000;
        }

        #shoutbox.mobile-open {
            display: flex;
        }

        #shout-toggle {
            display: block;
            position: fixed;
            bottom: 16px;
            right: 16px;
            background: #ff4757;
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 10px 18px;
            font-size: 0.9rem;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 4px 15px rgba(255,71,87,0.4);
        }
    }

    /* ---- Shoutbox internals ---- */
    #shout-header {
        background: #ff4757;
        color: #fff;
        padding: 12px 14px;
        font-weight: bold;
        font-size: 0.95rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    #shout-online-count {
        font-size: 0.78rem;
        opacity: 0.9;
    }

    #shout-online-list {
        background: #121214;
        padding: 6px 10px;
        font-size: 0.75rem;
        color: #2ed573;
        min-height: 24px;
        flex-shrink: 0;
        border-bottom: 1px solid #2f3542;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #shout-messages {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        scroll-behavior: smooth;
    }

    #shout-messages::-webkit-scrollbar { width: 4px; }
    #shout-messages::-webkit-scrollbar-thumb { background: #2f3542; border-radius: 4px; }

    .shout-msg {
        background: #1e1e24;
        border: 1px solid #2f3542;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 0.82rem;
        line-height: 1.4;
        word-break: break-word;
    }

    .shout-msg-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3px;
    }

    .shout-msg-name { color: #ff4757; font-weight: bold; }
    .shout-msg-time { color: #57606f; font-size: 0.72rem; }
    .shout-msg-text { color: #e1e1e6; }

    .shout-empty {
        color: #57606f;
        font-size: 0.82rem;
        text-align: center;
        padding: 20px 0;
    }

    #shout-form {
        padding: 10px;
        border-top: 1px solid #2f3542;
        background: #1a1a20;
        flex-shrink: 0;
    }

    #shout-form input {
        width: 100%;
        background: #121214;
        border: 1px solid #2f3542;
        border-radius: 6px;
        color: #e1e1e6;
        padding: 7px 10px;
        font-size: 0.85rem;
        outline: none;
        margin-bottom: 6px;
        font-family: inherit;
        box-sizing: border-box;
    }

    #shout-form input:focus { border-color: #ff4757; }

    #shout-input-row {
        display: flex;
        gap: 6px;
    }

    #shout-input-row input { margin-bottom: 0; }

    #shout-send {
        background: #ff4757;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 7px 12px;
        cursor: pointer;
        font-size: 1rem;
        flex-shrink: 0;
        transition: background 0.2s;
    }

    #shout-send:hover { background: #e84057; }

    #shout-error {
        color: #ff4757;
        font-size: 0.75rem;
        margin-top: 5px;
        min-height: 16px;
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
            list.style.color = '#57606f';
        } else {
            list.textContent = users.map(u => u.name || 'Guest').join(' · ');
            list.style.color = '#2ed573';
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
