// public/widget.js
(function() {
    const token = document.currentScript.getAttribute('data-api-token');
    if (!token) {
        console.error('[Widget] API token not provided.');
        return;
    }

    // Создание кнопки
    const button = document.createElement('div');
    button.innerHTML = '<img src="https://gpt.daker.az/public/assets/images/dai.png" alt="chat" style="width: 30px; height: 30px;" />';
    Object.assign(button.style, {
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        width: '60px',
        height: '60px',
        borderRadius: '50%',
        backgroundColor: '#1F9D96',
        color: 'white',
        fontSize: '28px',
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        cursor: 'pointer',
        boxShadow: '0 4px 12px rgba(0, 0, 0, 0.3)',
        zIndex: '2147483647',
        transition: 'background-color 0.3s ease',
    });

    button.addEventListener('mouseover', () => {
        button.style.backgroundColor = '#188a84';
    });
    button.addEventListener('mouseout', () => {
        button.style.backgroundColor = '#1F9D96';
    });

    document.body.appendChild(button);

    // Создание iframe
    const iframe = document.createElement('iframe');
    iframe.src = 'http://127.0.0.1:8000/chat-widget/' + token;
    Object.assign(iframe.style, {
        position: 'fixed',
        bottom: '90px',
        right: '20px',
        width: '370px',
        height: '520px',
        border: 'none',
        borderRadius: '12px',
        boxShadow: '0 8px 20px rgba(0, 0, 0, 0.2)',
        zIndex: '2147483646',
        display: 'none',
        overflow: 'hidden',
        transform: 'translateY(100px)',
        opacity: '0',
        transition: 'transform 0.3s ease, opacity 0.3s ease',
    });
    document.body.appendChild(iframe);

    // Показ с анимацией
    function showIframe() {
        iframe.style.display = 'block';
        setTimeout(() => {
            iframe.style.transform = 'translateY(0)';
            iframe.style.opacity = '1';
        }, 10);
    }

    // Скрытие с анимацией
    function hideIframe() {
        iframe.style.transform = 'translateY(100px)';
        iframe.style.opacity = '0';
        setTimeout(() => {
            iframe.style.display = 'none';
        }, 300);
    }

    // Переключение отображения iframe
    button.addEventListener('click', function() {
        if (iframe.style.display === 'none') {
            showIframe();
        } else {
            hideIframe();
        }
    });

    // Слушаем сообщение от iframe (закрытие чата)
    window.addEventListener('message', function(event) {
        if (event.data === 'close-chat') {
            hideIframe();
        }
    });
})();