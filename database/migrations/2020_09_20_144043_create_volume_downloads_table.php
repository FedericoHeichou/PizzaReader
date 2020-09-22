<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolumeDownloadsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('volume_downloads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('comic_id', false, true);
            $table->string('language', 2);
            $table->integer('volume', false, true);
            $table->string('name');
            $table->string('filename');
            $table->integer('size', false, true);
            $table->timestamp('last_download')->nullable();
            $table->timestamps();

            $table->unique(['comic_id', 'language', 'volume']);
            $table->foreign('comic_id')->references('id')->on('comics')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('volume_downloads');
    }
}
