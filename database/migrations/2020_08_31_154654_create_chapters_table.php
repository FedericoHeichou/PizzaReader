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
            $table->integer('volume', false, true);
            $table->integer('chapter', false, true);
            $table->integer('subchapter', false, true);
            $table->string('title');
            $table->string('stub');
            $table->string('salt');
            $table->boolean('hidden');
            $table->bigInteger('views', false, true)->default(0);
            $table->string('download_link')->nullable();
            $table->timestamps();

            $table->foreign('comic_id')->references('id')->on('comics');
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
