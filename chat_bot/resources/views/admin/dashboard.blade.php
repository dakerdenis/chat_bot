{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Главная')

@section('content')
    <h2>Привет, {{ $admin->name }}</h2>

    <p>Добро пожаловать в главную админ-панель.</p>

    <hr>

    <h3>📊 Общая статистика</h3>

    <ul>
        <li>Клиентов: {{ \App\Models\Client::count() }}</li>
        <li>Активных клиентов: {{ \App\Models\Client::where('is_active', true)->count() }}</li>
        <li>Запросов за сегодня: {{ \DB::table('client_usage_logs')->whereDate('created_at', today())->count() }}</li>
    </ul>

    <hr>

    <h3>🔧 Быстрые действия</h3>

    <ul>
        <li><a href="{{ route('admin.clients.index') }}">📁 Управление клиентами</a></li>
        <li><a href="{{ route('admin.clients.create') }}">➕ Создать нового клиента</a></li>
    </ul>
@endsection
