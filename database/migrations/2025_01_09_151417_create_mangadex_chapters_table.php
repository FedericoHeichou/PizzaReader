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
        Schema::create('mangadex_chapters', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('mangadex_id')->unique()->index();
            $table->string('title');
            $table->integer('chapter_number');
            $table->integer('volume_number')->nullable();
            $table->string('language', '5');
            $table->boolean('is_processed')->default(false);
            $table->foreignIdFor(\App\Models\MangadexManga::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Chapter::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mangadex_chapters');
    }
};
