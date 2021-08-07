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
            $table->string('language', 2);
            $table->integer('volume', false, true)->nullable();
            $table->integer('chapter', false, true)->nullable();
            $table->integer('subchapter', false, true)->nullable();
            $table->string('title')->nullable();
            $table->string('slug');
            $table->string('salt');
            $table->string('prefix')->nullable();
            $table->boolean('hidden')->default(0);
            $table->bigInteger('views', false, true)->default(0);
            $table->decimal('rating', 4, 2, true)->nullable()->default(null);
            $table->string('download_link', 512)->nullable();
            $table->bigInteger('team_id', false, true)->nullable();
            $table->bigInteger('team2_id', false, true)->nullable();
            $table->timestamp('published_on')->useCurrent();
            $table->timestamp('publish_start')->useCurrent();
            $table->timestamp('publish_end')->nullable();
            $table->timestamps();

            // This is not very useful because multiple unique constraint with some values at NULL permits duplicates
            // id | lan | vol | ch | sub
            //  1 |  en |   1 |  1 | NULL
            //  2 |  en |   1 |  1 | NULL
            // This can happen in the database without code controls
            $table->unique(['comic_id', 'language', 'volume', 'chapter', 'subchapter']);
            $table->unique(['comic_id', 'slug']);
            $table->foreign('comic_id')->references('id')->on('comics')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
            $table->foreign('team2_id')->references('id')->on('teams')->onDelete('set null');
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
