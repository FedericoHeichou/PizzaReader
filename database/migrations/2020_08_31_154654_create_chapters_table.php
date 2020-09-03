<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChaptersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('comic_id', false, true);
            $table->bigInteger('team_id', false, true);
            $table->bigInteger('team2_id', false, true)->nullable();
            $table->integer('volume', false, true)->nullable();
            $table->integer('chapter', false, true)->nullable();
            $table->integer('subchapter', false, true)->nullable();
            $table->string('title')->nullable();
            $table->string('slug');
            $table->string('salt');
            $table->string('prefix')->nullable();
            $table->boolean('hidden')->default(0);
            $table->bigInteger('views', false, true)->default(0);
            $table->string('download_link')->nullable();
            $table->string('language', 2);
            $table->timestamps();

            $table->foreign('comic_id')->references('id')->on('comics')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->foreign('team2_id')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('chapters');
    }
}
