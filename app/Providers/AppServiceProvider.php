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
            $settings['logo_path_72'] = isset($settings['logo']) && $settings['logo'] ?
                asset('storage/img/logo/' . substr($settings['logo'], 0, -4)) . '-72.png' :  asset('img/logo/PizzaReader.png');
            config([
                'settings' => $settings
            ]);
        }
    }
}
