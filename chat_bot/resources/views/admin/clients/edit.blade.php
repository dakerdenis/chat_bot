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

    <p><a href="{{ route('admin.clients.index') }}">← Назад к списку</a></p>
@endsection
