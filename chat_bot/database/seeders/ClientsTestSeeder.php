<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientsTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            ['plan' => 'basic', 'limit' => 1000, 'rate_limit' => 20],
            ['plan' => 'standard', 'limit' => 2000, 'rate_limit' => 60],
            ['plan' => 'premium', 'limit' => 5000, 'rate_limit' => 100],
        ];
        
    
        foreach (range(1, 5) as $i) {
            $selected = $plans[array_rand($plans)];
    
            Client::create([
                'name' => "Client $i",
                'email' => "client$i@example.com",
                'password' => Hash::make('password123'),
                'api_token' => Str::random(60),
                'language' => 'en',
                'is_active' => true,
                'plan' => $selected['plan'],
                'dialog_limit' => $selected['limit'],
                'dialog_used' => 0,
                'rate_limit' => $selected['rate_limit'], // 💡
            ]);
            
        }
    }
}
