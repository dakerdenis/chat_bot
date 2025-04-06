<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Клиентская админ-панель')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f9f9f9; }
        header, footer { background: #004488; color: white; padding: 10px; }
        nav a { margin-right: 10px; color: white; text-decoration: none; }
        main { background: white; padding: 20px; border-radius: 6px; margin-top: 20px; }
    </style>
</head>
<body>
    <header>
        <strong>🤖 Панель клиента</strong>
        <nav style="float:right;">
            <a href="{{ url('/client/dashboard') }}">🏠 Главная</a>
            <form method="POST" action="{{ route('client.logout') }}" style="display:inline;">
                @csrf
                <button type="submit">Выйти</button>
            </form>
        </nav>
        <div style="clear:both;"></div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        Клиентский доступ © {{ now()->year }}
    </footer>
</body>
</html>
