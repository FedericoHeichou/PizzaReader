<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChapterDownloadsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('chapter_downloads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chapter_id', false, true)->unique();
            $table->integer('size', false, true);
            $table->string('name');
            $table->string('filename');
            $table->timestamp('last_download')->nullable();
            $table->timestamps();

            $table->foreign('chapter_id')->references('id')->on('chapters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('chapter_downloads');
    }
}
