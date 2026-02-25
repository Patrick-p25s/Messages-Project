function formatDateTime(value) {
    const date = new Date(value.replace(' ', 'T'));
    return Number.isNaN(date.getTime()) ? value : date.toLocaleString('fr-FR');
}

function renderMessages(container, messages, currentUserId) {
    const nearBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 32;

    container.innerHTML = '';
    for (const message of messages) {
        const mine = Number(message.sender_id) === Number(currentUserId);
        const wrapper = document.createElement('article');
        wrapper.className = `msg ${mine ? 'me' : 'other'}`;

        const meta = document.createElement('div');
        meta.className = 'meta';
        meta.textContent = `${message.sender_name} • ${formatDateTime(message.created_at)}`;

        const body = document.createElement('div');
        body.textContent = message.body;

        wrapper.appendChild(meta);
        wrapper.appendChild(body);
        container.appendChild(wrapper);
    }

    if (nearBottom || messages.length < 3) {
        container.scrollTop = container.scrollHeight;
    }
}

async function loadMessages(chatBox) {
    const type = chatBox.dataset.chatType;
    const targetId = chatBox.dataset.targetId;

    const endpoint = type === 'private'
        ? `/fetch_private.php?user_id=${encodeURIComponent(targetId)}`
        : `/fetch_group.php?group_id=${encodeURIComponent(targetId)}`;

    const res = await fetch(endpoint, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (!res.ok) {
        throw new Error('Unable to fetch messages');
    }

    const data = await res.json();
    renderMessages(chatBox, data.messages || [], data.current_user_id || 0);
}

function initChat() {
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');

    if (!chatBox || !chatForm) {
        return;
    }

    let busy = false;

    const refresh = async () => {
        if (busy) {
            return;
        }
        busy = true;
        try {
            await loadMessages(chatBox);
        } catch (err) {
            console.error(err);
        } finally {
            busy = false;
        }
    };

    chatForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(chatForm);
        const action = chatForm.getAttribute('action');

        const res = await fetch(action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        if (!res.ok) {
            alert('Impossible d\'envoyer le message.');
            return;
        }

        const textArea = chatForm.querySelector('textarea[name="body"]');
        if (textArea) {
            textArea.value = '';
        }

        await refresh();
    });

    refresh();
    setInterval(refresh, 3000);
}

document.addEventListener('DOMContentLoaded', initChat);
