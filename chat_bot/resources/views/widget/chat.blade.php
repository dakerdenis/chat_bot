<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Чат-бот</title>

    <link rel="stylesheet" href="http://127.0.0.1:8000/assets/style/chat-bot.css">
</head>

<body>
    <div class="widget-container">

        <div class="widget__header__container">
            <div class="widget__header__container-content">
                <div class="widget-header">
                    <div class="header-info">
                        <img src="http://127.0.0.1:8000/assets/images/dai-logo.png" alt="Logo" class="logo" />
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
                <img src="http://127.0.0.1:8000/assets/images/back.png" alt="">
            </div>
        </div>
    
        <div class="widget-chat-area">
            <div id="chat" class="widget-body"></div>
        </div>
    
        <div class="widget-footer">
            <form id="form">
                <input type="text" maxlength="200" id="message" placeholder="Yazın və ENTER düyməsini basın..." autocomplete="off" />
                <button class="send-message-btn" type="submit">
                    <img src="http://127.0.0.1:8000/assets/images/arrow.svg" alt="">
                </button>
            </form>
        </div>
    </div>
    
    

    <audio id="chat-sound" src="https://www.soundjay.com/buttons/sounds/button-3.mp3" preload="auto"></audio>
    <script>
        const apiToken = "{{ $client->api_token }}";
        const clientId = "{{ $client->id }}";
    </script>
    <script src="http://127.0.0.1:8000/assets/js/widget.js"></script>
</body>

</html>
