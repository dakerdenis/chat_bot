<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password'); // хранить через bcrypt
            $table->string('api_token', 80)->unique();
            $table->string('language')->default('en');
            $table->enum('plan', ['basic', 'standard', 'premium'])->default('basic');
            $table->integer('dialog_limit')->default(1000);
            $table->integer('dialog_used')->default(0); // обновляется каждый раз при запросе
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
