<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
            'password' => Hash::make('password123'),
            'api_token' => Str::random(60),
            'language' => 'en',
            'is_active' => true,
        ]);
    }
}
