<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingSumAndCountToChaptersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('chapters', function (Blueprint $table) {
            $table->bigInteger('rating_sum', false, true)->default(0);
            $table->bigInteger('rating_count', false, true)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rating_sum');
            $table->dropColumn('rating_count');
        });
    }
}
