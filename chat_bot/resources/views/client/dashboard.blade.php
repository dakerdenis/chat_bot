<!DOCTYPE html>
<html>
<head>
    <title>Админ-панель клиента</title>
</head>
<body>
    <h2>Добро пожаловать, {{ $client->name }}</h2>
    <p>Email: {{ $client->email }}</p>

    <form method="POST" action="{{ route('client.logout') }}">
        @csrf
        <button type="submit">Выйти</button>
    </form>
</body>
</html>
 