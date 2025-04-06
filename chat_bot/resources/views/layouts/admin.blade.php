<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Админ-панель')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <header style="background:#333;color:#fff;padding:10px;">
        <h1>🌐 Главная админ-панель</h1>
        <nav>
            <a href="{{ route('admin.clients.index') }}">Клиенты</a> |
            <a href="{{ route('admin.clients.create') }}">➕ Новый клиент</a> |
            <a href="{{ url('/admin/dashboard') }}">Статистика</a> |
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Выйти</button>
            </form>
        </nav>
    </header>

    <main style="padding:20px;">
        @yield('content')
    </main>

    <footer style="text-align:center;padding:10px;color:gray;">
        SaaS © {{ now()->year }}
    </footer>
</body>
</html>
