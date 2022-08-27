<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('comics', function (Blueprint $table) {
            $table->id();
            $table->float('order_index', 8, 3);
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('salt');
            $table->boolean('hidden')->default(0);
            $table->string('author')->nullable();
            $table->string('artist')->nullable();
            $table->string('target')->nullable();
            $table->string('genres')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable()->default(null);
            $table->string('custom_chapter')->nullable();
            $table->bigInteger('comic_format_id', false, true);
            $table->boolean('adult')->default(0);
            $table->timestamps();

            $table->foreign('comic_format_id')->references('id')->on('comic_formats');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('comics');
        Storage::deleteDirectory('public/comics');
    }
};
