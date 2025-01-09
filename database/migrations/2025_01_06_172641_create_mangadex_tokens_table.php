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
        Schema::create('mangadex_tokens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('access_token');
            $table->dateTime('expires_at');
            $table->text('refresh_token');
            $table->dateTime('refresh_expires_at');
            $table->string('token_type',20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mangadex_tokens');
    }
};
