<?php

namespace Database\Seeders;

use Hamcrest\Core\Set;
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
        $s->value = 'PizzaReader.png';
        $s->save();

        $s = new Settings();
        $s->key = 'cover';
        $s->value = 'cover.png';
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
        $s->key = 'pdf_chapter';
        $s->value = '1';
        $s->save();

        $s = new Settings();
        $s->key = 'max_cache_pdf';
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
        $s->key = 'default_hidden_comic';
        $s->value = '1';
        $s->save();

        $s = new Settings();
        $s->key = 'default_hidden_chapter';
        $s->value = '1';
        $s->save();

        $s = new Settings();
        $s->key = 'social_facebook';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'social_instagram';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'social_twitter';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'social_telegram_channel';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'social_telegram_group';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'social_telegram_bot';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'social_discord';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'homepage_html';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'menu';
        $s->value = null;
        $s->save();

        $s = new Settings();
        $s->key = 'default_chapter_thumbnail';
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
