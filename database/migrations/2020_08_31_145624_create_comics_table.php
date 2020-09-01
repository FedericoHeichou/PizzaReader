<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComicsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('comics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('stub')->unique();
            $table->string('salt')->unique();
            $table->boolean('hidden');
            $table->string('author');
            $table->string('artist');
            $table->string('target');
            $table->string('status');
            $table->string('description');
            $table->string('thumbnail');
            $table->string('custom_chapter');
            $table->bigInteger('comic_format_id', false, true);
            $table->boolean('adult');
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
    }
}
