<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Чат-бот</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }

        .widget-container {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .widget-header {
            background: #4CAF50;
            color: white;
            padding: 10px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .widget-close {
            cursor: pointer;
            font-size: 18px;
            background: transparent;
            border: none;
            color: white;
        }

        .widget-body {
            flex-grow: 1;
            padding: 10px;
            overflow-y: auto;
            background: white;
        }

        .widget-footer {
            border-top: 1px solid #ccc;
            padding: 10px;
            background: #fff;
        }

        #message {
            width: 100%;
            padding: 8px;
        }

        .msg {
            margin: 5px 0;
        }

        .me {
            text-align: right;
            color: blue;
        }

        .bot {
            text-align: left;
            color: green;
        }
        .bot.typing {
    color: #888;
    font-style: italic;
}
    </style>
</head>

<body>
    <div class="widget-container">
        <div class="widget-header">
            🤖 Чат-бот
            <button class="widget-close" onclick="window.parent.postMessage('close-chat', '*')">✖</button>
        </div>

        <div id="chat" class="widget-body"></div>

        <div class="widget-footer">
            <form id="form">
                <input type="text" maxlength="200" id="message" placeholder="Ваш вопрос..." autocomplete="off" />
                <button type="submit">Отправить</button>
            </form>
        </div>
    </div>
    <audio id="chat-sound" src="https://www.soundjay.com/buttons/sounds/button-3.mp3" preload="auto"></audio>

    <script>
        const token = "{{ $client->api_token }}";
        const chat = document.getElementById('chat');
        const form = document.getElementById('form');
        const input = document.getElementById('message');
        const STORAGE_KEY = 'chat_history_{{ $client->id }}';
        const TIMESTAMP_KEY = 'chat_timestamp_{{ $client->id }}';
        const MAX_AGE_MS = 2 * 24 * 60 * 60 * 1000; // 2 дня

        // 🧠 Сохранение истории
        function saveChat() {
            localStorage.setItem(STORAGE_KEY, chat.innerHTML);
            localStorage.setItem(TIMESTAMP_KEY, Date.now().toString());
        }

        // 🧠 Загрузка истории
        function loadChat() {
            const saved = localStorage.getItem(STORAGE_KEY);
            const timestamp = localStorage.getItem(TIMESTAMP_KEY);

            if (saved && timestamp) {
                const age = Date.now() - parseInt(timestamp);
                if (age <= MAX_AGE_MS) {
                    chat.innerHTML = saved;
                    chat.scrollTop = chat.scrollHeight;
                    return;
                }
            }

            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(TIMESTAMP_KEY);
        }

        // 📦 Вставляем приветствие при загрузке
        window.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                loadChat(); // загружаем историю
            } else {
                chat.innerHTML += `<div class="msg bot">Здравствуйте! Я бот, чем могу помочь?</div>`;
                saveChat(); // только если история новая
            }

            chat.scrollTop = chat.scrollHeight;
        });


        // 📨 Отправка сообщений
        form.addEventListener('submit', async (e) => {
            document.getElementById('chat-sound').play();

            e.preventDefault();
            const text = input.value.trim();
            if (!text || text.length > 200) {
                alert("Сообщение должно быть не более 200 символов.");
                return;
            }

            chat.innerHTML += `<div class="msg me">${text}</div>`;
            input.value = '';
            saveChat();
            const typing = document.createElement('div');
typing.className = 'msg bot typing';
typing.innerText = 'Бот печатает...';
chat.appendChild(typing);
chat.scrollTop = chat.scrollHeight;

            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: text
                })
            });

            const data = await response.json();
            typing.remove();
            chat.innerHTML += `<div class="msg bot">${data.answer ?? '[ошибка ответа]'}</div>`;
            chat.scrollTop = chat.scrollHeight;
            saveChat();
        });

        // ❌ Закрытие iframe по сообщению
        window.addEventListener('message', (e) => {
            if (e.data === 'close-chat') {
                window.parent.postMessage('close-chat', '*');
            }
        });
    </script>

</body>

</html>
