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
        Schema::create('mangas', function (Blueprint $table) {
            $table->id();
            $table->integer('anilist_id')->unsigned()->index()->unique();
            $table->integer('idMal')->nullable()->unsigned()->index();
            $table->string('name')->nullable(false);
            $table->string('format')->nullable(false);
            $table->json('titles')->nullable(false);
            $table->json('synonyms')->nullable();
            $table->boolean('is_adult')->default(false)->nullable(false);
            $table->string('status')->nullable(true);
            $table->integer('chapters')->unsigned()->nullable(true);
            $table->integer('volumes')->unsigned()->nullable(true);
            $table->text('description')->nullable(false);
            $table->string('source')->nullable(true);
            $table->integer('score')->nullable(true);
//            $table->hasMany();//TODO add many-to-many relation to Genres
//            $table->hasMany();//TODO add many-to-many relation to Tags
            $table->string('cover_image')->nullable(false);//TODO probably change (or add in addition) relation to local file
            $table->date('started_at')->nullable(true);
            $table->date('ended_at')->nullable(true);
            //todo add relations (links to another related manga's)
            //todo add characters
            //todo add external links

            $table->timestamps();
        });


        Schema::create('mangas_tags', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Anilist\Manga::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Anilist\Tag::class)->constrained()->cascadeOnDelete();
            $table->unique(['manga_id', 'tag_id']);
        });

        Schema::create('mangas_genres', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Anilist\Manga::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Anilist\Genre::class)->constrained()->cascadeOnDelete();
            $table->unique(['manga_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mangas');
    }
};
