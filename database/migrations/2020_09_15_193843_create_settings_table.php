<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->primary(['key']);
        });

        $seeder = new \Database\Seeders\SettingsSeeder();
        $seeder->run();
        try {
            \Illuminate\Support\Facades\Artisan::call('storage:link');
        } catch (Exception $e) {
            // Some webservers can return 'symlink() has been disabled for security reasons'
            // In this case the webmaster need to manually exec inside the public directory
            // 'ln -s ../storage/app/public storage' using SSH or cronjob
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('settings');
    }
};
