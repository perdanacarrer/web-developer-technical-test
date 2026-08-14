<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OmdbService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(OmdbService::class, function () {
            return new OmdbService(
                config('services.omdb.base_url'),
                config('services.omdb.key')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
