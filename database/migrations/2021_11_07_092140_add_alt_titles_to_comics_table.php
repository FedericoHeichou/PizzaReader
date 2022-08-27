<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration  {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('comics', function (Blueprint $table) {
            $table->text('alt_titles')->nullable()->default(null);;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('comics', function (Blueprint $table) {
            $table->dropColumn('alt_titles');
        });
    }
};
