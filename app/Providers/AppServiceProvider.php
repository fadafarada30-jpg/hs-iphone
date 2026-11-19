<?php

namespace App\Providers;

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
        // Saat di-hosting (Railway), host publik tidak diketahui di build time.
        // Ambil dari request sehingga tautan tidak bergantung pada env APP_URL.
        if (! app()->runningInConsole() && ! empty($_SERVER['HTTP_HOST'])) {
            $host = preg_replace('/[^a-zA-Z0-9.\-:]/', '', $_SERVER['HTTP_HOST']);

            if ($host !== '') {
                config(['app.url' => 'https://'.$host]);
            }
        }
    }
}
