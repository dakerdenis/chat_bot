@extends('layouts.admin')

@section('title', 'Клиенты')

@section('content')
    <h2>Список клиентов</h2>

    <a href="{{ route('admin.clients.create') }}">➕ Добавить нового клиента</a>

    @if (session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Тариф</th>
                <th>Диалоги</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->plan }}</td>
                    <td>{{ $client->dialog_used }} / {{ $client->dialog_limit }}</td>
                    <td>{{ $client->is_active ? 'Активен' : 'Заблокирован' }}</td>
                    <td>
                        <a href="{{ route('admin.clients.show', $client) }}">👁️</a>
                        <a href="{{ route('admin.clients.edit', $client) }}">✏️</a>
                        <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Удалить клиента?')">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
