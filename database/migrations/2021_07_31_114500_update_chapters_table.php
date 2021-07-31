<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateChaptersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('chapters', function (Blueprint $table) {
            $table->boolean('licensed')->default(0);
            $table->string('thumbnail')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('licensed');
            $table->dropColumn('thumbnail');
        });
    }
}
