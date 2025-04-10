@extends('layouts.client')

@section('title', 'Панель клиента')

@section('content')

    <h2>Добро пожаловать, {{ $client->name }}</h2>

    <p>Email: {{ $client->email }}</p>
    <p>Ваш тариф: <strong>{{ $client->plan }}</strong></p>
    <p>Диалоги: {{ $client->dialog_used }} из {{ $client->dialog_limit }}</p>
    <p>Апи ключ: {{ $client->api_token }}</p>
    <p>Ограничение по запросам в минуту: {{ $client->rate_limit }}</p>
    <hr>

    <h3>🔧 Что можно будет сделать здесь:</h3>
    <ul>
        <li>📄 Загрузка базы знаний</li>
        <li>📊 Статистика диалогов</li>
        <li>⚙️ Настройки аккаунта</li>
    </ul>

    @php
        use Illuminate\Support\Facades\DB;

        $prompts = DB::table('client_prompts')->where('client_id', $client->id)->get();
        $promptCount = $prompts->count();

        $maxPrompts = match ($client->plan) {
            'trial' => 1,
            'basic' => 2,
            'standard' => 4,
            'premium' => 6,
            default => 1,
        };

        $maxLength = match ($client->plan) {
            'trial' => 300,
            'basic' => 450,
            'standard' => 600,
            'premium' => 750,
            default => 300,
        };
    @endphp

    <h3>🧠 Ваши промты</h3>

    <p>Вы использовали <strong>{{ $promptCount }}</strong> из <strong>{{ $maxPrompts }}</strong> возможных инструкций.
    </p>

    @if (session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    @error('limit')
        <p style="color:red">{{ $message }}</p>
    @enderror
    @error('content')
        <p style="color:red">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('client.prompts.store') }}">
        @csrf
        <label>Название:</label><br>
        <input type="text" name="title" maxlength="100" required><br><br>


        <label>Текст промта (макс {{ $maxLength }} символов):</label><br>
        <textarea id="prompt-content" name="content" rows="4" cols="50" maxlength="{{ $maxLength }}" required></textarea><br>
        <small>Символов: <span id="char-count">0</span> / {{ $maxLength }}</small><br><br>

        <button type="button" id="compress-btn">💡 Сжать с помощью ИИ</button>
        <div id="compress-status" style="font-size: 13px; margin-top: 5px;"></div>



        <button type="submit" @if ($promptCount >= $maxPrompts) disabled @endif>Добавить промт</button>
    </form>

    @if ($promptCount >= $maxPrompts)
        <p style="color:red">Вы достигли лимита инструкций. Удалите одну или обновите тариф.</p>
    @endif

    <hr>
    <h4>📋 Список ваших промтов:</h4>
    <ul>
        @foreach ($prompts as $prompt)
        <li style="margin-bottom: 10px;">
            <strong>{{ $prompt->title }}</strong><br>
            {{ $prompt->content }}<br>
    
            <button type="button" onclick="openEditModal({{ $prompt->id }}, '{{ addslashes($prompt->title) }}', `{{ addslashes($prompt->content) }}`)">✏️ Редактировать</button>
    
            <form method="POST" action="{{ route('client.prompts.destroy', $prompt->id) }}" style="display:inline">
                @csrf
                @method('DELETE')
                <button onclick="return confirm('Удалить этот промт?')" style="color: red;">🗑 Удалить</button>
            </form>
        </li>
    @endforeach
    
    </ul>

    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index:9999;">
        <div style="background:white; width:90%; max-width:600px; margin:50px auto; padding:20px; border-radius:8px; position:relative;">
            <h3>✏️ Редактировать промт</h3>
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editId">
                <label>Название:</label><br>
                <input type="text" id="editTitle" name="title" maxlength="100" readonly><br><br>
    
                <label>Текст:</label><br>
                <textarea id="editContent" name="content" rows="4" style="width:100%;"></textarea><br><br>
    
                <button type="submit">💾 Сохранить</button>
                <button type="button" onclick="closeEditModal()">❌ Отмена</button>
            </form>
        </div>
    </div>
    
    <script>
        const textarea = document.getElementById('prompt-content');
        const counter = document.getElementById('char-count');
        const compressBtn = document.getElementById('compress-btn');
        const status = document.getElementById('compress-status');

        // Счётчик символов
        textarea.addEventListener('input', () => {
            counter.textContent = textarea.value.length;
        });

        // Сжатие промта
        compressBtn.addEventListener('click', async () => {
            const text = textarea.value.trim();
            if (!text) return;

            status.textContent = '⏳ Сжимаем...';

            try {
                const response = await fetch('{{ route('client.prompts.compress') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        text
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    textarea.value = data.result;
                    counter.textContent = data.result.length;
                    status.textContent = '✅ Сжато и вставлено!';
                } else {
                    status.textContent = '❌ Ошибка: ' + (data.error ?? 'неизвестно');
                }
            } catch (error) {
                status.textContent = '❌ Ошибка сети. Проверь соединение.';
            }
        });
    </script>
<script>
    function openEditModal(id, title, content) {
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('editId').value = id;
        document.getElementById('editTitle').value = title;
        document.getElementById('editContent').value = content;

        const form = document.getElementById('editForm');
        form.action = `/client/prompts/${id}`;
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>



@endsection
