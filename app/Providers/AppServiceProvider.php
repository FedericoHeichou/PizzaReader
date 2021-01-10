<?php

namespace App\Providers;

use App\Models\Settings;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Schema::defaultStringLength(191);
        if (getenv('APP_NAME') && Schema::hasTable('settings')) {
            $settings = Settings::whereNotNull('value')->where('value', '<>', '')->pluck('value', 'key')->toArray();
            $settings['logo_asset_72'] = asset('storage/img/logo/' . substr($settings['logo'], 0, -4)) . '-72.png';
            $settings['logo_asset_192'] = asset('storage/img/logo/' . substr($settings['logo'], 0, -4)) . '-192.png';
            $settings['cover_path'] = 'storage/img/cover/' . $settings['cover'];
            config([
                'settings' => $settings
            ]);
        }
    }
}
