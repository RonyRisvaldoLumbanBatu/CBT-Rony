<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Aktifkan mode ketat untuk database:
        // - Mencegah lazy loading (N+1 query)
        // - Mencegah pengisian atribut yang tidak terdaftar (silent discarding)
        // - Mencegah akses ke atribut yang tidak diambil (missing attributes)
        Model::shouldBeStrict(! $this->app->isProduction());
    }
}
