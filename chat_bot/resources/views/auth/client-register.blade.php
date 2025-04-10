<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация клиента</title>
</head>
<body>
    <h2>Регистрация</h2>

    <form method="POST" action="{{ route('client.register.submit') }}">
        @csrf

        <label>Имя:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Пароль:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Подтвердите пароль:</label><br>
        <input type="password" name="password_confirmation" required><br><br>

        <label>Домен сайта (пример: yoursite.com):</label><br>
        <input type="text" name="domain" required><br><br>

        <button type="submit">Зарегистрироваться</button>
    </form>

    <p><a href="{{ route('client.login') }}">← Уже есть аккаунт? Войти</a></p>
</body>
</html>
