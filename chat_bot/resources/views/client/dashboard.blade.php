@extends('layouts.client')

@section('title', 'Панель клиента')

@section('content')
    <h2>Добро пожаловать, {{ $client->name }}</h2>

    <p>Email: {{ $client->email }}</p>
    <p>Ваш тариф: <strong>{{ $client->plan }}</strong></p>
    <p>Диалоги: {{ $client->dialog_used }} из {{ $client->dialog_limit }}</p>
    <p>Апи ключ: {{ $client->api_token }}</p>
    <p>Ограничение по запросам в минуту: {{ $client -> rate_limit }}</p>
    <hr>

    <h3>🔧 Что можно будет сделать здесь:</h3>
    <ul>
        <li>📄 Загрузка базы знаний</li>
        <li>📊 Статистика диалогов</li>
        <li>⚙️ Настройки аккаунта</li>
    </ul>
@endsection
