<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>DAKER One — умный AI-ассистент для бизнеса.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <h1 class="mb-4">👋 Добро пожаловать в <strong>DAKER One</strong> — умный AI-ассистент для бизнеса</h1>

        <p class="lead">Это чат-бот платформа для автоматизации поддержки клиентов.</p>

        <div class="mb-4">
            <h3>🔐 Вход в систему</h3>
            <ul>
                <li><a href="{{ route('client.login') }}">Войти как клиент</a></li>
            </ul>
        </div>

        <hr class="my-5">

        <div class="card shadow">
            <div class="card-header bg-success text-white">
                🤖 Общение с AI-ассистентом
            </div>
            <div class="card-body">
                <div id="chat-box" class="border rounded p-3 mb-3"
                    style="height: 250px; overflow-y: auto; background: #f8f9fa;">
                    <div class="text-muted">AI ждёт вашего вопроса...</div>
                </div>

                <form id="chat-form">
                    <div class="input-group">
                        <input type="text" id="user-input" class="form-control" placeholder="Напишите сообщение..."
                            required>
                        <button class="btn btn-success" type="submit">Отправить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('chat-form');
        const input = document.getElementById('user-input');
        const chatBox = document.getElementById('chat-box');

        let history = [];

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const text = input.value.trim();

            // 🚨 Ограничение по длине (не более 300 символов)
            if (text.length === 0 || text.length > 300) {
                alert('Пожалуйста, введите сообщение до 300 символов.');
                return;
            }

            // 🚨 Ограничение на количество предложений (не более 3)
            const sentences = text.split(/[.!?]/).filter(s => s.trim().length > 0);
            if (sentences.length > 3) {
                alert('Пожалуйста, напишите не более 2–3 предложений.');
                return;
            }

            // Показываем сообщение пользователя
            const userMsg = document.createElement('div');
            userMsg.classList.add('text-end', 'mb-2');
            userMsg.innerHTML = `<span class="badge bg-primary">${text}</span>`;
            chatBox.appendChild(userMsg);
            input.value = '';

            // Добавляем заглушку
            const botMsg = document.createElement('div');
            botMsg.classList.add('text-start', 'text-muted', 'mb-2');
            botMsg.textContent = '🤖 AI печатает...';
            chatBox.appendChild(botMsg);
            chatBox.scrollTop = chatBox.scrollHeight;

            // Сохраняем историю
            history.push({
                role: 'user',
                content: text
            });
            if (history.length > 6) history.shift();

            try {
                const response = await fetch('/api/public-chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        history: history
                    })
                });

                const data = await response.json();
                botMsg.textContent = data.answer ?? '[Ошибка ответа от AI]';

                if (data.answer) {
                    history.push({
                        role: 'assistant',
                        content: data.answer
                    });
                    if (history.length > 6) history.shift();
                }
            } catch (error) {
                botMsg.textContent = '❌ Ошибка соединения с AI.';
            }
        });


        // 🕒 Автообновление страницы через 15 минут бездействия

        let inactivityTimer;

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                location.reload(); // 🔄 перезагрузка страницы
            }, 15 * 60 * 1000); // 15 минут = 900000 мс
        }

        // Сбрасываем таймер при любом действии пользователя
        ['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, resetInactivityTimer, false);
        });

        resetInactivityTimer(); // запускаем таймер при загрузке страницы
    </script>


</body>

</html>
