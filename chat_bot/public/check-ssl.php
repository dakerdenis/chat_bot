<?php

$ch = curl_init("https://api.openai.com/v1/models");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "Ошибка: " . curl_error($ch);
} else {
    echo "✅ Успешно! OpenAI ответил.";
}

curl_close($ch);
