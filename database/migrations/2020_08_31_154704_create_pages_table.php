<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chapter_id', false, true);
            $table->string('filename');
            $table->integer('size', false, true);
            $table->integer('width', false, true);
            $table->integer('height', false, true);
            $table->string('mime', 32);
            $table->boolean('hidden')->default(0);
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
        Schema::dropIfExists('pages');
    }
}
