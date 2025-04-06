<h2>Вход в админ-панель</h2>

@if ($errors->any())
    <div style="color:red;">{{ $errors->first() }}</div>
@endif

<form method="POST" action="/admin/login">
    @csrf
    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="password" name="password" placeholder="Пароль"><br><br>
    <button type="submit">Войти</button>
</form>
