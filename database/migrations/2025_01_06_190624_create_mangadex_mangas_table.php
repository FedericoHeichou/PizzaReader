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
        Schema::create('mangadex_mangas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('mangadex_id')->unique()->index();
            $table->foreignIdFor(\App\Models\Comic::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mangadex_mangas');
    }
};
