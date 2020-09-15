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
        if (Schema::hasTable('settings')) {
            config([
                'settings' => Settings::whereNotNull('value')->where('value', '<>', '')->pluck('value', 'key')->toArray()
            ]);
        }
    }
}
