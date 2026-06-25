<!-- SHOUTBOX UI -->
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
            <button id="shout-send">➤</button>
        </div>
        <div id="shout-error"></div>
    </div>
</div>

<style>
/* (keep your existing CSS — unchanged) */
#shoutbox {
    position: fixed;
    right: 0;
    top: 0;
    width: 300px;
    height: 100vh;
    background: #1a1a20;
    color: white;
    z-index: 9999;
    display: flex;
    flex-direction: column;
}
#shout-messages { flex: 1; overflow-y: auto; padding: 10px; }
#shout-form { padding: 10px; }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {

    let lastTs = 0;

    const nameInput = document.getElementById('shout-name');
    const msgInput  = document.getElementById('shout-msg');
    const errorDiv  = document.getElementById('shout-error');
    const sendBtn   = document.getElementById('shout-send');

    // ❗ SAFE CHECK (prevents crash)
    if (!nameInput || !msgInput || !sendBtn) {
        console.log("Shoutbox elements missing");
        return;
    }

    nameInput.value = localStorage.getItem('shout_name') || '';

    nameInput.addEventListener('change', () => {
        localStorage.setItem('shout_name', nameInput.value.trim());
    });

    msgInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') sendMessage();
    });

    sendBtn.addEventListener('click', sendMessage);

    function sendMessage() {
        const name = nameInput.value.trim();
        const msg  = msgInput.value.trim();

        if (!name || !msg) return;

        fetch('?shout_action=post', {
            method: 'POST',
            body: new URLSearchParams({ name, msg })
        }).then(() => {
            msgInput.value = '';
            fetchMessages();
        });
    }

    function fetchMessages() {
        fetch(`?shout_action=fetch&since=${lastTs}`)
            .then(r => r.json())
            .then(data => {
                lastTs = data.ts || lastTs;

                const box = document.getElementById('shout-messages');
                if (!box) return;

                box.innerHTML = (data.messages || []).map(m => `
                    <div style="padding:5px;border-bottom:1px solid #333">
                        <b>${m.name}</b>: ${m.msg}
                    </div>
                `).join('');
            })
            .catch(err => console.log("shout error", err));
    }

    fetchMessages();
    setInterval(fetchMessages, 5000);
});
</script>
