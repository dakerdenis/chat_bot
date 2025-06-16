<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Чат-бот</title>

    <link rel="stylesheet" href="https://gpt.daker.az/public/assets/style/chat-bot.css">
</head>

<body>
    <div class="widget-container">

        <div class="widget__header__container">
            <div class="widget__header__container-content">
                <div class="widget-header">
                    <div class="header-info">
                        <img src="https://gpt.daker.az/public/assets/images/dai-logo.png" alt="Logo"
                            class="logo" />
                        <div>
                            <div class="title">D.A.I. Chat bot</div>
                            <div class="subtitle">Daker Artificial Intelligence</div>
                        </div>
                    </div>
                    <button class="widget-close" onclick="window.parent.postMessage('close-chat', '*')">✕</button>
                </div>

                <div class="intro">
                    <p class="welcome">Salam və xoş gəlmisiniz!</p>
                    <p class="instruction">İstədiyiniz sualları verə bilərsiniz.</p>
                </div>

            </div>
            <div class="widget__header__container-image">
                <img src="https://gpt.daker.az/public/assets/images/back.png" alt="">
            </div>
        </div>

        <div class="widget-chat-area">
            <div id="chat" class="widget-body"></div>
        </div>

        <div class="widget-footer">
            <form id="form">
                <input type="text" maxlength="200" id="message" placeholder="Yazın və ENTER düyməsini basın..."
                    autocomplete="off" />
                <button class="send-message-btn" type="submit">
                    <img src="https://gpt.daker.az/public/assets/images/arrow.svg" alt="">
                </button>
            </form>
        </div>
    </div>



    <audio id="chat-sound" src="https://www.soundjay.com/buttons/sounds/button-3.mp3" preload="auto"></audio>
    <script>
        const apiToken = "{{ $client->api_token }}";
        const clientId = "{{ $client->id }}";
    </script>
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
                chat.innerHTML += `
        <div class="msg-row bot">
          <div class="msg bot">Salam! Mən D.A.I. köməkçisiyəm. Sizə necə kömək edə bilərəm?</div>
        </div>`;


                saveChat(); // только если история новая
            }

            chat.scrollTop = chat.scrollHeight;
        });

        let isSending = false;
        // 📨 Отправка сообщений
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (isSending) return; // 🔒 Защита от двойного клика
            isSending = true;

            document.getElementById('chat-sound').play();

            const text = input.value.trim();
            if (!text || text.length > 200) {
                alert("Mesaj 200 simvoldan çox olmamalıdır.");

                isSending = false;
                return;
            }

            chat.innerHTML += `
    <div class="msg-row me">
      <div class="msg me message-me">${text}</div>
    </div>`;


            input.value = '';
            saveChat();

            const typing = document.createElement('div');
            typing.className = 'msg-row bot typing';
            typing.innerHTML = `<div class="msg">Bot yazır...</div>`;

            chat.appendChild(typing);


            chat.scrollTop = chat.scrollHeight;

            try {
                const response = await fetch('https://gpt.daker.az/api/chat', {
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
                chat.innerHTML += `
        <div class="msg-row bot">
          <div class="msg bot message-bot">${data.answer ?? '[ Sorğu zamanı xəta baş verdi]'}</div>
        </div>`;

                chat.scrollTop = chat.scrollHeight;
                saveChat();
            } catch (error) {
                chat.innerHTML += `<div class="msg bot">❌ Sorğu zamanı xəta baş verdi</div>`;

            } finally {
                isSending = false; // 🔓 Снова разрешаем отправку
            }
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
