@extends('layouts.admin')

@section('title', 'Редактировать клиента')

@section('content')
    <h2>✏️ Редактировать клиента</h2>

    <form method="POST" action="{{ route('admin.clients.update', $client) }}">
        @csrf
        @method('PUT')

        <label>Имя:</label><br>
        <input type="text" name="name" value="{{ old('name', $client->name) }}" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="{{ old('email', $client->email) }}" required><br><br>

        <label>Пароль (если нужно изменить):</label><br>
        <input type="password" name="password"><br><br>

        <label>Тариф:</label><br>
        <select name="plan">
            @foreach (['trial', 'basic', 'standard', 'premium'] as $plan)
                <option value="{{ $plan }}" @if ($client->plan === $plan) selected @endif>{{ $plan }}</option>
            @endforeach
        </select><br><br>

        <label>Активен:</label>
        <input type="checkbox" name="is_active" value="1" {{ $client->is_active ? 'checked' : '' }}><br><br>

        <button type="submit">💾 Сохранить</button>
    </form>

    <hr>
<h3>🌐 Домены</h3>

@if ($client->domains->isEmpty())
    <p>Нет доменов</p>
@else
    @foreach ($client->domains as $domain)
        <div style="margin-bottom: 10px;">
            <form action="{{ route('admin.clients.update', $client) }}" method="POST" style="display:inline;">
                @csrf
                @method('PUT')
                <input type="hidden" name="edit_domain_id" value="{{ $domain->id }}">
                <input type="text" name="edit_domain" value="{{ $domain->domain }}" required>
                <button type="submit">💾 Обновить</button>
            </form>

            <form action="{{ route('admin.clients.domains.destroy', ['client' => $client->id, 'domain_id' => $domain->id]) }}" method="POST" style="display:inline;">

                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Удалить этот домен?')">🗑️</button>
            </form>
        </div>
    @endforeach
@endif

<hr>
<h4>➕ Добавить домен</h4>
<form method="POST" action="{{ route('admin.clients.update', $client) }}">
    @csrf
    @method('PUT')
    <input type="text" name="new_domain" placeholder="example.com" required>
    <button type="submit">Добавить</button>
</form>

    <p><a href="{{ route('admin.clients.index') }}">← Назад к списку</a></p>
@endsection
