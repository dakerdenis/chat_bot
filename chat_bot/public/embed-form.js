(function () {
    const token = document.currentScript.getAttribute('data-api-token');
    if (!token) return console.error('[DAKER Embed] API token missing');

    const container = document.createElement('div');
    container.innerHTML = `
        <div id="embed-chat-box" style="border:1px solid #ccc;padding:10px;margin:20px 0;background:#f9f9f9;max-width:600px">
            <div id="chat-area" style="height:200px;overflow:auto;margin-bottom:10px;color:#333;font-family:sans-serif;font-size:14px;"></div>
            <form id="embed-chat-form">
                <input type="text" id="chat-input" style="width:80%" placeholder="Введите сообщение..." required maxlength="300">
                <button type="submit">Отправить</button>
            </form>
        </div>
    `;
    document.body.appendChild(container);

    const chatArea = document.getElementById('chat-area');
    const form = document.getElementById('embed-chat-form');
    const input = document.getElementById('chat-input');

    let history = [];

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text || text.length > 300) return;

        const sentences = text.split(/[.!?]/).filter(s => s.trim() !== '');
        if (sentences.length > 3) {
            alert('Не более 2–3 предложений.');
            return;
        }

        chatArea.innerHTML += `<div><b>Вы:</b> ${text}</div>`;
        history.push({ role: 'user', content: text });
        if (history.length > 6) history.shift();
        input.value = '';

        const loading = document.createElement('div');
        loading.innerHTML = `<i>AI печатает...</i>`;
        chatArea.appendChild(loading);
        chatArea.scrollTop = chatArea.scrollHeight;

        try {
            const res = await fetch('https://gpt.daker.az/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-API-TOKEN': token
                },
                body: JSON.stringify({ message: text })
            });

            const data = await res.json();
            loading.remove();

            if (data.answer) {
                chatArea.innerHTML += `<div><b>Бот:</b> ${data.answer}</div>`;
                history.push({ role: 'assistant', content: data.answer });
                if (history.length > 6) history.shift();
            } else {
                chatArea.innerHTML += `<div><b>Бот:</b> Ошибка ответа</div>`;
            }
        } catch (e) {
            loading.remove();
            chatArea.innerHTML += `<div style="color:red;"><b>Ошибка:</b> не удалось подключиться</div>`;
        }

        chatArea.scrollTop = chatArea.scrollHeight;
    });
})();
