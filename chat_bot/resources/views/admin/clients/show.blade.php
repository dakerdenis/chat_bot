@extends('layouts.admin')

@section('title', 'Клиент: ' . $client->name)

@section('content')
    <h2>👤 Клиент: {{ $client->name }}</h2>

    <p>Email: {{ $client->email }}</p>
    <p>Тариф: <strong>{{ $client->plan }}</strong></p>
    <p>Диалоги: {{ $client->dialog_used }} из {{ $client->dialog_limit }}</p>
    <p>Активация: {{ $client->is_active ? '✅ Да' : '❌ Нет' }}</p>
    <p>AI-упрощение использовано: <strong>{{ $compressions }}</strong> раз</p>
    <p><strong>API-ключ:</strong> <code>{{ $client->api_token }}</code></p>

    <hr>
    <h3>📝 История запросов</h3>

    @if ($logs->count() === 0)
        <p>Нет запросов.</p>
    @else
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Метод</th>
                    <th>Endpoint</th>
                    <th>Payload</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at }}</td>
                        <td>{{ $log->method }}</td>
                        <td>{{ $log->endpoint }}</td>
                        @php
                            $data = json_decode($log->payload, true);
                            $message = $data['message'] ?? '[нет сообщения]';
                        @endphp
                        <td>
                            <pre style="max-width: 300px; white-space: pre-wrap;">{{ $message }}</pre>
                        </td>

                        <td>{{ $log->ip_address }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Пагинация --}}
        <div style="margin-top: 20px;">
            {{ $logs->links() }}
        </div>
    @endif

    <p><a href="{{ route('admin.clients.index') }}">← Назад к списку</a></p>
@endsection
