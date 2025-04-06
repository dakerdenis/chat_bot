<h2>Добро пожаловать, {{ $admin->name }}</h2>
<p>Вы в главной админ-панели</p>

<form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button type="submit">Выйти</button>
</form>
