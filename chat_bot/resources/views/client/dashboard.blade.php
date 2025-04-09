@extends('layouts.client')

@section('title', 'Панель клиента')

@section('content')
    <h2>Добро пожаловать, {{ $client->name }}</h2>

    <p>Email: {{ $client->email }}</p>
    <p>Ваш тариф: <strong>{{ $client->plan }}</strong></p>
    <p>Диалоги: {{ $client->dialog_used }} из {{ $client->dialog_limit }}</p>
    <p>Апи ключ: {{ $client->api_token }}</p>
    <p>Ограничение по запросам в минуту: {{ $client->rate_limit }}</p>
    <hr>

    <h3>🔧 Что можно будет сделать здесь:</h3>
    <ul>
        <li>📄 Загрузка базы знаний</li>
        <li>📊 Статистика диалогов</li>
        <li>⚙️ Настройки аккаунта</li>
    </ul>

    <h3>🧠 Ваши промты</h3>

    @if (session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    @error('limit')
        <p style="color:red">{{ $message }}</p>
    @enderror
    @error('content')
        <p style="color:red">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('client.prompts.store') }}">
        @csrf
        <label>Название:</label><br>
        <input type="text" name="title" maxlength="100"><br><br>

        <label>Текст промта:</label><br>
        <textarea name="content" rows="4" cols="50" maxlength="500"></textarea><br><br>

        <button type="submit">Добавить промт</button>
    </form>

    <hr>
    <h4>📋 Список промтов:</h4>
    <ul>
        @foreach (DB::table('client_prompts')->where('client_id', $client->id)->get() as $prompt)
            <li><strong>{{ $prompt->title }}</strong>: {{ Str::limit($prompt->content, 100) }}</li>
        @endforeach
    </ul>

@endsection
