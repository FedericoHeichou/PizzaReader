<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComicDownloadsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('comic_downloads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('comic_id', false, true)->unique();
            $table->integer('size', false, true);
            $table->timestamp('last_download')->nullable();
            $table->timestamps();

            $table->foreign('comic_id')->references('id')->on('comics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('comic_downloads');
    }
}
