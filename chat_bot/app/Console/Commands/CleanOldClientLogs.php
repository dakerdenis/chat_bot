<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanOldClientLogs extends Command
{
    protected $signature = 'logs:clean-old';
    protected $description = 'Удаляет старые логи использования чата (старше 30 дней)';

    public function handle(): void
    {
        $deleted = DB::table('client_usage_logs')
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        $this->info("Удалено $deleted старых записей из client_usage_logs.");
    }
}