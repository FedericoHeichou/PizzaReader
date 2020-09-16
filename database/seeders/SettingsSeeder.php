<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Settings;

class SettingsSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Schema::disableForeignKeyConstraints();
        Settings::truncate();
        Schema::enableForeignKeyConstraints();

        $s = new Settings();
        $s->key = 'reader_name';
        $s->value = 'PizzaReader';
        $s->save();

        $s = new Settings();
        $s->key = 'reader_name_long';
        $s->value = 'PizzaReader - Read manga online';
        $s->save();

        $s = new Settings();
        $s->key = 'description';
        $s->value = 'Read manga online using PizzaReader';
        $s->save();

        $s = new Settings();
        $s->key = 'logo';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'home_link';
        $s->value = null;
        $s->save();


        $s = new Settings();
        $s->key = 'default_language';
        $s->value = 'en';
        $s->save();

        $s = new Settings();
        $s->key = 'download_chapter';
        $s->value = '1';
        $s->save();

        $s = new Settings();
        $s->key = 'download_volume';
        $s->value = '1';
        $s->save();

        $s = new Settings();
        $s->key = 'max_cache_download';
        $s->value = '350';
        $s->save();

        $s = new Settings();
        $s->key = 'footer';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'registration_enabled';
        $s->value = '1';
        $s->save();

        $s = new Settings();
        $s->key = 'recaptcha_public';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'recaptcha_private';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'adsense_publisher';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'banner_top';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'banner_bottom';
        $s->value = null;
        $s->save();
    }
}
