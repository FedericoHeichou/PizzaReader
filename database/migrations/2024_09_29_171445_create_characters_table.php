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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->integer('anilist_id')->unsigned()->index()->unique();
            $table->string('name')->nullable(false);
            $table->string('role')->nullable(false);
            $table->text('image')->nullable(true);
            $table->timestamps();
        });

        Schema::create('mangas_characters', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Anilist\Manga::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Anilist\Character::class)->constrained()->cascadeOnDelete();
            $table->unique(['manga_id', 'character_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
