@extends('layouts.admin')

@section('title', 'Создать клиента')

@section('content')
    <h2>➕ Новый клиент</h2>

    @if ($errors->any())
        <ul style="color:red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.clients.store') }}" method="POST">
        @csrf
        <label>Имя клиента:</label><br>
        <input type="text" name="name" required><br><br>
    
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
    
        <label>Пароль:</label><br>
        <input type="password" name="password" required><br><br>
    
        <label>Тариф:</label><br>
        <select name="plan">
            <option value="trial">trial</option>
            <option value="basic">basic</option>
            <option value="standard">standard</option>
            <option value="premium">premium</option>
        </select><br><br>
    
        <label>Домены клиента (через запятую):</label><br>
        <input type="text" name="domains" placeholder="example.com, test.ru" required><br><br>
    
        <button type="submit">Создать клиента</button>
    </form>
    

    <br><a href="{{ route('admin.clients.index') }}">← Назад к списку</a>
@endsection
